<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Student;
use App\Models\Shuttle;
use App\Models\Route;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // Hitung data untuk statistik dashboard
        $totalStudents = Student::count();
        $totalDrivers = User::where('role', 'driver')->count();
        $totalRoutes = Route::count();
        $activeShuttles = Shuttle::where('status', 'available')->count();

        return view('admin_dashboard.index', compact('totalStudents', 'totalDrivers', 'totalRoutes', 'activeShuttles'));
    }
}