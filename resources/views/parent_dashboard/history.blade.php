@extends('layouts.parent')

@section('content')
<div class="container py-4">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold text-dark mb-1">📜 Riwayat Perjalanan</h4>
            <p class="text-muted small mb-0">Laporan aktivitas antar-jemput anak.</p>
        </div>
    </div>

    {{-- FILTER SECTION --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body bg-light bg-opacity-50">
            <form action="{{ route('parents.history') }}" method="GET">
                <div class="row g-3 align-items-end">
                    
                    <div class="col-md-4">
                        <label class="form-label small fw-bold text-muted text-uppercase">Cari Tanggal</label>
                        <input type="date" name="date" class="form-control" value="{{ request('date') }}">
                    </div>

                    <div class="col-md-1 text-center fw-bold text-muted py-2 d-none d-md-block">
                        - ATAU -
                    </div>

                    <div class="col-6 col-md-3">
                        <label class="form-label small fw-bold text-muted text-uppercase">Bulan</label>
                        <select name="month" class="form-select">
                            <option value="">-- Pilih --</option>
                            @foreach(range(1, 12) as $m)
                                <option value="{{ $m }}" {{ request('month', date('n')) == $m ? 'selected' : '' }}>
                                    {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-6 col-md-2">
                        <label class="form-label small fw-bold text-muted text-uppercase">Tahun</label>
                        <select name="year" class="form-select">
                            <option value="">-- Pilih --</option>
                            @foreach(range(date('Y'), 2023) as $y)
                                <option value="{{ $y }}" {{ request('year', date('Y')) == $y ? 'selected' : '' }}>
                                    {{ $y }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary fw-bold text-white shadow-sm flex-grow-1">
                                <i class="bi bi-funnel-fill"></i> Filter
                            </button>
                            @if(request()->has('date') || request()->has('month'))
                                <a href="{{ route('parents.history') }}" class="btn btn-outline-danger shadow-sm">
                                    <i class="bi bi-x-lg"></i>
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
                
                // Cari Data Pagi & Sore dalam grup ini
                $pickup = $group->first(fn($i) => $i->trip->type == 'pickup');
                $dropoff = $group->first(fn($i) => $i->trip->type == 'dropoff');
            @endphp

            <div class="col-12">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden card-history">
                    
                    {{-- Header Card (Nama & Tanggal) --}}
                    <div class="card-header bg-white border-bottom py-3 px-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="fw-bold text-dark mb-1">{{ $student->name }}</h5>
                                <div class="text-muted small">
                                    <i class="bi bi-calendar-event me-1"></i> {{ $date->translatedFormat('l, d F Y') }}
                                </div>
                            </div>
                            <div>
                                {{-- Status Keseluruhan --}}
                                @if($pickup && $dropoff && $pickup->status == 'dropped_off' && $dropoff->status == 'dropped_off')
                                    <span class="badge bg-success-subtle text-success border border-success px-3 py-2 rounded-pill">
                                        <i class="bi bi-check-all me-1"></i> Selesai Lengkap
                                    </span>
                                @elseif(($pickup && $pickup->status == 'absent') || ($dropoff && $dropoff->status == 'absent'))
                                    <span class="badge bg-danger-subtle text-danger border border-danger px-3 py-2 rounded-pill">
                                        <i class="bi bi-x-circle me-1"></i> Ada Izin/Absen
                                    </span>
                                @else
                                    <span class="badge bg-light text-secondary border px-3 py-2 rounded-pill">
                                        <i class="bi bi-info-circle me-1"></i> Info
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="card-body p-0">
                        <div class="row g-0">
                            
                            {{-- KOLOM KIRI: JEMPUT PAGI --}}
                            <div class="col-lg-6 border-end-lg p-4 position-relative">
                                <div class="d-flex align-items-start gap-3">
                                    <div class="icon-circle bg-warning bg-opacity-10 text-warning">
                                        <i class="bi bi-sunrise-fill fs-4"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="fw-bold text-dark mb-1">Jemput Pagi (Ke Sekolah)</h6>
                                        
                                        @if($pickup)
                                            <div class="mb-2">
                                                <span class="badge {{ $pickup->status == 'dropped_off' ? 'bg-success' : ($pickup->status == 'absent' ? 'bg-danger' : 'bg-warning') }} mb-2">
                                                    {{ $pickup->status == 'dropped_off' ? 'Tiba di Sekolah' : ucfirst($pickup->status) }}
                                                </span>
                                            </div>
                                            
                                            <div class="row g-2 small">
                                                <div class="col-6">
                                                    <div class="text-muted">Waktu Tiba</div>
                                                    <div class="fw-bold text-dark fs-6">
                                                        {{ $pickup->dropped_at ? \Carbon\Carbon::parse($pickup->dropped_at)->format('H:i') . ' WIB' : '-' }}
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="text-muted">Driver</div>
                                                    <div class="fw-bold text-dark">{{ $pickup->trip->driver->name ?? '-' }}</div>
                                                </div>
                                            </div>
                                            <div class="mt-2 small text-secondary">
                                                <i class="bi bi-geo-alt me-1"></i> {{ $pickup->trip->route->name ?? '-' }}
                                            </div>
                                        @else
                                            <div class="alert alert-light border border-dashed text-center mt-2 mb-0 py-3">
                                                <small class="text-muted">Tidak ada jadwal pagi</small>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            {{-- KOLOM KANAN: ANTAR SORE --}}
                            <div class="col-lg-6 p-4">
                                <div class="d-flex align-items-start gap-3">
                                    <div class="icon-circle bg-info bg-opacity-10 text-info">
                                        <i class="bi bi-sunset-fill fs-4"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="fw-bold text-dark mb-1">Antar Sore (Ke Rumah)</h6>
                                        
                                        @if($dropoff)
                                            <div class="mb-2">
                                                <span class="badge {{ $dropoff->status == 'dropped_off' ? 'bg-success' : ($dropoff->status == 'absent' ? 'bg-danger' : 'bg-warning') }} mb-2">
                                                    {{ $dropoff->status == 'dropped_off' ? 'Tiba di Rumah' : ucfirst($dropoff->status) }}
                                                </span>
                                            </div>

                                            <div class="row g-2 small">
                                                <div class="col-6">
                                                    <div class="text-muted">Waktu Tiba</div>
                                                    <div class="fw-bold text-dark fs-6">
                                                        {{ $dropoff->dropped_at ? \Carbon\Carbon::parse($dropoff->dropped_at)->format('H:i') . ' WIB' : '-' }}
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="text-muted">Driver</div>
                                                    <div class="fw-bold text-dark">{{ $dropoff->trip->driver->name ?? '-' }}</div>
                                                </div>
                                            </div>
                                            <div class="mt-2 small text-secondary">
                                                <i class="bi bi-geo-alt me-1"></i> {{ $dropoff->trip->route->name ?? '-' }}
                                            </div>
                                        @else
                                            <div class="alert alert-light border border-dashed text-center mt-2 mb-0 py-3">
                                                <small class="text-muted">Tidak ada jadwal sore</small>
                                            </div>
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
                    <div class="bg-light rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                        <i class="bi bi-journal-x fs-1 text-muted opacity-50"></i>
                    </div>
                    <h5 class="fw-bold text-muted">Tidak Ada Data</h5>
                    <p class="text-secondary small">Belum ada riwayat perjalanan yang ditemukan.</p>
                </div>
            </div>
        @endforelse
    </div>

    {{-- Pagination (Tetap pakai object asli) --}}
    <div class="mt-4 d-flex justify-content-center">
        {{ $histories->links() }}
    </div>

    <div style="height: 50px;"></div>
</div>

<style>
    .bg-success-subtle { background-color: #d1fae5; color: #065f46; }
    .bg-danger-subtle { background-color: #fee2e2; color: #991b1b; }
    
    .icon-circle {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    /* Desktop: Ada garis pemisah di tengah */
    @media (min-width: 992px) {
        .border-end-lg {
            border-right: 1px solid #e5e7eb;
        }
    }

    /* Mobile: Tumpuk dan beri garis pemisah horizontal */
    @media (max-width: 991.98px) {
        .col-lg-6 {
            border-bottom: 1px solid #f3f4f6;
        }
        .col-lg-6:last-child {
            border-bottom: none;
        }
    }
</style>
@endsection