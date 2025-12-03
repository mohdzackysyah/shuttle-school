@extends('layouts.admin')

@section('content')
<div class="d-flex justify-content-between align-items-end mb-4">
    <div>
        <h3 class="fw-bold text-dark">🚐 Data Armada Mobil</h3>
        <p class="text-muted mb-0">Manajemen kendaraan operasional sekolah.</p>
    </div>
    <a href="{{ route('shuttles.create') }}" class="btn btn-primary shadow-sm">
        <i class="bi bi-plus-lg"></i> Tambah Armada
    </a>
</div>

<div class="card border-0 shadow-sm rounded-4 overflow-hidden">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th width="50" class="text-center py-3">No</th>
                        <th>Plat Nomor</th>
                        <th>Model</th>
                        <th>Kapasitas</th>
                        <th>Status Penggunaan</th>
                        <th width="150" class="text-end pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($shuttles as $index => $shuttle)
                    <tr>
                        <td class="text-center text-muted">{{ $index + 1 }}</td>
                        <td class="fw-bold">{{ $shuttle->plate_number }}</td>
                        <td>{{ $shuttle->car_model }}</td>
                        <td>{{ $shuttle->capacity }} Kursi</td>
                        <td>
                            @if($shuttle->status == 'maintenance')
                                <span class="badge bg-danger">
                                    <i class="bi bi-tools me-1"></i> Perbaikan
                                </span>
                            @else
                                @if($shuttle->schedules_count > 0)
                                    <span class="badge bg-primary bg-opacity-10 text-primary border border-primary">
                                        <i class="bi bi-calendar-check me-1"></i> Aktif di {{ $shuttle->schedules_count }} Jadwal
                                    </span>
                                @else
                                    <span class="badge bg-success bg-opacity-10 text-success border border-success">
                                        <i class="bi bi-check-circle me-1"></i> Siap Pakai (Free)
                                    </span>
                                @endif
                            @endif
                        </td>
                        <td class="text-end pe-4">
                            <a href="{{ route('shuttles.edit', $shuttle->id) }}" class="btn btn-sm btn-outline-warning">Edit</a>
                            <form action="{{ route('shuttles.destroy', $shuttle->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger">Hapus</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center py-5 text-muted">Belum ada armada.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection