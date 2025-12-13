@if($tripData)
                        
    <div class="bg-light rounded-3 p-3 mb-3 border border-light">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <span class="badge {{ $tripData->trip->type == 'pickup' ? 'bg-warning text-dark' : 'bg-info text-white' }}">
                {{ $tripData->trip->type == 'pickup' ? 'Rute Pagi' : 'Rute Sore' }}
            </span>
            <span class="fw-bold text-dark small">{{ $tripData->trip->route->name }}</span>
        </div>
        <div class="small text-muted">
            <div class="mb-1"><i class="bi bi-person-badge me-2"></i> {{ $tripData->trip->driver->name }}</div>
            <div><i class="bi bi-car-front me-2"></i> {{ $tripData->trip->shuttle->plate_number }}</div>
        </div>
    </div>

    @php
        $statusClass = 'bg-secondary bg-opacity-10 text-secondary border-secondary';
        $statusIcon = 'bi-question-circle';
        $statusText = 'UNKNOWN';
        $statusDesc = 'Status tidak diketahui';

        if($tripData->status == 'pending') {
            $statusClass = 'bg-info bg-opacity-10 text-info border border-info';
            $statusIcon = 'bi-hourglass-split';
            $statusText = 'MENUNGGU';
            $statusDesc = 'Driver dalam perjalanan';
        } 
        // [BARU] STATUS WAITING
        elseif($tripData->status == 'waiting') {
            $statusClass = 'bg-warning border border-warning shadow-sm animate__animated animate__pulse animate__infinite';
            $statusIcon = 'bi-geo-alt-fill text-dark';
            $statusText = 'DRIVER SAMPAI!';
            $statusDesc = 'Driver menunggu di depan rumah';
        }
        elseif($tripData->status == 'picked_up') {
            $statusClass = 'bg-primary bg-opacity-10 text-primary border border-primary';
            $statusIcon = 'bi-bus-front';
            $statusText = 'DI DALAM MOBIL';
            $statusDesc = 'Sedang dalam perjalanan';
        } elseif($tripData->status == 'dropped_off') {
            $statusClass = 'bg-success bg-opacity-10 text-success border border-success';
            $statusIcon = 'bi-check-circle-fill';
            $statusText = 'SUDAH SAMPAI';
            $statusDesc = $tripData->trip->type == 'pickup' ? 'Tiba di Sekolah' : 'Tiba di Rumah';
        } elseif($tripData->status == 'absent') {
            $statusClass = 'bg-danger bg-opacity-10 text-danger border border-danger';
            $statusIcon = 'bi-x-circle-fill';
            $statusText = 'IZIN / ABSEN';
            $statusDesc = 'Anak tidak mengikuti layanan ini';
        }
    @endphp

    <div class="text-center py-4 rounded-3 mb-3 {{ $statusClass }}">
        <h2 class="fw-bold mb-0 {{ $tripData->status == 'waiting' ? 'text-dark' : '' }}">
            <i class="bi {{ $statusIcon }}"></i> {{ $statusText }}
        </h2>
        <small class="{{ $tripData->status == 'waiting' ? 'text-dark fw-bold' : '' }}">{{ $statusDesc }}</small>
        
        @if($tripData->status == 'dropped_off' && $tripData->dropped_at)
            <div class="mt-1 fw-bold small">
                {{ \Carbon\Carbon::parse($tripData->dropped_at)->format('H:i') }} WIB
            </div>
        @endif
    </div>

    <div class="d-grid gap-2">
        @if($tripData->status != 'absent')
            <a href="{{ route('parents.trip.detail', $tripData->id) }}" class="btn btn-primary shadow-sm fw-bold">
                <i class="bi bi-info-circle me-2"></i> DETAIL DRIVER
            </a>
        @endif

        @if($tripData->status == 'pending' || $tripData->status == 'waiting')
            <form action="{{ route('parents.set_absent', $tripData->student_id) }}" method="POST" onsubmit="return confirm('Yakin ingin mengizinkan anak?')">
                @csrf
                <button class="btn btn-outline-danger w-100 fw-bold">
                    <i class="bi bi-envelope-x me-2"></i> Laporkan Izin
                </button>
            </form>
        @endif
    </div>

@else
    <div class="text-center py-5 text-muted">
        <i class="bi bi-moon-stars fs-1 opacity-25"></i>
        <p class="mt-2 mb-0 fw-bold">Jadwal {{ $type }} Belum Dimulai</p>
        <small>Menunggu Driver memulai perjalanan.</small>
    </div>
@endif