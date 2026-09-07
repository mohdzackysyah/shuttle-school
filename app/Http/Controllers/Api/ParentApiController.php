<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\TripPassenger;
use App\Models\Schedule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class ParentApiController extends Controller
{
    public function dashboard(Request $request)
    {
        $parent = Auth::user();
        
        // Ambil semua anak wali murid beserta data komplek rutenya
        $students = Student::where('parent_id', $parent->id)
            ->with(['complex.route'])
            ->get();

        $today = Carbon::today();
        $todayEnglish = Carbon::now()->format('l');

        $data = $students->map(function($student) use ($today, $todayEnglish) {
            // Dapatkan trip hari ini (pagi & sore)
            $pagi = TripPassenger::where('student_id', $student->id)
                ->whereHas('trip', function($q) use ($today) {
                    $q->where('type', 'pickup')
                      ->whereDate('date', $today);
                })
                ->with(['trip.driver', 'trip.shuttle', 'trip.route'])
                ->first();

            $sore = TripPassenger::where('student_id', $student->id)
                ->whereHas('trip', function($q) use ($today) {
                    $q->where('type', 'dropoff')
                      ->whereDate('date', $today);
                })
                ->with(['trip.driver', 'trip.shuttle', 'trip.route'])
                ->first();

            // Master schedule fallback untuk data terdaftar (sebelum driver mulai)
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

            $pagiStatus = ($pagi && ($pagi->status === 'dropped_off' || ($pagi->trip && $pagi->trip->status === 'finished' && $pagi->status === 'picked_up'))) ? 'dropped_off' : ($pagi ? $pagi->status : 'pending');
            $soreStatus = ($sore && ($sore->status === 'dropped_off' || ($sore->trip && $sore->trip->status === 'finished' && $sore->status === 'picked_up'))) ? 'dropped_off' : ($sore ? $sore->status : 'pending');

            $pagiNote = 'Perjalanan telah dimulai oleh supir.';
            if ($pagiStatus === 'dropped_off') {
                $pagiNote = 'Siswa sudah sampai di sekolah.';
            } elseif ($pagiStatus === 'picked_up') {
                $pagiNote = 'Siswa sudah dijemput.';
            } elseif ($pagiStatus === 'waiting') {
                $pagiNote = 'Driver sudah sampai di titik penjemputan.';
            } elseif ($pagiStatus === 'skipped') {
                $pagiNote = 'Penjemputan dilewati.';
            } elseif ($pagiStatus === 'absent') {
                $pagiNote = 'Siswa izin / tidak hadir.';
            }

            $soreNote = 'Perjalanan telah dimulai oleh supir.';
            if ($soreStatus === 'dropped_off') {
                $soreNote = 'Siswa sudah sampai di rumah.';
            } elseif ($soreStatus === 'picked_up') {
                $soreNote = 'Siswa sudah dijemput di sekolah.';
            } elseif ($soreStatus === 'waiting') {
                $soreNote = 'Driver sudah sampai di titik penjemputan.';
            } elseif ($soreStatus === 'skipped') {
                $soreNote = 'Pengantaran dilewati.';
            } elseif ($soreStatus === 'absent') {
                $soreNote = 'Siswa izin / tidak hadir.';
            }

            $tripPagiData = $pagi ? [
                'is_started' => true,
                'passenger_id' => $pagi->id,
                'status' => $pagiStatus,
                'trip_status' => $pagi->trip->status,
                'route_name' => $pagi->trip->route->name ?? $routeTitle,
                'driver_name' => $pagi->trip->driver->name ?? '-',
                'driver_phone' => $pagi->trip->driver->phone ?? '',
                'shuttle_plate' => $pagi->trip->shuttle->plate_number ?? '-',
                'scheduled_time' => $pagi->picked_at ? Carbon::parse($pagi->picked_at)->format('H:i:s') . ' WIB' : '07:00:00 WIB',
                'status_text' => $pagi->trip->status == 'active' ? 'Dalam Perjalanan' : ($pagi->trip->status == 'finished' ? 'Selesai' : 'Sedang Berjalan'),
                'status_note' => $pagiNote,
            ] : [
                'is_started' => false,
                'passenger_id' => null,
                'status' => 'pending',
                'trip_status' => 'not_started',
                'route_name' => $masterSchedule->route->name ?? $routeTitle,
                'driver_name' => $masterSchedule->driver->name ?? 'Belum Ditentukan',
                'driver_phone' => $masterSchedule->driver->phone ?? '',
                'shuttle_plate' => $masterSchedule->shuttle->plate_number ?? '-',
                'scheduled_time' => ($masterSchedule && $masterSchedule->pickup_time) ? Carbon::parse($masterSchedule->pickup_time)->format('H:i:s') . ' WIB' : '07:00:00 WIB',
                'status_text' => 'Belum Dimulai',
                'status_note' => 'Menunggu supir memulai perjalanan.',
            ];

            $tripSoreData = $sore ? [
                'is_started' => true,
                'passenger_id' => $sore->id,
                'status' => $soreStatus,
                'trip_status' => $sore->trip->status,
                'route_name' => $sore->trip->route->name ?? $routeTitle,
                'driver_name' => $sore->trip->driver->name ?? '-',
                'driver_phone' => $sore->trip->driver->phone ?? '',
                'shuttle_plate' => $sore->trip->shuttle->plate_number ?? '-',
                'scheduled_time' => $sore->dropped_at ? Carbon::parse($sore->dropped_at)->format('H:i:s') . ' WIB' : '13:00:00 WIB',
                'status_text' => $sore->trip->status == 'active' ? 'Dalam Perjalanan' : ($sore->trip->status == 'finished' ? 'Selesai' : 'Sedang Berjalan'),
                'status_note' => $soreNote,
            ] : [
                'is_started' => false,
                'passenger_id' => null,
                'status' => 'pending',
                'trip_status' => 'not_started',
                'route_name' => $masterSchedule->route->name ?? $routeTitle,
                'driver_name' => $masterSchedule->driver->name ?? 'Belum Ditentukan',
                'driver_phone' => $masterSchedule->driver->phone ?? '',
                'shuttle_plate' => $masterSchedule->shuttle->plate_number ?? '-',
                'scheduled_time' => ($masterSchedule && $masterSchedule->dropoff_time) ? Carbon::parse($masterSchedule->dropoff_time)->format('H:i:s') . ' WIB' : '13:00:00 WIB',
                'status_text' => 'Belum Dimulai',
                'status_note' => 'Menunggu supir memulai perjalanan.',
            ];

            return [
                'id' => $student->id,
                'name' => $student->name,
                'photo_url' => $student->photo ? asset('storage/' . $student->photo) : null,
                'complex_name' => $student->complex->name ?? '-',
                'route_name' => $student->complex->route->name ?? '-',
                'address_note' => $student->address_note,
                'latitude' => $student->latitude,
                'longitude' => $student->longitude,
                'trip_pagi' => $tripPagiData,
                'trip_sore' => $tripSoreData,
            ];
        });

        $announcements = \App\Models\Announcement::where('is_active', true)
            ->whereIn('target_role', ['all', 'parent'])
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
            'students' => $data,
            'announcements' => $announcements
        ]);
    }

    // === LIVE TRACKING / TRIP DETAIL ===
    public function tripDetail(Request $request, $passengerId)
    {
        // Selalu query ulang dari database (hindari cache Eloquent / CDN)
        $passenger = TripPassenger::with(['student'])->findOrFail($passengerId);
        
        // Ambil trip secara terpisah agar selalu fresh (koordinat driver terbaru)
        $trip = \App\Models\Trip::with(['driver', 'shuttle', 'route'])->findOrFail($passenger->trip_id);
        
        // Validasi keamanan: Pastikan ini anak si wali murid
        if (intval($passenger->student->parent_id) !== intval(Auth::id())) {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak.'
            ], 403);
        }

        $effectiveStatus = $passenger->status;
        if ($trip->status === 'finished' && $passenger->status === 'picked_up') {
            $effectiveStatus = 'dropped_off';
        }

        return response()->json([
            'success' => true,
            'passenger_id' => $passenger->id,
            'passenger_status' => $effectiveStatus,
            'picked_at' => $passenger->picked_at ? Carbon::parse($passenger->picked_at)->format('H:i') : null,
            'dropped_at' => $passenger->dropped_at ? Carbon::parse($passenger->dropped_at)->format('H:i') : null,
            'trip_status' => $trip->status,
            'trip_type' => $trip->type,
            'driver' => [
                'name' => $trip->driver->name,
                'phone' => $trip->driver->phone,
                'photo_url' => $trip->driver->photo ? asset('storage/' . $trip->driver->photo) : null,
            ],
            'vehicle' => [
                'plate_number' => $trip->shuttle->plate_number,
                'model' => $trip->shuttle->car_model,
            ],
            'route_name' => $trip->route->name,
            'driver_lat' => $trip->current_latitude,
            'driver_lng' => $trip->current_longitude,
            'student_lat' => $passenger->student->latitude,
            'student_lng' => $passenger->student->longitude
        ], 200, [
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ]);
    }

    // === UPDATE PROFILE ANAK (ALAMAT & KOORDINAT & FOTO) ===
    public function updateChild(Request $request, $studentId)
    {
        $student = Student::where('id', $studentId)->where('parent_id', Auth::id())->firstOrFail();
        
        $request->validate([
            'address_note' => 'nullable|string|max:255',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $data = [
            'address_note' => $request->address_note,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
        ];

        if ($request->hasFile('photo')) {
            if ($student->photo) {
                Storage::disk('public')->delete($student->photo);
            }
            $path = $request->file('photo')->store('student-photos', 'public');
            $data['photo'] = $path;
        }

        $student->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Profil anak berhasil diperbarui.',
            'student' => [
                'id' => $student->id,
                'photo_url' => $student->photo ? asset('storage/' . $student->photo) : null,
                'address_note' => $student->address_note,
                'latitude' => $student->latitude,
                'longitude' => $student->longitude,
            ]
        ]);
    }

    // === IZIN / ABSENT SISWA HARI INI ===
    public function setAbsent(Request $request, $studentId)
    {
        $student = Student::where('id', $studentId)->where('parent_id', Auth::id())->firstOrFail();
        
        $today = Carbon::today();
        $passengers = TripPassenger::where('student_id', $student->id)
            ->whereHas('trip', function($q) use ($today) {
                $q->whereDate('date', $today);
            })->get();

        if ($passengers->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Jadwal jemputan untuk hari ini belum dibuat oleh Admin.'
            ], 400);
        }

        foreach ($passengers as $p) {
            $p->update(['status' => 'absent']);
        }

        return response()->json([
            'success' => true,
            'message' => 'Status absen anak berhasil didaftarkan.'
        ]);
    }

    // === RIWAYAT PERJALANAN ANAK ===
    public function history(Request $request)
    {
        $parent = Auth::user();
        $childIds = Student::where('parent_id', $parent->id)->pluck('id');
        
        $query = TripPassenger::whereIn('student_id', $childIds)
            ->with(['trip.route', 'trip.driver', 'student'])
            ->whereHas('trip', function($q) { $q->where('status', 'finished'); });

        if ($request->filled('date')) {
            $query->whereHas('trip', function($q) use ($request) { $q->whereDate('date', $request->date); });
        }

        $histories = $query->join('trips', 'trip_passengers.trip_id', '=', 'trips.id')
                           ->select('trip_passengers.*')
                           ->orderBy('trips.date', 'desc')
                           ->get()
                           ->map(function($h) {
                               return [
                                   'id' => $h->id,
                                   'student_name' => $h->student->name,
                                   'route_name' => $h->trip->route->name ?? '-',
                                   'driver_name' => $h->trip->driver->name ?? '-',
                                   'date' => Carbon::parse($h->trip->date)->format('d M Y'),
                                   'type' => $h->trip->type === 'pickup' ? 'Pagi (Jemput)' : 'Sore (Antar)',
                                   'status' => $h->status,
                                   'picked_at' => $h->picked_at ? Carbon::parse($h->picked_at)->format('H:i') : null,
                                   'dropped_at' => $h->dropped_at ? Carbon::parse($h->dropped_at)->format('H:i') : null,
                               ];
                           });

        return response()->json([
            'success' => true,
            'histories' => $histories
        ]);
    }

    // === PROFIL WALI MURID ===
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
            'email' => 'nullable|email|max:255',
            'phone' => 'required|string|max:20',
            'password' => 'nullable|string|min:6',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
        ];

        if ($request->filled('password')) {
            $data['password'] = \Illuminate\Support\Facades\Hash::make($request->password);
        }

        if ($request->hasFile('photo')) {
            if ($user->photo) {
                Storage::disk('public')->delete($user->photo);
            }
            $path = $request->file('photo')->store('user-photos', 'public');
            $data['photo'] = $path;
        }

        $user->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Profil berhasil diperbarui.',
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
