<?php

namespace App\Http\Controllers;

use App\Models\TripPassenger;
use App\Models\Trip;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class TripPassengerController extends Controller
{
    /**
     * 1. Halaman Proses Perjalanan
     * Memuat data siswa, orang tua (HP), dan komplek (Alamat).
     */
    public function process($tripId)
    {
        $trip = Trip::with([
            'passengers.student.parent',   // PENTING: Untuk ambil No HP (phone)
            'passengers.student.complex',  // PENTING: Untuk ambil Nama Komplek
            'route', 
            'shuttle'
        ])->findOrFail($tripId);

        // Validasi Pemilik Trip
        if (Auth::user()->id != $trip->driver_id) {
            return redirect()->route('driver.dashboard')->with('error', 'Akses ditolak.');
        }

        // --- PERBAIKAN DISINI ---
        // Jika status sudah 'finished', jangan biarkan masuk ke halaman ini lagi.
        // Langsung lempar ke dashboard.
        if ($trip->status == 'finished') {
            return redirect()->route('driver.dashboard')->with('success', 'Perjalanan ini sudah selesai.');
        }
        // -------------------------

        // Auto-start trip jika masih 'scheduled'
        if ($trip->status == 'scheduled') {
            $trip->update(['status' => 'active']);
        }

        return view('driver_dashboard.perjalanan', [
            'trip' => $trip,
            'passengers' => $trip->passengers
        ]);
    }

    /**
     * 2. LOGIKA JEMPUT (NAIK KE MOBIL)
     * Berlaku untuk Pagi (di rumah) dan Sore (di sekolah).
     */
    public function pickup($id)
    {
        $passenger = TripPassenger::findOrFail($id);
        
        $passenger->update([
            'status' => 'picked_up',
            'picked_at' => Carbon::now()
        ]);
        
        // Pastikan status Trip induk menjadi 'active' jika siswa pertama naik
        if ($passenger->trip && $passenger->trip->status == 'scheduled') {
            $passenger->trip->update(['status' => 'active']);
        }

        return back()->with('success', 'Siswa berhasil naik/dijemput.');
    }

    /**
     * 3. LOGIKA SKIP (LEWATI)
     * Jika siswa tidak masuk atau izin.
     */
    public function skip($id)
    {
        $passenger = TripPassenger::findOrFail($id);
        
        $passenger->update([
            'status' => 'skipped', 
        ]);

        return back()->with('warning', 'Penjemputan siswa dilewati.');
    }

    /**
     * 4. LOGIKA TURUN (SAMPAI RUMAH) - BARU!
     * Khusus digunakan saat tombol Hijau "TURUN" diklik pada pengantaran sore.
     */
    public function dropoff($id)
    {
        $passenger = TripPassenger::findOrFail($id);
        
        $passenger->update([
            'status' => 'dropped_off',
            'dropped_at' => Carbon::now()
        ]);

        return back()->with('success', 'Siswa telah sampai di rumah.');
    }

    /**
     * 5. SELESAI SESI (FINISH)
     * Mengakhiri seluruh perjalanan.
     */
    public function finishTrip($tripId)
    {
        $trip = Trip::findOrFail($tripId);
        
        // --- LOGIKA KHUSUS JEMPUT PAGI ---
        // Jika pagi, driver hanya klik "Naik" satu per satu.
        // Saat sampai sekolah (Finish), semua siswa di dalam mobil otomatis dianggap "Turun".
        if ($trip->type == 'pickup') { 
            $trip->passengers()->where('status', 'picked_up')->update([
                'status' => 'dropped_off',
                'dropped_at' => Carbon::now()
            ]);
        }

        // --- LOGIKA ANTAR SORE ---
        // Untuk sore, driver seharusnya sudah mengklik tombol "TURUN" satu per satu di method dropoff().
        // Method ini hanya menutup status Trip utamanya saja.

        // Update status Trip jadi selesai
        $trip->update([
            'status' => 'finished'
            // 'finished_at' => Carbon::now() // Uncomment jika punya kolom ini
        ]);
        
        return redirect()->route('driver.dashboard')
            ->with('success', 'Perjalanan selesai. Terima kasih!');
    }
}