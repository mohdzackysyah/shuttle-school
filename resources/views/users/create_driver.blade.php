@extends('layouts.admin')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card border-primary">
            <div class="card-header bg-primary text-white">Tambah Driver Baru</div>
            <div class="card-body">
                <form action="{{ route('users.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="role" value="driver">

                    <div class="mb-3">
                        <label class="form-label">Nama Driver</label>
                        <input type="text" class="form-control" name="name" placeholder="Pak Budi" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Email (Untuk Login)</label>
                        <input type="email" class="form-control" name="email" placeholder="driver@sekolah.com" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">No HP (WhatsApp)</label>
                        <input type="text" class="form-control" name="phone" placeholder="0812..." required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Nomor SIM</label>
                        <input type="text" class="form-control" name="license_number" placeholder="Contoh: 1234-5678-9000" required>
                        <div class="form-text">Wajib diisi untuk data driver.</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" class="form-control" name="password" required>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('users.drivers') }}" class="btn btn-secondary">Kembali</a>
                        
                        <button type="submit" class="btn btn-primary">Simpan Driver</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection