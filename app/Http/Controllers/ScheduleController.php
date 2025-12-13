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
        // Tampilkan semua jadwal
        $schedules = Schedule::with(['driver', 'route', 'shuttle', 'students'])
            ->orderByRaw("FIELD(day_of_week, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday')")
            ->orderBy('pickup_time')
            ->get()
            // Grouping Unik: Route + Driver + Shuttle
            // Agar jika ada rute sama tapi beda driver, dia terpisah card-nya
            ->groupBy(function ($item) {
                return $item->route_id . '-' . $item->driver_id . '-' . $item->shuttle_id;
            });

        return view('schedules.index', compact('schedules'));
    }

    public function create()
    {
        // 1. Filter Driver: Hanya yang BELUM punya jadwal apapun
        $drivers = User::where('role', 'driver')
                    ->whereDoesntHave('schedules') 
                    ->get();

        // 2. Filter Mobil: Hanya yang BELUM dipakai di jadwal apapun
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
        // Asumsi: Parameter $routeId disini sebenarnya adalah ID salah satu jadwal (schedule_id) 
        
        $referenceSchedule = Schedule::find($routeId); // Anggap $routeId ini adalah ID Jadwal
        
        if (!$referenceSchedule) {
            // Fallback: Mungkin user benar-benar kirim route_id
            $schedules = Schedule::where('route_id', $routeId)->get();
        } else {
            // Ambil semua jadwal yang MEMILIKI Route, Driver, dan Mobil yang SAMA dengan referensi ini
            $schedules = Schedule::where('route_id', $referenceSchedule->route_id)
                        ->where('driver_id', $referenceSchedule->driver_id)
                        ->where('shuttle_id', $referenceSchedule->shuttle_id)
                        ->get();
        }
        
        if ($schedules->isEmpty()) {
            return back()->with('error', 'Jadwal tidak ditemukan.');
        }

        $firstSchedule = $schedules->first();
        $mappedSchedules = $schedules->keyBy('day_of_week');

        // 1. Ambil Siswa Existing di GRUP JADWAL INI
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
        $shuttles = Shuttle::where('status', 'available')
            ->where(function($q) use ($firstSchedule) {
                $q->whereDoesntHave('schedules')
                  ->orWhere('id', $firstSchedule->shuttle_id);
            })->get();
        
        // 4. Filter Siswa: (Yang Bebas) GABUNG (Siswa saat ini)
        $routeObj = Route::with('complexes')->find($firstSchedule->route_id);
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

    public function updateBulk(Request $request, $id)
    {
        // $id disini adalah ID salah satu jadwal yang jadi referensi grup
        $referenceSchedule = Schedule::findOrFail($id);
        $currentRouteId = $referenceSchedule->route_id;
        $currentDriverId = $referenceSchedule->driver_id;
        $currentShuttleId = $referenceSchedule->shuttle_id;

        $request->validate([
            'driver_id' => 'required',
            'shuttle_id' => 'required',
            'student_ids' => 'required|array|min:1',
        ]);

        $inputData = $request->input('days');
        $studentIds = $request->input('student_ids');

        if(!$inputData) return back()->with('error', 'Pilih minimal satu hari!');

        foreach ($inputData as $dayName => $data) {
            
            // Cari jadwal spesifik di hari ini milik grup ini
            $schedule = Schedule::where('route_id', $currentRouteId)
                        ->where('driver_id', $currentDriverId)
                        ->where('shuttle_id', $currentShuttleId)
                        ->where('day_of_week', $dayName)
                        ->first();

            if (isset($data['active']) && $data['active'] == 1) {
                
                // Data baru yang mau disimpan
                $pickupTime = $data['pickup_time'] ?? null;
                $dropoffTime = $data['dropoff_time'] ?? null;

                if ($schedule) {
                    // Update yang sudah ada
                    $schedule->update([
                        'driver_id' => $request->driver_id, // Bisa ganti driver
                        'shuttle_id' => $request->shuttle_id, // Bisa ganti mobil
                        'pickup_time' => $pickupTime,
                        'dropoff_time' => $dropoffTime,
                    ]);
                } else {
                    // Buat baru jika hari ini belum ada
                    $schedule = Schedule::create([
                        'route_id' => $currentRouteId, // Rute tidak berubah
                        'day_of_week' => $dayName,
                        'driver_id' => $request->driver_id,
                        'shuttle_id' => $request->shuttle_id,
                        'pickup_time' => $pickupTime,
                        'dropoff_time' => $dropoffTime,
                    ]);
                }

                // Sync Siswa
                $schedule->students()->sync($studentIds);

            } else {
                // Jika hari ini dimatikan (uncheck), hapus jadwalnya
                if ($schedule) {
                    $schedule->students()->detach();
                    $schedule->delete();
                }
            }
        }

        return redirect()->route('schedules.index')->with('success', 'Rangkaian jadwal diperbarui.');
    }

    // --- [BARU] HAPUS RANGKAIAN (BULK DELETE) ---
    public function destroyBulk($id)
    {
        // 1. Ambil satu jadwal sebagai referensi
        $referenceSchedule = Schedule::findOrFail($id);
        
        // 2. Cari semua jadwal yang memiliki Rute, Driver, dan Mobil yang SAMA
        // (Ini mendefinisikan "Rangkaian" jadwal tersebut)
        $schedules = Schedule::where('route_id', $referenceSchedule->route_id)
                    ->where('driver_id', $referenceSchedule->driver_id)
                    ->where('shuttle_id', $referenceSchedule->shuttle_id)
                    ->get();

        // 3. Loop dan hapus (termasuk detach siswa)
        foreach($schedules as $schedule) {
            $schedule->students()->detach(); // Hapus relasi siswa di pivot
            $schedule->delete(); // Hapus jadwal
        }

        return back()->with('success', 'Seluruh rangkaian jadwal berhasil dihapus.');
    }

    // --- SINGLE EDIT ---
    public function edit($id)
    {
        $schedule = Schedule::with('students')->findOrFail($id);
        
        $drivers = User::where('role', 'driver')
            ->where(function($q) use ($schedule) {
                $q->whereDoesntHave('schedules')->orWhere('id', $schedule->driver_id);
            })->get();

        $shuttles = Shuttle::where('status', 'available')
            ->where(function($q) use ($schedule) {
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
        return response()->json(['status' => 'available']);
    }
}