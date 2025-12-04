@extends('layouts.admin')

@section('content')
<div class="d-flex justify-content-between align-items-end mb-4">
    <div>
        <h3 class="fw-bold text-dark">📅 Master Jadwal Rutin</h3>
        <p class="text-muted mb-0">Kelola jadwal operasional, driver, dan daftar penumpang.</p>
    </div>
    <a href="{{ route('schedules.create') }}" class="btn btn-primary shadow-sm">
        <i class="bi bi-plus-lg me-1"></i> Buat Jadwal Baru
    </a>
</div>

@php
    $indoDays = [
        'Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu',
        'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu', 'Sunday' => 'Minggu'
    ];
@endphp

<div class="accordion custom-accordion" id="scheduleAccordion">
    @forelse($schedules as $routeId => $routeSchedules)
        @php 
            $firstItem = $routeSchedules->first(); 
            // Urutkan jadwal berdasarkan hari (Senin-Minggu)
            $schedulesInRoute = $routeSchedules->sortBy(function($s) {
                return array_search($s->day_of_week, ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday']);
            });
        @endphp

        <div class="card border-0 shadow-sm rounded-4 mb-3 overflow-hidden">
            {{-- HEADER WRAPPER --}}
            <div class="card-header bg-white p-0 border-0">
                <div class="d-flex align-items-center">
                    
                    {{-- 1. TOMBOL TRIGGER ACCORDION --}}
                    <button class="accordion-button collapsed shadow-none bg-white p-4" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $routeId }}" aria-expanded="false">
                        <div class="d-flex align-items-center gap-3 w-100 me-3">
                            {{-- Icon Rute --}}
                            <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 50px; height: 50px;">
                                <i class="bi bi-map-fill fs-4"></i>
                            </div>
                            
                            {{-- Info Rute --}}
                            <div>
                                <h5 class="fw-bold text-dark mb-1">{{ $firstItem->route->name }}</h5>
                                <div class="d-flex align-items-center text-muted small gap-3 flex-wrap">
                                    <span>
                                        <i class="bi bi-person-badge me-1 text-secondary"></i> {{ $firstItem->driver->name }}
                                    </span>
                                    <span class="d-none d-sm-inline text-light-gray">|</span>
                                    <span>
                                        <i class="bi bi-bus-front me-1 text-secondary"></i> {{ $firstItem->shuttle->plate_number }}
                                    </span>
                                    <span class="d-none d-sm-inline text-light-gray">|</span>
                                    <span class="badge bg-light text-secondary border">
                                        {{ $schedulesInRoute->count() }} Hari Aktif
                                    </span>
                                </div>
                            </div>
                        </div>
                    </button>

                    {{-- 2. TOMBOL EDIT RANGKAIAN (Terpisah agar tidak ikut toggle) --}}
                    <div class="py-3 pe-4 ps-2 bg-white border-start-0 d-none d-md-block">
                        <a href="{{ route('schedules.editBulk', $routeId) }}" class="btn btn-sm btn-outline-warning text-nowrap">
                            <i class="bi bi-pencil-square me-1"></i> Edit Rangkaian
                        </a>
                    </div>
                </div>
            </div>

            {{-- BODY ACCORDION (Tabel) --}}
            <div id="collapse{{ $routeId }}" class="accordion-collapse collapse" data-bs-parent="#scheduleAccordion">
                <div class="accordion-body p-0 border-top">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4 py-3" width="15%">Hari</th>
                                    <th width="30%">Jam Operasional</th>
                                    <th width="40%">Penumpang</th>
                                    <th class="text-end pe-4" width="15%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($schedulesInRoute as $s)
                                <tr>
                                    <td class="ps-4">
                                        <span class="fw-bold text-dark">{{ $indoDays[$s->day_of_week] ?? $s->day_of_week }}</span>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column gap-1">
                                            @if($s->pickup_time)
                                                <div class="d-flex align-items-center">
                                                    <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 rounded-pill me-2" style="width: 80px;">
                                                        <i class="bi bi-sun-fill me-1"></i> Pagi
                                                    </span>
                                                    <span class="fw-bold text-dark">{{ \Carbon\Carbon::parse($s->pickup_time)->format('H:i') }}</span>
                                                </div>
                                            @endif
                                            @if($s->dropoff_time)
                                                <div class="d-flex align-items-center">
                                                    <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25 rounded-pill me-2" style="width: 80px;">
                                                        <i class="bi bi-moon-stars-fill me-1"></i> Sore
                                                    </span>
                                                    <span class="fw-bold text-dark">{{ \Carbon\Carbon::parse($s->dropoff_time)->format('H:i') }}</span>
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-start">
                                            <span class="badge bg-light text-dark border me-2 mt-1">
                                                <i class="bi bi-people me-1"></i> {{ $s->students->count() }}
                                            </span>
                                            <small class="text-muted lh-sm">
                                                {{ $s->students->take(5)->pluck('name')->join(', ') }}
                                                @if($s->students->count() > 5)
                                                    <span class="fst-italic text-secondary">+{{ $s->students->count() - 5 }} lainnya</span>
                                                @endif
                                            </small>
                                        </div>
                                    </td>
                                    <td class="text-end pe-4">
                                        <form action="{{ route('schedules.destroy', $s->id) }}" method="POST" onsubmit="return confirm('Hapus jadwal hari {{ $indoDays[$s->day_of_week] }}?');">
                                            @csrf @method('DELETE')
                                            
                                            <a href="{{ route('schedules.edit', $s->id) }}" class="btn btn-sm btn-outline-secondary me-1" title="Edit Hari Ini">
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
                    
                    {{-- Tombol Edit Rangkaian Mobile (Hanya muncul di HP) --}}
                    <div class="p-3 text-center d-md-none bg-light border-top">
                        <a href="{{ route('schedules.editBulk', $routeId) }}" class="btn btn-warning w-100">
                            <i class="bi bi-pencil-square me-1"></i> Edit Rangkaian Jadwal
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="text-center py-5">
            <div class="mb-3">
                <i class="bi bi-calendar-x text-muted" style="font-size: 3rem; opacity: 0.5;"></i>
            </div>
            <h5 class="text-muted">Belum ada jadwal rutin.</h5>
            <p class="text-muted small mb-4">Mulai dengan membuat jadwal baru untuk rute yang tersedia.</p>
            <a href="{{ route('schedules.create') }}" class="btn btn-primary px-4 rounded-pill">
                Buat Jadwal Sekarang
            </a>
        </div>
    @endforelse
</div>

{{-- Custom CSS untuk Accordion Arrow & Hover --}}
<style>
    /* Hilangkan background biru default bootstrap saat accordion aktif */
    .accordion-button:not(.collapsed) {
        background-color: #fff;
        color: var(--dark);
        box-shadow: inset 0 -1px 0 rgba(0,0,0,.125);
    }
    
    /* Warna panah accordion */
    .accordion-button::after {
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%2364748b'%3e%3cpath fill-rule='evenodd' d='M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z'/%3e%3c/svg%3e");
    }
    .accordion-button:not(.collapsed)::after {
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%234f46e5'%3e%3cpath fill-rule='evenodd' d='M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z'/%3e%3c/svg%3e");
    }

    /* Efek Hover pada baris header */
    .accordion-button:hover {
        background-color: #f8fafc;
    }
</style>
@endsection