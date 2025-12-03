@extends('layouts.admin')

@section('content')
<div class="d-flex justify-content-between align-items-end mb-4">
    <div>
        <h3 class="fw-bold text-dark">📅 Master Jadwal Rutin</h3>
        <p class="text-muted mb-0">Kelola jadwal operasional mingguan (Senin - Minggu).</p>
    </div>
    <a href="{{ route('schedules.create') }}" class="btn btn-primary shadow-sm">
        <i class="bi bi-plus-lg"></i> Tambah Jadwal
    </a>
</div>
    @php
        $indoDays = [
            'Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu', 'Sunday' => 'Minggu'
        ];
    @endphp

    <div class="accordion" id="scheduleAccordion">
        
        @forelse($schedules as $routeId => $routeSchedules)
            @php $firstItem = $routeSchedules->first(); @endphp

            <div class="accordion-item mb-3 shadow-sm border-0 overflow-hidden" style="border-radius: 8px;">
                
                <h2 class="accordion-header" id="heading{{ $routeId }}">
                    <button class="accordion-button collapsed bg-white py-4" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $routeId }}" aria-expanded="false" aria-controls="collapse{{ $routeId }}">
                        <div class="d-flex align-items-center w-100">
                            <div class="bg-light text-primary rounded-circle p-3 me-3 d-none d-md-block">
                                <i class="bi bi-geo-alt-fill fs-4"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold text-dark mb-1">{{ $firstItem->route->name }}</h5>
                                <div class="text-muted small">
                                    <i class="bi bi-person-badge"></i> <strong>{{ $firstItem->driver->name }}</strong> 
                                    <span class="mx-2">|</span> 
                                    <i class="bi bi-car-front"></i> {{ $firstItem->shuttle->plate_number }}
                                </div>
                            </div>
                        </div>
                    </button>
                </h2>

                <div id="collapse{{ $routeId }}" class="accordion-collapse collapse" aria-labelledby="heading{{ $routeId }}" data-bs-parent="#scheduleAccordion">
                    <div class="accordion-body p-0">
                        <div class="table-responsive">
                            <table class="table table-bordered mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-4" width="20%">Hari</th>
                                        <th width="65%">Jadwal Operasional</th>
                                        <th class="text-center" width="15%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($routeSchedules->groupBy('day_of_week') as $day => $schedulesOfDay)
                                    <tr>
                                        <td class="ps-4 align-middle fw-bold bg-light">
                                            {{ $indoDays[$day] ?? $day }}
                                        </td>
                                        <td class="p-0">
                                            <table class="table table-borderless mb-0">
                                                @foreach($schedulesOfDay as $s)
                                                <tr class="{{ !$loop->last ? 'border-bottom' : '' }}">
                                                    <td width="30%" class="text-primary fw-bold ps-3">
                                                        {{ \Carbon\Carbon::parse($s->departure_time)->format('H:i') }} WIB
                                                    </td>
                                                    <td>
                                                        @if($s->type == 'pickup')
                                                            <span class="badge bg-warning text-dark">🌞 Penjemputan</span>
                                                        @else
                                                            <span class="badge bg-info text-dark">🌥️ Pengantaran</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </table>
                                        </td>
                                        <td class="align-middle text-center">
                                            <a href="{{ route('schedules.edit', $schedulesOfDay->first()->id) }}" class="btn btn-sm btn-outline-warning px-3">
                                                <i class="bi bi-pencil-square"></i> Edit
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-5">
                <h3 class="text-muted">📭</h3>
                <p class="lead">Belum ada jadwal rutin.</p>
                <a href="{{ route('schedules.create') }}" class="btn btn-primary">Buat Jadwal Sekarang</a>
            </div>
        @endforelse
    </div>
</div>

<style>
    .accordion-button:not(.collapsed) { background-color: #eef5ff; color: #000; }
    .accordion-button:focus { box-shadow: none; border-color: rgba(0,0,0,.125); }
</style>
@endsection