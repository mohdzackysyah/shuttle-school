@extends('layouts.admin')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-bold">Tambah Rute Baru</h5>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('routes.store') }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="form-label fw-bold">Nama Rute / Wilayah</label>
                        <input type="text" class="form-control" name="name" placeholder="Contoh: Batam Center (Utara)" required>
                    </div>
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('routes.index') }}" class="btn btn-light">Batal</a>
                        <button type="submit" class="btn btn-primary px-4">Simpan Rute</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection