<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up(): void
{
    Schema::create('announcements', function (Blueprint $table) {
        $table->id();
        $table->string('title');
        $table->text('content');
        // Target: 'all' (Semua), 'driver' (Supir), 'parent' (Wali Murid)
        $table->enum('target_role', ['all', 'driver', 'parent']);
        $table->boolean('is_active')->default(true); // Opsi untuk menyembunyikan tanpa menghapus
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('announcements');
    }
};
