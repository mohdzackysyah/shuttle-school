<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use App\Models\User;
use App\Models\Shuttle;
use App\Models\Route;
use App\Models\Complex; // Import Model Complex
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    public function index()
    {
        // Tampilkan semua jadwal, grouping berdasarkan RUTE
        $schedules = Schedule::with(['driver', 'route', 'shuttle'])
            ->orderByRaw("FIELD(day_of_week, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday')")
            ->orderBy('departure_time')
            ->get()
            ->groupBy('route_id');

        return view('schedules.index', compact('schedules'));
    }

    public function create()
    {
        $drivers = User::where('role', 'driver')->get();
        $shuttles = Shuttle::where('status', 'available')->get();
        $routes = Route::all();

        return view('schedules.create', compact('drivers', 'shuttles', 'routes'));
    }

    // --- API UNTUK JAVASCRIPT (LOAD KOMPLEK) ---
    public function getComplexesByRoute($routeId)
    {
        $complexes = Complex::where('route_id', $routeId)->get();
        return response()->json($complexes);
    }

    // --- LOGIKA PENYIMPANAN DENGAN VALIDASI BENTROK & KOMPLEK ---
    public function store(Request $request)
    {
        $request->validate([
            'route_id' => 'required',
            'driver_id' => 'required',
            'shuttle_id' => 'required',
            'complex_ids' => 'required|array|min:1', // Wajib pilih minimal 1 komplek
        ]);

        $inputData = $request->input('days');
        if(!$inputData) return back()->with('error', 'Pilih minimal satu hari!');

        $count = 0;
        $conflicts = [];

        foreach ($inputData as $dayName => $data) {
            if (isset($data['active'])) {
                
                // Helper Function untuk Simpan & Attach Komplek
                $saveSchedule = function($type, $time) use ($request, $dayName, &$conflicts, &$count) {
                    
                    // Cek Bentrok Dulu
                    $isBusy = Schedule::where('day_of_week', $dayName)
                        ->where('departure_time', $time)
                        ->where(function($q) use ($request) {
                            $q->where('driver_id', $request->driver_id)
                              ->orWhere('shuttle_id', $request->shuttle_id);
                        })
                        ->exists();

                    if ($isBusy) {
                        $conflicts[] = "Gagal ($dayName - $type): Driver/Mobil sibuk di jam $time";
                    } else {
                        // Simpan Jadwal
                        $schedule = Schedule::create([
                            'day_of_week' => $dayName,
                            'departure_time' => $time,
                            'type' => $type,
                            'route_id' => $request->route_id,
                            'driver_id' => $request->driver_id,
                            'shuttle_id' => $request->shuttle_id,
                        ]);

                        // Simpan Komplek yang dipilih (PENTING!)
                        $schedule->complexes()->attach($request->complex_ids);
                        
                        $count++;
                    }
                };

                // Proses Jemput
                if (!empty($data['pickup_time'])) {
                    $saveSchedule('pickup', $data['pickup_time']);
                }

                // Proses Antar
                if (!empty($data['dropoff_time'])) {
                    $saveSchedule('dropoff', $data['dropoff_time']);
                }
            }
        }

        $redirect = redirect()->route('schedules.index');
        
        if ($count > 0) {
            $redirect->with('success', "$count Jadwal berhasil dibuat.");
        } else {
            $redirect->with('error', "Gagal membuat jadwal.");
        }

        if (count($conflicts) > 0) {
            $redirect->with('error_list', $conflicts);
        }

        return $redirect;
    }

    // --- CEK KETERSEDIAAN VIA AJAX (JAVASCRIPT) ---
    public function checkAvailability(Request $request)
    {
        $driverId = $request->driver_id;
        $shuttleId = $request->shuttle_id;
        $day = $request->day;
        $time = $request->time;

        $conflict = Schedule::where('day_of_week', $day)
            ->where('departure_time', $time)
            ->where(function($q) use ($driverId, $shuttleId) {
                $q->where('driver_id', $driverId)
                  ->orWhere('shuttle_id', $shuttleId);
            })
            ->with(['driver', 'shuttle', 'route'])
            ->first();

        if ($conflict) {
            $msg = "BENTROK! ";
            if ($conflict->driver_id == $driverId) $msg .= "Driver {$conflict->driver->name} ";
            if ($conflict->shuttle_id == $shuttleId) $msg .= "& Mobil {$conflict->shuttle->plate_number} ";
            $msg .= "sudah bertugas di rute {$conflict->route->name}.";

            return response()->json(['status' => 'conflict', 'message' => $msg]);
        }

        return response()->json(['status' => 'available']);
    }

    public function edit($id)
    {
        $schedule = Schedule::findOrFail($id);
        
        $schedulesOfDay = Schedule::where('day_of_week', $schedule->day_of_week)
            ->where('route_id', $schedule->route_id)
            ->get();

        $pickup = $schedulesOfDay->where('type', 'pickup')->first();
        $dropoff = $schedulesOfDay->where('type', 'dropoff')->first();

        $drivers = User::where('role', 'driver')->get();
        $shuttles = Shuttle::all();
        $routes = Route::all();

        return view('schedules.edit', compact('schedule', 'pickup', 'dropoff', 'drivers', 'shuttles', 'routes'));
    }

    public function update(Request $request, $id)
    {
        $originalSchedule = Schedule::findOrFail($id);
        
        // Helper Update
        $updateSchedule = function($type, $time) use ($request, $originalSchedule) {
            Schedule::updateOrCreate(
                ['day_of_week' => $originalSchedule->day_of_week, 'route_id' => $request->route_id, 'type' => $type],
                ['departure_time' => $time, 'driver_id' => $request->driver_id, 'shuttle_id' => $request->shuttle_id]
            );
        };

        // Helper Delete
        $deleteSchedule = function($type) use ($request, $originalSchedule) {
            Schedule::where('day_of_week', $originalSchedule->day_of_week)
                ->where('route_id', $originalSchedule->route_id)
                ->where('type', $type)
                ->delete();
        };
        
        // Proses Jemput
        if ($request->pickup_time) $updateSchedule('pickup', $request->pickup_time);
        else $deleteSchedule('pickup');

        // Proses Antar
        if ($request->dropoff_time) $updateSchedule('dropoff', $request->dropoff_time);
        else $deleteSchedule('dropoff');

        return redirect()->route('schedules.index')->with('success', 'Jadwal diperbarui.');
    }

    public function destroy($id)
    {
        Schedule::destroy($id);
        return back()->with('success', 'Jadwal dihapus.');
    }
}