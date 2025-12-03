@extends('layouts.admin')

@section('content')
<div class="d-flex justify-content-between align-items-end mb-4">
    <div>
        <h3 class="fw-bold text-dark">👮‍♂️ Data Driver</h3>
        <p class="text-muted mb-0">Daftar supir yang bertugas antar jemput.</p>
    </div>
    <a href="{{ route('drivers.create') }}" class="btn btn-primary shadow-sm">
        <i class="bi bi-plus-lg"></i> Tambah Driver
    </a>
</div>

<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-4">Nama</th>
                    <th>Email</th>
                    <th>No HP</th>
                    <th>Nomor SIM</th>
                    <th class="text-end pe-4">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($drivers as $driver)
                <tr>
                    <td class="ps-4 fw-bold">{{ $driver->name }}</td>
                    <td>{{ $driver->email }}</td>
                    <td>{{ $driver->phone }}</td>
                    <td>{{ $driver->driverProfile->license_number ?? '-' }}</td>
                    <td class="text-end pe-4">
                        <a href="{{ route('drivers.edit', $driver->id) }}" class="btn btn-sm btn-outline-warning">Edit</a>
                        <form action="{{ route('drivers.destroy', $driver->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus driver ini?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">Hapus</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center py-5 text-muted">Belum ada data driver.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection