<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

  protected $fillable = [
    'name',
    'photo', // <--- Tambahkan ini
    'username',
    'email',
    'password',
    'phone',
    'role',
];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    // Relasi
    public function students() { return $this->hasMany(Student::class, 'parent_id'); }
    public function driverProfile() { return $this->hasOne(Driver::class, 'user_id'); }
}