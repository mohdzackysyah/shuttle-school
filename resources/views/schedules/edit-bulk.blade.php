@extends('layouts.admin')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-10">
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold text-warning">✏️ Edit Rangkaian Jadwal</h4>
                <p class="text-muted mb-0">Ubah Driver, Mobil, dan Penumpang untuk <strong>{{ $firstSchedule->route->name }}</strong> sekaligus.</p>
            </div>
            <a href="{{ route('schedules.index') }}" class="btn btn-light border shadow-sm">
                <i class="bi bi-arrow-left"></i> Batal
            </a>
        </div>

        {{-- PERBAIKAN DISINI: Menggunakan $firstSchedule->id (ID Jadwal), BUKAN route_id --}}
        <form action="{{ route('schedules.updateBulk', $firstSchedule->id) }}" method="POST">
            @csrf
            @method('PUT')

            {{-- 1. PENGATURAN UMUM --}}
            <div class="card shadow-sm border-0 mb-4 rounded-4">
                <div class="card-header bg-warning text-dark py-3 fw-bold">
                    <i class="bi bi-sliders"></i> Pengaturan Umum
                </div>
                <div class="card-body bg-light bg-opacity-50">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-bold small text-uppercase">Rute (Read-only)</label>
                            <input type="text" class="form-control" value="{{ $firstSchedule->route->name }}" disabled>
                            {{-- Input hidden route_id tetap diperlukan untuk logika controller jika buat jadwal baru --}}
                            <input type="hidden" name="route_id" value="{{ $firstSchedule->route_id }}">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-bold small text-uppercase">Supir</label>
                            <div class="input-group">
                                <select name="driver_id" class="form-select" required>
                                    <option value="">-- Pilih Driver --</option>
                                    @foreach($drivers as $d) 
                                        <option value="{{ $d->id }}" {{ $firstSchedule->driver_id == $d->id ? 'selected' : '' }}>
                                            {{ $d->name }}
                                        </option> 
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-text text-muted small">*Hanya driver yang belum punya jadwal lain.</div>
                        </div>
                        
                        <div class="col-md-4">
                            <label class="form-label fw-bold small text-uppercase">Mobil</label>
                            <select name="shuttle_id" id="shuttle_selector" class="form-select" onchange="updateCapacity()" required>
                                <option value="" data-capacity="0">-- Pilih Mobil --</option>
                                @foreach($shuttles as $s) 
                                    <option value="{{ $s->id }}" data-capacity="{{ $s->capacity }}" {{ $firstSchedule->shuttle_id == $s->id ? 'selected' : '' }}>
                                        {{ $s->plate_number }} (Kap: {{ $s->capacity }})
                                    </option> 
                                @endforeach
                            </select>
                            <div class="form-text text-muted small">*Hanya mobil yang tersedia.</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 2. PILIH SISWA --}}
            <div class="card shadow-sm border-0 mb-4 rounded-4">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold text-dark"><i class="bi bi-people"></i> Atur Penumpang (Berlaku Semua Hari)</h6>
                    <span class="badge bg-secondary" id="capacity-badge">
                        Terpilih: <span id="selected-count">0</span> / <span id="max-capacity">0</span>
                    </span>
                </div>
                <div class="card-body">
                    <div class="alert alert-info py-2 small">
                        <i class="bi bi-info-circle me-1"></i> Daftar di bawah ini hanya menampilkan siswa yang <strong>belum memiliki jadwal</strong> atau siswa yang <strong>sudah ada di jadwal ini</strong>.
                    </div>

                    <div class="row g-2">
                        @forelse($availableStudents as $s)
                            <div class="col-md-6">
                                <div class="form-check bg-white border rounded p-3 h-100 shadow-sm student-card">
                                    <input class="form-check-input student-checkbox" type="checkbox" name="student_ids[]" 
                                           value="{{ $s->id }}" id="s-{{ $s->id }}" 
                                           {{ in_array($s->id, $existingStudentIds) ? 'checked' : '' }} 
                                           onchange="countSelected()">
                                    <label class="form-check-label w-100" for="s-{{ $s->id }}" style="cursor:pointer;">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <strong>{{ $s->name }}</strong>
                                            <span class="badge bg-light text-dark border">{{ $s->complex->name ?? '?' }}</span>
                                        </div>
                                        <small class="text-muted d-block mt-1 text-truncate">{{ $s->address_note ?? '-' }}</small>
                                    </label>
                                </div>
                            </div>
                        @empty
                            <div class="col-12 text-center text-muted py-4 border rounded bg-light">
                                <i class="bi bi-person-x fs-1"></i>
                                <p class="mt-2 mb-0">Tidak ada siswa yang tersedia di rute ini (Mungkin semua sudah masuk jadwal lain).</p>
                            </div>
                        @endforelse
                    </div>
                    <div id="capacity-alert" class="alert alert-danger mt-3 d-none">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i> <strong>Kapasitas Penuh!</strong> Kurangi siswa.
                    </div>
                </div>
            </div>

            {{-- 3. ATUR JADWAL MINGGUAN --}}
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="mb-0 fw-bold text-dark"><i class="bi bi-clock"></i> Atur Jam Operasional</h6>
                </div>
                <div class="card-body p-0">
                    @php $days = ['Monday'=>'Senin','Tuesday'=>'Selasa','Wednesday'=>'Rabu','Thursday'=>'Kamis','Friday'=>'Jumat','Saturday'=>'Sabtu','Sunday'=>'Minggu']; @endphp
                    <div class="list-group list-group-flush">
                        @foreach($days as $eng => $indo)
                        
                        @php 
                            // Cek apakah hari ini ada di database?
                            $currentDay = $mappedSchedules[$eng] ?? null;
                            $isActive = $currentDay ? true : false;
                        @endphp

                        <div class="list-group-item p-3">
                            {{-- Input Hidden untuk memastikan array key hari tetap terkirim meski unchecked --}}
                            <input type="hidden" name="days[{{ $eng }}][day_key]" value="{{ $eng }}">

                            <div class="d-flex align-items-center gap-3">
                                <div class="form-check form-switch" style="min-width: 120px;">
                                    <input class="form-check-input" type="checkbox" name="days[{{ $eng }}][active]" value="1" 
                                           id="check-{{ $eng }}" onchange="toggleDay('{{ $eng }}')" 
                                           {{ $isActive ? 'checked' : '' }}
                                           style="cursor: pointer; width: 3em; height: 1.5em;">
                                    <label class="form-check-label fw-bold ms-2 mt-1" for="check-{{ $eng }}">{{ $indo }}</label>
                                </div>
                                <div class="row g-2 flex-grow-1 {{ $isActive ? '' : 'opacity-25' }}" id="input-row-{{ $eng }}" style="pointer-events: {{ $isActive ? 'auto' : 'none' }};">
                                    <div class="col-md-6">
                                        <div class="input-group">
                                            <span class="input-group-text bg-warning border-warning text-dark">🌞 Jemput</span>
                                            <input type="time" name="days[{{ $eng }}][pickup_time]" class="form-control border-warning" 
                                                   value="{{ $currentDay && $currentDay->pickup_time ? \Carbon\Carbon::parse($currentDay->pickup_time)->format('H:i') : '' }}"
                                                   {{ $isActive ? '' : 'disabled' }}>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="input-group">
                                            <span class="input-group-text bg-info border-info text-white">🌥️ Antar</span>
                                            <input type="time" name="days[{{ $eng }}][dropoff_time]" class="form-control border-info" 
                                                   value="{{ $currentDay && $currentDay->dropoff_time ? \Carbon\Carbon::parse($currentDay->dropoff_time)->format('H:i') : '' }}"
                                                   {{ $isActive ? '' : 'disabled' }}>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                <div class="card-footer bg-white d-flex justify-content-end py-4 border-top-0">
                    <button type="submit" id="btn-submit" class="btn btn-warning btn-lg px-5 shadow rounded-pill fw-bold">Update Semua</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    let maxCapacity = 0;
    
    document.addEventListener('DOMContentLoaded', function() {
        updateCapacity();
        countSelected();
    });

    function updateCapacity() {
        const selector = document.getElementById('shuttle_selector');
        const selectedOption = selector.options[selector.selectedIndex];
        
        // Handle jika belum ada mobil dipilih (misal data kosong)
        if (selectedOption) {
            maxCapacity = parseInt(selectedOption.getAttribute('data-capacity')) || 0;
        } else {
            maxCapacity = 0;
        }
        
        document.getElementById('max-capacity').innerText = maxCapacity;
        checkCapacityLimit();
    }

    function countSelected() {
        const count = document.querySelectorAll('.student-checkbox:checked').length;
        document.getElementById('selected-count').innerText = count;
        checkCapacityLimit();
    }

    function checkCapacityLimit() {
        const count = document.querySelectorAll('.student-checkbox:checked').length;
        const alertBox = document.getElementById('capacity-alert');
        const submitBtn = document.getElementById('btn-submit');
        const badge = document.getElementById('capacity-badge');

        if (maxCapacity > 0 && count > maxCapacity) {
            alertBox.classList.remove('d-none');
            submitBtn.disabled = true;
            badge.classList.remove('bg-secondary', 'bg-success');
            badge.classList.add('bg-danger');
        } else {
            alertBox.classList.add('d-none');
            // Enable button jika minimal ada 1 siswa terpilih dan 1 mobil terpilih
            if(count > 0 && maxCapacity > 0) {
                submitBtn.disabled = false;
                badge.classList.remove('bg-secondary', 'bg-danger');
                badge.classList.add('bg-success');
            } else {
                submitBtn.disabled = true; // Kunci jika 0 siswa
                badge.classList.remove('bg-success', 'bg-danger');
                badge.classList.add('bg-secondary');
            }
        }
    }

    function toggleDay(day) {
        var checkBox = document.getElementById("check-" + day);
        var inputRow = document.getElementById("input-row-" + day);
        var inputs = inputRow.querySelectorAll('input[type="time"]');

        if (checkBox.checked) {
            inputRow.classList.remove("opacity-25");
            inputRow.style.pointerEvents = "auto";
            inputs.forEach(el => el.disabled = false);
        } else {
            inputRow.classList.add("opacity-25");
            inputRow.style.pointerEvents = "none";
            inputs.forEach(el => {
                el.disabled = true;
                // Opsional: el.value = ''; // Jika ingin mereset jam saat di-uncheck
            });
        }
    }
</script>
@endsection