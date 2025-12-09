@extends('layouts.admin')

@section('content')
<div class="container-fluid px-0">

    {{-- 1. HEADER HALAMAN --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4">
        <div class="mb-3 mb-md-0">
            <h3 class="fw-bold text-dark mb-1">
                <i class="bi bi-people-fill text-primary me-2"></i>Data Wali Murid
            </h3>
            <p class="text-muted mb-0">Manajemen data orang tua dan kontak darurat siswa.</p>
        </div>
        <a href="{{ route('parents.create') }}" class="btn btn-primary rounded-pill shadow-sm px-4 fw-bold">
            <i class="bi bi-plus-lg me-2"></i> Tambah Wali Murid
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
                            <th class="ps-4 py-3 text-secondary text-uppercase small fw-bold">Nama Wali</th>
                            <th class="py-3 text-secondary text-uppercase small fw-bold">Kontak & Akun</th>
                            <th class="py-3 text-secondary text-uppercase small fw-bold">No. WhatsApp</th>
                            <th class="py-3 text-secondary text-uppercase small fw-bold text-end pe-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($parents as $parent)
                        <tr>
                            <td class="ps-4 py-3">
                                <div class="d-flex align-items-center">
                                    {{-- Avatar Inisial --}}
                                    <div class="bg-info bg-opacity-10 text-info rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                        <span class="fw-bold">{{ substr($parent->name, 0, 1) }}</span>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark">{{ $parent->name }}</div>
                                        <small class="text-muted">ID: #{{ $parent->id }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center text-muted">
                                    <i class="bi bi-envelope me-2"></i> {{ $parent->email }}
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-success bg-opacity-10 text-success border border-success px-3 py-2 rounded-pill fw-normal">
                                    <i class="bi bi-whatsapp me-1"></i> {{ $parent->phone }}
                                </span>
                            </td>
                            <td class="text-end pe-4">
                                <div class="btn-group">
                                    <a href="{{ route('parents.edit', $parent->id) }}" class="btn btn-sm btn-light text-primary border shadow-sm rounded-start" title="Edit Data">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    
                                    <form action="{{ route('parents.destroy', $parent->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data Wali Murid {{ $parent->name }}?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-light text-danger border shadow-sm rounded-end border-start-0" title="Hapus Data">
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
                                        <i class="bi bi-people display-4 text-secondary"></i>
                                    </div>
                                    <h5 class="fw-bold text-secondary">Data Kosong</h5>
                                    <p class="text-muted small mb-0">Belum ada data wali murid yang terdaftar.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination Check --}}
            @if(method_exists($parents, 'hasPages') && $parents->hasPages())
                <div class="card-footer bg-white py-3 border-top">
                    {{ $parents->links() }}
                </div>
            @endif

        </div>
    </div>
</div>

<style>
    /* Spacing tabel agar tidak terlalu padat */
    .table > :not(caption) > * > * {
        padding: 1rem 0.5rem;
    }
    .table-hover tbody tr:hover {
        background-color: #f8f9fa;
    }
</style>
@endsection