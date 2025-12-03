@extends('layouts.admin')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card border-success">
            <div class="card-header bg-success text-white">Tambah Wali Murid</div>
            <div class="card-body">
                <form action="{{ route('users.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="role" value="parent">

                    <div class="mb-3">
                        <label class="form-label">Nama Orang Tua</label>
                        <input type="text" class="form-control" name="name" placeholder="Ibu Siti" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Email (Untuk Login)</label>
                        <input type="email" class="form-control" name="email" placeholder="ortu@gmail.com" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">No HP (WhatsApp)</label>
                        <input type="text" class="form-control" name="phone" placeholder="0812..." required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" class="form-control" name="password" required>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('users.parents') }}" class="btn btn-secondary">Kembali</a>
                        
                        <button type="submit" class="btn btn-success">Simpan Wali Murid</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection