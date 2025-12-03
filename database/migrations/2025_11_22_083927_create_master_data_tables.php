<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tabel Rute (Buat duluan karena akan dipanggil Komplek)
        Schema::create('routes', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Contoh: "Rute Batam Center"
            $table->timestamps();
        });

        // 2. Tabel Komplek (Sekarang punya route_id)
        Schema::create('complexes', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Contoh: "Perumahan Cendana"
            
            // Relasi: Satu Komplek milik Satu Rute
            // nullable() agar bisa dibuat dulu tanpa rute jika perlu, tapi sebaiknya diisi.
            $table->foreignId('route_id')->nullable()->constrained('routes')->onDelete('set null');
            
            $table->timestamps();
        });

        // 3. Tabel Armada (Tetap sama)
        Schema::create('shuttles', function (Blueprint $table) {
            $table->id();
            $table->string('plate_number');
            $table->string('car_model');
            $table->integer('capacity');
            $table->string('status')->default('available');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shuttles');
        Schema::dropIfExists('complexes'); // Hapus komplek dulu karena ada foreign key ke routes
        Schema::dropIfExists('routes');
    }
};