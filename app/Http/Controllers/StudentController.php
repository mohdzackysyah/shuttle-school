<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\User;
use App\Models\Complex;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage; // <--- PENTING: Tambahkan ini untuk hapus foto

class StudentController extends Controller
{
    public function index()
    {
        $students = Student::with(['parent', 'complex'])->latest()->get();
        return view('students.index', compact('students'));
    }

    public function create()
    {
        $parents = User::where('role', 'parent')->get();
        $complexes = Complex::all();
        return view('students.create', compact('parents', 'complexes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'required',
            'complex_id' => 'required',
            'address_note' => 'nullable|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048', // Validasi Foto
        ]);

        $data = $request->all();

        // 1. LOGIKA UPLOAD FOTO
        if ($request->hasFile('photo')) {
            // Simpan ke folder 'storage/app/public/student-photos'
            $path = $request->file('photo')->store('student-photos', 'public');
            $data['photo'] = $path;
        }

        Student::create($data);

        return redirect()->route('students.index')->with('success', 'Data siswa berhasil ditambahkan.');
    }

    public function edit(Student $student)
    {
        $parents = User::where('role', 'parent')->get();
        $complexes = Complex::all();
        return view('students.edit', compact('student', 'parents', 'complexes'));
    }

    public function update(Request $request, Student $student)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'required',
            'complex_id' => 'required',
            'address_note' => 'nullable|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $data = $request->all();

        // 2. LOGIKA UPDATE FOTO
        if ($request->hasFile('photo')) {
            // Hapus foto lama jika ada
            if ($student->photo) {
                Storage::disk('public')->delete($student->photo);
            }
            // Simpan foto baru
            $path = $request->file('photo')->store('student-photos', 'public');
            $data['photo'] = $path;
        }

        $student->update($data);

        return redirect()->route('students.index')->with('success', 'Data siswa diperbarui.');
    }

    public function destroy(Student $student)
    {
        // Hapus foto saat data siswa dihapus
        if ($student->photo) {
            Storage::disk('public')->delete($student->photo);
        }
        
        $student->delete();
        return redirect()->route('students.index')->with('success', 'Data siswa dihapus.');
    }
}