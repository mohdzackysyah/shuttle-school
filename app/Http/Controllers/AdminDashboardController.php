<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Student;
use App\Models\Shuttle;
use App\Models\Route;
use App\Models\Trip; 
use Carbon\Carbon;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // 1. Hitung Statistik Umum
        $totalStudents = Student::count();
        $totalDrivers = User::where('role', 'driver')->count();
        $totalRoutes = Route::count();
        $activeShuttles = Shuttle::where('status', 'available')->count();

        // 2. Ambil Data Trip HARI INI
        // PERBAIKAN: Tambahkan 'route.complexes' di dalam with()
        $todaysTrips = Trip::with(['driver', 'route.complexes', 'shuttle'])
                        ->withCount('passengers')
                        ->whereDate('date', Carbon::today()) 
                        ->orderByRaw("FIELD(status, 'active', 'scheduled', 'finished')")
                        ->get();

        return view('admin_dashboard.index', compact(
            'totalStudents', 
            'totalDrivers', 
            'totalRoutes', 
            'activeShuttles',
            'todaysTrips'
        ));
    }
}