<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\User;
use App\Models\Complex;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class StudentController extends Controller
{
    /**
     * Menampilkan daftar siswa dengan fitur pencarian (Nama & ID).
     */
    public function index(Request $request)
    {
        // 1. Mulai Query dengan Relasi
        $query = Student::with(['parent', 'complex']);

        // 2. Logika Pencarian (Nama atau ID)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('id', 'like', "%{$search}%");
            });
        }

        // 3. Eksekusi dengan Pagination
        // 'latest()' mengurutkan dari yang terbaru dibuat
        $students = $query->latest()->paginate(10)->withQueryString();

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

    // --- FITUR PENCARIAN KHUSUS & DETAIL (Biarkan Tetap Ada) ---

    public function search()
    {
        return view('students.search');
    }

    public function find(Request $request)
    {
        $request->validate([
            'keyword' => 'required'
        ]);

        $keyword = $request->keyword;

        $student = Student::where('id', $keyword)
                    ->orWhere('name', 'LIKE', "%{$keyword}%")
                    ->first();

        if ($student) {
            return redirect()->route('students.show', $student->id);
        } else {
            return back()->with('error', 'Siswa tidak ditemukan. Periksa ID atau Nama kembali.');
        }
    }

    public function show($id)
    {
        $student = Student::with(['parent', 'complex'])->findOrFail($id);
        
        $schedules = $student->schedules()
                        ->with(['driver', 'shuttle', 'route'])
                        ->orderByRaw("FIELD(day_of_week, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday')")
                        ->get();

        return view('students.show', compact('student', 'schedules'));
    }
}