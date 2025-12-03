<?php

namespace App\Http\Controllers;

use App\Models\Complex;
use App\Models\Route; // <--- Pastikan Model Route di-import
use Illuminate\Http\Request;

class ComplexController extends Controller
{
    public function index()
    {
        // Tampilkan komplek beserta nama rutenya
        $complexes = Complex::with('route')->orderBy('name')->get();
        return view('complexes.index', compact('complexes'));
    }

    // --- PERBAIKAN DISINI ---
    public function create()
    {
        // Ambil semua data rute untuk dropdown
        $routes = Route::orderBy('name')->get(); 
        
        // Kirim variable $routes ke view
        return view('complexes.create', compact('routes')); 
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'route_id' => 'required|exists:routes,id', // Wajib pilih rute
        ]);

        Complex::create($request->all());

        return redirect()->route('complexes.index')->with('success', 'Komplek berhasil ditambahkan.');
    }

    public function edit(Complex $complex)
    {
        $routes = Route::orderBy('name')->get();
        return view('complexes.edit', compact('complex', 'routes'));
    }

    public function update(Request $request, Complex $complex)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'route_id' => 'required|exists:routes,id',
        ]);

        $complex->update($request->all());

        return redirect()->route('complexes.index')->with('success', 'Komplek berhasil diperbarui.');
    }

    public function destroy(Complex $complex)
    {
        $complex->delete();
        return redirect()->route('complexes.index')->with('success', 'Komplek dihapus.');
    }
}