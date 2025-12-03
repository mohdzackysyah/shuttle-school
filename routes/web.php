<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;

// Admin Controllers
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\ComplexController;
use App\Http\Controllers\ShuttleController;
use App\Http\Controllers\RouteController;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\ParentController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\TripController;

// Driver & Parent Controllers
use App\Http\Controllers\DriverDashboardController;
use App\Http\Controllers\TripPassengerController;
use App\Http\Controllers\ParentDashboardController;

// ====================================================
// 1. ROUTE PUBLIK (TIDAK PERLU LOGIN)
// ====================================================

// --- HALAMAN UTAMA (LANDING PAGE) ---
// Diakses saat pertama kali buka website
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Login User Biasa (Driver & Ortu)
Route::get('login', [AuthController::class, 'showUserLogin'])->name('login');
Route::post('login', [AuthController::class, 'loginUser'])->name('login.post');

// Login Admin
Route::get('admin/login', [AuthController::class, 'showAdminLogin'])->name('admin.login');
Route::post('admin/login', [AuthController::class, 'loginAdmin'])->name('admin.login.post');

// Logout
Route::post('logout', [AuthController::class, 'logout'])->name('logout');


// ====================================================
// 2. AREA TERPROTEKSI (HARUS LOGIN)
// ====================================================
Route::middleware(['auth'])->group(function () {

    // --- DASHBOARD REDIRECTOR (Terminal) ---
    // Route ini dipanggil setelah login sukses atau klik tombol "Dashboard" di Beranda
    Route::get('/dashboard', function () {
        $role = auth()->user()->role;
        
        if ($role == 'admin') return redirect()->route('admin.dashboard');
        if ($role == 'driver') return redirect()->route('driver.dashboard');
        
        // Default ke Parent
        return redirect()->route('parents.dashboard'); 
    })->name('dashboard');

    // --- PROFIL USER (Global) ---
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // --- SHARED RESOURCES ---
    // Agar Driver bisa akses 'trips.store' (Mulai Perjalanan) tanpa kena blokir middleware admin
    Route::resource('trips', TripController::class); 


    // ====================================================
    // 3. GROUP ADMIN (Prefix: /admin)
    // ====================================================
    Route::prefix('admin')->group(function () {
        
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');

        // CRUD Data Master
        Route::resource('complexes', ComplexController::class);
        Route::resource('shuttles', ShuttleController::class);
        Route::resource('routes', RouteController::class);
        
        // Manajemen User
        Route::resource('drivers', DriverController::class);
        Route::resource('parents', ParentController::class);
        Route::resource('students', StudentController::class);
        
        // Manajemen Jadwal
        Route::resource('schedules', ScheduleController::class);

        // AJAX Helpers
        Route::post('/check-availability', [ScheduleController::class, 'checkAvailability'])->name('schedules.check');
        Route::get('/get-complexes/{route_id}', [ScheduleController::class, 'getComplexesByRoute'])->name('api.get_complexes');
    });


    // ====================================================
    // 4. GROUP DRIVER (Prefix: /driver)
    // ====================================================
    Route::prefix('driver')->group(function () {
        
        // Dashboard Utama
        Route::get('/dashboard', [DriverDashboardController::class, 'index'])->name('driver.dashboard');
        
        // Data Anak Asuh
        Route::get('/my-students', [DriverDashboardController::class, 'myStudents'])->name('driver.my_students');
        
        // History Trip
        Route::get('/history/{id}', [TripController::class, 'showDriverHistory'])->name('driver.trip.history');

        // --- OPERASIONAL PERJALANAN ---
        
        // 1. Tampilan Proses
        Route::get('/trip/{tripId}/process', [TripPassengerController::class, 'process'])->name('driver.trip.process');

        // 2. Aksi Tombol
        Route::post('/passenger/{id}/pickup', [TripPassengerController::class, 'pickup'])->name('driver.passenger.pickup');
        Route::post('/passenger/{id}/skip', [TripPassengerController::class, 'skip'])->name('driver.passenger.skip');
        Route::post('/passenger/{id}/dropoff', [TripPassengerController::class, 'dropoff'])->name('driver.passenger.dropoff'); 
        
        // 3. Selesai
        Route::post('/trip/{tripId}/finish', [TripPassengerController::class, 'finishTrip'])->name('driver.trip.finish');
    });


    // ====================================================
    // 5. GROUP PARENT (Prefix: /parent)
    // ====================================================
    Route::prefix('parent')->group(function () {
        
        Route::get('/dashboard', [ParentDashboardController::class, 'index'])->name('parents.dashboard');
        Route::get('/my-children', [ParentDashboardController::class, 'myChildren'])->name('parents.my_children');
        Route::get('/trip-detail/{passenger_id}', [ParentDashboardController::class, 'showTripDetail'])->name('parents.trip.detail');
        
        // Aksi Ortu
        Route::post('/student/{id}/absent', [ParentDashboardController::class, 'setAbsent'])->name('parents.set_absent');
    });

});