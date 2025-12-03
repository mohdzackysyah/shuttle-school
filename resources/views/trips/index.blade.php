@extends('layouts.admin')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Jadwal Antar Jemput</h5>
        <a href="{{ route('trips.create') }}" class="btn btn-primary btn-sm">+ Buat Jadwal Baru</a>
    </div>
    <div class="card-body">
        <table class="table table-hover">
            <thead class="table-light">
                <tr>
                    <th>Tanggal</th>
                    <th>Tipe</th>
                    <th>Rute</th>
                    <th>Supir & Mobil</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($trips as $trip)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($trip->date)->format('d M Y') }}</td>
                    <td>
                        @if($trip->type == 'pickup')
                            <span class="badge bg-warning text-dark">🌞 Jemput Pagi</span>
                        @else
                            <span class="badge bg-info text-dark">🌥️ Antar Pulang</span>
                        @endif
                    </td>
                    <td>{{ $trip->route->name }}</td>
                    <td>
                        <strong>{{ $trip->driver->name }}</strong><br>
                        <small>{{ $trip->shuttle->car_model }} ({{ $trip->shuttle->plate_number }})</small>
                    </td>
                    <td>
                        @if($trip->status == 'scheduled')
                            <span class="badge bg-secondary">Terjadwal</span>
                        @elseif($trip->status == 'active')
                            <span class="badge bg-success">Sedang Jalan</span>
                        @else
                            <span class="badge bg-dark">Selesai</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('trips.show', $trip->id) }}" class="btn btn-info btn-sm text-white">Lihat Detail</a>
                        <form action="{{ route('trips.destroy', $trip->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus jadwal ini?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-danger btn-sm">Hapus</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center">Belum ada jadwal perjalanan.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection