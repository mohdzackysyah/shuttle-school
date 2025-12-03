@extends('layouts.admin')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card border-primary">
            <div class="card-header bg-primary text-white">Buat Jadwal Perjalanan Baru</div>
            <div class="card-body">
                <form action="{{ route('trips.store') }}" method="POST">
                    @csrf
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Tanggal</label>
                            <input type="date" class="form-control" name="date" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tipe Perjalanan</label>
                            <select name="type" class="form-select" required>
                                <option value="pickup">🌞 Penjemputan (Pagi - Ke Sekolah)</option>
                                <option value="dropoff">🌥️ Pengantaran (Sore - Pulang)</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Pilih Rute</label>
                        <select name="route_id" class="form-select" required>
                            <option value="">-- Pilih Rute --</option>
                            @foreach($routes as $route)
                                <option value="{{ $route->id }}">{{ $route->name }}</option>
                            @endforeach
                        </select>
                        <div class="form-text">Siswa akan otomatis terpilih berdasarkan rute ini.</div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Supir</label>
                            <select name="driver_id" class="form-select" required>
                                <option value="">-- Pilih Driver --</option>
                                @foreach($drivers as $driver)
                                    <option value="{{ $driver->id }}">{{ $driver->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Armada (Mobil)</label>
                            <select name="shuttle_id" class="form-select" required>
                                <option value="">-- Pilih Mobil --</option>
                                @foreach($shuttles as $shuttle)
                                    <option value="{{ $shuttle->id }}">
                                        {{ $shuttle->car_model }} - {{ $shuttle->plate_number }} (Kap: {{ $shuttle->capacity }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between mt-4">
                        <a href="{{ route('trips.index') }}" class="btn btn-secondary">Kembali</a>
                        <button type="submit" class="btn btn-primary">Generate Jadwal & Penumpang</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection