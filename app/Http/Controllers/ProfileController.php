<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage; // <-- Tambahkan ini
use App\Models\Driver;

class ProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        return view('profile.index', compact('user'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        // Validasi
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'required|numeric',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Validasi Foto (Max 2MB)
            'license_number' => 'nullable|string', 
            'password' => 'nullable|min:6|confirmed',
        ]);

        // 1. Upload Foto Baru (Jika Ada)
        if ($request->hasFile('photo')) {
            // Hapus foto lama jika bukan default dan filenya ada
            if ($user->photo && Storage::exists('public/' . $user->photo)) {
                Storage::delete('public/' . $user->photo);
            }

            // Simpan foto baru ke folder 'public/profile_photos'
            $path = $request->file('photo')->store('profile_photos', 'public');
            $user->photo = $path;
        }

        // 2. Update Data Lain
        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone = $request->phone;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        // Update SIM Driver
        if ($user->role == 'driver') {
            Driver::updateOrCreate(
                ['user_id' => $user->id],
                ['license_number' => $request->license_number]
            );
        }

        return back()->with('success', 'Profil berhasil diperbarui!');
    }
}