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

use Illuminate\Support\Facades\Artisan;

Route::get('/setup-hosting-khusus', function() {
    try {
        Artisan::call('storage:link');
        $link = 'Storage Link: Berhasil. ';
    } catch (\Exception $e) {
        $link = 'Storage Link: Gagal/Sudah ada. ';
    }

    try {
        Artisan::call('migrate', ['--force' => true]);
        $migrate = 'Migration: Berhasil. ';
    } catch (\Exception $e) {
        $migrate = 'Migration: Gagal (' . $e->getMessage() . '). ';
    }

    try {
        Artisan::call('optimize:clear');
        $cache = 'Cache Clear: Berhasil.';
    } catch (\Exception $e) {
        $cache = 'Cache Clear: Gagal.';
    }

    return $link . $migrate . $cache;
});
// ====================================================
// 1. ROUTE PUBLIK (TIDAK PERLU LOGIN)
// ====================================================

// --- HALAMAN UTAMA (LANDING PAGE) ---
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Login Routes
Route::get('login', [AuthController::class, 'showUserLogin'])->name('login');
Route::post('login', [AuthController::class, 'loginUser'])->name('login.post');

Route::get('admin/login', [AuthController::class, 'showAdminLogin'])->name('admin.login');
Route::post('admin/login', [AuthController::class, 'loginAdmin'])->name('admin.login.post');

// Logout
Route::post('logout', [AuthController::class, 'logout'])->name('logout');


// ====================================================
// 2. AREA TERPROTEKSI (HARUS LOGIN)
// ====================================================
Route::middleware(['auth'])->group(function () {

    // --- DASHBOARD REDIRECTOR (Terminal) ---
    Route::get('/dashboard', function () {
        $role = auth()->user()->role;
        if ($role == 'admin') return redirect()->route('admin.dashboard');
        if ($role == 'driver') return redirect()->route('driver.dashboard');
        return redirect()->route('parents.dashboard'); 
    })->name('dashboard');

    // --- PROFIL USER ---
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // --- SHARED RESOURCES (Bisa Diakses Admin & Driver) ---
    // 1. Trip Resource (Agar driver bisa akses 'store' utk mulai trip)
    Route::resource('trips', TripController::class); 
    
    // 2. API Get Siswa by Rute (Untuk Select Box di Jadwal)
    Route::get('/get-students-by-route/{route_id}', [ScheduleController::class, 'getStudentsByRoute'])->name('api.get_students');


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
        
        // CRUD Students & PENCARIAN SISWA (Baru)
        Route::resource('students', StudentController::class);
        Route::get('/search-student', [StudentController::class, 'search'])->name('students.search');
        Route::post('/find-student', [StudentController::class, 'find'])->name('students.find');
        
        // --- MANAJEMEN JADWAL ---
        Route::resource('schedules', ScheduleController::class);
        
        // Fitur Bulk Edit (Edit Rangkaian)
        Route::get('/schedules/bulk-edit/{id}', [ScheduleController::class, 'editBulk'])->name('schedules.editBulk');
        Route::put('/schedules/bulk-update/{id}', [ScheduleController::class, 'updateBulk'])->name('schedules.updateBulk');

        // AJAX Helpers (Cek Bentrok & Komplek)
        Route::post('/check-availability', [ScheduleController::class, 'checkAvailability'])->name('schedules.check');
        Route::get('/get-complexes/{route_id}', [ScheduleController::class, 'getComplexesByRoute'])->name('api.get_complexes');
    });


    // ====================================================
    // 4. GROUP DRIVER (Prefix: /driver)
    // ====================================================
    Route::prefix('driver')->group(function () {
        
        // Dashboard
        Route::get('/dashboard', [DriverDashboardController::class, 'index'])->name('driver.dashboard');
        Route::get('/my-students', [DriverDashboardController::class, 'myStudents'])->name('driver.my_students');
        Route::get('/history/{id}', [TripController::class, 'showDriverHistory'])->name('driver.trip.history');

        // --- OPERASIONAL PERJALANAN ---
        Route::get('/trip/{tripId}/process', [TripPassengerController::class, 'process'])->name('driver.trip.process');
        Route::post('/passenger/{id}/pickup', [TripPassengerController::class, 'pickup'])->name('driver.passenger.pickup');
        Route::post('/passenger/{id}/skip', [TripPassengerController::class, 'skip'])->name('driver.passenger.skip');
        Route::post('/passenger/{id}/dropoff', [TripPassengerController::class, 'dropoff'])->name('driver.passenger.dropoff'); 
        Route::post('/trip/{tripId}/finish', [TripPassengerController::class, 'finishTrip'])->name('driver.trip.finish');
    });


    // ====================================================
    // 5. GROUP PARENT (Prefix: /parent)
    // ====================================================
    Route::prefix('parent')->group(function () {
        
        Route::get('/dashboard', [ParentDashboardController::class, 'index'])->name('parents.dashboard');
        Route::get('/my-children', [ParentDashboardController::class, 'myChildren'])->name('parents.my_children');
        
        // Edit Data Anak (Foto & Alamat)
        Route::get('/my-children/{id}/edit', [ParentDashboardController::class, 'editChild'])->name('parents.children.edit');
        Route::put('/my-children/{id}', [ParentDashboardController::class, 'updateChild'])->name('parents.children.update');

        Route::get('/trip-detail/{passenger_id}', [ParentDashboardController::class, 'showTripDetail'])->name('parents.trip.detail');
        
        // Menu Riwayat / Laporan
        Route::get('/history', [ParentDashboardController::class, 'history'])->name('parents.history');

        // Ajax Auto-Refresh
        Route::get('/get-status/{student_id}', [ParentDashboardController::class, 'ajaxStatus'])->name('parents.ajax_status');
        
        // Aksi Ortu
        Route::post('/student/{id}/absent', [ParentDashboardController::class, 'setAbsent'])->name('parents.set_absent');
    });

});