@extends('layouts.admin')

@section('content')
<div class="d-flex justify-content-between align-items-end mb-4">
    <div>
        <h3 class="fw-bold text-dark">🗺️ Data Rute & Wilayah</h3>
        <p class="text-muted mb-0">Daftar rute penjemputan dan area jangkauan.</p>
    </div>
    <a href="{{ route('routes.create') }}" class="btn btn-primary shadow-sm">
        <i class="bi bi-plus-lg"></i> Tambah Rute
    </a>
</div>

<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4 py-3">Nama Rute</th>
                        <th>Wilayah / Komplek Dilewati</th>
                        <th>Armada</th>
                        <th class="text-end pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($routes as $route)
                    <tr>
                        <td class="ps-4 fw-bold">
                            <div class="d-flex align-items-center">
                                <div class="bg-light rounded-circle p-2 me-3 text-primary d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                    <i class="bi bi-map-fill fs-5"></i>
                                </div>
                                {{ $route->name }}
                            </div>
                        </td>
                        <td>
                            <div class="d-flex flex-wrap gap-1">
                                @forelse($route->complexes as $complex)
                                    <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill border border-primary border-opacity-10">
                                        {{ $complex->name }}
                                    </span>
                                @empty
                                    <span class="text-muted small fst-italic">Tidak ada wilayah</span>
                                @endforelse
                            </div>
                        </td>
                        <td>
                            {{-- Contoh jika ada relasi ke shuttles --}}
                            <span class="text-muted small">
                                <i class="bi bi-bus-front me-1"></i> {{ $route->shuttles_count ?? '0' }} Bus
                            </span>
                        </td>
                        <td class="text-end pe-4">
                            <a href="{{ route('routes.edit', $route->id) }}" class="btn btn-sm btn-outline-warning me-1">
                                Edit
                            </a>
                            <form action="{{ route('routes.destroy', $route->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus rute ini?')">
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
                            <div class="mb-2"><i class="bi bi-map fs-1"></i></div>
                            <h6>Belum ada rute yang dibuat.</h6>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection