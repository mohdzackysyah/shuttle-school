@extends('layouts.admin')

@section('content')
<div class="d-flex justify-content-between align-items-end mb-4">
    <div>
        <h3 class="fw-bold text-dark">📋 Riwayat Perjalanan</h3>
        <p class="text-muted mb-0">Monitor status pelaksanaan dan histori perjalanan armada.</p>
    </div>
    {{-- Tombol Buat Baru dihapus sesuai permintaan --}}
    
    {{-- Opsional: Tambahan Filter Tanggal sederhana agar header tidak kosong --}}
    <div class="d-none d-md-block">
        <div class="input-group input-group-sm shadow-sm">
            <span class="input-group-text bg-white border-end-0"><i class="bi bi-calendar3"></i></span>
            <input type="text" class="form-control border-start-0" placeholder="Filter Tanggal" readonly style="max-width: 150px; background: white;">
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4 py-3">Waktu & Tipe</th>
                        <th>Rute Perjalanan</th>
                        <th>Driver & Armada</th>
                        <th>Status</th>
                        <th class="text-end pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($trips as $trip)
                    <tr>
                        {{-- Kolom Waktu --}}
                        <td class="ps-4">
                            <div class="d-flex flex-column">
                                <span class="fw-bold text-dark">
                                    {{ \Carbon\Carbon::parse($trip->date)->translatedFormat('d M Y') }}
                                </span>
                                <div class="mt-1">
                                    @if($trip->type == 'pickup')
                                        <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 rounded-pill">
                                            <i class="bi bi-sun-fill me-1"></i> Pagi
                                        </span>
                                    @else
                                        <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25 rounded-pill">
                                            <i class="bi bi-moon-stars-fill me-1"></i> Sore
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </td>

                        {{-- Kolom Rute --}}
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="bg-light rounded-circle p-2 me-2 text-secondary d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
                                    <i class="bi bi-map"></i>
                                </div>
                                <span class="fw-semibold text-dark">{{ $trip->route->name }}</span>
                            </div>
                        </td>

                        {{-- Kolom Driver --}}
                        <td>
                            <div class="d-flex flex-column">
                                <span class="fw-semibold text-dark">{{ $trip->driver->name }}</span>
                                <span class="small text-muted">
                                    <i class="bi bi-car-front me-1"></i> {{ $trip->shuttle->car_model }} 
                                    <span class="fw-bold text-secondary ms-1">({{ $trip->shuttle->plate_number }})</span>
                                </span>
                            </div>
                        </td>

                        {{-- Kolom Status --}}
                        <td>
                            @if($trip->status == 'scheduled')
                                <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 px-3 py-2 rounded-pill">
                                    <i class="bi bi-calendar-event me-1"></i> Terjadwal
                                </span>
                            @elseif($trip->status == 'active')
                                <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-3 py-2 rounded-pill">
                                    <span class="spinner-grow spinner-grow-sm me-1" role="status" aria-hidden="true" style="width: 0.5rem; height: 0.5rem;"></span>
                                    Sedang Jalan
                                </span>
                            @else
                                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 py-2 rounded-pill">
                                    <i class="bi bi-check-circle-fill me-1"></i> Selesai
                                </span>
                            @endif
                        </td>

                        {{-- Kolom Aksi --}}
                        <td class="text-end pe-4">
                            <a href="{{ route('trips.show', $trip->id) }}" class="btn btn-sm btn-outline-info me-1" title="Lihat Detail">
                                <i class="bi bi-eye"></i>
                            </a>
                            <form action="{{ route('trips.destroy', $trip->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus riwayat perjalanan ini? Data presensi siswa di dalamnya akan ikut terhapus.');">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger" title="Hapus Riwayat">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">
                            <div class="mb-2"><i class="bi bi-clipboard-x fs-1 opacity-50"></i></div>
                            <h6>Belum ada riwayat perjalanan.</h6>
                            <small>Data akan muncul otomatis saat jadwal dibuat atau trip dimulai.</small>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        {{-- Pagination (Opsional) --}}
        @if(method_exists($trips, 'links'))
            <div class="p-3 border-top d-flex justify-content-end">
                {{ $trips->links() }}
            </div>
        @endif
    </div>
</div>
@endsection