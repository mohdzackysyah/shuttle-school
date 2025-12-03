<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Complex extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'route_id']; // Tambahkan route_id

    // Satu Komplek milik Satu Rute
    public function route()
    {
        return $this->belongsTo(Route::class);
    }

    public function students()
    {
        return $this->hasMany(Student::class);
    }
}