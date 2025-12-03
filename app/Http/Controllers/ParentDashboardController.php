<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Student;
use App\Models\TripPassenger;
use Carbon\Carbon;

class ParentDashboardController extends Controller
{
    public function index()
    {
        $parent = Auth::user();
        if ($parent->role !== 'parent') return redirect('/')->with('error', 'Akses khusus Wali Murid.');

        $students = Student::where('parent_id', $parent->id)->get();

        foreach ($students as $student) {
            // Trip Pagi
            $student->trip_pagi = TripPassenger::where('student_id', $student->id)
                ->whereDate('created_at', Carbon::today())
                ->whereHas('trip', function($q) {
                    $q->where('type', 'pickup')->whereIn('status', ['active', 'finished', 'scheduled']);
                })->with(['trip.driver', 'trip.shuttle', 'trip.route'])->first();

            // Trip Sore
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
        return back()->with('error', 'Tidak bisa izin saat ini.');
    }
}