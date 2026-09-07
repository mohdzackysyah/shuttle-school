<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Trip;
use App\Models\TripPassenger;
use App\Models\Student;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DriverApiController extends Controller
{
    // === DASHBOARD DRIVER (DAFTAR TRIP HARI INI) ===
    public function dashboard(Request $request)
    {
        $driver = Auth::user();
        $todayEnglish = Carbon::now()->format('l');    
        $todayDate = Carbon::now()->format('Y-m-d');

        $rawSchedules = \App\Models\Schedule::with(['route', 'shuttle'])
                        ->where('driver_id', $driver->id)
                        ->where('day_of_week', $todayEnglish)
                        ->get();

        $tasks = collect();

        foreach ($rawSchedules as $sched) {
            // Jemput Pagi
            if ($sched->pickup_time) {
                $todayTrip = Trip::where('driver_id', $driver->id)
                    ->where('date', $todayDate)
                    ->where('type', 'pickup')
                    ->where('route_id', $sched->route_id)
                    ->first();

                $total = 0;
                $done = 0;
                $percent = 0;
                if ($todayTrip) {
                    $total = $todayTrip->passengers()->count();
                    $done = $todayTrip->passengers()->whereNotIn('status', ['pending', 'waiting'])->count();
                    $percent = $total > 0 ? ($done / $total) * 100 : 0;
                }

                $tasks->push([
                    'id' => $sched->id, // schedule_id
                    'trip_id' => $todayTrip ? $todayTrip->id : null,
                    'type' => 'pickup',
                    'departure_time' => Carbon::parse($sched->pickup_time)->format('H:i'),
                    'route_id' => $sched->route_id,
                    'route_name' => $sched->route->name ?? '-',
                    'shuttle_id' => $sched->shuttle_id,
                    'shuttle_model' => $sched->shuttle->car_model ?? '-',
                    'shuttle_plate' => $sched->shuttle->plate_number ?? '-',
                    'status' => $todayTrip ? $todayTrip->status : 'scheduled',
                    'total_passengers' => $total,
                    'done_passengers' => $done,
                    'progress_percent' => $percent
                ]);
            }

            // Antar Sore
            if ($sched->dropoff_time) {
                $todayTrip = Trip::where('driver_id', $driver->id)
                    ->where('date', $todayDate)
                    ->where('type', 'dropoff')
                    ->where('route_id', $sched->route_id)
                    ->first();

                $total = 0;
                $done = 0;
                $percent = 0;
                if ($todayTrip) {
                    $total = $todayTrip->passengers()->count();
                    $done = $todayTrip->passengers()->whereNotIn('status', ['pending', 'waiting'])->count();
                    $percent = $total > 0 ? ($done / $total) * 100 : 0;
                }

                $tasks->push([
                    'id' => $sched->id, // schedule_id
                    'trip_id' => $todayTrip ? $todayTrip->id : null,
                    'type' => 'dropoff',
                    'departure_time' => Carbon::parse($sched->dropoff_time)->format('H:i'),
                    'route_id' => $sched->route_id,
                    'route_name' => $sched->route->name ?? '-',
                    'shuttle_id' => $sched->shuttle_id,
                    'shuttle_model' => $sched->shuttle->car_model ?? '-',
                    'shuttle_plate' => $sched->shuttle->plate_number ?? '-',
                    'status' => $todayTrip ? $todayTrip->status : 'scheduled',
                    'total_passengers' => $total,
                    'done_passengers' => $done,
                    'progress_percent' => $percent
                ]);
            }
        }

        // Sort by departure_time
        $sortedTasks = $tasks->sortBy('departure_time')->values()->all();

        $announcements = \App\Models\Announcement::where('is_active', true)
            ->whereIn('target_role', ['all', 'driver'])
            ->whereDate('created_at', Carbon::today())
            ->latest()
            ->get()
            ->map(function($a) {
                return [
                    'id' => $a->id,
                    'title' => $a->title,
                    'content' => $a->content,
                    'created_at' => $a->created_at->format('d M Y H:i'),
                ];
            });

        return response()->json([
            'success' => true,
            'trips' => $sortedTasks,
            'announcements' => $announcements
        ]);
    }

    // === DETAIL TRIP (DAFTAR PENUMPANG) ===
    public function tripPassengers(Request $request, $tripId)
    {
        $trip = Trip::with(['route', 'shuttle', 'passengers.student.complex', 'passengers.student.parent'])->findOrFail($tripId);

        if (intval($trip->driver_id) !== intval(Auth::id())) {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak.'
            ], 403);
        }

        // Jika trip status scheduled, ubah jadi active saat driver membuka detailnya
        if ($trip->status === 'scheduled') {
            $trip->update(['status' => 'active']);
            foreach ($trip->passengers as $p) {
                if ($p->student && $p->student->parent_id) {
                    $tripTypeLabel = ($trip->type === 'pickup') ? 'Penjemputan Pagi' : 'Pengantaran Sore';
                    $title = 'Perjalanan Dimulai 🚐';
                    $message = 'Perjalanan ' . $tripTypeLabel . ' untuk Ananda ' . $p->student->name . ' telah dimulai oleh driver.';
                    Notification::create([
                        'user_id' => $p->student->parent_id,
                        'title' => $title,
                        'message' => $message,
                        'type' => 'trip_start',
                        'data' => ['trip_id' => $trip->id, 'student_id' => $p->student_id]
                    ]);
                    \App\Services\FcmService::sendToUser($p->student->parent_id, $title, $message);
                }
            }
        }

        $passengers = $trip->passengers->map(function($p) {
            return [
                'passenger_id' => $p->id,
                'student_id' => $p->student->id,
                'student_name' => $p->student->name,
                'photo_url' => $p->student->photo ? asset('storage/' . $p->student->photo) : null,
                'complex_name' => $p->student->complex->name ?? '-',
                'address_note' => $p->student->address_note,
                'latitude' => $p->student->latitude,
                'longitude' => $p->student->longitude,
                'status' => $p->status, // pending, waiting, picked_up, dropped_off, skipped, absent
                'picked_at' => $p->picked_at,
                'dropped_at' => $p->dropped_at,
                'parent_phone' => $p->student->parent->phone ?? null,
            ];
        });

        $total = $trip->passengers()->count();
        $done = $trip->passengers()->whereNotIn('status', ['pending', 'waiting'])->count();
        $percent = $total > 0 ? ($done / $total) * 100 : 0;

        return response()->json([
            'success' => true,
            'trip' => [
                'id' => $trip->id,
                'status' => $trip->status,
                'type' => $trip->type,
                'route_name' => $trip->route->name ?? '-',
                'shuttle_plate' => $trip->shuttle->plate_number ?? '-',
                'progress_percent' => $percent,
                'done_passengers' => $done,
                'total_passengers' => $total
            ],
            'passengers' => $passengers
        ]);
    }

    // === UPDATE LOKASI LIVE DRIVER ===
    public function updateLocation(Request $request, $tripId)
    {
        $trip = Trip::findOrFail($tripId);
        
        if (intval($trip->driver_id) !== intval(Auth::id())) {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak.'
            ], 403);
        }

        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric'
        ]);

        // Update langsung via query builder untuk menghindari model caching
        \App\Models\Trip::where('id', $tripId)->update([
            'current_latitude' => $request->latitude,
            'current_longitude' => $request->longitude,
            'updated_at' => Carbon::now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Lokasi live driver terupdate.',
            'lat' => $request->latitude,
            'lng' => $request->longitude
        ], 200, [
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ]);
    }

    // === UPDATE STATUS PENUMPANG ===
    public function updatePassengerStatus(Request $request, $passengerId)
    {
        $passenger = TripPassenger::with(['trip', 'student'])->findOrFail($passengerId);

        if (intval($passenger->trip->driver_id) !== intval(Auth::id())) {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak.'
            ], 403);
        }

        $request->validate([
            'status' => 'required|in:waiting,picked_up,dropped_off,skipped,absent'
        ]);

        $updateData = ['status' => $request->status];

        if ($request->status === 'waiting') {
            if ($passenger->student && $passenger->student->parent_id) {
                $isPickup = ($passenger->trip && $passenger->trip->type === 'pickup');
                $pointText = $isPickup ? 'titik penjemputan' : 'lokasi rumah';
                $title = 'Driver Tiba 📍';
                $message = 'Driver telah tiba di ' . $pointText . ' untuk Ananda ' . $passenger->student->name . '.';
                Notification::create([
                    'user_id' => $passenger->student->parent_id,
                    'title' => $title,
                    'message' => $message,
                    'type' => 'driver_arrived',
                    'data' => ['passenger_id' => $passenger->id, 'student_id' => $passenger->student_id]
                ]);
                \App\Services\FcmService::sendToUser($passenger->student->parent_id, $title, $message);
            }
        } elseif ($request->status === 'picked_up') {
            $updateData['picked_at'] = Carbon::now();
            if ($passenger->student && $passenger->student->parent_id) {
                $isPickup = ($passenger->trip && $passenger->trip->type === 'pickup');
                $title = $isPickup ? 'Siswa Dijemput 🎒' : 'Siswa Naik Mobil Pulang 🚌';
                $message = $isPickup 
                    ? 'Ananda ' . $passenger->student->name . ' telah naik armada penjemputan.'
                    : 'Ananda ' . $passenger->student->name . ' telah naik armada pengantaran pulang dari sekolah.';
                Notification::create([
                    'user_id' => $passenger->student->parent_id,
                    'title' => $title,
                    'message' => $message,
                    'type' => 'picked_up',
                    'data' => ['passenger_id' => $passenger->id, 'student_id' => $passenger->student_id]
                ]);
                \App\Services\FcmService::sendToUser($passenger->student->parent_id, $title, $message);
            }
        } elseif ($request->status === 'dropped_off') {
            $updateData['dropped_at'] = Carbon::now();
            if ($passenger->student && $passenger->student->parent_id) {
                $isPickup = ($passenger->trip && $passenger->trip->type === 'pickup');
                $destText = $isPickup ? 'sekolah' : 'lokasi rumah';
                $title = $isPickup ? 'Siswa Telah Tiba 🏫' : 'Siswa Tiba di Rumah 🏠';
                $message = 'Ananda ' . $passenger->student->name . ' telah sampai dengan selamat di ' . $destText . '.';
                Notification::create([
                    'user_id' => $passenger->student->parent_id,
                    'title' => $title,
                    'message' => $message,
                    'type' => 'dropped_off',
                    'data' => ['passenger_id' => $passenger->id, 'student_id' => $passenger->student_id]
                ]);
                \App\Services\FcmService::sendToUser($passenger->student->parent_id, $title, $message);
            }
        }

        $passenger->update($updateData);

        // Hitung ulang progres trip
        $trip = $passenger->trip;
        $total = $trip->passengers()->count();
        $done = $trip->passengers()->whereNotIn('status', ['pending', 'waiting'])->count();
        $percent = $total > 0 ? ($done / $total) * 100 : 0;

        return response()->json([
            'success' => true,
            'message' => 'Status penumpang berhasil diperbarui.',
            'passenger_id' => $passenger->id,
            'status' => $passenger->status,
            'progress' => [
                'percent' => $percent,
                'done' => $done,
                'total' => $total
            ]
        ]);
    }

    // === SELESAIKAN PERJALANAN (FINISH TRIP) ===
    public function finishTrip(Request $request, $tripId)
    {
        $trip = Trip::findOrFail($tripId);

        if (intval($trip->driver_id) !== intval(Auth::id())) {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak.'
            ], 403);
        }

        // Cek jika ada penumpang yang masih berstatus pending/waiting
        $hasPending = $trip->passengers()
            ->whereIn('status', ['pending', 'waiting'])
            ->exists();

        if ($hasPending) {
            return response()->json([
                'success' => false,
                'message' => 'Masih ada siswa dengan status pending/menunggu. Harap tentukan statusnya (jemput/skip) terlebih dahulu.'
            ], 400);
        }

        // Otomatis ubah status siswa yang picked_up menjadi dropped_off saat trip diselesaikan (tiba di tujuan)
        $pickedUpPassengers = $trip->passengers()->where('status', 'picked_up')->with('student')->get();
        foreach ($pickedUpPassengers as $p) {
            $p->update([
                'status' => 'dropped_off',
                'dropped_at' => Carbon::now()
            ]);
            if ($p->student && $p->student->parent_id) {
                $isPickup = ($trip->type === 'pickup');
                $destText = $isPickup ? 'sekolah' : 'lokasi rumah';
                $title = $isPickup ? 'Siswa Telah Tiba 🏫' : 'Siswa Tiba di Rumah 🏠';
                $message = 'Ananda ' . $p->student->name . ' telah sampai dengan selamat di ' . $destText . '.';
                Notification::create([
                    'user_id' => $p->student->parent_id,
                    'title' => $title,
                    'message' => $message,
                    'type' => 'dropped_off',
                    'data' => ['passenger_id' => $p->id, 'student_id' => $p->student_id]
                ]);
                \App\Services\FcmService::sendToUser($p->student->parent_id, $title, $message);
            }
        }

        $trip->update([
            'status' => 'finished'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Perjalanan berhasil diselesaikan.'
        ]);
    }

    // === DAFTAR SELURUH SISWA JEMPUTAN (MY STUDENTS) ===
    public function myStudents(Request $request)
    {
        $driverId = Auth::id();
        $scheduleIds = \App\Models\Schedule::where('driver_id', $driverId)->pluck('id');
        $studentIds = \Illuminate\Support\Facades\DB::table('schedule_student')
                        ->whereIn('schedule_id', $scheduleIds)
                        ->pluck('student_id')
                        ->unique();

        $students = Student::whereIn('id', $studentIds)
                    ->with(['parent', 'complex']) 
                    ->get()
                    ->sortBy(function($student) {
                        return $student->complex->name ?? 'Z'; 
                    })
                    ->values()
                    ->map(function($s) {
                        return [
                            'id' => $s->id,
                            'name' => $s->name,
                            'photo_url' => $s->photo ? asset('storage/' . $s->photo) : null,
                            'complex_name' => $s->complex->name ?? '-',
                            'parent_name' => $s->parent->name ?? '-',
                            'parent_phone' => $s->parent->phone ?? '-',
                            'address_note' => $s->address_note
                        ];
                    });

        return response()->json([
            'success' => true,
            'students' => $students
        ]);
    }

    // === RIWAYAT PERJALANAN DRIVER ===
    public function history(Request $request)
    {
        $driverId = Auth::id();
        $trips = Trip::where('driver_id', $driverId)
            ->where('status', 'finished')
            ->with(['route', 'shuttle'])
            ->orderBy('date', 'desc')
            ->get()
            ->map(function($t) {
                return [
                    'id' => $t->id,
                    'date' => Carbon::parse($t->date)->format('d M Y'),
                    'type' => $t->type === 'pickup' ? 'Pagi (Jemput)' : 'Sore (Antar)',
                    'route_name' => $t->route->name ?? '-',
                    'shuttle_plate' => $t->shuttle->plate_number ?? '-',
                    'total_passengers' => $t->passengers()->count(),
                ];
            });

        return response()->json([
            'success' => true,
            'trips' => $trips
        ]);
    }

    // === MULAI PERJALANAN (CREATE TRIP DYNAMICALLY) ===
    public function startTrip(Request $request)
    {
        $request->validate([
            'shuttle_id' => 'required',
            'route_id' => 'required',
            'type' => 'required',
        ]);

        $driver = Auth::user();
        $todayDate = Carbon::now()->format('Y-m-d');
        
        // Cek jika trip tipe ini hari ini sudah ada
        $existing = Trip::where('driver_id', $driver->id)
            ->where('date', $todayDate)
            ->where('type', $request->type)
            ->where('route_id', $request->route_id)
            ->first();

        if ($existing) {
            return response()->json([
                'success' => true,
                'message' => 'Trip sudah dimulai sebelumnya.',
                'trip_id' => $existing->id
            ]);
        }

        $shuttle = \App\Models\Shuttle::findOrFail($request->shuttle_id);
        $dayName = Carbon::now()->format('l');

        $schedule = \App\Models\Schedule::where('driver_id', $driver->id)
                    ->where('day_of_week', $dayName)
                    ->where('route_id', $request->route_id)
                    ->with('students') 
                    ->first();

        $students = $schedule ? $schedule->students : collect();
        $totalSiswa = $students->count();

        if ($totalSiswa > $shuttle->capacity) {
            return response()->json([
                'success' => false,
                'message' => "Kapasitas mobil hanya {$shuttle->capacity} kursi, tapi jadwal ini memiliki {$totalSiswa} siswa terdaftar."
            ], 400);
        }

        $trip = Trip::create([
            'driver_id' => $driver->id,
            'shuttle_id' => $request->shuttle_id,
            'route_id' => $request->route_id,
            'date' => $todayDate,
            'type' => $request->type,
            'status' => 'active' 
        ]);

        foreach($students as $student) {
            TripPassenger::create([
                'trip_id' => $trip->id,
                'student_id' => $student->id,
                'status' => 'pending'
            ]);

            if ($student->parent_id) {
                $isPickup = ($request->type === 'pickup');
                $tripTypeLabel = $isPickup ? 'Penjemputan Pagi' : 'Pengantaran Sore';
                $title = $isPickup ? 'Perjalanan Jemput Dimulai 🚐' : 'Perjalanan Pulang Dimulai 🚌';
                $message = 'Perjalanan ' . $tripTypeLabel . ' untuk Ananda ' . $student->name . ' telah dimulai oleh driver ' . $driver->name . '.';
                Notification::create([
                    'user_id' => $student->parent_id,
                    'title' => $title,
                    'message' => $message,
                    'type' => 'trip_start',
                    'data' => ['trip_id' => $trip->id, 'student_id' => $student->id]
                ]);
                \App\Services\FcmService::sendToUser($student->parent_id, $title, $message);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Perjalanan dimulai! Semangat bertugas.',
            'trip_id' => $trip->id
        ]);
    }

    // === PROFIL DRIVER ===
    public function getProfile(Request $request)
    {
        $user = Auth::user();
        return response()->json([
            'success' => true,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email ?? '',
                'phone' => $user->phone,
                'role' => strtoupper($user->role),
                'photo_url' => $user->photo ? asset('storage/' . $user->photo) : null,
            ]
        ]);
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'password' => 'nullable|string|min:6',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = [
            'name' => $request->name,
            'phone' => $request->phone,
        ];

        if ($request->filled('email')) {
            $data['email'] = $request->email;
        }

        if ($request->filled('password')) {
            $data['password'] = \Illuminate\Support\Facades\Hash::make($request->password);
        }

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('user-photos', 'public');
            $data['photo'] = $path;
        }

        $user->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Profil Driver berhasil diperbarui.',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email ?? '',
                'phone' => $user->phone,
                'role' => strtoupper($user->role),
                'photo_url' => $user->photo ? asset('storage/' . $user->photo) : null,
            ]
        ]);
    }
}

