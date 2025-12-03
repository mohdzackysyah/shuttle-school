@extends('layouts.driver')

@section('content')
<style>
    body { background-color: #f8f9fa; }
    
    .card-custom {
        background: white;
        border: none;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.04);
        margin-bottom: 20px;
    }

    /* TOMBOL KUNING (NAIK) */
    .btn-pickup {
        background-color: #ffc107;
        color: #000;
        font-weight: 800;
        border: none;
        width: 100%;
        padding: 12px;
        border-radius: 6px;
        font-size: 0.95rem;
        display: flex; align-items: center; justify-content: center;
        text-transform: uppercase;
    }
    .btn-pickup:hover { background-color: #e0a800; }

    /* TOMBOL HIJAU (TURUN/SAMPAI) - BARU */
    .btn-dropoff {
        background-color: #198754; /* Hijau Bootstrap */
        color: #fff;
        font-weight: 800;
        border: none;
        width: 100%;
        padding: 12px;
        border-radius: 6px;
        font-size: 0.95rem;
        display: flex; align-items: center; justify-content: center;
        text-transform: uppercase;
    }
    .btn-dropoff:hover { background-color: #157347; }

    /* TOMBOL SKIP */
    .btn-skip {
        background-color: #fff;
        color: #dc3545;
        border: 1px solid #dc3545;
        font-weight: 700;
        width: 100%;
        padding: 12px;
        border-radius: 6px;
        font-size: 0.95rem;
        text-transform: uppercase;
    }
    .btn-skip:hover { background-color: #fef2f2; }

    .icon-small { width: 16px; height: 16px; color: #6c757d; margin-right: 6px; vertical-align: text-bottom; }
    .text-data { color: #495057; font-size: 0.95rem; }
</style>

<div class="container py-4">

    {{-- ALERT SUKSES --}}
    @if(session('success'))
    <div class="alert alert-success border-0 shadow-sm rounded-3 mb-4 d-flex align-items-center" style="background-color: #d1e7dd; color: #0f5132;">
        <div class="fw-bold">✅ {{ session('success') }}</div>
    </div>
    @endif

    {{-- CARD RUTE --}}
    <div class="card-custom p-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h4 class="fw-bold text-dark mb-1">{{ $trip->route->name ?? 'Rute' }}</h4>
                @if($trip->type == 'pickup')
                    <span class="badge bg-warning text-dark border border-warning rounded-2">☀ JEMPUT PAGI</span>
                @else
                    <span class="badge bg-info text-white rounded-2">🌙 ANTAR SORE</span>
                @endif
            </div>
            
            {{-- Tombol Finish Global --}}
            <form action="{{ route('driver.trip.finish', $trip->id) }}" method="POST" onsubmit="return confirm('Apakah semua siswa sudah diantar? Selesaikan sesi ini?');">
                @csrf
                <button type="submit" class="btn btn-dark fw-bold text-white px-3 py-2 rounded-3">
                     🏁 SELESAI
                </button>
            </form>
        </div>
    </div>

    {{-- LIST SISWA --}}
    @foreach($passengers as $p)
    <div class="card-custom p-4">
        
        {{-- HEADER: NAMA & STATUS --}}
        <div class="d-flex justify-content-between align-items-start mb-2">
            <h5 class="fw-bold text-dark m-0">{{ $p->student->name }}</h5>
            
            {{-- Logic Badge Status --}}
            @if($p->status == 'pending')
                <span class="badge bg-secondary">⏳ Menunggu</span>
            @elseif($p->status == 'picked_up')
                {{-- Jika Sore: Tampilkan "Di dalam Mobil" --}}
                @if($trip->type == 'dropoff')
                    <span class="badge bg-info text-dark">🚌 Di Mobil</span>
                @else
                    <span class="badge bg-success">✅ Sudah Naik</span>
                @endif
            @elseif($p->status == 'skipped')
                <span class="badge bg-danger">🚫 Skip</span>
            @else
                <span class="badge bg-primary">🏠 Sampai Rumah</span>
            @endif
        </div>

        {{-- DETAIL ALAMAT --}}
        <div class="mb-4 ps-1">
            <div class="mb-1 text-data d-flex align-items-start">
                <svg class="icon-small flex-shrink-0 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                <span>
                    <strong>{{ $p->student->complex->name ?? 'Komplek ?' }}</strong><br>
                    <span class="text-muted small">{{ $p->student->address_note ?? '-' }}</span>
                </span>
            </div>
            <div class="text-data d-flex align-items-center">
                <svg class="icon-small flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" /></svg>
                <span>Ortu: {{ $p->student->parent->phone ?? '-' }}</span>
            </div>
        </div>

        {{-- LOGIKA TOMBOL AKSI --}}
        
        {{-- KONDISI 1: Belum Naik (Berlaku Pagi & Sore) --}}
        @if($p->status == 'pending')
            <div class="row g-2">
                <div class="col-8">
                    <form action="{{ route('driver.passenger.pickup', $p->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn-pickup shadow-sm">
                            <svg style="width:18px; height:18px; margin-right:6px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14"></path></svg>
                            NAIK (JEMPUT)
                        </button>
                    </form>
                </div>
                <div class="col-4">
                    <form action="{{ route('driver.passenger.skip', $p->id) }}" method="POST" onsubmit="return confirm('Lewati siswa ini?');">
                        @csrf
                        <button type="submit" class="btn-skip">SKIP</button>
                    </form>
                </div>
            </div>

        {{-- KONDISI 2: Sudah Naik & Ini adalah ANTAR SORE (dropoff) --}}
        {{-- Munculkan tombol TURUN hanya jika tipe trip bukan pickup (pagi) --}}
        @elseif($p->status == 'picked_up' && $trip->type != 'pickup')
            <div class="row">
                <div class="col-12">
                    <form action="{{ route('driver.passenger.dropoff', $p->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn-dropoff shadow-sm">
                            <svg style="width:18px; height:18px; margin-right:6px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path></svg>
                            TURUN (SAMPAI RUMAH)
                        </button>
                    </form>
                </div>
            </div>

        @endif

    </div>
    @endforeach

    <div style="height: 100px;"></div>
</div>
@endsection