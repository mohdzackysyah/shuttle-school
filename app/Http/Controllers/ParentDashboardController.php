<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Student;
use App\Models\TripPassenger;
use App\Models\Trip; // Pastikan Model Trip diimport untuk sorting
use Carbon\Carbon;

class ParentDashboardController extends Controller
{
    public function index()
    {
        $parent = Auth::user();
        if ($parent->role !== 'parent') return redirect('/')->with('error', 'Akses khusus Wali Murid.');

        $students = Student::where('parent_id', $parent->id)->get();

        foreach ($students as $student) {
            // Trip Pagi (Hari Ini)
            $student->trip_pagi = TripPassenger::where('student_id', $student->id)
                ->whereDate('created_at', Carbon::today())
                ->whereHas('trip', function($q) {
                    $q->where('type', 'pickup')->whereIn('status', ['active', 'finished', 'scheduled']);
                })->with(['trip.driver', 'trip.shuttle', 'trip.route'])->first();

            // Trip Sore (Hari Ini)
            $student->trip_sore = TripPassenger::where('student_id', $student->id)
                ->whereDate('created_at', Carbon::today())
                ->whereHas('trip', function($q) {
                    $q->where('type', 'dropoff')->whereIn('status', ['active', 'finished', 'scheduled']);
                })->with(['trip.driver', 'trip.shuttle', 'trip.route'])->first();
        }

        return view('parent_dashboard.index', compact('students'));
    }

    public function myChildren()
    {
        $parent = Auth::user();
        $students = Student::where('parent_id', $parent->id)->with('complex')->get();
        return view('parent_dashboard.children', compact('students'));
    }

    // Menampilkan Halaman Detail
    public function showTripDetail($passengerId)
    {
        $passenger = TripPassenger::with(['trip.driver', 'trip.shuttle', 'trip.route', 'student'])
                        ->findOrFail($passengerId);

        if ($passenger->student->parent_id !== Auth::id()) {
            return redirect()->route('parents.dashboard')->with('error', 'Anda tidak memiliki akses.');
        }

        return view('parent_dashboard.trip_detail', compact('passenger'));
    }

    public function setAbsent(Request $request, $studentId)
    {
        $passenger = TripPassenger::where('student_id', $studentId)
                    ->whereDate('created_at', Carbon::today())
                    ->where('status', 'pending')
                    ->first();

        if ($passenger) {
            $passenger->update(['status' => 'absent']);
            return back()->with('success', 'Status anak berhasil diubah menjadi Izin.');
        }
        return back()->with('error', 'Tidak bisa izin saat ini (Mungkin jadwal belum dibuat atau perjalanan sudah dimulai).');
    }

    // --- FITUR BARU: RIWAYAT / LAPORAN PERJALANAN ---
    public function history(Request $request)
    {
        $parent = Auth::user();
        
        // 1. Ambil ID semua anak
        $childIds = Student::where('parent_id', $parent->id)->pluck('id');

        // 2. Query Dasar
        $query = TripPassenger::whereIn('student_id', $childIds)
            ->with(['trip.route', 'trip.driver', 'student']) // Load relasi
            ->whereHas('trip', function($q) {
                $q->where('status', 'finished'); // Hanya ambil yang sudah selesai
            });

        // 3. Logika Filter
        if ($request->filled('date')) {
            // Filter A: Per Tanggal Spesifik
            $query->whereHas('trip', function($q) use ($request) {
                $q->whereDate('date', $request->date);
            });
        } elseif ($request->filled('month') && $request->filled('year')) {
            // Filter B: Per Bulan & Tahun
            $query->whereHas('trip', function($q) use ($request) {
                $q->whereMonth('date', $request->month)
                  ->whereYear('date', $request->year);
            });
        } else {
            // Default: Tampilkan Bulan Ini Saja (Agar tidak berat loadingnya)
            $query->whereHas('trip', function($q) {
                $q->whereMonth('date', Carbon::now()->month)
                  ->whereYear('date', Carbon::now()->year);
            });
        }

        // 4. Sorting (Terbaru diatas) & Pagination
        // Kita join manual sedikit agar bisa sort by trip date
        $histories = $query->join('trips', 'trip_passengers.trip_id', '=', 'trips.id')
                           ->select('trip_passengers.*') // Ambil data passenger saja
                           ->orderBy('trips.date', 'desc')
                           ->paginate(10)
                           ->withQueryString();

        return view('parent_dashboard.history', compact('histories'));
    }
}