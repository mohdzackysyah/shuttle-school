@extends('layouts.admin')

@section('content')
<div class="container-fluid px-0">
    
    {{-- 1. HEADER HALAMAN --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4">
        <div class="mb-3 mb-md-0">
            <h3 class="fw-bold text-dark mb-1">
                <i class="bi bi-mortarboard-fill text-primary me-2"></i>Data Siswa
            </h3>
            <p class="text-muted mb-0">Kelola data siswa, lokasi penjemputan, dan relasi orang tua.</p>
        </div>
        <a href="{{ route('students.create') }}" class="btn btn-primary rounded-pill shadow-sm px-4 fw-bold">
            <i class="bi bi-plus-lg me-2"></i> Tambah Siswa
        </a>
    </div>

    {{-- 2. TABEL CARD --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-body p-0">
            
            {{-- Wrapper Responsive --}}
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 text-nowrap">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4 py-3 text-secondary text-uppercase small fw-bold">Profil Siswa</th>
                            <th class="py-3 text-secondary text-uppercase small fw-bold">Lokasi Penjemputan</th>
                            <th class="py-3 text-secondary text-uppercase small fw-bold">Wali Murid</th>
                            <th class="py-3 text-secondary text-uppercase small fw-bold text-end pe-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($students as $student)
                        <tr>
                            <td class="ps-4 py-3">
                                <div class="d-flex align-items-center">
                                    {{-- Avatar Logic: Foto atau Inisial --}}
                                    <div class="me-3 flex-shrink-0">
                                        @if($student->photo)
                                            <img src="{{ asset('storage/' . $student->photo) }}" alt="{{ $student->name }}" 
                                                 class="rounded-circle border shadow-sm" style="width: 40px; height: 40px; object-fit: cover;">
                                        @else
                                            <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center fw-bold border border-primary border-opacity-10" 
                                                 style="width: 40px; height: 40px;">
                                                {{ substr($student->name, 0, 1) }}
                                            </div>
                                        @endif
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark">{{ $student->name }}</div>
                                        <small class="text-muted">ID: #{{ $student->id }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex flex-column">
                                    <div>
                                        <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25 mb-1 fw-normal">
                                            <i class="bi bi-building me-1"></i> {{ $student->complex->name ?? '-' }}
                                        </span>
                                    </div>
                                    <small class="text-muted text-truncate" style="max-width: 250px;" title="{{ $student->address_note }}">
                                        <i class="bi bi-geo-alt me-1 text-secondary"></i> {{ $student->address_note ?? '-' }}
                                    </small>
                                </div>
                            </td>
                            <td>
                                @if($student->parent)
                                    <div class="d-flex flex-column">
                                        <span class="fw-bold text-dark">{{ $student->parent->name }}</span>
                                        <small class="text-success">
                                            <i class="bi bi-whatsapp me-1"></i> {{ $student->parent->phone }}
                                        </small>
                                    </div>
                                @else
                                    <span class="text-muted fst-italic small">- Tidak ada data -</span>
                                @endif
                            </td>
                            <td class="text-end pe-4">
                                <div class="btn-group">
                                    <a href="{{ route('students.edit', $student->id) }}" class="btn btn-sm btn-light text-primary border shadow-sm rounded-start" title="Edit Data">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    
                                    <form action="{{ route('students.destroy', $student->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data siswa {{ $student->name }}?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-light text-danger border shadow-sm rounded-end border-start-0" title="Hapus Siswa">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-5">
                                <div class="d-flex flex-column align-items-center justify-content-center opacity-50">
                                    <div class="bg-light rounded-circle p-3 mb-3">
                                        <i class="bi bi-mortarboard display-4 text-secondary"></i>
                                    </div>
                                    <h5 class="fw-bold text-secondary">Data Siswa Kosong</h5>
                                    <p class="text-muted small mb-0">Belum ada siswa yang terdaftar dalam sistem.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if(method_exists($students, 'hasPages') && $students->hasPages())
                <div class="card-footer bg-white py-3 border-top">
                    {{ $students->links() }}
                </div>
            @endif

        </div>
    </div>
</div>

<style>
    /* Agar baris tabel tidak terlalu rapat */
    .table > :not(caption) > * > * {
        padding: 1rem 0.5rem;
    }
    .table-hover tbody tr:hover {
        background-color: #f8f9fa;
    }
</style>
@endsection