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
use Illuminate\Support\Facades\Auth;

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

    // --- LOGIKA PEMBUATAN TRIP (REVISI: BERDASARKAN JADWAL) ---
    public function store(Request $request)
    {
        $request->validate([
            'driver_id' => 'required',
            'shuttle_id' => 'required',
            'route_id' => 'required',
            'date' => 'required',
            'type' => 'required',
        ]);

        // 1. SIAPKAN DATA
        $shuttle = Shuttle::findOrFail($request->shuttle_id);
        $dayName = Carbon::parse($request->date)->format('l'); // Nama Hari (Monday, etc)

        // 2. CARI JADWAL YANG SESUAI (LOGIKA BARU)
        // Kita mencari jadwal yang sudah dibuat Admin sebelumnya untuk hari & rute ini.
        $schedule = Schedule::where('driver_id', $request->driver_id)
                    ->where('day_of_week', $dayName)
                    ->where('route_id', $request->route_id)
                    // Ambil relasi students yang sudah disimpan di tabel pivot
                    ->with('students') 
                    ->first();

        // 3. AMBIL DAFTAR SISWA DARI JADWAL
        // Jika jadwal ketemu, ambil siswanya. Jika tidak, kosong (0 siswa).
        $students = $schedule ? $schedule->students : collect();
        $totalSiswa = $students->count();

        // ============================================================
        // 🛑 VALIDASI KAPASITAS (CEK REAL-TIME)
        // ============================================================
        if ($totalSiswa > $shuttle->capacity) {
            $over = $totalSiswa - $shuttle->capacity;
            return redirect()->back()
                ->withInput()
                ->with('error', "GAGAL! Kapasitas mobil hanya {$shuttle->capacity} kursi, tapi jadwal ini memiliki {$totalSiswa} siswa terdaftar (Kelebihan {$over} orang).");
        }
        // ============================================================

        // 4. BUAT HEADER TRIP
        $trip = Trip::create([
            'driver_id' => $request->driver_id,
            'shuttle_id' => $request->shuttle_id,
            'route_id' => $request->route_id,
            'date' => $request->date,
            'type' => $request->type,
            'status' => 'active' 
        ]);

        // 5. MASUKKAN PENUMPANG KE MANIFEST (Hanya siswa terpilih)
        foreach($students as $student) {
            TripPassenger::create([
                'trip_id' => $trip->id,
                'student_id' => $student->id,
                'status' => 'pending'
            ]);
        }

        // REDIRECT (Sesuai Role)
        if (Auth::user()->role == 'driver') {
            return redirect()->route('driver.trip.process', $trip->id)
                ->with('success', 'Perjalanan dimulai! Semangat bertugas.');
        }

        $msg = $totalSiswa > 0 
            ? 'Perjalanan dijadwalkan! ' . $totalSiswa . ' siswa masuk daftar.' 
            : 'Perjalanan dibuat KOSONG (Tidak ada jadwal/siswa ditemukan untuk kriteria ini).';

        return redirect()->route('trips.show', $trip->id)->with('success', $msg);
    }

    public function show(Trip $trip)
    {
        // Tampilan Detail Trip (Versi Admin)
        $passengers = TripPassenger::with(['student.parent', 'student.complex'])
                        ->where('trip_id', $trip->id)
                        ->join('students', 'trip_passengers.student_id', '=', 'students.id')
                        ->join('complexes', 'students.complex_id', '=', 'complexes.id')
                        ->orderBy('complexes.name') 
                        ->select('trip_passengers.*') 
                        ->get();

        return view('trips.show', compact('trip', 'passengers'));
    }

    // KHUSUS DRIVER: History
    public function showDriverHistory($id)
    {
        $trip = Trip::with(['route', 'shuttle', 'passengers.student.parent'])->findOrFail($id);

        if (Auth::user()->id != $trip->driver_id) {
            return redirect()->route('driver.dashboard')->with('error', 'Anda tidak memiliki akses.');
        }

        $passengers = $trip->passengers;

        return view('driver_dashboard.history', compact('trip', 'passengers'));
    }

    public function destroy(Trip $trip)
    {
        $trip->passengers()->delete();
        $trip->delete();
        return redirect()->route('trips.index')->with('success', 'Data perjalanan dihapus.');
    }
}