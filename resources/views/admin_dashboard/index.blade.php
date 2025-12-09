@extends('layouts.admin')

@section('content')
<div class="container-fluid px-0">

    {{-- 1. HEADER & JAM DIGITAL --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4">
        <div class="mb-3 mb-md-0">
            <h3 class="fw-bold text-dark mb-1">Dashboard Overview</h3>
            <p class="text-muted mb-0">
                Ringkasan operasional hari ini: 
                <strong class="text-dark">{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</strong>
            </p>
        </div>
        <div class="text-md-end text-center bg-white shadow-sm rounded-pill px-4 py-2 border">
            <h4 class="fw-bold text-primary mb-0 d-inline-block" id="liveClock">{{ date('H:i') }}</h4>
            <small class="text-muted ms-1 text-uppercase fw-bold" style="font-size: 0.7rem;">WIB</small>
        </div>
    </div>

    {{-- 2. STATISTIC CARDS --}}
    <div class="row g-4 mb-5">
        {{-- Card 1: Total Siswa --}}
        <div class="col-md-3">
            <div class="card h-100 p-3 border-0 shadow-sm rounded-4 hover-scale transition">
                <div class="d-flex justify-content-between align-items-center h-100">
                    <div>
                        <span class="text-secondary small fw-bold text-uppercase ls-1">Total Siswa</span>
                        <h2 class="fw-bold mt-2 mb-0 text-dark">{{ $totalStudents ?? 0 }}</h2>
                    </div>
                    <div class="bg-primary bg-opacity-10 text-primary rounded-circle p-3 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                        <i class="bi bi-people-fill fs-3"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card 2: Total Driver --}}
        <div class="col-md-3">
            <div class="card h-100 p-3 border-0 shadow-sm rounded-4 hover-scale transition">
                <div class="d-flex justify-content-between align-items-center h-100">
                    <div>
                        <span class="text-secondary small fw-bold text-uppercase ls-1">Total Driver</span>
                        <h2 class="fw-bold mt-2 mb-0 text-dark">{{ $totalDrivers ?? 0 }}</h2>
                    </div>
                    <div class="bg-success bg-opacity-10 text-success rounded-circle p-3 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                        <i class="bi bi-person-badge fs-3"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card 3: Rute Aktif --}}
        <div class="col-md-3">
            <div class="card h-100 p-3 border-0 shadow-sm rounded-4 hover-scale transition">
                <div class="d-flex justify-content-between align-items-center h-100">
                    <div>
                        <span class="text-secondary small fw-bold text-uppercase ls-1">Rute Aktif</span>
                        <h2 class="fw-bold mt-2 mb-0 text-dark">{{ $totalRoutes ?? 0 }}</h2>
                    </div>
                    <div class="bg-warning bg-opacity-10 text-warning rounded-circle p-3 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                        <i class="bi bi-map-fill fs-3"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card 4: Armada --}}
        <div class="col-md-3">
            <div class="card h-100 p-3 border-0 shadow-sm rounded-4 hover-scale transition">
                <div class="d-flex justify-content-between align-items-center h-100">
                    <div>
                        <span class="text-secondary small fw-bold text-uppercase ls-1">Armada</span>
                        <h2 class="fw-bold mt-2 mb-0 text-dark">{{ $activeShuttles ?? 0 }}</h2>
                    </div>
                    <div class="bg-info bg-opacity-10 text-info rounded-circle p-3 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                        <i class="bi bi-bus-front-fill fs-3"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- 3. TABEL LIVE MONITORING PERJALANAN --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
        <div class="card-header bg-white py-3 px-4 border-bottom d-flex justify-content-between align-items-center">
            <h5 class="fw-bold text-dark mb-0 d-flex align-items-center">
                <span class="position-relative d-inline-block me-3">
                    <i class="bi bi-broadcast text-danger fs-5"></i>
                    <span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle blink-soft"></span>
                </span>
                Live Monitoring
            </h5>
            <a href="{{ route('trips.index') }}" class="btn btn-sm btn-light border text-muted shadow-sm rounded-pill px-3">
                Lihat Semua <i class="bi bi-arrow-right ms-1"></i>
            </a>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 text-nowrap">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4 py-3 text-secondary text-uppercase small fw-bold">Status</th>
                            <th class="py-3 text-secondary text-uppercase small fw-bold">Tipe</th>
                            <th class="py-3 text-secondary text-uppercase small fw-bold" style="width: 30%;">Rute & Area</th>
                            <th class="py-3 text-secondary text-uppercase small fw-bold">Driver & Mobil</th>
                            <th class="py-3 text-secondary text-uppercase small fw-bold text-center">Penumpang</th>
                            <th class="py-3 text-secondary text-uppercase small fw-bold text-end pe-4">Aksi</th>
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
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary px-3 py-2 rounded-pill">
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
                                    <span class="badge bg-warning bg-opacity-10 text-warning rounded-pill px-2 fw-normal">
                                        <i class="bi bi-sunrise-fill me-1"></i> Pagi
                                    </span>
                                @else
                                    <span class="badge bg-info bg-opacity-10 text-info rounded-pill px-2 fw-normal">
                                        <i class="bi bi-sunset-fill me-1"></i> Sore
                                    </span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex flex-column">
                                    <span class="fw-bold text-dark">{{ $trip->route->name ?? '-' }}</span>
                                    <small class="text-muted text-truncate" style="max-width: 250px;" title="{{ $trip->route && $trip->route->complexes ? $trip->route->complexes->pluck('name')->join(', ') : '' }}">
                                        <i class="bi bi-geo-alt me-1 text-secondary"></i>
                                        @if($trip->route && $trip->route->complexes->count() > 0)
                                            {{ $trip->route->complexes->pluck('name')->join(', ') }}
                                        @else
                                            <span class="fst-italic">-</span>
                                        @endif
                                    </small>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="bg-light rounded-circle p-1 me-2 d-flex align-items-center justify-content-center text-primary" style="width: 32px; height: 32px;">
                                        <i class="bi bi-person-fill"></i>
                                    </div>
                                    <div class="lh-1">
                                        <div class="fw-bold small text-dark">{{ $trip->driver->name ?? '?' }}</div>
                                        <small class="text-muted" style="font-size: 0.75rem;">{{ $trip->shuttle->plate_number ?? '-' }}</small>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-light text-dark border rounded-pill">
                                    <i class="bi bi-people-fill me-1 text-secondary"></i> {{ $trip->passengers_count }}
                                </span>
                            </td>
                            <td class="text-end pe-4">
                                <a href="{{ route('trips.show', $trip->id) }}" class="btn btn-sm btn-light text-primary border shadow-sm rounded-pill px-3 fw-bold">
                                    Detail <i class="bi bi-arrow-right ms-1"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <div class="d-flex flex-column align-items-center opacity-50">
                                    <i class="bi bi-calendar-x display-4 mb-2"></i>
                                    <p class="mb-0 fw-bold">Tidak ada jadwal perjalanan hari ini.</p>
                                    <small>Jadwal akan muncul otomatis sesuai pengaturan rutin.</small>
                                </div>
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
            <div class="card p-4 border-0 shadow-sm bg-primary text-white rounded-4 position-relative overflow-hidden" 
                 style="background: linear-gradient(135deg, #0d6efd 0%, #0043a8 100%);">
                
                {{-- Background Decoration --}}
                <div class="position-absolute end-0 top-0 opacity-10 me-n5 mt-n5">
                    <i class="bi bi-calendar-week-fill" style="font-size: 15rem;"></i>
                </div>

                <div class="d-flex justify-content-between align-items-center position-relative z-1">
                    <div>
                        <h4 class="fw-bold mb-1">Kelola Jadwal Rutin</h4>
                        <p class="mb-0 opacity-75">Atur jadwal mingguan driver dan penumpang dengan mudah.</p>
                    </div>
                    <div>
                        <a href="{{ route('schedules.index') }}" class="btn btn-light text-primary fw-bold shadow px-4 py-2 rounded-pill">
                            <i class="bi bi-calendar-plus me-2"></i> Buka Master Jadwal
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- CSS Tambahan --}}
<style>
    /* Letter Spacing untuk header kecil */
    .ls-1 { letter-spacing: 1px; }

    /* Efek Hover pada Card Statistik */
    .hover-scale { transition: transform 0.2s ease-in-out; }
    .hover-scale:hover { transform: translateY(-5px); }

    /* Animasi Kedip Halus */
    @keyframes blink-soft {
        0% { opacity: 1; }
        50% { opacity: 0.5; }
        100% { opacity: 1; }
    }
    .blink-soft { animation: blink-soft 2s infinite; }
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