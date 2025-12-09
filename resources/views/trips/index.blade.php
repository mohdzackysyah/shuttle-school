@extends('layouts.admin')

@section('content')
<div class="container-fluid px-0">

    {{-- 1. HEADER HALAMAN --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4">
        <div class="mb-3 mb-md-0">
            <h3 class="fw-bold text-dark mb-1">
                <i class="bi bi-clock-history text-primary me-2"></i>Riwayat Perjalanan
            </h3>
            <p class="text-muted mb-0">Monitor status pelaksanaan dan histori perjalanan armada.</p>
        </div>
        
        {{-- Filter Tanggal (Visual Only - Bisa diaktifkan nanti) --}}
        <div class="d-none d-md-block">
            <div class="input-group shadow-sm rounded-pill overflow-hidden bg-white">
                <span class="input-group-text bg-white border-0 ps-3 text-muted"><i class="bi bi-calendar3"></i></span>
                <input type="text" class="form-control border-0 bg-white" placeholder="Filter Tanggal" readonly style="max-width: 150px;">
            </div>
        </div>
    </div>

    {{-- 2. TABEL CARD --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-body p-0">
            
            {{-- Wrapper Responsive --}}
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 text-nowrap">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4 py-3 text-secondary text-uppercase small fw-bold">Waktu & Tipe</th>
                            <th class="py-3 text-secondary text-uppercase small fw-bold">Rute Perjalanan</th>
                            <th class="py-3 text-secondary text-uppercase small fw-bold">Driver & Armada</th>
                            <th class="py-3 text-secondary text-uppercase small fw-bold">Status</th>
                            <th class="py-3 text-secondary text-uppercase small fw-bold text-end pe-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($trips as $trip)
                        <tr>
                            {{-- Waktu & Tipe --}}
                            <td class="ps-4 py-3">
                                <div class="d-flex flex-column">
                                    <span class="fw-bold text-dark d-flex align-items-center">
                                        <i class="bi bi-calendar-event me-2 text-primary"></i>
                                        {{ \Carbon\Carbon::parse($trip->date)->translatedFormat('d M Y') }}
                                    </span>
                                    <div class="mt-1 ms-4">
                                        @if($trip->type == 'pickup')
                                            <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 rounded-pill px-2 fw-normal">
                                                <i class="bi bi-sun-fill me-1"></i> Pagi (Jemput)
                                            </span>
                                        @else
                                            <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25 rounded-pill px-2 fw-normal">
                                                <i class="bi bi-moon-stars-fill me-1"></i> Sore (Antar)
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            {{-- Rute --}}
                            <td>
                                <span class="badge bg-light text-dark border fw-normal px-3 py-2 rounded-pill">
                                    <i class="bi bi-map-fill me-1 text-secondary"></i> {{ $trip->route->name ?? 'Rute Dihapus' }}
                                </span>
                            </td>

                            {{-- Driver & Armada --}}
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="bg-white border rounded-circle p-1 me-2 d-flex align-items-center justify-content-center text-secondary" style="width: 35px; height: 35px;">
                                        <i class="bi bi-person-badge"></i>
                                    </div>
                                    <div class="d-flex flex-column">
                                        <span class="fw-bold text-dark small">{{ $trip->driver->name ?? 'Driver Dihapus' }}</span>
                                        <span class="text-muted" style="font-size: 0.75rem;">
                                            <i class="bi bi-bus-front me-1"></i> {{ $trip->shuttle->plate_number ?? '-' }}
                                        </span>
                                    </div>
                                </div>
                            </td>

                            {{-- Status --}}
                            <td>
                                @if($trip->status == 'scheduled')
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 px-3 py-2 rounded-pill fw-normal">
                                        <i class="bi bi-clock me-1"></i> Terjadwal
                                    </span>
                                @elseif($trip->status == 'active')
                                    <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-3 py-2 rounded-pill fw-normal blink-soft">
                                        <span class="spinner-grow spinner-grow-sm me-1" style="width: 0.5rem; height: 0.5rem;"></span> Sedang Jalan
                                    </span>
                                @else
                                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 py-2 rounded-pill fw-normal">
                                        <i class="bi bi-check-circle-fill me-1"></i> Selesai
                                    </span>
                                @endif
                            </td>

                            {{-- Aksi --}}
                            <td class="text-end pe-4">
                                <div class="btn-group">
                                    <a href="{{ route('trips.show', $trip->id) }}" class="btn btn-sm btn-light text-primary border shadow-sm rounded-start" title="Lihat Detail">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    
                                    <form action="{{ route('trips.destroy', $trip->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus riwayat perjalanan ini? Data presensi siswa di dalamnya akan ikut terhapus.');">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-light text-danger border shadow-sm rounded-end border-start-0" title="Hapus Riwayat">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <div class="d-flex flex-column align-items-center justify-content-center opacity-50">
                                    <div class="bg-light rounded-circle p-3 mb-3">
                                        <i class="bi bi-clipboard-data display-4 text-secondary"></i>
                                    </div>
                                    <h5 class="fw-bold text-secondary">Belum ada riwayat</h5>
                                    <p class="text-muted small mb-0">Data perjalanan akan muncul di sini setelah jadwal dibuat.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination Check --}}
            @if(method_exists($trips, 'hasPages') && $trips->hasPages())
                <div class="card-footer bg-white py-3 border-top">
                    {{ $trips->links() }}
                </div>
            @endif

        </div>
    </div>
</div>

<style>
    /* Spacing tabel */
    .table > :not(caption) > * > * {
        padding: 1rem 0.5rem;
    }
    .table-hover tbody tr:hover {
        background-color: #f8f9fa;
    }
    
    /* Animasi kedip halus untuk status aktif */
    @keyframes blink-soft {
        0% { opacity: 1; }
        50% { opacity: 0.6; }
        100% { opacity: 1; }
    }
    .blink-soft { animation: blink-soft 2s infinite; }
</style>
@endsection