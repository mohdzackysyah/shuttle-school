@php
    $layout = 'layouts.app'; 
    if(Auth::user()->role == 'admin') $layout = 'layouts.admin';
    elseif(Auth::user()->role == 'driver') $layout = 'layouts.driver';
    elseif(Auth::user()->role == 'parent') $layout = 'layouts.parent';
@endphp

@extends($layout)

@section('content')
<div class="container py-4">
    
    <div class="mb-4">
        <h3 class="fw-bold text-dark">Profil Saya</h3>
        <p class="text-muted">Kelola informasi akun dan foto profil Anda.</p>
    </div>

    <div class="row justify-content-center">
        
        <div class="col-md-4 mb-4">
            <div class="card border-0 shadow-sm rounded-4 text-center h-100">
                <div class="card-body p-4">
                    <div class="mb-3 position-relative d-inline-block">
                        @if(Auth::user()->photo)
                            <img src="{{ asset('storage/' . Auth::user()->photo) }}" 
                                 class="rounded-circle shadow-sm object-fit-cover" 
                                 style="width: 120px; height: 120px; border: 4px solid #fff;">
                        @else
                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center shadow-sm" 
                                 style="width: 120px; height: 120px; font-size: 3rem; border: 4px solid #fff;">
                                {{ substr(Auth::user()->name, 0, 1) }}
                            </div>
                        @endif
                        
                        <label for="photoInput" class="position-absolute bottom-0 end-0 bg-white p-2 rounded-circle shadow-sm text-primary" 
                               style="cursor: pointer; border: 1px solid #eee;">
                            <i class="bi bi-camera-fill"></i>
                        </label>
                    </div>

                    <h4 class="fw-bold mb-1">{{ Auth::user()->name }}</h4>
                    <p class="text-muted mb-2">{{ Auth::user()->email ?? 'Belum ada email' }}</p>
                    
                    <span class="badge rounded-pill px-3 py-2 
                        {{ Auth::user()->role == 'admin' ? 'bg-dark' : (Auth::user()->role == 'driver' ? 'bg-primary' : 'bg-warning text-dark') }}">
                        {{ strtoupper(Auth::user()->role) }}
                    </span>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="mb-0 fw-bold">Edit Informasi</h5>
                </div>
                <div class="card-body p-4">
                    
                    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <input type="file" name="photo" id="photoInput" class="d-none" onchange="previewImage()">
                        
                        <div id="fileNameDisplay" class="alert alert-info py-2 d-none mb-3">
                            <i class="bi bi-image me-2"></i> <span id="fileNameText"></span>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold text-secondary">Nama Lengkap</label>
                            <input type="text" name="name" class="form-control" value="{{ $user->name }}" required>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold text-secondary">Email</label>
                                <input type="email" name="email" class="form-control bg-light" value="{{ $user->email }}" readonly>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold text-secondary">No. Handphone / WA</label>
                                <input type="number" name="phone" class="form-control" value="{{ $user->phone }}" required>
                            </div>
                        </div>

                        @if($user->role == 'driver')
                        <div class="mb-4">
                            <label class="form-label fw-bold text-primary"><i class="bi bi-card-heading"></i> Nomor SIM</label>
                            <input type="text" name="license_number" class="form-control border-primary" 
                                   value="{{ $user->driverProfile->license_number ?? '' }}">
                        </div>
                        @endif

                        <hr class="my-4">
                        <h6 class="fw-bold text-danger mb-3"><i class="bi bi-shield-lock"></i> Ganti Password (Opsional)</h6>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label small text-muted">Password Baru</label>
                                <input type="password" name="password" class="form-control" placeholder="Kosongkan jika tidak ganti">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label small text-muted">Konfirmasi Password</label>
                                <input type="password" name="password_confirmation" class="form-control" placeholder="Ulangi password baru">
                            </div>
                        </div>

                        <div class="text-end mt-3">
                            <button type="submit" class="btn btn-success px-4 py-2 fw-bold shadow-sm">
                                <i class="bi bi-save me-2"></i> Simpan Perubahan
                            </button>
                        </div>

                    </form>

                </div>
            </div>
        </div>

    </div>
    <div style="height: 50px;"></div>
</div>

<script>
    function previewImage() {
        const input = document.getElementById('photoInput');
        const displayBox = document.getElementById('fileNameDisplay');
        const text = document.getElementById('fileNameText');
        
        if (input.files && input.files[0]) {
            text.textContent = "Foto terpilih: " + input.files[0].name;
            displayBox.classList.remove('d-none');
        }
    }
</script>
@endsection