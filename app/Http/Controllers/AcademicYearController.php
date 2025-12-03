<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use Illuminate\Http\Request;

class AcademicYearController extends Controller
{
    public function index()
    {
        $years = AcademicYear::orderBy('created_at', 'desc')->get();
        return view('academic_years.index', compact('years'));
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required']);
        AcademicYear::create($request->all());
        return back()->with('success', 'Semester berhasil dibuat.');
    }

    public function activate($id)
    {
        // Matikan semua, lalu aktifkan satu
        AcademicYear::query()->update(['is_active' => false]);
        AcademicYear::where('id', $id)->update(['is_active' => true]);

        return back()->with('success', 'Semester AKTIF berhasil diganti.');
    }

    public function destroy($id)
    {
        AcademicYear::destroy($id);
        return back()->with('success', 'Dihapus.');
    }
}