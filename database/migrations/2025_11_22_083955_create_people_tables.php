<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tabel Siswa
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            
            // Terhubung ke User (Orang Tua)
            $table->foreignId('parent_id')->constrained('users')->onDelete('cascade');
            
            // Terhubung ke Komplek (Untuk penentuan rute otomatis)
            $table->foreignId('complex_id')->constrained('complexes')->onDelete('cascade');
            
            $table->text('address_note')->nullable(); // Detail alamat rumah (Blok/Nomor)
            $table->string('photo')->nullable(); // Foto siswa (opsional)
            $table->timestamps();
        });

        // 2. Tabel Driver (Detail Driver)
        Schema::create('drivers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Akun Login Driver
            $table->string('license_number')->nullable(); // Nomor SIM
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('drivers');
        Schema::dropIfExists('students');
    }
};