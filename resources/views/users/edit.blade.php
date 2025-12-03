@extends('layouts.admin')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">Edit Data User</div>
            <div class="card-body">
                <form action="{{ route('users.update', $user->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label class="form-label">Nama Lengkap</label>
                        <input type="text" class="form-control" name="name" value="{{ $user->name }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" name="email" value="{{ $user->email }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">No Handphone</label>
                        <input type="text" class="form-control" name="phone" value="{{ $user->phone }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Role</label>
                        <input type="text" class="form-control bg-light" value="{{ $user->role == 'driver' ? 'Driver' : 'Wali Murid' }}" readonly>
                        <input type="hidden" name="role" value="{{ $user->role }}">
                    </div>

                    @if($user->role == 'driver' || $user->driverProfile)
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nomor SIM (Khusus Driver)</label>
                        <input type="text" class="form-control" name="license_number" 
                               value="{{ $user->driverProfile ? $user->driverProfile->license_number : '' }}" 
                               placeholder="Isi jika role adalah Driver">
                    </div>
                    @endif

                    <div class="mb-3">
                        <label class="form-label">Password Baru (Opsional)</label>
                        <input type="password" class="form-control" name="password" placeholder="Kosongkan jika tidak ingin ganti">
                    </div>

                    <div class="d-flex justify-content-between">
                        @if($user->role == 'driver')
                            <a href="{{ route('users.drivers') }}" class="btn btn-secondary">Kembali</a>
                        @else
                            <a href="{{ route('users.parents') }}" class="btn btn-secondary">Kembali</a>
                        @endif

                        <button type="submit" class="btn btn-primary">Update User</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection