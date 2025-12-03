<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\User;
use App\Models\Complex;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function index()
    {
        // Kita gunakan 'with' agar query database lebih hemat (Eager Loading)
        // Mengambil data siswa beserta data orang tua dan kompleknya
        $students = Student::with(['parent', 'complex'])->orderBy('name')->get();
        return view('students.index', compact('students'));
    }

    public function create()
    {
        // Ambil daftar User yang role-nya 'parent' untuk dropdown
        $parents = User::where('role', 'parent')->orderBy('name')->get();
        
        // Ambil daftar Komplek untuk dropdown
        $complexes = Complex::orderBy('name')->get();

        return view('students.create', compact('parents', 'complexes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'required|exists:users,id', // Harus user yang valid
            'complex_id' => 'required|exists:complexes,id', // Harus komplek yang valid
            'address_note' => 'nullable|string', // Blok/No Rumah
        ]);

        Student::create($request->all());

        return redirect()->route('students.index')->with('success', 'Data Siswa berhasil ditambahkan.');
    }

    public function edit(Student $student)
    {
        $parents = User::where('role', 'parent')->orderBy('name')->get();
        $complexes = Complex::orderBy('name')->get();

        return view('students.edit', compact('student', 'parents', 'complexes'));
    }

    public function update(Request $request, Student $student)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'required|exists:users,id',
            'complex_id' => 'required|exists:complexes,id',
            'address_note' => 'nullable|string',
        ]);

        $student->update($request->all());

        return redirect()->route('students.index')->with('success', 'Data Siswa berhasil diperbarui.');
    }

    public function destroy(Student $student)
    {
        $student->delete();
        return redirect()->route('students.index')->with('success', 'Data Siswa berhasil dihapus.');
    }
}