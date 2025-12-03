<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'day_of_week', 'departure_time', 'type',
        'route_id', 'driver_id', 'shuttle_id'
    ];

    public function route() { return $this->belongsTo(Route::class); }
    public function driver() { return $this->belongsTo(User::class, 'driver_id'); }
    public function shuttle() { return $this->belongsTo(Shuttle::class); }

    // HUBUNGAN BARU: Jadwal memiliki banyak komplek jemputan
    public function complexes() {
        return $this->belongsToMany(Complex::class, 'complex_schedule');
    }
}