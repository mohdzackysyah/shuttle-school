<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class ApiAuthController extends Controller
{
    // === LOGIN ===
    public function login(Request $request)
    {
        // 1. Validasi Input
        $request->validate([
            'phone' => 'required',
            'password' => 'required',
        ]);

        // 2. Cari User berdasarkan No HP
        $user = User::where('phone', $request->phone)->first();

        // 3. Cek Password & User
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Nomor HP atau Password salah.'
            ], 401);
        }

        // 4. Cek Role (Admin dilarang login di App)
        if (!in_array($user->role, ['driver', 'parent'])) {
            return response()->json([
                'success' => false,
                'message' => 'Maaf, Admin hanya boleh login via Website.'
            ], 403);
        }

        // 5. Buat Token (Tiket Masuk Digital)
        // Kita hapus token lama user ini supaya bersih (opsional)
        $user->tokens()->delete();
        
        $token = $user->createToken('auth_token')->plainTextToken;

        // 6. Kirim Respon JSON ke HP
        return response()->json([
            'success' => true,
            'message' => 'Login Berhasil',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'role' => $user->role,
                    'phone' => $user->phone,
                    // Pastikan user punya foto, kalau tidak pakai placeholder
                    'photo_url' => $user->photo ? asset('storage/' . $user->photo) : null,
                ],
                'access_token' => $token,
                'token_type' => 'Bearer'
            ]
        ]);
    }

    // === LOGOUT ===
    public function logout(Request $request)
    {
        // Hapus token yang sedang dipakai
        $request->user()->currentAccessToken()->delete();
        
        return response()->json([
            'success' => true, 
            'message' => 'Logout berhasil'
        ]);
    }
    
    // === CEK USER (Test Token) ===
    public function getUser(Request $request)
    {
        return response()->json([
            'success' => true,
            'user' => $request->user()
        ]);
    }
}