<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        // Hapus user lama jika ada (biar bersih)
        User::where('email', 'admin@sekolah.com')->delete();

        // Buat Akun Admin Baru
        User::create([
            'name' => 'Administrator Utama',
            'username' => 'admin', // <--- Username untuk login
            'email' => 'admin@sekolah.com',
            'phone' => '0812000000',
            'password' => Hash::make('admin123'), // Password
            'role' => 'admin'
        ]);
    }
}