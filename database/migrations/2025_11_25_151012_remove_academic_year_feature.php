<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Hapus kolom academic_year_id di tabel schedules
        Schema::table('schedules', function (Blueprint $table) {
            $table->dropForeign(['academic_year_id']); // Hapus relasi
            $table->dropColumn('academic_year_id');    // Hapus kolom
        });

        // 2. Hapus tabel academic_years
        Schema::dropIfExists('academic_years');
    }

    public function down(): void
    {
        // Kosongkan saja (kita tidak berencana mengembalikan fitur ini)
    }
};