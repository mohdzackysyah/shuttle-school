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

    // Relasi ke Trip (Jadwal) tetap sama
    public function trips()
    {
        return $this->hasMany(Trip::class);
    }
}