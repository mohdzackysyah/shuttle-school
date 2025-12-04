<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            
            // Relasi ke Semester (Pastikan tabel 'academic_years' sudah ada migration-nya di atas file ini)
            $table->foreignId('academic_year_id')->constrained('academic_years')->onDelete('cascade');

            // Data Jadwal
            $table->enum('day_of_week', ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday']);
            
            // PERUBAHAN DISINI:
            // Mengganti 'departure_time' & 'type' dengan dua kolom waktu terpisah
            $table->time('pickup_time')->nullable();  // Jam Jemput (Pagi)
            $table->time('dropoff_time')->nullable(); // Jam Antar (Sore)

            // Relasi Operasional
            $table->foreignId('route_id')->constrained('routes')->onDelete('cascade');
            $table->foreignId('driver_id')->constrained('users')->onDelete('cascade'); 
            $table->foreignId('shuttle_id')->constrained('shuttles')->onDelete('cascade');
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};