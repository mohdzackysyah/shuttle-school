<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ApiAuthController;
use App\Http\Controllers\Api\ParentApiController;
use App\Http\Controllers\Api\DriverApiController;

// --- JALUR PUBLIK (Tanpa Kunci) ---
Route::post('/login', [ApiAuthController::class, 'login']);

// --- JALUR TERKUNCI (Harus bawa Token) ---
Route::middleware('auth:sanctum')->group(function () {
    
    Route::post('/logout', [ApiAuthController::class, 'logout']);
    Route::get('/user', [ApiAuthController::class, 'getUser']);

    // --- API WALI MURID (PARENT) ---
    Route::prefix('parent')->group(function () {
        Route::get('/dashboard', [ParentApiController::class, 'dashboard']);
        Route::get('/trip-detail/{passenger_id}', [ParentApiController::class, 'tripDetail']);
        Route::post('/student/{id}/update', [ParentApiController::class, 'updateChild']);
        Route::post('/student/{id}/absent', [ParentApiController::class, 'setAbsent']);
        Route::get('/history', [ParentApiController::class, 'history']);
        Route::get('/profile', [ParentApiController::class, 'getProfile']);
        Route::post('/profile/update', [ParentApiController::class, 'updateProfile']);
    });

    // --- API DRIVER ---
    Route::prefix('driver')->group(function () {
        Route::get('/dashboard', [DriverApiController::class, 'dashboard']);
        Route::post('/trip/start', [DriverApiController::class, 'startTrip']);
        Route::get('/trip/{trip_id}/passengers', [DriverApiController::class, 'tripPassengers']);
        Route::post('/trip/{trip_id}/location', [DriverApiController::class, 'updateLocation']);
        Route::post('/passenger/{passenger_id}/status', [DriverApiController::class, 'updatePassengerStatus']);
        Route::post('/trip/{trip_id}/finish', [DriverApiController::class, 'finishTrip']);
        Route::get('/students', [DriverApiController::class, 'myStudents']);
        Route::get('/history', [DriverApiController::class, 'history']);
        Route::get('/profile', [DriverApiController::class, 'getProfile']);
        Route::post('/profile/update', [DriverApiController::class, 'updateProfile']);
    });

    // --- API NOTIFIKASI ---
    Route::get('/notifications', [\App\Http\Controllers\Api\NotificationApiController::class, 'index']);
    Route::post('/notifications/{id}/read', [\App\Http\Controllers\Api\NotificationApiController::class, 'markAsRead']);
    Route::post('/notifications/read-all', [\App\Http\Controllers\Api\NotificationApiController::class, 'markAllRead']);
    Route::post('/notifications/fcm-token', [\App\Http\Controllers\Api\NotificationApiController::class, 'updateFcmToken']);

});