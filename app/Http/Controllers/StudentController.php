<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\User;
use App\Models\Complex;
use App\Models\Schedule; // Tambahkan ini
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class StudentController extends Controller
{
    // ... method index, create, store, edit, update, destroy biarkan saja ...
    
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
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $data = $request->all();

        if ($request->hasFile('photo')) {
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

        if ($request->hasFile('photo')) {
            if ($student->photo) {
                Storage::disk('public')->delete($student->photo);
            }
            $path = $request->file('photo')->store('student-photos', 'public');
            $data['photo'] = $path;
        }

        $student->update($data);

        return redirect()->route('students.index')->with('success', 'Data siswa diperbarui.');
    }

    public function destroy(Student $student)
    {
        if ($student->photo) {
            Storage::disk('public')->delete($student->photo);
        }
        $student->delete();
        return redirect()->route('students.index')->with('success', 'Data siswa dihapus.');
    }

    // --- FITUR BARU: PENCARIAN & DETAIL ---

    // 1. Tampilkan Halaman Pencarian
    public function search()
    {
        return view('students.search');
    }

    // 2. Proses Pencarian ID/Nama
    public function find(Request $request)
    {
        $request->validate([
            'keyword' => 'required'
        ]);

        $keyword = $request->keyword;

        // Cari berdasarkan ID atau Nama
        $student = Student::where('id', $keyword)
                    ->orWhere('name', 'LIKE', "%{$keyword}%")
                    ->first();

        if ($student) {
            return redirect()->route('students.show', $student->id);
        } else {
            return back()->with('error', 'Siswa tidak ditemukan. Periksa ID atau Nama kembali.');
        }
    }

    // 3. Tampilkan Detail Lengkap
    public function show($id)
    {
        // Ambil data siswa dengan relasi lengkap
        $student = Student::with(['parent', 'complex'])->findOrFail($id);
        
        // Ambil Jadwal Rutin Siswa Ini (Logika Baru)
        // Kita ambil dari relasi 'schedules' yang sudah kita buat di Model Student (via pivot)
        $schedules = $student->schedules()
                        ->with(['driver', 'shuttle', 'route'])
                        ->orderByRaw("FIELD(day_of_week, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday')")
                        ->get();

        return view('students.show', compact('student', 'schedules'));
    }
}