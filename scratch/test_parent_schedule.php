<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Student;
use App\Models\TripPassenger;
use App\Models\Schedule;
use Carbon\Carbon;

$parent = User::where('role', 'parent')->first();
$today = Carbon::today();
$todayEnglish = Carbon::now()->format('l');

$students = Student::where('parent_id', $parent->id)->with(['complex.route'])->get();

$data = $students->map(function($student) use ($today, $todayEnglish) {
    // 1. Check Active Trip Passenger today
    $pagi = TripPassenger::where('student_id', $student->id)
        ->whereHas('trip', function($q) use ($today) {
            $q->where('type', 'pickup')->whereDate('date', $today);
        })->with(['trip.driver', 'trip.shuttle'])->first();

    $sore = TripPassenger::where('student_id', $student->id)
        ->whereHas('trip', function($q) use ($today) {
            $q->where('type', 'dropoff')->whereDate('date', $today);
        })->with(['trip.driver', 'trip.shuttle'])->first();

    // 2. Check Master Schedule if trip not started yet
    $masterSchedule = Schedule::whereHas('students', function($q) use ($student) {
            $q->where('students.id', $student->id);
        })
        ->where('day_of_week', $todayEnglish)
        ->with(['driver', 'shuttle'])
        ->first();

    if (!$masterSchedule) {
        $masterSchedule = Schedule::whereHas('students', function($q) use ($student) {
                $q->where('students.id', $student->id);
            })
            ->with(['driver', 'shuttle'])
            ->first();
    }

    $pagiData = $pagi ? [
        'passenger_id' => $pagi->id,
        'status' => $pagi->status,
        'trip_status' => $pagi->trip->status,
        'driver_name' => $pagi->trip->driver->name ?? '-',
        'driver_phone' => $pagi->trip->driver->phone ?? '',
        'shuttle_plate' => $pagi->trip->shuttle->plate_number ?? '-',
        'scheduled_time' => $pagi->picked_at ? Carbon::parse($pagi->picked_at)->format('H:i') : null,
    ] : ($masterSchedule && $masterSchedule->pickup_time ? [
        'passenger_id' => null,
        'status' => 'pending',
        'trip_status' => 'not_started',
        'driver_name' => $masterSchedule->driver->name ?? '-',
        'driver_phone' => $masterSchedule->driver->phone ?? '',
        'shuttle_plate' => $masterSchedule->shuttle->plate_number ?? '-',
        'scheduled_time' => Carbon::parse($masterSchedule->pickup_time)->format('H:i'),
    ] : null);

    $soreData = $sore ? [
        'passenger_id' => $sore->id,
        'status' => $sore->status,
        'trip_status' => $sore->trip->status,
        'driver_name' => $sore->trip->driver->name ?? '-',
        'driver_phone' => $sore->trip->driver->phone ?? '',
        'shuttle_plate' => $sore->trip->shuttle->plate_number ?? '-',
        'scheduled_time' => $sore->dropped_at ? Carbon::parse($sore->dropped_at)->format('H:i') : null,
    ] : ($masterSchedule && $masterSchedule->dropoff_time ? [
        'passenger_id' => null,
        'status' => 'pending',
        'trip_status' => 'not_started',
        'driver_name' => $masterSchedule->driver->name ?? '-',
        'driver_phone' => $masterSchedule->driver->phone ?? '',
        'shuttle_plate' => $masterSchedule->shuttle->plate_number ?? '-',
        'scheduled_time' => Carbon::parse($masterSchedule->dropoff_time)->format('H:i'),
    ] : null);

    return [
        'id' => $student->id,
        'name' => $student->name,
        'trip_pagi' => $pagiData,
        'trip_sore' => $soreData,
    ];
});

echo json_encode($data, JSON_PRETTY_PRINT);
