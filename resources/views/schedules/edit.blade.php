@extends('layouts.admin')
@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow border-0">
            <div class="card-header bg-warning text-dark py-3">
                <h5 class="mb-0 fw-bold">
                    ✏️ Edit Jadwal: {{ $schedule->day_of_week }}
                </h5>
                <small>Ubah jam operasional untuk hari ini.</small>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('schedules.update', $schedule->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">🌞 Jam Jemput (Pagi)</label>
                            <input type="time" name="pickup_time" class="form-control form-control-lg border-warning" 
                                   value="{{ $pickup ? \Carbon\Carbon::parse($pickup->departure_time)->format('H:i') : '' }}">
                            <div class="form-text">Kosongkan jika tidak ada penjemputan.</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">🌥️ Jam Antar (Sore)</label>
                            <input type="time" name="dropoff_time" class="form-control form-control-lg border-info" 
                                   value="{{ $dropoff ? \Carbon\Carbon::parse($dropoff->departure_time)->format('H:i') : '' }}">
                            <div class="form-text">Kosongkan jika tidak ada pengantaran.</div>
                        </div>
                    </div>

                    <hr>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Rute</label>
                        <select name="route_id" class="form-select">
                            @foreach($routes as $r) 
                                <option value="{{ $r->id }}" {{ $schedule->route_id == $r->id ? 'selected' : '' }}>{{ $r->name }}</option> 
                            @endforeach
                        </select>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Supir</label>
                            <select name="driver_id" class="form-select">
                                @foreach($drivers as $d) 
                                    <option value="{{ $d->id }}" {{ $schedule->driver_id == $d->id ? 'selected' : '' }}>{{ $d->name }}</option> 
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Mobil</label>
                            <select name="shuttle_id" class="form-select">
                                @foreach($shuttles as $s) 
                                    <option value="{{ $s->id }}" {{ $schedule->shuttle_id == $s->id ? 'selected' : '' }}>
                                        {{ $s->plate_number }} ({{ $s->car_model }})
                                    </option> 
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('schedules.index') }}" class="btn btn-secondary px-4">Batal</a>
                        <button type="submit" class="btn btn-warning px-5 fw-bold shadow-sm">Simpan Perubahan</button>
                    </div>
                </form>
                
                <div class="mt-4 pt-3 border-top text-end">
                    <form action="{{ route('schedules.destroy', $schedule->id) }}" method="POST" onsubmit="return confirm('Hapus SELURUH jadwal hari {{ $schedule->day_of_week }} untuk rute ini?');">
                        @csrf @method('DELETE')
                        <small class="text-muted me-2">Ingin menghapus? Kosongkan jam di atas lalu simpan.</small>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection