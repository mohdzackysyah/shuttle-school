@extends('layouts.admin')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">Tambah Armada Baru</div>
            <div class="card-body">
                <form action="{{ route('shuttles.store') }}" method="POST">
                    @csrf
                    
                    <div class="mb-3">
                        <label class="form-label">Plat Nomor</label>
                        <input type="text" class="form-control" name="plate_number" placeholder="BP 1234 XY" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Model Mobil</label>
                        <input type="text" class="form-control" name="car_model" placeholder="Toyota Hiace" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Kapasitas (Jumlah Kursi)</label>
                        <input type="number" class="form-control" name="capacity" placeholder="15" required>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('shuttles.index') }}" class="btn btn-secondary">Kembali</a>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection