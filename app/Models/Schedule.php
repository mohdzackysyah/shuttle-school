<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'day_of_week', 
        'pickup_time',  // Waktu jemput pagi
        'dropoff_time', // Waktu antar sore
        'route_id', 
        'driver_id', 
        'shuttle_id'
    ];

    public function route() { 
        return $this->belongsTo(Route::class); 
    }

    public function driver() { 
        return $this->belongsTo(User::class, 'driver_id'); 
    }

    public function shuttle() { 
        return $this->belongsTo(Shuttle::class); 
    }

    // --- RELASI BARU (PENTING) ---
    // Jadwal sekarang terhubung langsung ke Siswa (Many-to-Many)
    // Data ini disimpan di tabel pivot 'schedule_student'
    public function students() {
        return $this->belongsToMany(Student::class, 'schedule_student');
    }
}