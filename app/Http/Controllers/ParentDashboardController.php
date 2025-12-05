<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Student;
use App\Models\TripPassenger;
use App\Models\Trip;
use Carbon\Carbon;

class ParentDashboardController extends Controller
{
    public function index()
    {
        $parent = Auth::user();
        if ($parent->role !== 'parent') return redirect('/')->with('error', 'Akses khusus Wali Murid.');

        $students = Student::where('parent_id', $parent->id)->get();
        $today = Carbon::today();

        foreach ($students as $student) {
            // Trip Pagi (Hari Ini)
            // Menggunakan whereHas 'trip' -> whereDate 'date' agar lebih akurat
            $student->trip_pagi = TripPassenger::where('student_id', $student->id)
                ->whereHas('trip', function($q) use ($today) {
                    $q->where('type', 'pickup')
                      ->whereDate('date', $today)
                      ->whereIn('status', ['active', 'finished', 'scheduled']);
                })
                ->with(['trip.driver', 'trip.shuttle', 'trip.route'])
                ->latest() // Ambil yang terbaru jika ada duplikat
                ->first();

            // Trip Sore (Hari Ini)
            $student->trip_sore = TripPassenger::where('student_id', $student->id)
                ->whereHas('trip', function($q) use ($today) {
                    $q->where('type', 'dropoff')
                      ->whereDate('date', $today)
                      ->whereIn('status', ['active', 'finished', 'scheduled']);
                })
                ->with(['trip.driver', 'trip.shuttle', 'trip.route'])
                ->latest()
                ->first();
        }

        return view('parent_dashboard.index', compact('students'));
    }

    // --- [PENTING] API UNTUK AUTO REFRESH ---
    // Method ini dipanggil Javascript setiap 3 detik
    public function ajaxStatus($studentId)
    {
        $parent = Auth::user();
        
        // Validasi kepemilikan anak
        $student = Student::where('id', $studentId)
                    ->where('parent_id', $parent->id)
                    ->firstOrFail();

        $today = Carbon::today();

        // Cek Status Pagi
        $pagi = TripPassenger::where('student_id', $studentId)
                ->whereHas('trip', function($q) use ($today) {
                    $q->where('type', 'pickup')
                      ->whereDate('date', $today)
                      ->whereIn('status', ['active', 'finished', 'scheduled']);
                })->first();

        // Cek Status Sore
        $sore = TripPassenger::where('student_id', $studentId)
                ->whereHas('trip', function($q) use ($today) {
                    $q->where('type', 'dropoff')
                      ->whereDate('date', $today)
                      ->whereIn('status', ['active', 'finished', 'scheduled']);
                })->first();

        // Return data JSON ringan
        return response()->json([
            'pagi' => [
                'status' => $pagi ? $pagi->status : 'waiting',
                'time'   => $pagi && $pagi->dropped_at ? Carbon::parse($pagi->dropped_at)->format('H:i') . ' WIB' : '-'
            ],
            'sore' => [
                'status' => $sore ? $sore->status : 'waiting',
                'time'   => $sore && $sore->dropped_at ? Carbon::parse($sore->dropped_at)->format('H:i') . ' WIB' : '-'
            ]
        ]);
    }

    public function myChildren()
    {
        $parent = Auth::user();
        $students = Student::where('parent_id', $parent->id)->with('complex')->get();
        return view('parent_dashboard.children', compact('students'));
    }

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
        $today = Carbon::today();
        
        // Cari jadwal trip hari ini yang statusnya masih pending
        $passenger = TripPassenger::where('student_id', $studentId)
                    ->whereHas('trip', function($q) use ($today) {
                        $q->whereDate('date', $today)->where('status', '!=', 'finished');
                    })
                    ->where('status', 'pending')
                    ->first();

        if ($passenger) {
            $passenger->update(['status' => 'absent']);
            return back()->with('success', 'Status anak berhasil diubah menjadi Izin.');
        }
        
        return back()->with('error', 'Tidak bisa izin saat ini (Mungkin jadwal belum dibuat atau perjalanan sudah dimulai).');
    }

    public function history(Request $request)
    {
        $parent = Auth::user();
        $childIds = Student::where('parent_id', $parent->id)->pluck('id');

        $query = TripPassenger::whereIn('student_id', $childIds)
            ->with(['trip.route', 'trip.driver', 'student'])
            ->whereHas('trip', function($q) {
                $q->where('status', 'finished');
            });

        if ($request->filled('date')) {
            $query->whereHas('trip', function($q) use ($request) {
                $q->whereDate('date', $request->date);
            });
        } elseif ($request->filled('month') && $request->filled('year')) {
            $query->whereHas('trip', function($q) use ($request) {
                $q->whereMonth('date', $request->month)
                  ->whereYear('date', $request->year);
            });
        } else {
            $query->whereHas('trip', function($q) {
                $q->whereMonth('date', Carbon::now()->month)
                  ->whereYear('date', Carbon::now()->year);
            });
        }

        $histories = $query->join('trips', 'trip_passengers.trip_id', '=', 'trips.id')
                           ->select('trip_passengers.*')
                           ->orderBy('trips.date', 'desc')
                           ->paginate(10)
                           ->withQueryString();

        return view('parent_dashboard.history', compact('histories'));
    }
}