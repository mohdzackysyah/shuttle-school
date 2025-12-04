@extends('layouts.admin')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-10">
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold text-primary">✏️ Edit Jadwal & Penumpang</h4>
                <p class="text-muted mb-0">Hari: <strong>{{ $schedule->day_of_week }}</strong> | Rute: <strong>{{ $schedule->route->name }}</strong></p>
            </div>
            <a href="{{ route('schedules.index') }}" class="btn btn-light border shadow-sm">
                <i class="bi bi-arrow-left"></i> Batal
            </a>
        </div>

        <form action="{{ route('schedules.update', $schedule->id) }}" method="POST">
            @csrf
            @method('PUT')

            {{-- 1. PENGATURAN UMUM --}}
            <div class="card shadow-sm border-0 mb-4 rounded-4">
                <div class="card-header bg-warning text-dark py-3 fw-bold">
                    <i class="bi bi-sliders"></i> Pengaturan Driver & Waktu
                </div>
                <div class="card-body bg-light bg-opacity-50">
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-uppercase">Supir</label>
                            <select name="driver_id" class="form-select">
                                @foreach($drivers as $d) 
                                    <option value="{{ $d->id }}" {{ $schedule->driver_id == $d->id ? 'selected' : '' }}>{{ $d->name }}</option> 
                                @endforeach
                            </select>
                            <div class="form-text text-muted small">*Hanya driver yang belum punya jadwal lain di jam ini.</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-uppercase">Mobil</label>
                            <select name="shuttle_id" id="shuttle_selector" class="form-select" onchange="updateCapacity()">
                                @foreach($shuttles as $s) 
                                    <option value="{{ $s->id }}" data-capacity="{{ $s->capacity }}" {{ $schedule->shuttle_id == $s->id ? 'selected' : '' }}>
                                        {{ $s->plate_number }} (Kap: {{ $s->capacity }})
                                    </option> 
                                @endforeach
                            </select>
                            <div class="form-text text-muted small">*Hanya mobil yang tersedia.</div>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="input-group">
                                <span class="input-group-text bg-warning border-warning">🌞 Jemput</span>
                                <input type="time" name="pickup_time" class="form-control border-warning" 
                                       value="{{ $schedule->pickup_time ? \Carbon\Carbon::parse($schedule->pickup_time)->format('H:i') : '' }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="input-group">
                                <span class="input-group-text bg-info border-info text-white">🌥️ Antar</span>
                                <input type="time" name="dropoff_time" class="form-control border-info" 
                                       value="{{ $schedule->dropoff_time ? \Carbon\Carbon::parse($schedule->dropoff_time)->format('H:i') : '' }}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 2. EDIT PENUMPANG --}}
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold text-dark"><i class="bi bi-people"></i> Atur Penumpang</h6>
                    <span class="badge bg-secondary" id="capacity-badge">
                        Terpilih: <span id="selected-count">0</span> / <span id="max-capacity">0</span>
                    </span>
                </div>
                <div class="card-body">
                    
                    <div class="alert alert-info py-2 small">
                        <i class="bi bi-info-circle me-1"></i> Daftar ini menampilkan siswa yang <strong>belum memiliki jadwal</strong> atau siswa yang <strong>sudah ada di jadwal ini</strong>.
                    </div>

                    <div class="row g-2">
                        @forelse($availableStudents as $s)
                            <div class="col-md-6">
                                <div class="form-check bg-white border rounded p-3 h-100 shadow-sm student-card">
                                    <input class="form-check-input student-checkbox" type="checkbox" name="student_ids[]" 
                                           value="{{ $s->id }}" id="s-{{ $s->id }}" 
                                           {{ in_array($s->id, $selectedStudentIds) ? 'checked' : '' }} 
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
                                <p class="mt-2 mb-0">Tidak ada siswa yang tersedia (Semua siswa di rute ini mungkin sudah punya jadwal lain).</p>
                            </div>
                        @endforelse
                    </div>

                    <div id="capacity-alert" class="alert alert-danger mt-3 d-none">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i> <strong>Kapasitas Penuh!</strong> Kurangi jumlah siswa.
                    </div>

                </div>
                <div class="card-footer bg-white d-flex justify-content-end py-4 border-top-0">
                    <button type="submit" id="btn-submit" class="btn btn-warning px-5 fw-bold shadow-sm">Simpan Perubahan</button>
                </div>
            </div>

        </form>
    </div>
</div>

<script>
    let maxCapacity = 0;
    
    // Inisialisasi saat halaman dimuat
    document.addEventListener('DOMContentLoaded', function() {
        updateCapacity(); 
        countSelected(); 
    });

    function updateCapacity() {
        const selector = document.getElementById('shuttle_selector');
        const selectedOption = selector.options[selector.selectedIndex];
        
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
            submitBtn.disabled = false;
            badge.classList.remove('bg-secondary', 'bg-danger');
            badge.classList.add('bg-success');
        }
    }
</script>
@endsection