@extends('layouts.admin')

@section('content')
<div class="d-flex justify-content-between align-items-end mb-4">
    <div>
        <h3 class="fw-bold text-dark">🎒 Data Siswa</h3>
        <p class="text-muted mb-0">Siswa yang terdaftar dalam layanan antar jemput.</p>
    </div>
    <a href="{{ route('students.create') }}" class="btn btn-primary shadow-sm">
        <i class="bi bi-plus-lg"></i> Tambah Siswa
    </a>
</div>
    <div class="card-body">
        <table class="table table-bordered table-hover">
            <thead class="table-light">
                <tr>
                    <th>Nama Siswa</th>
                    <th>Orang Tua</th>
                    <th>Komplek Tempat Tinggal</th>
                    <th>Detail Alamat</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($students as $student)
                <tr>
                    <td class="fw-bold">{{ $student->name }}</td>
                    <td>
                        {{ $student->parent->name }} <br>
                        <small class="text-muted">{{ $student->parent->phone }}</small>
                    </td>
                    <td>
                        <span class="badge bg-info text-dark">{{ $student->complex->name }}</span>
                    </td>
                    <td>{{ $student->address_note ?? '-' }}</td>
                    <td>
                        <a href="{{ route('students.edit', $student->id) }}" class="btn btn-warning btn-sm">Edit</a>
                        <form action="{{ route('students.destroy', $student->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus data siswa ini?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger btn-sm">Hapus</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center">Belum ada data siswa.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection