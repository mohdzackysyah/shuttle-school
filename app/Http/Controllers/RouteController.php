<?php

namespace App\Http\Controllers;

use App\Models\Route;
use Illuminate\Http\Request;

class RouteController extends Controller
{
    public function index()
    {
        $routes = Route::all();
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
        return view('routes.edit', compact('route'));
    }

    public function update(Request $request, Route $route)
    {
        $request->validate(['name' => 'required|string|max:255']);
        $route->update($request->all());
        return redirect()->route('routes.index')->with('success', 'Rute diperbarui.');
    }

    public function destroy(Route $route)
    {
        $route->delete();
        return redirect()->route('routes.index')->with('success', 'Rute dihapus.');
    }
}