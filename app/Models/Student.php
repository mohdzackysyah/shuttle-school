<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'parent_id',
        'complex_id',
        'address_note',
        'photo'
    ];

    // Dimiliki oleh Orang Tua (User)
    public function parent()
    {
        return $this->belongsTo(User::class, 'parent_id');
    }

    // Tinggal di Komplek tertentu
    public function complex()
    {
        return $this->belongsTo(Complex::class);
    }
}