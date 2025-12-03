@extends('layouts.admin')
@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">Edit Armada</div>
            <div class="card-body">
                <form action="{{ route('shuttles.update', $shuttle->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label class="form-label">Plat Nomor</label>
                        <input type="text" class="form-control" name="plate_number" value="{{ $shuttle->plate_number }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Model Mobil</label>
                        <input type="text" class="form-control" name="car_model" value="{{ $shuttle->car_model }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Kapasitas</label>
                        <input type="number" class="form-control" name="capacity" value="{{ $shuttle->capacity }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select class="form-select" name="status">
                            <option value="available" {{ $shuttle->status == 'available' ? 'selected' : '' }}>Tersedia</option>
                            <option value="maintenance" {{ $shuttle->status == 'maintenance' ? 'selected' : '' }}>Perbaikan (Bengkel)</option>
                        </select>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('shuttles.index') }}" class="btn btn-secondary">Kembali</a>
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection