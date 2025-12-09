@extends('layouts.admin')

@section('content')
<div class="container-fluid px-0">
    
    {{-- 1. HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold text-dark mb-1">✏️ Edit Wali Murid</h3>
            <p class="text-muted mb-0">Perbarui data profil dan kontak wali murid.</p>
        </div>
        <a href="{{ route('parents.index') }}" class="btn btn-light border shadow-sm rounded-pill px-4 fw-bold text-secondary">
            <i class="bi bi-arrow-left me-2"></i> Kembali
        </a>
    </div>

    {{-- 2. FORM CARD --}}
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                
                <div class="card-header bg-white py-3 px-4 border-bottom">
                    <h5 class="fw-bold text-dark mb-0">Form Perubahan Data</h5>
                </div>

                <div class="card-body p-4">
                    <form action="{{ route('parents.update', $parent->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        {{-- Nama Lengkap --}}
                        <div class="mb-4">
                            <label class="form-label fw-bold text-secondary small text-uppercase">Nama Lengkap</label>
                            {{-- Tambahkan class 'has-validation' agar border merah input-group rapi --}}
                            <div class="input-group has-validation">
                                <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-person"></i></span>
                                <input type="text" 
                                       name="name" 
                                       class="form-control border-start-0 ps-0 @error('name') is-invalid @enderror" 
                                       value="{{ old('name', $parent->name) }}" 
                                       placeholder="Masukkan nama wali murid...">
                                
                                {{-- Pesan Error diletakkan DI DALAM input-group dengan class invalid-feedback --}}
                                @error('name')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>

                        {{-- Email & No HP (Grid 2 Kolom) --}}
                        <div class="row g-4 mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-secondary small text-uppercase">Alamat Email</label>
                                <div class="input-group has-validation">
                                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-envelope"></i></span>
                                    <input type="email" 
                                           name="email" 
                                           class="form-control border-start-0 ps-0 @error('email') is-invalid @enderror" 
                                           value="{{ old('email', $parent->email) }}" 
                                           placeholder="email@contoh.com">
                                    
                                    @error('email')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-secondary small text-uppercase">No. WhatsApp</label>
                                <div class="input-group has-validation">
                                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-whatsapp"></i></span>
                                    <input type="text" 
                                           name="phone" 
                                           class="form-control border-start-0 ps-0 @error('phone') is-invalid @enderror" 
                                           value="{{ old('phone', $parent->phone) }}" 
                                           placeholder="0812xxx">
                                    
                                    @error('phone')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <hr class="my-4 text-muted opacity-25">

                        {{-- Password (Opsional) --}}
                        <div class="mb-4">
                            <label class="form-label fw-bold text-secondary small text-uppercase d-flex justify-content-between">
                                Password Baru
                                <small class="text-muted fw-normal text-transform-none fst-italic">*Opsional</small>
                            </label>
                            <div class="input-group has-validation">
                                <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-lock"></i></span>
                                <input type="password" 
                                       name="password" 
                                       class="form-control border-start-0 ps-0 @error('password') is-invalid @enderror" 
                                       placeholder="Minimal 6 karakter">
                                
                                @error('password')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>

                        {{-- Tombol Aksi --}}
                        <div class="d-flex gap-2 justify-content-end mt-5">
                            <a href="{{ route('parents.index') }}" class="btn btn-light border px-4 rounded-pill fw-bold text-secondary">Batal</a>
                            <button type="submit" class="btn btn-primary px-4 rounded-pill fw-bold shadow-sm">
                                <i class="bi bi-save me-2"></i> Simpan Perubahan
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Styling input group agar menyatu */
    .input-group-text { background-color: #f8f9fa; border-right: none; }
    
    /* Saat Fokus */
    .form-control:focus { box-shadow: none; border-color: #86b7fe; }
    .input-group:focus-within .input-group-text { border-color: #86b7fe; }
    .input-group:focus-within .form-control { border-color: #86b7fe; }

    /* Fix visual jika terjadi error validasi (warna merah) */
    .form-control.is-invalid {
        border-color: #dc3545 !important;
        background-image: none !important; /* Hilangkan icon seru bawaan bootstrap yang kadang menumpuk */
    }
    .input-group:focus-within .form-control.is-invalid {
        box-shadow: 0 0 0 0.25rem rgba(220, 53, 69, 0.25);
    }
    .input-group:has(.form-control.is-invalid) .input-group-text {
        border-color: #dc3545;
        color: #dc3545;
    }
</style>
@endsection