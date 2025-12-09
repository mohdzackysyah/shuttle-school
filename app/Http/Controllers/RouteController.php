<?php

namespace App\Http\Controllers;

use App\Models\Route;
use App\Models\Complex; // Pastikan Import ini ada jika dipakai di edit/create
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // Import DB untuk Raw Query

class RouteController extends Controller
{
    public function index()
    {
        // Ambil rute beserta kompleknya
        // Dan hitung jumlah mobil UNIK (DISTINCT) yang ada di jadwal rute tersebut
        $routes = Route::with('complexes')
            ->withCount(['schedules as shuttles_count' => function ($query) {
                $query->select(DB::raw('count(distinct shuttle_id)'));
            }])
            ->get();

        return view('routes.index', compact('routes'));
    }

    public function create()
    {
        return view('routes.create');
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255']);
        Route::create($request->all());
        return redirect()->route('routes.index')->with('success', 'Rute berhasil dibuat.');
    }

    public function edit(Route $route)
    {
        // Ambil semua komplek untuk pilihan
        $complexes = Complex::orderBy('name')->get();
        return view('routes.edit', compact('route', 'complexes'));
    }

    public function update(Request $request, Route $route)
    {
        $request->validate(['name' => 'required|string|max:255']);
        
        // Update Nama Rute
        $route->update(['name' => $request->name]);

        // Update Relasi Komplek (Jika ada input checkbox dari form edit)
        if ($request->has('complexes')) {
            // Reset dulu semua komplek yang terhubung ke rute ini jadi null
            Complex::where('route_id', $route->id)->update(['route_id' => null]);
            
            // Set komplek yang dipilih ke rute ini
            Complex::whereIn('id', $request->complexes)->update(['route_id' => $route->id]);
        }

        return redirect()->route('routes.index')->with('success', 'Rute diperbarui.');
    }

    public function destroy(Route $route)
    {
        $route->delete();
        return redirect()->route('routes.index')->with('success', 'Rute dihapus.');
    }
}