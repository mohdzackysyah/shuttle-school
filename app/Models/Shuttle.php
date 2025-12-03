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
        'capacity',
        'status'
    ];

    // Relasi ke Jadwal (PENTING UNTUK MENGHITUNG STATUS SIBUK/TIDAK)
    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }
}