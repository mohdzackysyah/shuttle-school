@extends('layouts.admin')
@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card border-primary">
            <div class="card-header bg-primary text-white">Tambah Driver Baru</div>
            <div class="card-body">
                <form action="{{ route('drivers.store') }}" method="POST">
                    @csrf
                    <div class="mb-3"><label>Nama</label><input type="text" name="name" class="form-control" required></div>
                    <div class="mb-3"><label>Email</label><input type="email" name="email" class="form-control" required></div>
                    <div class="mb-3"><label>No HP</label><input type="text" name="phone" class="form-control" required></div>
                    <div class="mb-3"><label>Nomor SIM</label><input type="text" name="license_number" class="form-control" required></div>
                    <div class="mb-3"><label>Password</label><input type="password" name="password" class="form-control" required></div>
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('drivers.index') }}" class="btn btn-secondary">Kembali</a>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection