@extends('layouts.admin')

@section('content')
{{-- 1. TITLE & JAM DIGITAL --}}
<div class="mb-4 d-flex justify-content-between align-items-center">
    <div>
        <h3 class="fw-bold text-dark">Dashboard Overview</h3>
        <p class="text-muted mb-0">Ringkasan operasional hari ini: <strong>{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</strong></p>
    </div>
    <div class="text-end">
        <h4 class="fw-bold text-primary mb-0" id="liveClock">{{ date('H:i') }}</h4>
        <small class="text-muted">WIB</small>
    </div>
</div>

{{-- 2. STATISTIC CARDS --}}
<div class="row g-4 mb-5">
    <div class="col-md-3">
        <div class="card h-100 p-3 border-0 shadow-sm">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted small fw-bold text-uppercase">Total Siswa</span>
                    <h2 class="fw-bold mt-2 mb-0 text-dark">{{ $totalStudents ?? 0 }}</h2>
                </div>
                <div class="bg-primary bg-opacity-10 text-primary rounded-3 p-3">
                    <i class="bi bi-backpack fs-2"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card h-100 p-3 border-0 shadow-sm">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted small fw-bold text-uppercase">Total Driver</span>
                    <h2 class="fw-bold mt-2 mb-0 text-dark">{{ $totalDrivers ?? 0 }}</h2>
                </div>
                <div class="bg-success bg-opacity-10 text-success rounded-3 p-3">
                    <i class="bi bi-person-badge fs-2"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card h-100 p-3 border-0 shadow-sm">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted small fw-bold text-uppercase">Rute Aktif</span>
                    <h2 class="fw-bold mt-2 mb-0 text-dark">{{ $totalRoutes ?? 0 }}</h2>
                </div>
                <div class="bg-warning bg-opacity-10 text-warning rounded-3 p-3">
                    <i class="bi bi-map fs-2"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card h-100 p-3 border-0 shadow-sm">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted small fw-bold text-uppercase">Armada</span>
                    <h2 class="fw-bold mt-2 mb-0 text-dark">{{ $activeShuttles ?? 0 }}</h2>
                </div>
                <div class="bg-info bg-opacity-10 text-info rounded-3 p-3">
                    <i class="bi bi-bus-front fs-2"></i>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- 3. TABEL LIVE MONITORING PERJALANAN --}}
<div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
    <div class="card-header bg-white py-3 px-4 border-bottom d-flex justify-content-between align-items-center">
        <h5 class="fw-bold text-dark mb-0">
            <i class="bi bi-broadcast text-danger me-2 pulse-icon"></i> Live Monitoring Perjalanan
        </h5>
        <a href="{{ route('trips.index') }}" class="btn btn-sm btn-light text-muted">Lihat Semua Riwayat <i class="bi bi-arrow-right"></i></a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Status</th>
                        <th>Tipe</th>
                        <th style="width: 30%;">Rute & Area</th> {{-- Kolom Rute Diperlebar --}}
                        <th>Driver & Mobil</th>
                        <th class="text-center">Penumpang</th>
                        <th class="text-end pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($todaysTrips as $trip)
                    <tr>
                        <td class="ps-4">
                            @if($trip->status == 'active')
                                <span class="badge bg-success bg-opacity-10 text-success border border-success px-3 py-2 rounded-pill blink-soft">
                                    <i class="bi bi-record-circle me-1"></i> Sedang Jalan
                                </span>
                            @elseif($trip->status == 'finished')
                                <span class="badge bg-secondary bg-opacity-10 text-secondary border px-3 py-2 rounded-pill">
                                    <i class="bi bi-check-circle me-1"></i> Selesai
                                </span>
                            @else
                                <span class="badge bg-warning bg-opacity-10 text-warning border border-warning px-3 py-2 rounded-pill">
                                    <i class="bi bi-clock me-1"></i> Menunggu
                                </span>
                            @endif
                        </td>
                        <td>
                            @if($trip->type == 'pickup')
                                <span class="text-warning fw-bold small"><i class="bi bi-sunrise-fill"></i> Pagi</span>
                            @else
                                <span class="text-info fw-bold small"><i class="bi bi-sunset-fill"></i> Sore</span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex flex-column">
                                {{-- Nama Rute --}}
                                <span class="fw-bold text-dark">{{ $trip->route->name ?? '-' }}</span>
                                
                                {{-- Daftar Komplek --}}
                                <small class="text-muted text-truncate" style="max-width: 250px;" 
                                       title="{{ $trip->route && $trip->route->complexes ? $trip->route->complexes->pluck('name')->join(', ') : '' }}">
                                    <i class="bi bi-geo-alt me-1 text-secondary"></i>
                                    @if($trip->route && $trip->route->complexes->count() > 0)
                                        {{ $trip->route->complexes->pluck('name')->join(', ') }}
                                    @else
                                        <span class="text-secondary fst-italic">-</span>
                                    @endif
                                </small>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="bg-light rounded-circle p-2 me-2 text-primary">
                                    <i class="bi bi-person-fill"></i>
                                </div>
                                <div class="lh-1">
                                    <div class="fw-bold small">{{ $trip->driver->name ?? '?' }}</div>
                                    <small class="text-muted" style="font-size: 0.75rem;">{{ $trip->shuttle->plate_number ?? '-' }}</small>
                                </div>
                            </div>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-light text-dark border">
                                <i class="bi bi-people-fill me-1"></i> {{ $trip->passengers_count }} Siswa
                            </span>
                        </td>
                        <td class="text-end pe-4">
                            <a href="{{ route('trips.show', $trip->id) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                Detail
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="bi bi-calendar-x display-4 opacity-25"></i>
                            <p class="mt-2 mb-0">Tidak ada jadwal perjalanan hari ini.</p>
                            <small>Silakan buat jadwal di menu Master Jadwal atau tunggu driver memulai perjalanan.</small>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- 4. SHORTCUTS --}}
<div class="row">
    <div class="col-md-12">
        <div class="card p-4 border-0 shadow-sm bg-primary text-white rounded-4" style="background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="fw-bold mb-1">Kelola Jadwal Rutin</h4>
                    <p class="mb-0 opacity-75">Atur jadwal mingguan untuk Driver dan Penumpang.</p>
                </div>
                <div>
                    <a href="{{ route('schedules.index') }}" class="btn btn-light text-primary fw-bold shadow px-4 py-2 rounded-pill">
                        <i class="bi bi-calendar-week me-2"></i> Buka Master Jadwal
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- CSS Tambahan --}}
<style>
    @keyframes pulse-red {
        0% { color: #dc3545; text-shadow: 0 0 0 rgba(220, 53, 69, 0.4); }
        50% { color: #ff6b6b; text-shadow: 0 0 10px rgba(220, 53, 69, 0.8); }
        100% { color: #dc3545; text-shadow: 0 0 0 rgba(220, 53, 69, 0.4); }
    }
    
    @keyframes blink-soft {
        0% { opacity: 1; }
        50% { opacity: 0.6; }
        100% { opacity: 1; }
    }

    .pulse-icon { animation: pulse-red 2s infinite; }
    .blink-soft { animation: blink-soft 1.5s infinite; }
</style>

{{-- Script Jam Digital --}}
<script>
    function updateClock() {
        const now = new Date();
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        document.getElementById('liveClock').textContent = `${hours}:${minutes}`;
    }
    setInterval(updateClock, 1000);
</script>
@endsection