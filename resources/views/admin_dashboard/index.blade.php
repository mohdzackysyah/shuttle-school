@extends('layouts.admin')

@section('content')
<div class="mb-4">
    <h3 class="fw-bold text-dark">Dashboard Overview</h3>
    <p class="text-muted">Ringkasan data sistem antar jemput sekolah.</p>
</div>

<div class="row g-4">
    <div class="col-md-3">
        <div class="card h-100 p-3 border-0 shadow-sm">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted small fw-bold text-uppercase">Total Siswa</span>
                    <h2 class="fw-bold mt-2 mb-0 text-dark">{{ $totalStudents ?? 0 }}</h2>
                    <small class="text-muted">Siswa terdaftar</small>
                </div>
                <div class="bg-primary bg-opacity-10 text-primary rounded-3 p-3">
                    <i class="bi bi-backpack fs-2"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card h-100 p-3 border-0 shadow-sm">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted small fw-bold text-uppercase">Total Driver</span>
                    <h2 class="fw-bold mt-2 mb-0 text-dark">{{ $totalDrivers ?? 0 }}</h2>
                    <small class="text-muted">Akun Driver aktif</small>
                </div>
                <div class="bg-success bg-opacity-10 text-success rounded-3 p-3">
                    <i class="bi bi-person-badge fs-2"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card h-100 p-3 border-0 shadow-sm">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted small fw-bold text-uppercase">Rute / Wilayah</span>
                    <h2 class="fw-bold mt-2 mb-0 text-dark">{{ $totalRoutes ?? 0 }}</h2>
                    <small class="text-muted">Rute tersedia</small>
                </div>
                <div class="bg-warning bg-opacity-10 text-warning rounded-3 p-3">
                    <i class="bi bi-map fs-2"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card h-100 p-3 border-0 shadow-sm">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted small fw-bold text-uppercase">Armada Mobil</span>
                    <h2 class="fw-bold mt-2 mb-0 text-dark">{{ $activeShuttles ?? 0 }}</h2>
                    <small class="text-muted">Siap operasi</small>
                </div>
                <div class="bg-info bg-opacity-10 text-info rounded-3 p-3">
                    <i class="bi bi-bus-front fs-2"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-12">
        <div class="card p-5 h-100 border-0 shadow-sm text-center">
            <div class="py-4">
                <i class="bi bi-pc-display-horizontal text-secondary opacity-25" style="font-size: 5rem;"></i>
                
                <h3 class="fw-bold mt-3">Selamat Datang di Panel Admin!</h3>
                <p class="text-muted">Silakan pilih menu di sidebar kiri untuk mengelola data Master, Pengguna, atau Jadwal.</p>
                
                <div class="mt-4">
                    <a href="{{ route('schedules.index') }}" class="btn btn-dark px-4 py-2 shadow-sm">
                        <i class="bi bi-calendar-check me-2"></i> Kelola Jadwal Rutin
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection