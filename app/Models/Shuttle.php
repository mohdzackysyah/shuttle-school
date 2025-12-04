<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shuttle extends Model
{
    use HasFactory;

    protected $fillable = [
        'plate_number',
        'car_model',
        'capacity', // Penting untuk validasi jumlah penumpang
        'status'    // available / maintenance
    ];

    // --- RELASI JADWAL ---
    // Digunakan untuk mengecek ketersediaan mobil (Anti-Bentrok)
    // $shuttle->schedules
    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }
}