<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trip extends Model
{
    use HasFactory;

    protected $fillable = [
        'driver_id',
        'shuttle_id',
        'route_id',
        'date',
        'type', // pickup / dropoff
        'status' // scheduled, active, finished
    ];

    // Relasi ke Driver
    public function driver()
    {
        return $this->belongsTo(User::class, 'driver_id');
    }

    // Relasi ke Mobil
    public function shuttle()
    {
        return $this->belongsTo(Shuttle::class);
    }

    // Relasi ke Rute
    public function route()
    {
        return $this->belongsTo(Route::class);
    }

    // Relasi ke Penumpang (Detail anak2 di dalam trip ini)
    public function passengers()
    {
        return $this->hasMany(TripPassenger::class);
    }
}