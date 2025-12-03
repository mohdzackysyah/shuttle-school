@extends('layouts.admin')

@section('content')
<div class="d-flex justify-content-between align-items-end mb-4">
    <div>
        <h3 class="fw-bold text-dark">🏙️ Data Perumahan / Komplek</h3>
        <p class="text-muted mb-0">Lokasi tempat tinggal siswa yang terdaftar.</p>
    </div>
    <a href="{{ route('complexes.create') }}" class="btn btn-primary shadow-sm">
        <i class="bi bi-plus-lg"></i> Tambah Komplek
    </a>
</div>

<div class="card border-0 shadow-sm rounded-4 overflow-hidden">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th width="50" class="text-center py-3">No</th>
                        <th>Nama Komplek</th>
                        <th>Rute Wilayah</th> <th width="200" class="text-end pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($complexes as $index => $complex)
                    <tr>
                        <td class="text-center text-muted">{{ $index + 1 }}</td>
                        <td class="fw-bold text-dark">
                            <i class="bi bi-building text-secondary me-2"></i> {{ $complex->name }}
                        </td>
                        <td>
                            @if($complex->route)
                                <span class="badge bg-info text-dark bg-opacity-10 border border-info px-3 py-2 rounded-pill">
                                    <i class="bi bi-map-fill me-1"></i> {{ $complex->route->name }}
                                </span>
                            @else
                                <span class="badge bg-secondary bg-opacity-10 text-secondary px-3 py-2 rounded-pill">
                                    Belum ada rute
                                </span>
                            @endif
                        </td>
                        <td class="text-end pe-4">
                            <a href="{{ route('complexes.edit', $complex->id) }}" class="btn btn-sm btn-outline-warning me-1">
                                <i class="bi bi-pencil-square"></i> Edit
                            </a>
                            
                            <form action="{{ route('complexes.destroy', $complex->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus komplek ini? Data siswa di dalamnya mungkin akan terdampak.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                    <i class="bi bi-trash"></i> Hapus
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center py-5 text-muted">
                            <div class="mb-3 opacity-25">
                                <i class="bi bi-buildings display-4"></i>
                            </div>
                            <h6 class="fw-bold">Belum ada data komplek.</h6>
                            <p class="small">Silakan tambahkan data baru.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection