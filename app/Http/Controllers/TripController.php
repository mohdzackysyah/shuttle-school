<?php

namespace App\Http\Controllers;

use App\Models\Trip;
use App\Models\TripPassenger;
use App\Models\User;
use App\Models\Shuttle;
use App\Models\Route;
use App\Models\Student;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth; // <--- Penting: Tambahkan Import Auth

class TripController extends Controller
{
    public function index()
    {
        $trips = Trip::with(['driver', 'shuttle', 'route'])->orderBy('date', 'desc')->get();
        return view('trips.index', compact('trips'));
    }

    public function create()
    {
        // Form Manual (Admin)
        $drivers = User::where('role', 'driver')->get();
        $shuttles = Shuttle::where('status', 'available')->get();
        $routes = Route::all();

        return view('trips.create', compact('drivers', 'shuttles', 'routes'));
    }

    // --- LOGIKA PEMBUATAN TRIP (CERDAS) ---
    public function store(Request $request)
    {
        $request->validate([
            'driver_id' => 'required',
            'shuttle_id' => 'required',
            'route_id' => 'required',
            'date' => 'required',
            'type' => 'required',
        ]);

        // 1. Buat Header Trip
        $trip = Trip::create([
            'driver_id' => $request->driver_id,
            'shuttle_id' => $request->shuttle_id,
            'route_id' => $request->route_id,
            'date' => $request->date,
            'type' => $request->type,
            'status' => 'active' // Langsung aktif karena tombolnya "Mulai Perjalanan"
        ]);

        // 2. TENTUKAN SISWA MANA YANG IKUT
        $complexIds = [];
        $dayName = Carbon::parse($request->date)->format('l'); // Nama Hari (Monday, etc)

        // Cari Schedule yang cocok
        $schedule = Schedule::where('driver_id', $request->driver_id)
                    ->where('day_of_week', $dayName)
                    ->where('type', $request->type)
                    ->where('route_id', $request->route_id)
                    ->with('complexes')
                    ->first();

        if ($schedule && $schedule->complexes->count() > 0) {
            // SKENARIO A: Jadwal Ketemu & Ada Komplek Spesifik
            $complexIds = $schedule->complexes->pluck('id');
        } else {
            // SKENARIO B: Fallback (Ambil semua komplek di Rute)
            $route = Route::with('complexes')->find($request->route_id);
            if ($route) {
                $complexIds = $route->complexes->pluck('id');
            }
        }

        // 3. Ambil Siswa
        $students = Student::whereIn('complex_id', $complexIds)->get();

        // 4. Masukkan ke Manifest
        foreach($students as $student) {
            TripPassenger::create([
                'trip_id' => $trip->id,
                'student_id' => $student->id,
                'status' => 'pending'
            ]);
        }

        // ============================================================
        // PERBAIKAN PENTING: REDIRECT BERDASARKAN ROLE
        // ============================================================
        
        // Jika yang klik adalah Driver -> Arahkan ke Tampilan Driver (Biru Putih)
        if (Auth::user()->role == 'driver') {
            return redirect()->route('driver.trip.process', $trip->id)
                ->with('success', 'Perjalanan dimulai! Semangat bertugas.');
        }

        // Jika yang klik adalah Admin -> Arahkan ke Tampilan Admin
        return redirect()->route('trips.show', $trip->id)
            ->with('success', 'Perjalanan dimulai! ' . $students->count() . ' siswa masuk daftar.');
    }

    public function show(Trip $trip)
    {
        // Tampilan Detail Trip (Versi Admin)
        $passengers = TripPassenger::with(['student.parent', 'student.complex'])
                        ->where('trip_id', $trip->id)
                        ->join('students', 'trip_passengers.student_id', '=', 'students.id')
                        ->join('complexes', 'students.complex_id', '=', 'complexes.id')
                        ->orderBy('complexes.name') // Urutkan komplek A-Z
                        ->select('trip_passengers.*') 
                        ->get();

        return view('trips.show', compact('trip', 'passengers'));
    }

    // KHUSUS DRIVER: History (Tampilan Mobile)
    public function showDriverHistory($id)
    {
        $trip = Trip::with(['route', 'shuttle', 'passengers.student.parent'])->findOrFail($id);

        // Validasi Pemilik Trip
        if (Auth::user()->id != $trip->driver_id) {
            return redirect()->route('driver.dashboard')->with('error', 'Anda tidak memiliki akses.');
        }

        $passengers = $trip->passengers;

        // Pastikan file view ini ada di folder resources/views/driver_dashboard/history.blade.php
        return view('driver_dashboard.history', compact('trip', 'passengers'));
    }

    public function destroy(Trip $trip)
    {
        // Hapus data anak (passenger) dulu agar bersih (opsional jika sudah cascade di DB)
        $trip->passengers()->delete();
        
        $trip->delete();
        return redirect()->route('trips.index')->with('success', 'Data perjalanan dihapus.');
    }
}