<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Driver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    // --- 1. LOGIC MENAMPILKAN LIST DRIVER ---
    public function indexDrivers()
    {
        // Ambil user yang rolenya driver
        $users = User::where('role', 'driver')->with('driverProfile')->orderBy('name', 'asc')->get();
        
        // Variable bantuan untuk View
        $pageTitle = 'Data Driver';
        $type = 'driver'; 

        return view('users.index', compact('users', 'pageTitle', 'type'));
    }

    // --- 2. LOGIC MENAMPILKAN LIST WALI MURID ---
    public function indexParents()
    {
        // Ambil user yang rolenya parent
        $users = User::where('role', 'parent')->orderBy('name', 'asc')->get();
        
        $pageTitle = 'Data Wali Murid';
        $type = 'parent';

        return view('users.index', compact('users', 'pageTitle', 'type'));
    }

    // --- 3. FORM CREATE ---
    public function createDriver()
    {
        return view('users.create_driver');
    }

    public function createParent()
    {
        return view('users.create_parent');
    }

    // --- 4. PROSES SIMPAN (STORE) ---
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'required|string|max:20',
            'password' => 'required|string|min:6',
            'role' => 'required|in:admin,driver,parent',
            'license_number' => 'nullable|required_if:role,driver|string|max:50',
        ]);

        // Buat User
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        // Jika Driver, simpan data SIM
        if ($request->role === 'driver') {
            Driver::create([
                'user_id' => $user->id,
                'license_number' => $request->license_number
            ]);
            
            // Redirect ke halaman driver
            return redirect()->route('users.drivers')->with('success', 'Driver berhasil ditambahkan.');
        }

        // Jika Parent, redirect ke halaman parent
        return redirect()->route('users.parents')->with('success', 'Wali Murid berhasil ditambahkan.');
    }

    // --- 5. EDIT & UPDATE ---
    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'phone' => 'required|string|max:20',
            'role' => 'required|in:admin,driver,parent',
            'password' => 'nullable|string|min:6',
            'license_number' => 'nullable|string|max:50',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'role' => $request->role,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        // Update data SIM jika driver
        if ($user->role === 'driver' && $request->has('license_number')) {
            Driver::updateOrCreate(
                ['user_id' => $user->id],
                ['license_number' => $request->license_number]
            );
        }

        // Redirect pintar (kembali ke halaman asal role user)
        if($user->role == 'driver') {
            return redirect()->route('users.drivers')->with('success', 'Data Driver diperbarui.');
        } else {
            return redirect()->route('users.parents')->with('success', 'Data Wali Murid diperbarui.');
        }
    }

    // --- 6. HAPUS ---
    public function destroy(User $user)
    {
        $role = $user->role; // Simpan role dulu buat redirect
        
        if (auth()->id() == $user->id) {
            return back()->with('error', 'Anda tidak bisa menghapus akun sendiri.');
        }
        $user->delete();

        if($role == 'driver') {
            return redirect()->route('users.drivers')->with('success', 'Driver berhasil dihapus.');
        } else {
            return redirect()->route('users.parents')->with('success', 'Wali Murid berhasil dihapus.');
        }
    }
}