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
    // 1. Live Trips Today
    $pagi = TripPassenger::where('student_id', $student->id)
        ->whereHas('trip', function($q) use ($today) {
            $q->where('type', 'pickup')->whereDate('date', $today);
        })->with(['trip.driver', 'trip.shuttle', 'trip.route'])->first();

    $sore = TripPassenger::where('student_id', $student->id)
        ->whereHas('trip', function($q) use ($today) {
            $q->where('type', 'dropoff')->whereDate('date', $today);
        })->with(['trip.driver', 'trip.shuttle', 'trip.route'])->first();

    // 2. Master Schedule for Today
    $masterSchedule = Schedule::whereHas('students', function($q) use ($student) {
            $q->where('students.id', $student->id);
        })
        ->where('day_of_week', $todayEnglish)
        ->with(['driver', 'shuttle', 'route'])
        ->first();

    if (!$masterSchedule) {
        $masterSchedule = Schedule::whereHas('students', function($q) use ($student) {
                $q->where('students.id', $student->id);
            })
            ->with(['driver', 'shuttle', 'route'])
            ->first();
    }

    $routeTitle = $student->complex->route->name ?? ($masterSchedule->route->name ?? '-');

    $pagiData = $pagi ? [
        'is_started' => true,
        'passenger_id' => $pagi->id,
        'status' => $pagi->status,
        'trip_status' => $pagi->trip->status,
        'route_name' => $pagi->trip->route->name ?? $routeTitle,
        'driver_name' => $pagi->trip->driver->name ?? '-',
        'driver_phone' => $pagi->trip->driver->phone ?? '',
        'shuttle_plate' => $pagi->trip->shuttle->plate_number ?? '-',
        'scheduled_time' => $pagi->picked_at ? Carbon::parse($pagi->picked_at)->format('H:i') . ' WIB' : '07:00:00 WIB',
        'status_text' => $pagi->trip->status == 'active' ? 'Dalam Perjalanan' : ($pagi->trip->status == 'finished' ? 'Selesai' : 'Sedang Berjalan'),
        'status_note' => $pagi->status == 'picked_up' ? 'Siswa sudah dijemput.' : ($pagi->status == 'dropped_off' ? 'Siswa sudah sampai.' : 'Perjalanan telah dimulai oleh supir.'),
    ] : [
        'is_started' => false,
        'passenger_id' => null,
        'status' => 'pending',
        'trip_status' => 'not_started',
        'route_name' => $masterSchedule->route->name ?? $routeTitle,
        'driver_name' => $masterSchedule->driver->name ?? 'Belum Ditentukan',
        'driver_phone' => $masterSchedule->driver->phone ?? '',
        'shuttle_plate' => $masterSchedule->shuttle->plate_number ?? '-',
        'scheduled_time' => $masterSchedule && $masterSchedule->pickup_time ? Carbon::parse($masterSchedule->pickup_time)->format('H:i:s') . ' WIB' : '07:00:00 WIB',
        'status_text' => 'Belum Dimulai',
        'status_note' => 'Menunggu supir memulai perjalanan.',
    ];

    $soreData = $sore ? [
        'is_started' => true,
        'passenger_id' => $sore->id,
        'status' => $sore->status,
        'trip_status' => $sore->trip->status,
        'route_name' => $sore->trip->route->name ?? $routeTitle,
        'driver_name' => $sore->trip->driver->name ?? '-',
        'driver_phone' => $sore->trip->driver->phone ?? '',
        'shuttle_plate' => $sore->trip->shuttle->plate_number ?? '-',
        'scheduled_time' => $sore->dropped_at ? Carbon::parse($sore->dropped_at)->format('H:i') . ' WIB' : '13:00:00 WIB',
        'status_text' => $sore->trip->status == 'active' ? 'Dalam Perjalanan' : ($sore->trip->status == 'finished' ? 'Selesai' : 'Sedang Berjalan'),
        'status_note' => $sore->status == 'picked_up' ? 'Siswa sudah dijemput.' : ($sore->status == 'dropped_off' ? 'Siswa sudah sampai.' : 'Perjalanan telah dimulai oleh supir.'),
    ] : [
        'is_started' => false,
        'passenger_id' => null,
        'status' => 'pending',
        'trip_status' => 'not_started',
        'route_name' => $masterSchedule->route->name ?? $routeTitle,
        'driver_name' => $masterSchedule->driver->name ?? 'Belum Ditentukan',
        'driver_phone' => $masterSchedule->driver->phone ?? '',
        'shuttle_plate' => $masterSchedule->shuttle->plate_number ?? '-',
        'scheduled_time' => $masterSchedule && $masterSchedule->dropoff_time ? Carbon::parse($masterSchedule->dropoff_time)->format('H:i:s') . ' WIB' : '13:00:00 WIB',
        'status_text' => 'Belum Dimulai',
        'status_note' => 'Menunggu supir memulai perjalanan.',
    ];

    return [
        'id' => $student->id,
        'name' => $student->name,
        'complex_name' => $student->complex->name ?? '-',
        'trip_pagi' => $pagiData,
        'trip_sore' => $soreData,
    ];
});

echo json_encode($data, JSON_PRETTY_PRINT);
