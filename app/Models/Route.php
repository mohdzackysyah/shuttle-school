<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Route extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    // Satu Rute punya Banyak Komplek
    public function complexes()
    {
        return $this->hasMany(Complex::class);
    }

    // Relasi ke Trip (History Perjalanan)
    public function trips()
    {
        return $this->hasMany(Trip::class);
    }

    // [BARU] Relasi ke Jadwal (Untuk menghitung armada)
    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }
}