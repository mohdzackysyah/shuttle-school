<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use App\Models\User;
use App\Models\Shuttle;
use App\Models\Route;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ScheduleController extends Controller
{
    public function index()
    {
        $schedules = Schedule::with(['driver', 'route', 'shuttle', 'students'])
            ->orderByRaw("FIELD(day_of_week, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday')")
            ->orderBy('pickup_time')
            ->get()
            ->groupBy('route_id');

        return view('schedules.index', compact('schedules'));
    }

    public function create()
    {
        // 1. Filter Driver: Hanya yang BELUM punya jadwal apapun
        $drivers = User::where('role', 'driver')
                    ->whereDoesntHave('schedules') 
                    ->get();

        // 2. Filter Mobil: Hanya yang BELUM dipakai di jadwal apapun (Opsional, agar konsisten)
        $shuttles = Shuttle::where('status', 'available')
                    ->whereDoesntHave('schedules')
                    ->get();

        $routes = Route::all();

        return view('schedules.create', compact('drivers', 'shuttles', 'routes'));
    }

    // --- API SISWA (FILTER OTOMATIS) ---
    public function getStudentsByRoute($routeId)
    {
        $route = Route::with('complexes')->findOrFail($routeId);
        $complexIds = $route->complexes->pluck('id');

        // LOGIKA BARU: 
        // Ambil siswa di rute ini YANG BELUM PUNYA JADWAL SAMA SEKALI
        $students = Student::whereIn('complex_id', $complexIds)
                    ->whereDoesntHave('schedules') // <--- Filter Anti-Double Booking
                    ->join('complexes', 'students.complex_id', '=', 'complexes.id')
                    ->select('students.id', 'students.name', 'students.address_note', 'complexes.name as complex_name')
                    ->orderBy('complexes.name')
                    ->orderBy('students.name')
                    ->get();

        return response()->json($students);
    }

    public function store(Request $request)
    {
        $request->validate([
            'route_id' => 'required',
            'driver_id' => 'required',
            'shuttle_id' => 'required',
            'student_ids' => 'required|array|min:1',
        ]);

        $inputData = $request->input('days');
        $studentIds = $request->input('student_ids');

        if(!$inputData) return back()->with('error', 'Pilih minimal satu hari!');

        $count = 0;
        foreach ($inputData as $dayName => $data) {
            if (isset($data['active']) && $data['active'] == 1) {
                
                $pickupTime = $data['pickup_time'] ?? null;
                $dropoffTime = $data['dropoff_time'] ?? null;

                if (!$pickupTime && !$dropoffTime) continue;

                $schedule = Schedule::create([
                    'day_of_week' => $dayName,
                    'pickup_time' => $pickupTime,
                    'dropoff_time' => $dropoffTime,
                    'route_id' => $request->route_id,
                    'driver_id' => $request->driver_id,
                    'shuttle_id' => $request->shuttle_id,
                ]);

                $schedule->students()->attach($studentIds);
                $count++;
            }
        }

        return redirect()->route('schedules.index')->with('success', "$count Jadwal berhasil dibuat.");
    }

    // --- BULK EDIT (LOGIKA CERDAS) ---
    public function editBulk($routeId)
    {
        $schedules = Schedule::where('route_id', $routeId)->get();
        
        if ($schedules->isEmpty()) {
            return back()->with('error', 'Jadwal tidak ditemukan.');
        }

        $firstSchedule = $schedules->first();
        $mappedSchedules = $schedules->keyBy('day_of_week');

        // 1. Ambil Siswa Existing (Agar checkbox mereka tetap menyala)
        $existingStudentIds = DB::table('schedule_student')
            ->whereIn('schedule_id', $schedules->pluck('id'))
            ->pluck('student_id')
            ->unique()
            ->toArray();

        // 2. Filter Driver: (Yang Bebas) GABUNG (Driver saat ini)
        $drivers = User::where('role', 'driver')
            ->where(function($q) use ($firstSchedule) {
                $q->whereDoesntHave('schedules') // Driver Bebas
                  ->orWhere('id', $firstSchedule->driver_id); // Driver Saat Ini
            })->get();

        // 3. Filter Mobil: (Yang Bebas) GABUNG (Mobil saat ini)
        $shuttles = Shuttle::where(function($q) use ($firstSchedule) {
                $q->whereDoesntHave('schedules')
                  ->orWhere('id', $firstSchedule->shuttle_id);
            })->get();
        
        // 4. Filter Siswa: (Yang Bebas) GABUNG (Siswa saat ini)
        $routeObj = Route::with('complexes')->find($routeId);
        $complexIds = $routeObj->complexes->pluck('id');

        $availableStudents = Student::whereIn('complex_id', $complexIds)
            ->where(function($q) use ($existingStudentIds) {
                $q->whereDoesntHave('schedules') // Siswa Bebas (Anak Baru)
                  ->orWhereIn('id', $existingStudentIds); // Siswa Lama (Agar tidak hilang)
            })
            ->with('complex')
            ->orderBy('name')
            ->get();

        return view('schedules.edit-bulk', compact(
            'firstSchedule',
            'mappedSchedules',
            'existingStudentIds',
            'drivers',
            'shuttles',
            'availableStudents'
        ));
    }

    public function updateBulk(Request $request, $routeId)
    {
        $request->validate([
            'driver_id' => 'required',
            'shuttle_id' => 'required',
            'student_ids' => 'required|array|min:1',
        ]);

        $inputData = $request->input('days');
        $studentIds = $request->input('student_ids');

        if(!$inputData) return back()->with('error', 'Pilih minimal satu hari!');

        foreach ($inputData as $dayName => $data) {
            
            if (isset($data['active']) && $data['active'] == 1) {
                
                $schedule = Schedule::updateOrCreate(
                    ['route_id' => $routeId, 'day_of_week' => $dayName],
                    [
                        'driver_id' => $request->driver_id,
                        'shuttle_id' => $request->shuttle_id,
                        'pickup_time' => $data['pickup_time'] ?? null,
                        'dropoff_time' => $data['dropoff_time'] ?? null,
                    ]
                );

                $schedule->students()->sync($studentIds);

            } else {
                $scheduleToDelete = Schedule::where('route_id', $routeId)->where('day_of_week', $dayName)->first();
                if($scheduleToDelete) {
                    $scheduleToDelete->students()->detach();
                    $scheduleToDelete->delete();
                }
            }
        }

        return redirect()->route('schedules.index')->with('success', 'Rangkaian jadwal diperbarui.');
    }

    // --- SINGLE EDIT ---
    public function edit($id)
    {
        $schedule = Schedule::with('students')->findOrFail($id);
        
        // Logic Filter Driver/Mobil/Siswa sama dengan Bulk Edit
        $drivers = User::where('role', 'driver')
            ->where(function($q) use ($schedule) {
                $q->whereDoesntHave('schedules')->orWhere('id', $schedule->driver_id);
            })->get();

        $shuttles = Shuttle::where(function($q) use ($schedule) {
                $q->whereDoesntHave('schedules')->orWhere('id', $schedule->shuttle_id);
            })->get();

        $routes = Route::all();

        $route = Route::with('complexes')->findOrFail($schedule->route_id);
        $complexIds = $route->complexes->pluck('id');
        $selectedStudentIds = $schedule->students->pluck('id')->toArray();

        $availableStudents = Student::whereIn('complex_id', $complexIds)
            ->where(function($q) use ($selectedStudentIds) {
                $q->whereDoesntHave('schedules')->orWhereIn('id', $selectedStudentIds);
            })
            ->with('complex')
            ->orderBy('name')
            ->get();

        return view('schedules.edit', compact('schedule', 'drivers', 'shuttles', 'routes', 'availableStudents', 'selectedStudentIds'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'driver_id' => 'required',
            'shuttle_id' => 'required',
            'pickup_time' => 'nullable',
            'dropoff_time' => 'nullable',
            'student_ids' => 'array',
        ]);

        $schedule = Schedule::findOrFail($id);
        
        $schedule->update([
            'driver_id' => $request->driver_id,
            'shuttle_id' => $request->shuttle_id,
            'pickup_time' => $request->pickup_time,
            'dropoff_time' => $request->dropoff_time,
        ]);

        $schedule->students()->sync($request->input('student_ids', []));

        return redirect()->route('schedules.index')->with('success', 'Jadwal hari ini diperbarui.');
    }

    public function destroy($id)
    {
        $schedule = Schedule::findOrFail($id);
        $schedule->students()->detach();
        $schedule->delete();
        return back()->with('success', 'Jadwal dihapus.');
    }

    public function checkAvailability(Request $request)
    {
        // Validasi bentrok tetap diperlukan untuk memastikan jam tidak tabrakan
        // meskipun kita sudah memfilter driver di dropdown.
        $driverId = $request->driver_id;
        $shuttleId = $request->shuttle_id;
        $day = $request->day;
        $time = $request->time;

        $conflict = Schedule::where('day_of_week', $day)
            ->where(function($q) use ($time) {
                $q->where('pickup_time', $time)
                  ->orWhere('dropoff_time', $time);
            })
            ->where(function($q) use ($driverId, $shuttleId) {
                $q->where('driver_id', $driverId)
                  ->orWhere('shuttle_id', $shuttleId);
            })
            ->with(['route'])
            ->first();

        if ($conflict) {
            $msg = "BENTROK! Driver/Mobil sudah ada jadwal di rute {$conflict->route->name} pada jam ini.";
            return response()->json(['status' => 'conflict', 'message' => $msg]);
        }

        return response()->json(['status' => 'available']);
    }
}