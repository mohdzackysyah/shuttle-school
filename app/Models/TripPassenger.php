<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TripPassenger extends Model
{
    use HasFactory;

    protected $fillable = [
        'trip_id',
        'student_id',
        'status', // pending, picked_up, dropped_off, absent
        'picked_at',
        'dropped_at',
        'note'
    ];

    public function trip()
    {
        return $this->belongsTo(Trip::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}