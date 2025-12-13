<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tabel Perjalanan (Satu sesi jalan: Pagi atau Sore)
        Schema::create('trips', function (Blueprint $table) {
            $table->id();
            $table->foreignId('driver_id')->constrained('users'); // Siapa yang nyetir
            $table->foreignId('shuttle_id')->constrained('shuttles'); // Pakai mobil apa
            $table->foreignId('route_id')->constrained('routes'); // Rute mana
            
            $table->date('date'); // Tanggal perjalanan
            $table->enum('type', ['pickup', 'dropoff']); // pickup=jemput pagi, dropoff=antar pulang
            
            // Status Perjalanan Driver
            $table->enum('status', ['scheduled', 'active', 'finished'])->default('scheduled');
            
            $table->timestamps();
        });

        // 2. Tabel Detail Penumpang (Manifest)
        Schema::create('trip_passengers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trip_id')->constrained('trips')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('students');
            
            // PERBAIKAN DISINI: Menambahkan 'waiting' dan 'skipped'
            $table->enum('status', [
                'pending',      // Menunggu/Belum diapa-apain
                'waiting',      // [BARU] Driver sudah sampai depan rumah
                'picked_up',    // Siswa naik mobil
                'dropped_off',  // Siswa turun (sampai tujuan)
                'skipped',      // [BARU] Dilewati oleh driver (tombol Skip)
                'absent'        // Izin/Sakit (dari Ortu)
            ])->default('pending');
            
            $table->time('picked_at')->nullable(); // Jam masuk mobil
            $table->time('dropped_at')->nullable(); // Jam turun mobil
            
            $table->text('note')->nullable(); // Catatan driver jika ada
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trip_passengers');
        Schema::dropIfExists('trips');
    }
};