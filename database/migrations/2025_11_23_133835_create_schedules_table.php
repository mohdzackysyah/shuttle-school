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
        
        // Relasi ke Semester (Wajib ada)
        $table->foreignId('academic_year_id')->constrained('academic_years')->onDelete('cascade');

        // Data Jadwal
        $table->enum('day_of_week', ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday']);
        $table->time('departure_time'); 
        $table->enum('type', ['pickup', 'dropoff']); 

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