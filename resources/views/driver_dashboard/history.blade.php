@extends('layouts.driver')

@section('content')
<div class="container py-2">
    
    <div class="mb-4 text-center">
        <h5 class="text-muted mb-1">Detail Riwayat Perjalanan</h5>
        <h2 class="fw-bold text-dark">{{ \Carbon\Carbon::parse($trip->date)->format('d M Y') }}</h2>
        <span class="badge bg-success px-3 py-2 rounded-pill shadow-sm">
            <i class="bi bi-check-circle-fill"></i> SELESAI TUNTAS
        </span>
    </div>

    <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
        <div class="card-header border-0 py-3 {{ $trip->type == 'pickup' ? 'bg-warning' : 'bg-info' }} text-white">
            <div class="d-flex justify-content-between align-items-center">
                <span class="fw-bold fs-5">
                    <i class="bi bi-geo-alt-fill"></i> {{ $trip->route->name }}
                </span>
                <span class="badge bg-white text-dark rounded-pill">
                    {{ $trip->type == 'pickup' ? 'Jemput Pagi' : 'Antar Pulang' }}
                </span>
            </div>
        </div>
        <div class="card-body">
            <div class="row text-center">
                <div class="col-6 border-end">
                    <small class="text-muted d-block">Mobil</small>
                    <strong>{{ $trip->shuttle->plate_number }}</strong>
                </div>
                <div class="col-6">
                    <small class="text-muted d-block">Total Siswa</small>
                    <strong>{{ $passengers->count() }} Anak</strong>
                </div>
            </div>
        </div>
    </div>

    <h6 class="text-muted fw-bold ms-2 mb-3">DAFTAR MANIFEST:</h6>
    
    <div class="d-flex flex-column gap-3">
        @forelse($passengers as $p)
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-3 d-flex justify-content-between align-items-center">
                
                <div class="d-flex align-items-center">
                    <div class="bg-light text-secondary rounded-circle p-2 me-3">
                        <i class="bi bi-person-fill fs-4"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-0 text-dark">{{ $p->student->name }}</h6>
                        <small class="text-muted">{{ $p->student->complex->name }}</small>
                    </div>
                </div>

                <div>
                    @if($p->status == 'dropped_off')
                        <div class="text-end">
                            <span class="badge bg-success bg-opacity-10 text-success px-2 py-1 rounded">
                                <i class="bi bi-check-lg"></i> Sampai
                            </span>
                            <div class="text-muted" style="font-size: 0.65rem; margin-top: 2px;">
                                {{ $p->dropped_at ? \Carbon\Carbon::parse($p->dropped_at)->format('H:i') : '-' }}
                            </div>
                        </div>
                    @elseif($p->status == 'absent')
                        <span class="badge bg-danger bg-opacity-10 text-danger px-2 py-1 rounded">
                            <i class="bi bi-x-lg"></i> Absen
                        </span>
                    @else
                        <span class="badge bg-secondary">Tidak Tuntas</span>
                    @endif
                </div>

            </div>
        </div>
        @empty
        <div class="text-center text-muted py-3">Data kosong.</div>
        @endforelse
    </div>

    <div class="mt-5 mb-5">
        <a href="{{ route('driver.dashboard') }}" class="btn btn-outline-secondary w-100 py-3 rounded-3 fw-bold shadow-sm">
            &larr; Kembali ke Dashboard
        </a>
    </div>

    <div style="height: 60px;"></div>
</div>
@endsection