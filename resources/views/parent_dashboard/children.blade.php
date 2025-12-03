@extends('layouts.parent')

@section('content')
<div class="container py-3">
    
    <div class="d-flex align-items-center mb-4">
        <div class="bg-warning text-dark rounded-circle p-3 me-3 shadow-sm">
            <i class="bi bi-people-fill fs-3"></i>
        </div>
        <div>
            <h4 class="fw-bold text-dark mb-0">Data Anak Saya</h4>
            <p class="text-muted mb-0 small">Daftar siswa yang terhubung dengan akun ini.</p>
        </div>
    </div>

    <div class="row">
        @forelse($students as $student)
        <div class="col-md-6 mb-3">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-4 d-flex align-items-center">
                    
                    <div class="me-4">
                        @if($student->photo)
                            <img src="{{ asset('storage/' . $student->photo) }}" 
                                 class="rounded-circle object-fit-cover shadow-sm" 
                                 style="width: 80px; height: 80px; border: 3px solid #fffbf0;">
                        @else
                            <div class="bg-warning bg-opacity-10 text-warning rounded-circle d-flex align-items-center justify-content-center shadow-sm" 
                                 style="width: 80px; height: 80px; font-size: 2.5rem; border: 3px solid #fffbf0;">
                                <i class="bi bi-emoji-smile"></i>
                            </div>
                        @endif
                    </div>

                    <div>
                        <h5 class="fw-bold mb-1 text-dark">{{ $student->name }}</h5>
                        
                        <div class="text-muted small mb-1">
                            <i class="bi bi-geo-alt-fill text-warning me-1"></i> 
                            {{ $student->complex->name ?? 'Komplek Belum Diatur' }}
                        </div>
                        
                        <div class="text-muted small">
                            <i class="bi bi-house-door me-1"></i> 
                            {{ $student->address_note ?? 'Detail alamat kosong' }}
                        </div>
                    </div>

                </div>
                
                <div class="card-footer bg-white border-top-0 pb-3 pt-0 px-4">
                    <span class="badge bg-success bg-opacity-10 text-success rounded-pill">
                        <i class="bi bi-check-circle-fill me-1"></i> Terdaftar Aktif
                    </span>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12 text-center py-5">
            <div class="mb-3 text-muted opacity-50">
                <i class="bi bi-person-x display-1"></i>
            </div>
            <h5>Belum ada data anak.</h5>
            <p class="text-muted small">Jika Anda memiliki anak yang bersekolah, silakan hubungi Admin Sekolah untuk mendaftarkannya ke akun nomor HP ini.</p>
        </div>
        @endforelse
    </div>

    <div style="height: 50px;"></div>
</div>
@endsection