@extends('layouts.admin')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-bold">Tambah Komplek / Perumahan</h5>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('complexes.store') }}" method="POST">
                    @csrf
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nama Komplek</label>
                        <input type="text" class="form-control" name="name" placeholder="Contoh: Perumahan Cendana" required>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Masuk ke Rute Mana?</label>
                        <select name="route_id" class="form-select" required>
                            <option value="">-- Pilih Rute --</option>
                            @foreach($routes as $route)
                                <option value="{{ $route->id }}">{{ $route->name }}</option>
                            @endforeach
                        </select>
                        <div class="form-text">Pastikan rute sudah dibuat sebelumnya.</div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('complexes.index') }}" class="btn btn-light">Batal</a>
                        <button type="submit" class="btn btn-primary px-4">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection