@extends('layouts.parent')

@section('content')
<div class="container py-4">
    <!-- Header -->
    <div class="d-flex justify-content-center mb-5 text-center">
        <div>
            <h3 class="fw-bold text-dark mb-2">Data Anak</h3>
            <p class="text-muted mb-0 small">Kelola informasi foto dan detail alamat jemputan.</p>
        </div>
    </div>

    <div class="row g-4 justify-content-center">
        @forelse($students as $student)
            <div class="col-md-6 col-lg-4">
                <div class="card border-0 shadow-sm h-100 position-relative overflow-hidden hover-card" style="border-radius: 20px;">
                    
                    <!-- Card Header (Background Pattern) -->
                    <div class="position-absolute top-0 start-0 end-0" style="height: 100px; background: linear-gradient(135deg, #e0f2fe 0%, #f0f9ff 100%); border-radius: 20px 20px 0 0;"></div>

                    <div class="card-body text-center p-4 position-relative">
                        
                        <!-- Avatar Section -->
                        <div class="mb-3 d-inline-block position-relative" style="margin-top: 30px;">
                            <div class="p-1 bg-white rounded-circle shadow-sm">
                                @if($student->photo)
                                    <img src="{{ asset('storage/' . $student->photo) }}" alt="{{ $student->name }}" 
                                         class="rounded-circle border" 
                                         style="width: 100px; height: 100px; object-fit: cover;">
                                @else
                                    <div class="rounded-circle bg-primary bg-opacity-10 d-flex align-items-center justify-content-center text-primary fw-bold border" 
                                         style="width: 100px; height: 100px; font-size: 2.5rem;">
                                        {{ substr($student->name, 0, 1) }}
                                    </div>
                                @endif
                            </div>
                            
                            <!-- Edit Photo Indicator -->
                            <a href="{{ route('parents.children.edit', $student->id) }}" class="position-absolute bottom-0 end-0 bg-white text-primary border rounded-circle shadow-sm d-flex align-items-center justify-content-center" 
                               style="width: 32px; height: 32px; text-decoration: none;" title="Ganti Foto">
                                <i class="bi bi-camera-fill small"></i>
                            </a>
                        </div>

                        <!-- Name & Complex -->
                        <h5 class="fw-bold text-dark mb-1">{{ $student->name }}</h5>
                        <div class="mb-4">
                            <span class="badge bg-light text-dark border px-3 py-2 rounded-pill fw-normal">
                                <i class="bi bi-geo-alt-fill text-danger me-1"></i> {{ $student->complex->name ?? 'Komplek ?' }}
                            </span>
                        </div>

                        <!-- Detail Address Box -->
                        <div class="text-start bg-light rounded-4 p-3 mb-4 border border-light">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <label class="small text-uppercase fw-bold text-muted" style="font-size: 0.7rem; letter-spacing: 0.5px;">Detail Alamat / Patokan</label>
                                <a href="{{ route('parents.children.edit', $student->id) }}" class="text-decoration-none small fw-bold">Edit <i class="bi bi-pencil-square"></i></a>
                            </div>
                            <div class="d-flex align-items-start">
                                <i class="bi bi-house-door text-secondary me-2 mt-1"></i>
                                <p class="mb-0 small text-dark" style="line-height: 1.5;">
                                    {{ $student->address_note ?? 'Belum ada detail alamat. Silakan tambahkan agar driver mudah menemukan rumah.' }}
                                </p>
                            </div>
                        </div>

                        <!-- Action Button -->
                        <a href="{{ route('parents.children.edit', $student->id) }}" class="btn btn-outline-primary rounded-pill w-100 fw-bold py-2">
                            Kelola Data Anak
                        </a>

                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 py-5 text-center">
                <div class="bg-light rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 100px; height: 100px;">
                    <i class="bi bi-emoji-frown text-muted display-4"></i>
                </div>
                <h5 class="fw-bold text-dark">Belum Ada Data Anak</h5>
                <p class="text-secondary mb-4">Silakan hubungi admin sekolah untuk mendaftarkan putra/putri Anda ke dalam sistem.</p>
                <a href="#" class="btn btn-primary px-4 rounded-pill disabled">Hubungi Admin</a>
            </div>
        @endforelse
    </div>
    
    <div style="height: 60px;"></div>
</div>

<style>
    .hover-card {
        transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1), box-shadow 0.3s ease;
        border: 1px solid rgba(0,0,0,0.05) !important;
    }
    .hover-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 15px 30px rgba(0,0,0,0.08) !important;
        border-color: rgba(37, 99, 235, 0.2) !important;
    }
</style>
@endsection