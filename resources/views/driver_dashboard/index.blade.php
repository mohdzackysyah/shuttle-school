@extends('layouts.driver')

@section('content')
<style>
    .card-task {
        transition: transform 0.2s, box-shadow 0.2s;
        border-left: 6px solid #e9ecef; /* Default border */
    }
    .card-task:active { transform: scale(0.98); }
    
    /* Warna Border Kiri */
    .border-pickup { border-left-color: #ffc107 !important; } /* Kuning */
    .border-dropoff { border-left-color: #0dcaf0 !important; } /* Biru Muda */
    
    /* Warna Badge Soft */
    .bg-soft-primary { background-color: #e7f1ff; color: #0d6efd; }
    .bg-soft-warning { background-color: #fff8e1; color: #d39e00; }
    .bg-soft-info { background-color: #e0f7fa; color: #00838f; }
    .bg-soft-success { background-color: #d1e7dd; color: #0f5132; }
    .bg-soft-secondary { background-color: #e2e3e5; color: #41464b; }

    .task-icon {
        width: 50px; height: 50px;
        display: flex; align-items: center; justify-content: center;
        border-radius: 12px;
        font-size: 1.5rem;
    }
</style>

<div class="container py-4">
    
    {{-- Header Dashboard --}}
    <div class="d-flex justify-content-between align-items-center mb-4 px-1">
        <div>
            <small class="text-muted fw-bold text-uppercase ls-1" style="font-size: 0.7rem;">Selamat Bertugas</small>
            <h3 class="fw-bold text-dark mb-0">{{ Auth::user()->name }}</h3>
        </div>
        <div class="text-end">
            <div class="bg-white shadow-sm px-3 py-2 rounded-3 border border-light">
                <div class="fw-bold text-dark" style="line-height: 1.1;">{{ \Carbon\Carbon::parse($todayDate)->format('d M') }}</div>
                <small class="text-primary fw-bold text-uppercase" style="font-size: 0.7rem;">{{ $today }}</small>
            </div>
        </div>
    </div>

    @if($schedules->isEmpty())
        {{-- State Kosong --}}
        <div class="card border-0 shadow-sm text-center py-5 rounded-4 mt-4 bg-white">
            <div class="card-body">
                <div class="bg-soft-secondary rounded-circle d-inline-flex p-4 mb-3">
                    <i class="bi bi-cup-hot-fill display-4"></i>
                </div>
                <h4 class="fw-bold text-dark">Tidak Ada Jadwal</h4>
                <p class="text-muted small px-4">Hari ini Anda tidak memiliki tugas antar jemput. Silakan istirahat.</p>
            </div>
        </div>
    @else
        {{-- Label Daftar --}}
        <div class="d-flex align-items-center mb-3 px-1">
            <div class="bg-primary rounded-pill me-2" style="width: 4px; height: 18px;"></div>
            <h6 class="fw-bold text-secondary mb-0 text-uppercase" style="font-size: 0.8rem; letter-spacing: 0.5px;">Daftar Tugas Hari Ini</h6>
        </div>
    @endif

    {{-- Loop Schedule --}}
    @foreach($schedules as $schedule)
        <div class="card card-task border-0 shadow-sm mb-3 rounded-4 overflow-hidden {{ $schedule->type == 'pickup' ? 'border-pickup' : 'border-dropoff' }}">
            <div class="card-body p-4">
                
                {{-- Jam & Badge Tipe --}}
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <h1 class="fw-bold mb-0 text-dark" style="font-size: 2.2rem;">
                            {{ \Carbon\Carbon::parse($schedule->departure_time)->format('H:i') }}
                            <span class="fs-6 text-muted fw-normal">WIB</span>
                        </h1>
                    </div>
                    
                    @if($schedule->type == 'pickup')
                        <span class="badge bg-soft-warning rounded-pill px-3 py-2 border border-warning border-opacity-25">
                            <i class="bi bi-sunrise-fill me-1"></i> JEMPUT PAGI
                        </span>
                    @else
                        <span class="badge bg-soft-info rounded-pill px-3 py-2 border border-info border-opacity-25">
                            <i class="bi bi-sunset-fill me-1"></i> ANTAR SORE
                        </span>
                    @endif
                </div>

                {{-- Info Rute --}}
                <div class="d-flex align-items-center mb-4 p-3 bg-light rounded-3 border border-light">
                    <div class="me-3">
                        <div class="task-icon bg-white shadow-sm text-primary">
                            <i class="bi bi-geo-alt-fill"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1">
                        <small class="text-muted text-uppercase fw-bold" style="font-size: 0.65rem;">Rute Tujuan</small>
                        <h5 class="fw-bold text-dark mb-1">{{ $schedule->route->name }}</h5>
                        <div class="d-flex align-items-center text-secondary small">
                            <i class="bi bi-car-front-fill me-1"></i> 
                            {{ $schedule->shuttle->car_model }} 
                            <span class="fw-bold text-dark ms-1">• {{ $schedule->shuttle->plate_number }}</span>
                        </div>
                    </div>
                </div>

                {{-- LOGIKA TOMBOL --}}
                @if($schedule->today_trip)
                    
                    {{-- Jika Sudah Selesai --}}
                    @if($schedule->today_trip->status == 'finished')
                        <div class="d-grid">
                            <a href="{{ route('driver.trip.history', $schedule->today_trip->id) }}" class="btn btn-outline-secondary py-3 rounded-3 fw-bold border-2 shadow-sm">
                                <i class="bi bi-clock-history me-2"></i> LIHAT RIWAYAT
                            </a>
                        </div>
                    
                    {{-- Jika Sedang Aktif / Belum Selesai --}}
                    @else
                        <div class="d-grid">
                            {{-- PERBAIKAN DISINI: Link ke Process Driver (Bukan Admin) --}}
                            <a href="{{ route('driver.trip.process', $schedule->today_trip->id) }}" class="btn btn-success py-3 rounded-3 fw-bold shadow position-relative overflow-hidden">
                                <span class="position-relative z-1">
                                    <i class="bi bi-arrow-repeat me-2"></i> LANJUTKAN TUGAS
                                </span>
                                <div class="position-absolute top-0 start-0 w-100 h-100 bg-white opacity-10" style="transform: skewX(-20deg) translateX(-50%);"></div>
                            </a>
                        </div>
                    @endif

                @else
                    
                    {{-- Tombol Mulai (Membuat Trip Baru) --}}
                    <form action="{{ route('trips.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="driver_id" value="{{ Auth::id() }}">
                        <input type="hidden" name="shuttle_id" value="{{ $schedule->shuttle_id }}">
                        <input type="hidden" name="route_id" value="{{ $schedule->route_id }}">
                        <input type="hidden" name="date" value="{{ $todayDate }}">
                        <input type="hidden" name="type" value="{{ $schedule->type }}">
                        
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary py-3 rounded-3 fw-bold shadow position-relative overflow-hidden">
                                <div class="d-flex justify-content-between px-4 align-items-center position-relative z-1">
                                    <span>MULAI PERJALANAN</span>
                                    <i class="bi bi-play-circle-fill fs-4"></i>
                                </div>
                            </button>
                        </div>
                    </form>

                @endif

            </div>
        </div>
    @endforeach
    
    <div style="height: 80px;"></div>
</div>
@endsection