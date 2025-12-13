<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ParentController extends Controller
{
    public function index(Request $request)
    {
        // 1. Cek Keamanan: Hanya Admin yang boleh masuk sini
        if (Auth::user()->role !== 'admin') {
            return redirect()->route('parents.dashboard'); 
        }

        // 2. Mulai Query
        $query = User::where('role', 'parent');

        // 3. Logika Pencarian (Nama atau ID)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('id', 'like', "%{$search}%");
            });
        }

        // 4. Eksekusi dengan Pagination
        $parents = $query->orderBy('name')->paginate(10)->withQueryString();

        return view('parents.index', compact('parents'));
    }

    public function create()
    {
        return view('parents.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'phone' => 'required|string',
            'password' => 'required|min:6',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'role' => 'parent',
        ]);

        return redirect()->route('parents.index')->with('success', 'Wali Murid berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $parent = User::where('role', 'parent')->findOrFail($id);
        return view('parents.edit', compact('parent'));
    }

    public function update(Request $request, $id)
    {
        $parent = User::where('role', 'parent')->findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($parent->id)],
            'phone' => 'required|string',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $parent->update($data);

        return redirect()->route('parents.index')->with('success', 'Data Wali Murid diperbarui.');
    }

    public function destroy($id)
    {
        $parent = User::where('role', 'parent')->findOrFail($id);
        $parent->delete();
        return redirect()->route('parents.index')->with('success', 'Wali Murid dihapus.');
    }
}