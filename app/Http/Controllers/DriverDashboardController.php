<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Schedule;
use App\Models\Trip;
use App\Models\Student;
use App\Models\Announcement; // Pastikan Model Announcement di-import
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB; 

class DriverDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->role !== 'driver') {
             return redirect('/')->with('error', 'Halaman ini khusus Driver.');
        }

        Carbon::setLocale('id');
        $todayIndo = Carbon::now()->isoFormat('dddd'); 
        $todayEnglish = Carbon::now()->format('l');    
        $todayDate = Carbon::now()->format('Y-m-d');

        // ============================================================
        // LOGIKA PENGUMUMAN (UPDATE: HANYA HARI INI)
        // ============================================================
        // Filter: 
        // 1. Status Aktif
        // 2. Target Sesuai ('all' atau 'driver')
        // 3. Tanggal Dibuat == Hari Ini (whereDate created_at)
        $announcements = Announcement::where('is_active', true)
                            ->whereIn('target_role', ['all', 'driver'])
                            ->whereDate('created_at', Carbon::today()) // <--- LOGIKA HILANG BESOK
                            ->latest()
                            ->get();

        // ============================================================
        // LOGIKA JADWAL & TRIP
        // ============================================================
        $rawSchedules = Schedule::with(['route', 'shuttle'])
                        ->where('driver_id', $user->id)
                        ->where('day_of_week', $todayEnglish)
                        ->get();

        $tasks = collect();

        foreach ($rawSchedules as $sched) {
            
            // --- TUGAS 1: JEMPUT PAGI ---
            if ($sched->pickup_time) {
                $task = new \stdClass();
                $task->id = $sched->id; 
                $task->type = 'pickup';
                $task->departure_time = $sched->pickup_time;
                $task->route = $sched->route;
                $task->shuttle = $sched->shuttle;
                $task->shuttle_id = $sched->shuttle_id;
                $task->route_id = $sched->route_id;

                $task->today_trip = Trip::where('driver_id', $user->id)
                    ->where('date', $todayDate)
                    ->where('type', 'pickup')
                    ->where('route_id', $sched->route_id)
                    ->first();

                $tasks->push($task);
            }

            // --- TUGAS 2: ANTAR SORE ---
            if ($sched->dropoff_time) {
                $task = new \stdClass();
                $task->id = $sched->id;
                $task->type = 'dropoff';
                $task->departure_time = $sched->dropoff_time;
                $task->route = $sched->route;
                $task->shuttle = $sched->shuttle;
                $task->shuttle_id = $sched->shuttle_id;
                $task->route_id = $sched->route_id;

                $task->today_trip = Trip::where('driver_id', $user->id)
                    ->where('date', $todayDate)
                    ->where('type', 'dropoff')
                    ->where('route_id', $sched->route_id)
                    ->first();

                $tasks->push($task);
            }
        }

        $schedules = $tasks->sortBy('departure_time');

        // Kirim data ke view
        return view('driver_dashboard.index', [
            'schedules' => $schedules,
            'today' => $todayIndo,
            'todayDate' => $todayDate,
            'announcements' => $announcements 
        ]);
    }

    // --- DAFTAR PENUMPANG (MY STUDENTS) ---
    public function myStudents()
    {
        $driverId = Auth::id();

        // 1. Ambil ID jadwal
        $scheduleIds = Schedule::where('driver_id', $driverId)->pluck('id');

        // 2. Ambil ID siswa dari pivot
        $studentIds = DB::table('schedule_student')
                        ->whereIn('schedule_id', $scheduleIds)
                        ->pluck('student_id')
                        ->unique();

        // 3. Ambil Data Siswa
        $students = Student::whereIn('id', $studentIds)
                    ->with(['parent', 'complex']) 
                    ->get()
                    ->sortBy(function($student) {
                        return $student->complex->name ?? 'Z'; 
                    });
        
        return view('driver_dashboard.students', compact('students'));
    }
}