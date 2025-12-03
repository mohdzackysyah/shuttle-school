<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // =========================================================
    // BAGIAN 1: LOGIN UNTUK USER BIASA (DRIVER & WALI MURID)
    // =========================================================
    
    // Menampilkan Form Login User (Tampilan Terang)
    public function showUserLogin()
    {
        return view('auth.login-user');
    }

    // Proses Login User
    public function loginUser(Request $request)
    {
        // 1. Validasi Input (Wajib Angka/No HP)
        $request->validate([
            'phone' => 'required|numeric',
            'password' => 'required|string',
        ]);

        // 2. Coba Login menggunakan No HP & Password
        $credentials = ['phone' => $request->phone, 'password' => $request->password];

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            $user = Auth::user();

            // KEAMANAN: Jika yang login ternyata ADMIN, tolak!
            if ($user->role == 'admin') {
                Auth::logout();
                return back()->withErrors(['login_error' => 'Admin dilarang login di sini! Silakan ke halaman khusus Admin.']);
            }

            // Redirect sesuai Role (Driver atau Orang Tua)
            if ($user->role === 'driver') {
                return redirect()->route('driver.dashboard');
            } else {
                // PERBAIKAN DISINI: Arahkan ke Dashboard Monitor Anak
                return redirect()->route('parents.dashboard'); 
            }
        }

        // Jika Gagal
        return back()->withErrors(['login_error' => 'Nomor HP atau Password salah.'])->withInput();
    }

    // =========================================================
    // BAGIAN 2: LOGIN KHUSUS ADMIN (TAMPILAN GELAP)
    // =========================================================

    // Menampilkan Form Login Admin
    public function showAdminLogin()
    {
        return view('auth.login-admin');
    }

    // Proses Login Admin
    public function loginAdmin(Request $request)
    {
        // 1. Validasi Input (Wajib Username)
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        // 2. Coba Login menggunakan Username & Password
        $credentials = ['username' => $request->username, 'password' => $request->password];

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            $user = Auth::user();

            // KEAMANAN: Jika yang login BUKAN Admin, tolak!
            if ($user->role !== 'admin') {
                Auth::logout();
                return back()->withErrors(['login_error' => 'Akses Ditolak! Anda bukan Administrator.']);
            }

            // Sukses -> Ke Dashboard Admin
            return redirect()->route('admin.dashboard');
        }

        // Jika Gagal
        return back()->withErrors(['login_error' => 'Username atau Password Admin salah.'])->withInput();
    }

    // =========================================================
    // BAGIAN 3: LOGOUT
    // =========================================================
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        // Kembali ke halaman login user biasa
        return redirect()->route('login');
    }
}