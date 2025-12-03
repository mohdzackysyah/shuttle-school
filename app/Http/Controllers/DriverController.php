<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Driver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class DriverController extends Controller
{
    public function index()
    {
        // Ambil hanya driver
        $drivers = User::where('role', 'driver')->with('driverProfile')->orderBy('name')->get();
        return view('drivers.index', compact('drivers'));
    }

    public function create()
    {
        return view('drivers.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'phone' => 'required|string',
            'password' => 'required|min:6',
            'license_number' => 'required|string', // Wajib untuk driver
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'role' => 'driver', // Otomatis set driver
        ]);

        Driver::create([
            'user_id' => $user->id,
            'license_number' => $request->license_number
        ]);

        return redirect()->route('drivers.index')->with('success', 'Driver berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $driver = User::where('role', 'driver')->findOrFail($id);
        return view('drivers.edit', compact('driver'));
    }

    public function update(Request $request, $id)
    {
        $driver = User::where('role', 'driver')->findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($driver->id)],
            'phone' => 'required|string',
            'license_number' => 'required|string',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $driver->update($data);

        Driver::updateOrCreate(
            ['user_id' => $driver->id],
            ['license_number' => $request->license_number]
        );

        return redirect()->route('drivers.index')->with('success', 'Data Driver diperbarui.');
    }

    public function destroy($id)
    {
        $driver = User::where('role', 'driver')->findOrFail($id);
        $driver->delete();
        return redirect()->route('drivers.index')->with('success', 'Driver dihapus.');
    }
}