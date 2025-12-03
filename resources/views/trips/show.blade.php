@extends('layouts.admin')

@section('content')
<div class="container py-2">
    
    <div class="card mb-3 border-0 shadow-sm sticky-top" style="z-index: 1020; top: 10px; border-radius: 15px;">
        <div class="card-body p-3 d-flex justify-content-between align-items-center">
            <div>
                <h5 class="fw-bold mb-0 text-dark">{{ $trip->route->name }}</h5>
                <div class="text-muted small">
                    @if($trip->type == 'pickup')
                        <span class="badge bg-warning text-dark"><i class="bi bi-sunrise-fill"></i> JEMPUT PAGI</span>
                    @else
                        <span class="badge bg-info text-dark"><i class="bi bi-sunset-fill"></i> ANTAR PULANG</span>
                    @endif
                </div>
            </div>

            @if($trip->status != 'finished')
                <form action="{{ route('trips.finish', $trip->id) }}" method="POST" onsubmit="return confirm('{{ $trip->type == 'pickup' ? 'Sudah sampai di sekolah? Semua siswa akan dianggap turun.' : 'Semua pengantaran selesai?' }}')">
                    @csrf
                    @if($trip->type == 'pickup')
                        <button class="btn btn-success fw-bold shadow">
                            🏫 TIBA DI SEKOLAH
                        </button>
                    @else
                        <button class="btn btn-dark fw-bold shadow">
                            🏁 SELESAI SESI
                        </button>
                    @endif
                </form>
            @else
                <span class="badge bg-secondary p-2">SESI SELESAI</span>
            @endif
        </div>
    </div>

    @php
        $total = $passengers->count();
        // Hitung progres berbeda untuk pagi dan sore
        if($trip->type == 'pickup') {
            // Pagi: Progres berdasarkan yang sudah dijemput (naik)
            $done = $passengers->where('status', 'picked_up')->count();
        } else {
            // Sore: Progres berdasarkan yang sudah turun di rumah
            $done = $passengers->where('status', 'dropped_off')->count();
        }
        $percent = $total > 0 ? ($done / $total) * 100 : 0;
    @endphp
    
    <div class="progress mb-4" style="height: 8px; border-radius: 5px;">
        <div class="progress-bar {{ $trip->type == 'pickup' ? 'bg-warning' : 'bg-info' }}" role="progressbar" style="width: {{ $percent }}%"></div>
    </div>

    <div class="d-flex flex-column gap-3">
        @forelse($passengers as $p)
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <h5 class="fw-bold mb-1 text-dark">{{ $p->student->name }}</h5>
                        <small class="text-muted d-block">
                            <i class="bi bi-geo-alt"></i> {{ $p->student->complex->name }}
                        </small>
                        <small class="text-muted d-block">
                            <i class="bi bi-telephone"></i> Ortu: {{ $p->student->parent->phone }}
                        </small>
                    </div>
                    
                    <div class="text-end">
                        @if($p->status == 'pending')
                            <span class="badge bg-secondary">⏳ Menunggu</span>
                        @elseif($p->status == 'picked_up')
                            <span class="badge bg-primary">🚌 Di Mobil</span>
                        @elseif($p->status == 'dropped_off')
                            <span class="badge bg-success">✅ Sampai</span>
                        @elseif($p->status == 'absent')
                            <span class="badge bg-danger">❌ Izin</span>
                        @endif
                    </div>
                </div>

                @if($trip->status != 'finished')
                    
                    @if($trip->type == 'pickup')
                        
                        @if($p->status == 'pending')
                            <div class="row g-2">
                                <div class="col-8">
                                    <form action="{{ route('passengers.pickup', $p->id) }}" method="POST">
                                        @csrf
                                        <button class="btn btn-warning w-100 py-2 fw-bold text-dark shadow-sm">
                                            <i class="bi bi-box-arrow-in-right"></i> NAIK (JEMPUT)
                                        </button>
                                    </form>
                                </div>
                                <div class="col-4">
                                    <form action="{{ route('passengers.absent', $p->id) }}" method="POST" onsubmit="return confirm('Siswa tidak masuk?')">
                                        @csrf
                                        <button class="btn btn-outline-danger w-100 py-2 fw-bold">
                                            SKIP
                                        </button>
                                    </form>
                                </div>
                            </div>
                        
                        @elseif($p->status == 'picked_up')
                            <div class="alert alert-success py-2 text-center mb-0">
                                <i class="bi bi-check-circle"></i> Sudah di dalam mobil
                            </div>
                        @endif


                    @elseif($trip->type == 'dropoff')

                        @if($p->status == 'pending')
                            <div class="row g-2">
                                <div class="col-12">
                                    <form action="{{ route('passengers.pickup', $p->id) }}" method="POST">
                                        @csrf
                                        <button class="btn btn-primary w-100 py-2 fw-bold shadow-sm">
                                            <i class="bi bi-person-check"></i> ABSEN NAIK (DI SEKOLAH)
                                        </button>
                                    </form>
                                </div>
                            </div>

                        @elseif($p->status == 'picked_up')
                            <form action="{{ route('passengers.dropoff', $p->id) }}" method="POST">
                                @csrf
                                <button class="btn btn-info w-100 py-3 fw-bold text-white shadow">
                                    <i class="bi bi-house-door-fill"></i> SUDAH SAMPAI RUMAH
                                </button>
                            </form>
                        @endif

                    @endif

                @endif
            </div>
        </div>
        @empty
        <div class="text-center py-5 text-muted">
            <p>Tidak ada data siswa di rute ini.</p>
        </div>
        @endforelse
    </div>
    
    <div style="height: 100px;"></div>
</div>
@endsection