@extends('layouts.admin')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-10">
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold text-primary">📅 Buat Jadwal Baru</h4>
                <p class="text-muted mb-0">Atur Driver, Mobil, dan Penumpang Tetap.</p>
            </div>
            <a href="{{ route('schedules.index') }}" class="btn btn-light border shadow-sm">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>

        <form action="{{ route('schedules.store') }}" method="POST">
            @csrf

            {{-- 1. PILIH RUTE, DRIVER, MOBIL --}}
            <div class="card shadow-sm border-0 mb-4 rounded-4">
                <div class="card-header bg-primary text-white py-3 border-bottom">
                    <h6 class="mb-0 fw-bold"><i class="bi bi-sliders"></i> Pengaturan Umum</h6>
                </div>
                <div class="card-body bg-light bg-opacity-50">
                    <div class="row g-3">
                        
                        <div class="col-md-4">
                            <label class="form-label fw-bold small text-uppercase">Pilih Rute</label>
                            <select name="route_id" id="route_selector" class="form-select form-select-lg" required onchange="loadStudents()">
                                <option value="">-- Pilih Rute --</option>
                                @foreach($routes as $r) 
                                    <option value="{{ $r->id }}">{{ $r->name }}</option> 
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-bold small text-uppercase">Supir</label>
                            <select name="driver_id" id="driver_selector" class="form-select form-select-lg" required onchange="recheckAllTimes()">
                                <option value="">-- Pilih Driver --</option>
                                @foreach($drivers as $d) <option value="{{ $d->id }}">{{ $d->name }}</option> @endforeach
                            </select>
                        </div>
                        
                        <div class="col-md-4">
                            <label class="form-label fw-bold small text-uppercase">Mobil</label>
                            <select name="shuttle_id" id="shuttle_selector" class="form-select form-select-lg" required onchange="updateCapacity()">
                                <option value="" data-capacity="0">-- Pilih Mobil --</option>
                                @foreach($shuttles as $s) 
                                    {{-- Simpan kapasitas di atribut data-capacity --}}
                                    <option value="{{ $s->id }}" data-capacity="{{ $s->capacity }}">
                                        {{ $s->plate_number }} (Kap: {{ $s->capacity }})
                                    </option> 
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 2. PILIH SISWA (SESUAI RUTE) --}}
            <div class="card shadow-sm border-0 mb-4 rounded-4">
                <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold text-dark"><i class="bi bi-people"></i> Pilih Penumpang</h6>
                    
                    {{-- INDIKATOR KAPASITAS --}}
                    <span class="badge bg-secondary" id="capacity-badge">
                        Terpilih: <span id="selected-count">0</span> / <span id="max-capacity">0</span>
                    </span>
                </div>
                <div class="card-body">
                    
                    <div id="student-loading" class="text-center py-3 d-none">
                        <div class="spinner-border text-primary" role="status"></div>
                        <p class="text-muted small mt-2">Sedang memuat data siswa...</p>
                    </div>

                    <div id="student-container" class="row g-2">
                        <div class="col-12 text-center text-muted py-3">
                            <i class="bi bi-info-circle"></i> Silakan pilih <strong>Rute</strong> terlebih dahulu untuk melihat daftar siswa.
                        </div>
                    </div>

                    <div id="capacity-alert" class="alert alert-danger mt-3 d-none">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        <strong>Kapasitas Penuh!</strong> Jumlah siswa melebihi kursi yang tersedia di mobil ini.
                    </div>

                </div>
            </div>

            {{-- 3. ATUR HARI & JAM --}}
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="mb-0 fw-bold text-dark"><i class="bi bi-clock"></i> Atur Hari & Jam</h6>
                </div>
                <div class="card-body p-0">
                    @php $days = ['Monday'=>'Senin','Tuesday'=>'Selasa','Wednesday'=>'Rabu','Thursday'=>'Kamis','Friday'=>'Jumat','Saturday'=>'Sabtu','Sunday'=>'Minggu']; @endphp
                    <div class="list-group list-group-flush">
                        @foreach($days as $eng => $indo)
                        <div class="list-group-item p-3">
                            <div class="d-flex align-items-center gap-3">
                                <div class="form-check form-switch" style="min-width: 120px;">
                                    <input class="form-check-input" type="checkbox" name="days[{{ $eng }}][active]" value="1" id="check-{{ $eng }}" onchange="toggleDay('{{ $eng }}')" style="cursor: pointer; width: 3em; height: 1.5em;">
                                    <label class="form-check-label fw-bold ms-2 mt-1" for="check-{{ $eng }}">{{ $indo }}</label>
                                </div>
                                <div class="row g-2 flex-grow-1 opacity-25" id="input-row-{{ $eng }}" style="pointer-events: none;">
                                    
                                    <div class="col-md-6">
                                        <div class="input-group">
                                            <span class="input-group-text bg-warning border-warning text-dark">🌞 Jemput</span>
                                            <input type="time" name="days[{{ $eng }}][pickup_time]" class="form-control time-input border-warning" id="pickup-{{ $eng }}" data-day="{{ $eng }}" onchange="checkTime(this)" disabled>
                                        </div>
                                        <div id="error-pickup-{{ $eng }}" class="text-danger small fw-bold mt-1 d-none"></div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="input-group">
                                            <span class="input-group-text bg-info border-info text-white">🌥️ Antar</span>
                                            <input type="time" name="days[{{ $eng }}][dropoff_time]" class="form-control time-input border-info" id="dropoff-{{ $eng }}" data-day="{{ $eng }}" onchange="checkTime(this)" disabled>
                                        </div>
                                        <div id="error-dropoff-{{ $eng }}" class="text-danger small fw-bold mt-1 d-none"></div>
                                    </div>

                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                <div class="card-footer bg-white d-flex justify-content-end py-4 border-top-0">
                    <button type="submit" id="btn-submit" class="btn btn-primary btn-lg px-5 shadow rounded-pill" disabled>Simpan Jadwal</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    let maxCapacity = 0;
    let selectedStudents = 0;

    // 1. UPDATE KAPASITAS MOBIL
    function updateCapacity() {
        const selector = document.getElementById('shuttle_selector');
        const selectedOption = selector.options[selector.selectedIndex];
        maxCapacity = parseInt(selectedOption.getAttribute('data-capacity')) || 0;
        
        document.getElementById('max-capacity').innerText = maxCapacity;
        checkCapacityLimit();
        recheckAllTimes(); // Cek ulang bentrok jadwal jika mobil ganti
    }

    // 2. LOAD SISWA BERDASARKAN RUTE (AJAX)
    async function loadStudents() {
        const routeId = document.getElementById('route_selector').value;
        const container = document.getElementById('student-container');
        const loading = document.getElementById('student-loading');

        container.innerHTML = ''; 
        if (!routeId) {
            container.innerHTML = '<div class="col-12 text-center text-muted py-3">Pilih rute dulu.</div>';
            return;
        }

        loading.classList.remove('d-none');

        try {
            // Kita butuh endpoint baru: /get-students-by-route/{route_id}
            // Pastikan Anda membuat route dan controller function ini
            const response = await fetch(`/get-students-by-route/${routeId}`);
            const students = await response.json();
            loading.classList.add('d-none');

            if (students.length === 0) {
                container.innerHTML = '<div class="col-12 text-danger text-center">Tidak ada siswa yang terdaftar di rute/komplek ini.</div>';
                return;
            }

            students.forEach(s => {
                const col = document.createElement('div');
                col.className = 'col-md-6';
                col.innerHTML = `
                    <div class="form-check bg-white border rounded p-3 h-100 shadow-sm student-card">
                        <input class="form-check-input student-checkbox" type="checkbox" name="student_ids[]" value="${s.id}" id="s-${s.id}" onchange="countSelected()">
                        <label class="form-check-label w-100" for="s-${s.id}" style="cursor:pointer;">
                            <div class="d-flex justify-content-between align-items-center">
                                <strong>${s.name}</strong>
                                <span class="badge bg-light text-dark border">${s.complex_name}</span>
                            </div>
                            <small class="text-muted d-block mt-1 text-truncate">${s.address_note ?? '-'}</small>
                        </label>
                    </div>
                `;
                container.appendChild(col);
            });
            
            // Reset counter saat load baru
            selectedStudents = 0;
            document.getElementById('selected-count').innerText = 0;
            
        } catch (e) {
            console.error(e);
            loading.classList.add('d-none');
            container.innerHTML = '<div class="text-danger">Gagal memuat data siswa.</div>';
        }
    }

    // 3. HITUNG JUMLAH SISWA TERPILIH
    function countSelected() {
        const checkboxes = document.querySelectorAll('.student-checkbox:checked');
        selectedStudents = checkboxes.length;
        document.getElementById('selected-count').innerText = selectedStudents;
        checkCapacityLimit();
    }

    // 4. VALIDASI KAPASITAS
    function checkCapacityLimit() {
        const alertBox = document.getElementById('capacity-alert');
        const submitBtn = document.getElementById('btn-submit');
        const badge = document.getElementById('capacity-badge');

        if (maxCapacity > 0 && selectedStudents > maxCapacity) {
            alertBox.classList.remove('d-none');
            submitBtn.disabled = true;
            badge.classList.remove('bg-secondary', 'bg-success');
            badge.classList.add('bg-danger');
        } else {
            alertBox.classList.add('d-none');
            // Cek apakah minimal ada 1 siswa & mobil sudah dipilih?
            if (selectedStudents > 0 && maxCapacity > 0) {
                submitBtn.disabled = false;
                badge.classList.remove('bg-secondary', 'bg-danger');
                badge.classList.add('bg-success');
            } else {
                submitBtn.disabled = true; // Kunci jika belum pilih siswa/mobil
                badge.classList.remove('bg-danger', 'bg-success');
                badge.classList.add('bg-secondary');
            }
        }
    }

    // --- FUNGSI LAMA (TOGGLE DAY & CHECK TIME) TETAP SAMA ---
    function toggleDay(day) {
        var checkBox = document.getElementById("check-" + day);
        var inputRow = document.getElementById("input-row-" + day);
        var pickup = document.getElementById("pickup-" + day);
        var dropoff = document.getElementById("dropoff-" + day);

        if (checkBox.checked) {
            inputRow.classList.remove("opacity-25");
            inputRow.style.pointerEvents = "auto";
            pickup.disabled = false;
            dropoff.disabled = false;
        } else {
            inputRow.classList.add("opacity-25");
            inputRow.style.pointerEvents = "none";
            pickup.disabled = true;
            dropoff.disabled = true;
            pickup.value = "";
            dropoff.value = "";
            clearError(pickup);
            clearError(dropoff);
        }
    }

    async function checkTime(inputElement) {
        const time = inputElement.value;
        if (!time) { clearError(inputElement); return; }

        const driverId = document.getElementById('driver_selector').value;
        const shuttleId = document.getElementById('shuttle_selector').value;
        const day = inputElement.dataset.day;
        const errorBoxId = "error-" + inputElement.id;
        const errorBox = document.getElementById(errorBoxId);

        if (!driverId || !shuttleId) {
            alert("Mohon pilih Driver dan Mobil terlebih dahulu!");
            inputElement.value = "";
            return;
        }

        try {
            const response = await fetch("{{ route('schedules.check') }}", {
                method: "POST",
                headers: { "Content-Type": "application/json", "X-CSRF-TOKEN": "{{ csrf_token() }}" },
                body: JSON.stringify({ driver_id: driverId, shuttle_id: shuttleId, day: day, time: time })
            });

            const result = await response.json();

            if (result.status === 'conflict') {
                inputElement.classList.add('is-invalid', 'border-danger');
                errorBox.textContent = result.message;
                errorBox.classList.remove('d-none');
                document.getElementById('btn-submit').disabled = true;
            } else {
                clearError(inputElement, errorBoxId);
                inputElement.classList.add('is-valid');
                // Re-enable submit jika kapasitas aman
                checkCapacityLimit();
            }
        } catch (error) { console.error(error); }
    }

    function clearError(inputElement, errorBoxId = null) {
        inputElement.classList.remove('is-invalid', 'is-valid', 'border-danger');
        if(errorBoxId) document.getElementById(errorBoxId).classList.add('d-none');
    }

    function recheckAllTimes() {
        document.querySelectorAll('.time-input').forEach(input => {
            if (!input.disabled && input.value) checkTime(input);
        });
    }
</script>
@endsection