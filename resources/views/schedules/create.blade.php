@extends('layouts.admin')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-10">
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold text-primary">📅 Buat Jadwal Baru</h4>
                <p class="text-muted mb-0">Sistem otomatis mengecek ketersediaan Driver & Mobil.</p>
            </div>
            <a href="{{ route('schedules.index') }}" class="btn btn-light border shadow-sm">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>

        <form action="{{ route('schedules.store') }}" method="POST">
            @csrf

            <div class="card shadow-sm border-0 mb-4 rounded-4">
                <div class="card-header bg-primary text-white py-3 border-bottom">
                    <h6 class="mb-0 fw-bold"><i class="bi bi-sliders"></i> Pengaturan Umum</h6>
                </div>
                <div class="card-body bg-light bg-opacity-50">
                    <div class="row g-3">
                        
                        <div class="col-md-4">
                            <label class="form-label fw-bold small text-uppercase">Pilih Rute</label>
                            <select name="route_id" id="route_selector" class="form-select form-select-lg" required onchange="loadComplexes()">
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
                            <select name="shuttle_id" id="shuttle_selector" class="form-select form-select-lg" required onchange="recheckAllTimes()">
                                <option value="">-- Pilih Mobil --</option>
                                @foreach($shuttles as $s) <option value="{{ $s->id }}">{{ $s->plate_number }}</option> @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="mt-4 p-3 bg-white border rounded-3">
                        <label class="form-label fw-bold small text-uppercase text-primary mb-2">
                            <i class="bi bi-buildings"></i> Pilih Komplek (Jemput/Antar)
                        </label>
                        
                        <div id="complex-loading" class="text-muted small d-none">
                            <span class="spinner-border spinner-border-sm me-2"></span> Mengambil data...
                        </div>
                        
                        <div id="complex-container" class="d-flex flex-wrap gap-2">
                            <div class="text-muted small fst-italic">Pilih rute dulu untuk melihat daftar komplek.</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="mb-0 fw-bold text-dark">Atur Hari & Jam</h6>
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
                    <button type="submit" id="btn-submit" class="btn btn-primary btn-lg px-5 shadow rounded-pill">Simpan Jadwal</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    // 1. LOAD KOMPLEK VIA AJAX
    async function loadComplexes() {
        const routeId = document.getElementById('route_selector').value;
        const container = document.getElementById('complex-container');
        const loading = document.getElementById('complex-loading');

        container.innerHTML = ''; 
        if (!routeId) return;

        loading.classList.remove('d-none');

        try {
            const response = await fetch(`/get-complexes/${routeId}`);
            const complexes = await response.json();
            loading.classList.add('d-none');

            if (complexes.length === 0) {
                container.innerHTML = '<div class="text-danger small">Tidak ada komplek di rute ini.</div>';
                return;
            }

            complexes.forEach(c => {
                const div = document.createElement('div');
                div.className = 'form-check form-check-inline bg-light border rounded px-3 py-2 m-0';
                div.innerHTML = `
                    <input class="form-check-input" type="checkbox" name="complex_ids[]" value="${c.id}" id="c-${c.id}" checked>
                    <label class="form-check-label fw-bold" for="c-${c.id}">${c.name}</label>
                `;
                container.appendChild(div);
            });
        } catch (e) {
            console.error(e);
            loading.classList.add('d-none');
        }
    }

    // 2. TOGGLE HARI
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

    // 3. CEK KETERSEDIAAN (BENTROK)
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
            }
        } catch (error) { console.error(error); }
    }

    // 4. UTILITIES
    function clearError(inputElement, errorBoxId = null) {
        inputElement.classList.remove('is-invalid', 'is-valid', 'border-danger');
        if(errorBoxId) document.getElementById(errorBoxId).classList.add('d-none');
        
        if (document.querySelectorAll('.is-invalid').length === 0) {
            document.getElementById('btn-submit').disabled = false;
        }
    }

    function recheckAllTimes() {
        document.querySelectorAll('.time-input').forEach(input => {
            if (!input.disabled && input.value) checkTime(input);
        });
    }
</script>
@endsection