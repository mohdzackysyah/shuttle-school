@extends('layouts.admin')

@section('content')
<div class="container-fluid px-0">
    
    {{-- 1. HEADER: Responsif (Stack di Mobile, Row di Desktop) --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4">
        <div class="mb-3 mb-md-0">
            <h3 class="fw-bold text-dark mb-1">
                <i class="bi bi-person-badge text-primary me-2"></i>Data Driver
            </h3>
            <p class="text-muted mb-0">Kelola data supir, kontak, dan nomor lisensi berkendara.</p>
        </div>
        <a href="{{ route('drivers.create') }}" class="btn btn-primary rounded-pill shadow-sm px-4 fw-bold">
            <i class="bi bi-plus-lg me-2"></i> Tambah Driver
        </a>
    </div>

    {{-- 2. TABEL CARD --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-body p-0">
            
            {{-- Wrapper Responsive: Kunci agar tabel bisa di-scroll di HP --}}
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 text-nowrap">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4 py-3 text-secondary text-uppercase small fw-bold">Nama Driver</th>
                            <th class="py-3 text-secondary text-uppercase small fw-bold">Kontak</th>
                            <th class="py-3 text-secondary text-uppercase small fw-bold">Nomor SIM</th>
                            <th class="py-3 text-secondary text-uppercase small fw-bold text-end pe-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($drivers as $driver)
                        <tr>
                            <td class="ps-4 py-3">
                                <div class="d-flex align-items-center">
                                    {{-- Avatar Placeholder --}}
                                    <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                        <span class="fw-bold">{{ substr($driver->name, 0, 1) }}</span>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark">{{ $driver->name }}</div>
                                        <small class="text-muted">ID: #{{ $driver->id }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex flex-column">
                                    <span class="text-dark"><i class="bi bi-envelope me-2 text-muted"></i>{{ $driver->email }}</span>
                                    <small class="text-muted mt-1"><i class="bi bi-telephone me-2 text-muted"></i>{{ $driver->phone ?? '-' }}</small>
                                </div>
                            </td>
                            <td>
                                @if(isset($driver->driverProfile->license_number))
                                    <span class="badge bg-light text-dark border px-3 py-2 rounded-pill fw-normal">
                                        <i class="bi bi-card-heading me-1 text-secondary"></i> 
                                        {{ $driver->driverProfile->license_number }}
                                    </span>
                                @else
                                    <span class="text-muted fst-italic small">- Belum diisi -</span>
                                @endif
                            </td>
                            <td class="text-end pe-4">
                                <div class="btn-group">
                                    <a href="{{ route('drivers.edit', $driver->id) }}" class="btn btn-sm btn-light text-primary border shadow-sm rounded-start" title="Edit Data">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    
                                    <form action="{{ route('drivers.destroy', $driver->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus driver {{ $driver->name }}? Data yang dihapus tidak dapat dikembalikan.')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-light text-danger border shadow-sm rounded-end border-start-0" title="Hapus Driver">
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
                                    <i class="bi bi-person-x display-1 mb-3 text-secondary"></i>
                                    <h5 class="fw-bold text-secondary">Belum ada data Driver</h5>
                                    <p class="text-muted small">Silakan tambahkan driver baru untuk memulai.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination (Jika ada) --}}
            @if(method_exists($drivers, 'hasPages') && $drivers->hasPages())
                <div class="card-footer bg-white py-3 border-top">
                    {{ $drivers->links() }}
                </div>
            @endif

        </div>
    </div>
</div>

<style>
    /* Agar tampilan tabel lebih lega */
    .table > :not(caption) > * > * {
        padding: 1rem 0.5rem;
    }
    
    /* Efek hover baris tabel */
    .table-hover tbody tr:hover {
        background-color: #f8f9fa;
    }
</style>
@endsection