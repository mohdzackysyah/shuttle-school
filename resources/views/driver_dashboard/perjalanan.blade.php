@extends('layouts.driver')

@section('content')
<style>
    body { background-color: #f1f5f9; font-family: 'Poppins', sans-serif; }
    
    /* 1. Sticky Header */
    .sticky-header {
        position: sticky; top: 0; z-index: 1020;
        background: rgba(255, 255, 255, 0.98);
        backdrop-filter: blur(10px);
        box-shadow: 0 4px 20px rgba(0,0,0,0.05);
        border-bottom-left-radius: 20px;
        border-bottom-right-radius: 20px;
        margin-left: -12px; margin-right: -12px;
        padding: 1.25rem 1.5rem;
    }

    /* 2. Loading Bar */
    .refresh-track {
        position: absolute; top: 0; left: 0; width: 100%; height: 4px;
        background: #f1f5f9;
    }
    .refresh-bar {
        height: 100%; background: #2563eb; width: 0%;
        transition: width 1s linear;
    }

    /* 3. Card Siswa */
    .card-student {
        background: white; border: none; border-radius: 16px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.03);
        margin-bottom: 1rem; position: relative; overflow: hidden;
        transition: transform 0.2s;
        border: 1px solid #f1f5f9;
    }
    .card-student:active { transform: scale(0.98); }

    /* Indikator Status */
    .status-stripe { position: absolute; left: 0; top: 0; bottom: 0; width: 6px; }
    .stripe-pending { background: #cbd5e1; }
    .stripe-active { background: #f59e0b; }
    .stripe-done { background: #10b981; }
    .stripe-skip { background: #ef4444; }

    /* Background Card */
    .bg-done { background-color: #f0fdf4; border-color: #bbf7d0; }
    .bg-skip { background-color: #fef2f2; border-color: #fecaca; opacity: 0.8; }
    .bg-active { background-color: #fffbeb; border-color: #fde68a; }

    /* Avatar */
    .avatar-circle {
        width: 45px; height: 45px;
        background: #f1f5f9; color: #64748b;
        border-radius: 50%; font-weight: 700;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.1rem; border: 2px solid white;
        box-shadow: 0 2px 5px rgba(0,0,0,0.05);
    }

    /* Tombol Aksi */
    .btn-action {
        width: 100%; border: none; border-radius: 10px;
        padding: 12px; font-weight: 700; font-size: 0.9rem;
        display: flex; align-items: center; justify-content: center; gap: 8px;
        text-transform: uppercase; letter-spacing: 0.5px;
        transition: 0.2s;
    }
    
    .btn-pickup { background: #f59e0b; color: white; box-shadow: 0 4px 10px rgba(245, 158, 11, 0.2); }
    .btn-pickup:active { background: #d97706; transform: translateY(2px); }

    .btn-dropoff { background: #10b981; color: white; box-shadow: 0 4px 10px rgba(16, 185, 129, 0.2); }
    .btn-dropoff:active { background: #059669; transform: translateY(2px); }

    .btn-skip { background: white; color: #ef4444; border: 1px solid #fee2e2; }
    .btn-skip:active { background: #fef2f2; }

    /* Tombol Selesai Header */
    .btn-finish-header {
        background: #1e293b; color: white;
        border-radius: 50px; padding: 0.6rem 1.5rem;
        font-weight: 600; font-size: 0.85rem;
        display: flex; align-items: center; gap: 0.5rem;
        box-shadow: 0 4px 12px rgba(30, 41, 59, 0.2);
        border: none;
    }
    .btn-finish-header:active { transform: scale(0.95); }
</style>

{{-- Kalkulasi Progress (Menggunakan variabel $passengers dari Controller) --}}
@php
    $total = $passengers->count();
    // Hitung status != pending
    $done = $passengers->filter(function($p) {
        return $p->status != 'pending';
    })->count();
    
    $percent = $total > 0 ? ($done/$total)*100 : 0;
@endphp

<div class="container pb-5">

    {{-- 1. STICKY HEADER --}}
    <div class="sticky-header mb-4">
        <div class="refresh-track"><div class="refresh-bar" id="refreshBar"></div></div>

        <div class="d-flex justify-content-between align-items-center mb-3">
            <div style="flex: 1; min-width: 0; margin-right: 15px;">
                <div class="d-flex align-items-center gap-2 mb-1">
                    @if($trip->type == 'pickup')
                        <span class="badge bg-warning text-dark rounded-pill" style="font-size: 0.65rem;">
                            <i class="bi bi-sun-fill me-1"></i> PAGI
                        </span>
                    @else
                        <span class="badge bg-info text-white rounded-pill" style="font-size: 0.65rem;">
                            <i class="bi bi-moon-fill me-1"></i> SORE
                        </span>
                    @endif
                    <span class="text-secondary small fw-bold" id="realtimeClock">--:--</span>
                </div>
                <h5 class="fw-bold text-dark mb-0 text-truncate">{{ $trip->route->name ?? 'Nama Rute' }}</h5>
            </div>
            
            <form action="{{ route('driver.trip.finish', $trip->id) }}" method="POST" onsubmit="return confirm('Selesaikan seluruh perjalanan ini?');">
                @csrf
                <button type="submit" class="btn-finish-header">
                    <i class="bi bi-flag-fill text-warning"></i> Selesai
                </button>
            </form>
        </div>

        {{-- Progress Bar --}}
        <div class="d-flex align-items-center gap-2 mt-2">
            <div class="progress flex-grow-1" style="height: 6px; border-radius: 10px; background: #f1f5f9;">
                <div class="progress-bar bg-success" role="progressbar" style="width: {{ $percent }}%"></div>
            </div>
            <small class="fw-bold text-muted" style="font-size: 0.75rem;">{{ $done }}/{{ $total }} Siswa</small>
        </div>
    </div>

    {{-- Alert Sukses --}}
    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm rounded-3 d-flex align-items-center mb-3 py-2 px-3">
            <i class="bi bi-check-circle-fill fs-5 me-2"></i>
            <div class="small fw-bold ms-2">{{ session('success') }}</div>
        </div>
    @endif

    {{-- 2. LIST KARTU SISWA --}}
    <div class="pb-5 mb-5">
        @forelse($passengers as $p)
            @php
                // Logika Warna berdasarkan Status
                $stripeClass = 'stripe-pending';
                $cardBg = 'bg-white';
                
                if($p->status == 'picked_up') {
                    $stripeClass = 'stripe-active';
                    // Jika sore (dropoff), yang statusnya picked_up kita highlight kuning (ready to drop)
                    if($trip->type != 'pickup') $cardBg = 'bg-active'; 
                } 
                elseif($p->status == 'dropped_off') {
                    $stripeClass = 'stripe-done'; $cardBg = 'bg-done'; 
                }
                elseif($p->status == 'skipped') {
                    $stripeClass = 'stripe-skip'; $cardBg = 'bg-skip'; 
                }
            @endphp

            <div class="card-student p-3 {{ $cardBg }}">
                <div class="status-stripe {{ $stripeClass }}"></div>
                
                {{-- Info Siswa --}}
                <div class="d-flex align-items-center mb-3 ps-2">
                    <div class="me-3">
                        @if($p->student->photo)
                            <img src="{{ asset('storage/'.$p->student->photo) }}" class="rounded-circle" style="width: 45px; height: 45px; object-fit: cover;">
                        @else
                            <div class="avatar-circle">
                                {{ substr($p->student->name, 0, 1) }}
                            </div>
                        @endif
                    </div>
                    
                    <div class="flex-grow-1" style="min-width: 0;">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <h6 class="fw-bold text-dark mb-0 text-truncate">{{ $p->student->name }}</h6>
                            
                            {{-- Badge Status --}}
                            @if($p->status == 'picked_up')
                                <span class="badge bg-warning text-dark rounded-pill" style="font-size:0.6rem;">NAIK</span>
                            @elseif($p->status == 'dropped_off')
                                <span class="badge bg-success rounded-pill" style="font-size:0.6rem;">SAMPAI</span>
                            @elseif($p->status == 'skipped')
                                <span class="badge bg-danger rounded-pill" style="font-size:0.6rem;">SKIP</span>
                            @endif
                        </div>
                        <div class="d-flex align-items-center text-muted small">
                            <i class="bi bi-geo-alt-fill text-danger me-1" style="font-size: 0.7rem;"></i>
                            <span class="text-truncate">
                                {{ $p->student->complex->name ?? 'Umum' }}
                            </span>
                        </div>
                    </div>
                </div>

                {{-- TOMBOL AKSI --}}
                <div class="ps-2">
                    {{-- 1. Belum Dijemput --}}
                    @if($p->status == 'pending')
                        <div class="row g-2">
                            <div class="col-8">
                                <form action="{{ route('driver.passenger.pickup', $p->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn-action btn-pickup">
                                        <i class="bi bi-box-arrow-in-right fs-5"></i> JEMPUT (NAIK)
                                    </button>
                                </form>
                            </div>
                            <div class="col-4">
                                <form action="{{ route('driver.passenger.skip', $p->id) }}" method="POST" onsubmit="return confirm('Lewati siswa ini?');">
                                    @csrf
                                    <button type="submit" class="btn-action btn-skip">SKIP</button>
                                </form>
                            </div>
                        </div>

                    {{-- 2. Sudah Naik & Perjalanan Sore (Tombol Turun) --}}
                    @elseif($p->status == 'picked_up' && $trip->type != 'pickup') 
                        <form action="{{ route('driver.passenger.dropoff', $p->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn-action btn-dropoff">
                                <i class="bi bi-house-check-fill fs-5"></i> TURUN (SAMPAI)
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        @empty
            <div class="text-center py-5">
                <i class="bi bi-people text-muted display-1 opacity-25"></i>
                <p class="text-muted mt-3">Tidak ada data penumpang.</p>
            </div>
        @endforelse
    </div>
</div>

{{-- SCRIPT: Auto Refresh & Jam --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const refreshTime = 5; 
        let timeLeft = refreshTime;
        const progressBar = document.getElementById('refreshBar');

        function updateClock() {
            const now = new Date();
            const timeString = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' }).replace(/\./g, ':');
            const el = document.getElementById('realtimeClock');
            if(el) el.textContent = timeString;
        }

        function startAutoRefresh() {
            const timer = setInterval(() => {
                timeLeft--;
                if (progressBar) {
                    progressBar.style.width = ((refreshTime - timeLeft) / refreshTime) * 100 + '%';
                }
                if (timeLeft <= 0) {
                    clearInterval(timer);
                    sessionStorage.setItem('scrollPos', window.scrollY);
                    window.location.reload();
                }
            }, 1000);
        }

        const scrollPos = sessionStorage.getItem('scrollPos');
        if (scrollPos) {
            window.scrollTo(0, parseInt(scrollPos));
            sessionStorage.removeItem('scrollPos');
        }

        setInterval(updateClock, 1000);
        updateClock();
        startAutoRefresh();
    });
</script>
@endsection