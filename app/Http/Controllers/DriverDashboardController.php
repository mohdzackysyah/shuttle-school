<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Schedule;
use App\Models\Trip;
use App\Models\Route; // <--- Tambahkan ini
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DriverDashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::now()->format('l');
        $todayDate = Carbon::now()->format('Y-m-d');
        $user = Auth::user();

        if ($user->role !== 'driver') {
             return redirect('/')->with('error', 'Halaman ini khusus Driver.');
        }

        $schedules = Schedule::with(['route', 'shuttle'])
                    ->where('driver_id', $user->id)
                    ->where('day_of_week', $today)
                    ->orderBy('departure_time')
                    ->get();

        foreach($schedules as $schedule) {
            $existingTrip = Trip::where('driver_id', $user->id)
                                ->where('date', $todayDate)
                                ->where('route_id', $schedule->route_id)
                                ->where('type', $schedule->type)
                                ->first();

            $schedule->today_trip = $existingTrip;
        }

        return view('driver_dashboard.index', compact('schedules', 'today', 'todayDate'));
    }

    // --- FUNGSI BARU: DAFTAR PENUMPANG ---
    public function myStudents()
    {
        $user = Auth::user();

        // 1. Cari ID Rute yang ditugaskan ke driver ini (Berdasarkan Jadwal)
        // Kita ambil semua rute yang pernah dijadwalkan untuk driver ini
        $routeIds = Schedule::where('driver_id', $user->id)
                            ->pluck('route_id')
                            ->unique();

        // 2. Ambil Data Rute -> Komplek -> Siswa -> Orang Tua
        $routes = Route::whereIn('id', $routeIds)
            ->with(['complexes.students.parent']) 
            ->get();

        return view('driver_dashboard.students', compact('routes'));
    }
}