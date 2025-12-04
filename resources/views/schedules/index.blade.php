@extends('layouts.admin')

@section('content')
<div class="d-flex justify-content-between align-items-end mb-4">
    <div>
        <h3 class="fw-bold text-dark">📅 Master Jadwal Rutin</h3>
        <p class="text-muted mb-0">Kelola jadwal operasional & daftar penumpang.</p>
    </div>
    <a href="{{ route('schedules.create') }}" class="btn btn-primary shadow-sm rounded-pill px-4">
        <i class="bi bi-plus-lg me-2"></i> Buat Jadwal Baru
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
        @php 
            $firstItem = $routeSchedules->first(); 
            // Urutkan jadwal berdasarkan hari (Senin-Minggu)
            $schedulesInRoute = $routeSchedules->sortBy(function($s) {
                return array_search($s->day_of_week, ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday']);
            });
        @endphp

        <div class="accordion-item mb-3 shadow-sm border-0 overflow-hidden" style="border-radius: 8px;">
            
            {{-- HEADER ACCORDION --}}
            <h2 class="accordion-header" id="heading{{ $routeId }}">
                <div class="d-flex w-100 align-items-center bg-white border-bottom p-0">
                    
                    {{-- TOMBOL COLLAPSE (Kiri) --}}
                    <button class="accordion-button collapsed bg-white py-4 shadow-none flex-grow-1" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $routeId }}" aria-expanded="false" aria-controls="collapse{{ $routeId }}">
                        <div class="d-flex align-items-center w-100">
                            <div class="bg-light text-primary rounded-circle p-3 me-3 d-none d-md-block">
                                <i class="bi bi-geo-alt-fill fs-4"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold text-dark mb-1">{{ $firstItem->route->name }}</h5>
                                <div class="text-muted small">
                                    <span class="badge bg-secondary text-light me-2">{{ $schedulesInRoute->count() }} Hari Aktif</span>
                                    <i class="bi bi-person-badge"></i> {{ $firstItem->driver->name }} 
                                    <span class="mx-2">|</span> 
                                    <i class="bi bi-car-front"></i> {{ $firstItem->shuttle->plate_number }}
                                </div>
                            </div>
                        </div>
                    </button>

                    {{-- TOMBOL EDIT RANGKAIAN (Kanan) --}}
                    <div class="pe-4 bg-white py-4">
                        <a href="{{ route('schedules.editBulk', $routeId) }}" class="btn btn-warning btn-sm fw-bold shadow-sm text-dark">
                            <i class="bi bi-pencil-square me-1"></i> Edit Rangkaian
                        </a>
                    </div>

                </div>
            </h2>

            {{-- ISI ACCORDION (Tabel Jadwal Harian) --}}
            <div id="collapse{{ $routeId }}" class="accordion-collapse collapse" aria-labelledby="heading{{ $routeId }}" data-bs-parent="#scheduleAccordion">
                <div class="accordion-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4" width="15%">Hari</th>
                                    <th width="35%">Jam Operasional</th>
                                    <th width="35%">Penumpang</th>
                                    <th class="text-center" width="15%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($schedulesInRoute as $s)
                                <tr>
                                    <td class="ps-4 fw-bold text-secondary">
                                        {{ $indoDays[$s->day_of_week] ?? $s->day_of_week }}
                                    </td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            @if($s->pickup_time)
                                                <span class="badge bg-warning text-dark border border-warning">
                                                    🌞 Jemput: {{ \Carbon\Carbon::parse($s->pickup_time)->format('H:i') }}
                                                </span>
                                            @endif
                                            @if($s->dropoff_time)
                                                <span class="badge bg-info text-white border border-info">
                                                    🌥️ Antar: {{ \Carbon\Carbon::parse($s->dropoff_time)->format('H:i') }}
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="me-2">
                                                <span class="badge bg-light text-dark border">
                                                    <i class="bi bi-people-fill me-1"></i> 
                                                    {{ $s->students->count() }} Siswa
                                                </span>
                                            </div>
                                            <small class="text-muted text-truncate" style="max-width: 200px;">
                                                {{ $s->students->take(3)->pluck('name')->join(', ') }}
                                                {{ $s->students->count() > 3 ? '...' : '' }}
                                            </small>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <form action="{{ route('schedules.destroy', $s->id) }}" method="POST" onsubmit="return confirm('Hapus jadwal hari {{ $indoDays[$s->day_of_week] }}?');">
                                            @csrf @method('DELETE')
                                            
                                            <a href="{{ route('schedules.edit', $s->id) }}" class="btn btn-sm btn-outline-primary me-1" title="Edit Hari Ini">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus Jadwal">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
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
            <h3 class="text-muted mb-3">📭</h3>
            <p class="lead text-muted">Belum ada jadwal rutin yang dibuat.</p>
            <a href="{{ route('schedules.create') }}" class="btn btn-primary rounded-pill px-4">Buat Jadwal Sekarang</a>
        </div>
    @endforelse
</div>

<style>
    .accordion-button:not(.collapsed) { background-color: #f0f7ff; color: #000; box-shadow: none; }
    .accordion-button:focus { box-shadow: none; border-color: rgba(0,0,0,.125); }
</style>
@endsection