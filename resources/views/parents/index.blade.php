@extends('layouts.admin')  @section('content')
<div class="d-flex justify-content-between align-items-end mb-4">
    <div>
        <h3 class="fw-bold text-dark">👨‍👩‍👧‍👦 Data Wali Murid</h3>
        <p class="text-muted mb-0">Manajemen data orang tua dan kontak siswa.</p>
    </div>
    <a href="{{ route('parents.create') }}" class="btn btn-primary shadow-sm">
        <i class="bi bi-plus-lg"></i> Tambah Wali Murid
    </a>
</div>

<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4 py-3">Nama Lengkap</th>
                        <th>Email (Login)</th>
                        <th>No. WhatsApp</th>
                        <th class="text-end pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($parents as $parent)
                    <tr>
                        <td class="ps-4 fw-bold">
                            <div class="d-flex align-items-center">
                                <div class="bg-light rounded-circle p-2 me-3 text-primary">
                                    <i class="bi bi-person-fill"></i>
                                </div>
                                {{ $parent->name }}
                            </div>
                        </td>
                        <td>
                            <span class="text-muted"><i class="bi bi-envelope me-1"></i> {{ $parent->email }}</span>
                        </td>
                        <td>
                            <span class="badge bg-success bg-opacity-10 text-success px-3 py-2 rounded-pill">
                                <i class="bi bi-whatsapp me-1"></i> {{ $parent->phone }}
                            </span>
                        </td>
                        <td class="text-end pe-4">
                            <a href="{{ route('parents.edit', $parent->id) }}" class="btn btn-sm btn-outline-warning me-1">
                                Edit
                            </a>
                            <form action="{{ route('parents.destroy', $parent->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus data ini?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger">
                                    Hapus
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center py-5 text-muted">
                            <h6>Belum ada data wali murid.</h6>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection