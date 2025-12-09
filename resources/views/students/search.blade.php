@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    
    {{-- 1. HEADER HALAMAN --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold text-dark mb-1">Pencarian Data Siswa</h3>
            <p class="text-muted mb-0">Cari profil siswa, data wali murid, dan jadwal antar-jemput.</p>
        </div>
        <div class="mt-3 mt-md-0">
            <a href="{{ route('students.index') }}" class="btn btn-light border shadow-sm rounded-pill px-4 fw-bold text-secondary">
                <i class="bi bi-arrow-left me-2"></i> Kembali ke Daftar
            </a>
        </div>
    </div>

    {{-- 2. MAIN CONTENT --}}
    <div class="row g-4">
        
        {{-- KOLOM KIRI: FORM PENCARIAN --}}
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white py-3 px-4 border-bottom">
                    <h5 class="fw-bold text-dark mb-0 d-flex align-items-center">
                        <i class="bi bi-search text-primary me-2"></i> Form Pencarian
                    </h5>
                </div>
                
                <div class="card-body p-4 p-lg-5 d-flex flex-column justify-content-center">
                    <form action="{{ route('students.find') }}" method="POST">
                        @csrf
                        <div class="mb-2">
                            <label class="form-label fw-bold text-dark">Kata Kunci Pencarian</label>
                            <div class="input-group input-group-lg shadow-sm rounded-3 overflow-hidden border">
                                <span class="input-group-text bg-white border-0 ps-4 text-muted">
                                    <i class="bi bi-person-badge fs-4"></i>
                                </span>
                                <input type="text" 
                                       name="keyword" 
                                       class="form-control border-0 ps-3 py-3" 
                                       placeholder="Contoh: Budi Santoso atau ID 15" 
                                       style="font-size: 1.1rem;"
                                       required 
                                       autofocus 
                                       autocomplete="off">
                                <button type="submit" class="btn btn-primary px-4 fw-bold">
                                    CARI DATA <i class="bi bi-arrow-right ms-2"></i>
                                </button>
                            </div>
                        </div>
                        
                        <div class="mt-4">
                            <p class="fw-bold text-muted small mb-2 text-uppercase ls-1">Tips Pencarian:</p>
                            <div class="d-flex flex-wrap gap-2">
                                <span class="badge bg-light text-secondary border px-3 py-2 rounded-pill fw-normal">
                                    <i class="bi bi-check-circle-fill text-success me-1"></i> ID Sistem
                                </span>
                                <span class="badge bg-light text-secondary border px-3 py-2 rounded-pill fw-normal">
                                    <i class="bi bi-check-circle-fill text-success me-1"></i> Nama Lengkap
                                </span>
                            </div>
                        </div>
                    </form>

                    @if(session('error'))
                        <div class="alert alert-danger border-0 bg-danger bg-opacity-10 text-danger d-flex align-items-center mt-4 mb-0 rounded-3 fade show" role="alert">
                            <i class="bi bi-exclamation-triangle-fill fs-4 me-3"></i>
                            <div>
                                <strong>Pencarian Gagal!</strong> {{ session('error') }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- KOLOM KANAN: PANEL INFORMASI (CLEAN) --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 h-100 text-white overflow-hidden" 
                 style="background: linear-gradient(135deg, #0d6efd 0%, #0043a8 100%);">
                
                {{-- TIDAK ADA IKON BACKGROUND DI SINI (CLEAN) --}}

                <div class="card-body p-4 d-flex flex-column justify-content-center">
                    <div class="mb-4">
                        <h4 class="fw-bold mb-1">Informasi Detail</h4>
                        <p class="opacity-75 mb-0 small">Hasil pencarian akan menampilkan:</p>
                    </div>

                    <div class="d-flex flex-column gap-3">
                        
                        {{-- Item 1 --}}
                        <div class="bg-white rounded-3 p-3 d-flex align-items-center shadow-sm">
                            <div class="bg-primary bg-opacity-10 p-2 rounded-circle text-primary me-3 d-flex justify-content-center align-items-center" style="width: 45px; height: 45px;">
                                <i class="bi bi-person-vcard-fill fs-5"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold text-dark mb-0">Biodata & Foto</h6>
                                <small class="text-muted" style="font-size: 0.75rem;">Profil lengkap siswa</small>
                            </div>
                        </div>

                        {{-- Item 2 --}}
                        <div class="bg-white rounded-3 p-3 d-flex align-items-center shadow-sm">
                            <div class="bg-success bg-opacity-10 p-2 rounded-circle text-success me-3 d-flex justify-content-center align-items-center" style="width: 45px; height: 45px;">
                                <i class="bi bi-telephone-fill fs-5"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold text-dark mb-0">Kontak Wali</h6>
                                <small class="text-muted" style="font-size: 0.75rem;">No. HP & Alamat</small>
                            </div>
                        </div>

                        {{-- Item 3 --}}
                        <div class="bg-white rounded-3 p-3 d-flex align-items-center shadow-sm">
                            <div class="bg-warning bg-opacity-10 p-2 rounded-circle text-warning me-3 d-flex justify-content-center align-items-center" style="width: 45px; height: 45px;">
                                <i class="bi bi-car-front-fill fs-5"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold text-dark mb-0">Jadwal Jemputan</h6>
                                <small class="text-muted" style="font-size: 0.75rem;">Driver & Rute</small>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<style>
    .ls-1 { letter-spacing: 1px; }
    .input-group-text { background-color: #fff; }
    .form-control:focus { box-shadow: none; border-color: transparent; }
    .input-group:focus-within { 
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.15) !important; 
        border-color: #86b7fe !important; 
    }
</style>
@endsection