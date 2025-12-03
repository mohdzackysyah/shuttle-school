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
    <div class="card-body">
        <table class="table table-bordered table-hover">
            <thead class="table-light">
                <tr>
                    <th>Nama Rute</th>
                    <th>Wilayah / Komplek yang Dilewati</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($routes as $route)
                <tr>
                    <td width="20%"><strong>{{ $route->name }}</strong></td>
                    <td>
                        @foreach($route->complexes as $complex)
                            <span class="badge bg-info text-dark me-1">{{ $complex->name }}</span>
                        @endforeach
                    </td>
                    <td width="20%">
                        <a href="{{ route('routes.edit', $route->id) }}" class="btn btn-warning btn-sm">Edit</a>
                        <form action="{{ route('routes.destroy', $route->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus rute ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" class="text-center">Belum ada rute yang dibuat.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection