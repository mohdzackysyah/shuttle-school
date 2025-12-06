@extends('layouts.parent')

@section('content')
<div class="container py-4">
    
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold text-dark mb-1">📜 Riwayat Perjalanan</h4>
            <p class="text-muted small mb-0">Pantau semua aktivitas antar-jemput anak Anda.</p>
        </div>
    </div>

    {{-- FILTER SECTION --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body bg-light bg-opacity-50 p-4">
            <form action="{{ route('parents.history') }}" method="GET">
                <div class="row g-3 align-items-end">
                    
                    {{-- Filter Tanggal Spesifik --}}
                    <div class="col-md-4">
                        <label class="form-label small fw-bold text-muted text-uppercase mb-1">Cari Tanggal</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0"><i class="bi bi-calendar3"></i></span>
                            <input type="date" name="date" class="form-control border-start-0 ps-0" value="{{ request('date') }}">
                        </div>
                    </div>

                    <div class="col-md-1 text-center fw-bold text-muted py-2 d-none d-md-block align-self-center">
                        <span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill px-2">ATAU</span>
                    </div>

                    {{-- Filter Bulan & Tahun --}}
                    <div class="col-6 col-md-3">
                        <label class="form-label small fw-bold text-muted text-uppercase mb-1">Bulan</label>
                        <select name="month" class="form-select">
                            <option value="">-- Semua Bulan --</option>
                            @foreach(range(1, 12) as $m)
                                <option value="{{ $m }}" {{ request('month', date('n')) == $m ? 'selected' : '' }}>
                                    {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-6 col-md-2">
                        <label class="form-label small fw-bold text-muted text-uppercase mb-1">Tahun</label>
                        <select name="year" class="form-select">
                            @foreach(range(date('Y'), 2023) as $y)
                                <option value="{{ $y }}" {{ request('year', date('Y')) == $y ? 'selected' : '' }}>
                                    {{ $y }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- TOMBOL AKSI --}}
                    <div class="col-md-2">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary fw-bold text-white shadow-sm flex-grow-1">
                                <i class="bi bi-funnel-fill me-1"></i> Filter
                            </button>
                            
                            @if(request()->has('date') || request()->has('month'))
                                <a href="{{ route('parents.history') }}" class="btn btn-light border shadow-sm" title="Reset Filter">
                                    <i class="bi bi-arrow-counterclockwise text-danger"></i>
                                </a>
                            @endif
                        </div>
                    </div>

                </div>
            </form>
        </div>
    </div>

    {{-- LIST RIWAYAT --}}
    <div class="row g-4">
        @php
            // GROUPING DATA: Satukan Pagi & Sore berdasarkan (Tanggal + SiswaID)
            $groupedHistories = $histories->getCollection()->groupBy(function($item) {
                return $item->trip->date . '_' . $item->student_id;
            });
        @endphp

        @forelse($groupedHistories as $key => $group)
            @php
                $firstItem = $group->first();
                $student = $firstItem->student;
                $date = \Carbon\Carbon::parse($firstItem->trip->date);
                
                // Cari Data Pagi & Sore
                $pickup = $group->first(fn($i) => $i->trip->type == 'pickup');
                $dropoff = $group->first(fn($i) => $i->trip->type == 'dropoff');

                // Tentukan warna border kiri
                $borderColor = '#adb5bd'; 
                if ($pickup && $dropoff && $pickup->status == 'dropped_off' && $dropoff->status == 'dropped_off') {
                    $borderColor = '#198754'; // Hijau
                } elseif (($pickup && $pickup->status == 'absent') || ($dropoff && $dropoff->status == 'absent')) {
                    $borderColor = '#dc3545'; // Merah
                } elseif (($pickup && $pickup->status == 'picked_up') || ($dropoff && $dropoff->status == 'picked_up')) {
                    $borderColor = '#0d6efd'; // Biru
                }
            @endphp

            <div class="col-12">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100 position-relative">
                    
                    {{-- Garis Indikator Warna --}}
                    <div class="position-absolute top-0 start-0 bottom-0" style="width: 6px; background-color: {{ $borderColor }};"></div>

                    {{-- Header Card --}}
                    <div class="card-header bg-white border-bottom py-3 ps-4 pe-4">
                        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
                            
                            {{-- Info Anak & Tanggal --}}
                            <div class="d-flex align-items-center">
                                <div class="bg-light rounded-circle p-2 me-3 text-primary d-none d-sm-block">
                                    <i class="bi bi-calendar-check fs-4"></i>
                                </div>
                                <div>
                                    <h5 class="fw-bold text-dark mb-0">{{ $student->name }}</h5>
                                    <div class="text-muted small">
                                        <i class="bi bi-clock me-1"></i> {{ $date->translatedFormat('l, d F Y') }}
                                    </div>
                                </div>
                            </div>

                            {{-- Status Badge Global --}}
                            <div>
                                @if($pickup && $dropoff && $pickup->status == 'dropped_off' && $dropoff->status == 'dropped_off')
                                    <span class="badge bg-success-subtle text-success border border-success px-3 py-2 rounded-pill">
                                        <i class="bi bi-check-circle-fill me-1"></i> Selesai Lengkap
                                    </span>
                                @elseif(($pickup && $pickup->status == 'absent') || ($dropoff && $dropoff->status == 'absent'))
                                    <span class="badge bg-danger-subtle text-danger border border-danger px-3 py-2 rounded-pill">
                                        <i class="bi bi-x-circle-fill me-1"></i> Izin / Tidak Ikut
                                    </span>
                                @else
                                    <span class="badge bg-info-subtle text-info border border-info px-3 py-2 rounded-pill">
                                        <i class="bi bi-hourglass-split me-1"></i> Proses / Sebagian
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Body Card (Split Pagi & Sore) --}}
                    <div class="card-body p-0">
                        <div class="row g-0">
                            
                            {{-- BAGIAN KIRI: PAGI --}}
                            <div class="col-md-6 border-end-md p-4 bg-light-gradient-start">
                                <div class="d-flex gap-3">
                                    <div class="icon-box bg-warning bg-opacity-10 text-warning rounded-3 shadow-sm">
                                        <i class="bi bi-sunrise-fill fs-4"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="fw-bold text-dark mb-2">Jemput Pagi (Ke Sekolah)</h6>
                                        
                                        @if($pickup)
                                            <div class="d-flex flex-wrap gap-2 mb-3">
                                                <span class="badge {{ $pickup->status == 'dropped_off' ? 'bg-success' : ($pickup->status == 'absent' ? 'bg-danger' : 'bg-warning text-dark') }}">
                                                    {{ $pickup->status == 'dropped_off' ? 'Tiba di Sekolah' : ucfirst($pickup->status) }}
                                                </span>
                                            </div>
                                            
                                            <div class="info-grid">
                                                <div class="info-item">
                                                    <span class="label">Waktu Tiba</span>
                                                    <span class="value text-dark fw-bold">
                                                        {{ $pickup->dropped_at ? \Carbon\Carbon::parse($pickup->dropped_at)->format('H:i') . ' WIB' : '-' }}
                                                    </span>
                                                </div>
                                                <div class="info-item">
                                                    <span class="label">Driver</span>
                                                    <span class="value">{{ $pickup->trip->driver->name ?? '-' }}</span>
                                                </div>
                                            </div>
                                        @else
                                            <div class="text-muted small fst-italic py-3">Tidak ada jadwal jemput pagi.</div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            {{-- BAGIAN KANAN: SORE --}}
                            <div class="col-md-6 p-4 bg-white">
                                <div class="d-flex gap-3">
                                    <div class="icon-box bg-info bg-opacity-10 text-info rounded-3 shadow-sm">
                                        <i class="bi bi-sunset-fill fs-4"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="fw-bold text-dark mb-2">Antar Sore (Ke Rumah)</h6>
                                        
                                        @if($dropoff)
                                            <div class="d-flex flex-wrap gap-2 mb-3">
                                                <span class="badge {{ $dropoff->status == 'dropped_off' ? 'bg-success' : ($dropoff->status == 'absent' ? 'bg-danger' : 'bg-warning text-dark') }}">
                                                    {{ $dropoff->status == 'dropped_off' ? 'Tiba di Rumah' : ucfirst($dropoff->status) }}
                                                </span>
                                            </div>

                                            <div class="info-grid">
                                                <div class="info-item">
                                                    <span class="label">Waktu Tiba</span>
                                                    <span class="value text-dark fw-bold">
                                                        {{ $dropoff->dropped_at ? \Carbon\Carbon::parse($dropoff->dropped_at)->format('H:i') . ' WIB' : '-' }}
                                                    </span>
                                                </div>
                                                <div class="info-item">
                                                    <span class="label">Driver</span>
                                                    <span class="value">{{ $dropoff->trip->driver->name ?? '-' }}</span>
                                                </div>
                                            </div>
                                        @else
                                            <div class="text-muted small fst-italic py-3">Tidak ada jadwal antar sore.</div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 py-5">
                <div class="text-center">
                    <div class="bg-light rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center shadow-sm" style="width: 100px; height: 100px;">
                        <i class="bi bi-journal-x fs-1 text-muted opacity-50"></i>
                    </div>
                    <h5 class="fw-bold text-dark">Data Tidak Ditemukan</h5>
                    <p class="text-muted small mb-4">Tidak ada riwayat perjalanan yang sesuai dengan filter Anda.</p>
                    @if(request()->has('date') || request()->has('month'))
                        <a href="{{ route('parents.history') }}" class="btn btn-outline-primary px-4 rounded-pill">
                            Hapus Filter
                        </a>
                    @endif
                </div>
            </div>
        @endforelse
    </div>

    {{-- PAGINATION YANG DIRAPIKAN --}}
    <div class="mt-5 d-flex justify-content-center">
        {{-- Menggunakan style Bootstrap 5 agar rapi --}}
        {{ $histories->appends(request()->query())->links('pagination::bootstrap-5') }}
    </div>
    
    {{-- Info Showing --}}
    <div class="text-center text-muted small mt-2">
        Menampilkan {{ $histories->firstItem() ?? 0 }} - {{ $histories->lastItem() ?? 0 }} dari {{ $histories->total() }} data
    </div>

    <div style="height: 60px;"></div>
</div>

<style>
    .bg-light-gradient-start { background: linear-gradient(to right, #f8f9fa, #ffffff); }
    .bg-success-subtle { background-color: #d1fae5 !important; color: #065f46 !important; }
    .bg-danger-subtle { background-color: #fee2e2 !important; color: #991b1b !important; }
    .bg-info-subtle { background-color: #cffafe !important; color: #0e7490 !important; }

    .icon-box { width: 48px; height: 48px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
    .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; font-size: 0.85rem; }
    .info-item { display: flex; flex-direction: column; }
    .info-item .label { font-size: 0.75rem; color: #6c757d; text-transform: uppercase; font-weight: 600; margin-bottom: 2px; }

    /* Responsive Borders */
    @media (min-width: 768px) {
        .border-end-md { border-right: 1px solid #e9ecef; }
    }
    @media (max-width: 767.98px) {
        .col-md-6:first-child { border-bottom: 1px solid #e9ecef; }
    }
</style>
@endsection