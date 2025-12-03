<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Antar Jemput Sekolah</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">

    <style>
        body { background-color: #f8f9fa; }
        .navbar { box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .card { box-shadow: 0 2px 4px rgba(0,0,0,0.05); border: none; }
        .nav-link.active { font-weight: bold; color: #fff !important; }
        .dropdown-menu { border: none; box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4 sticky-top">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold" href="#">🚍 Shuttle Sekolah</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                
                <ul class="navbar-nav me-auto">
                    
                    @auth
                        @if(auth()->user()->role == 'admin')
                            
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                    📂 Data Master
                                </a>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="{{ route('complexes.index') }}">Data Komplek</a></li>
                                    <li><a class="dropdown-item" href="{{ route('shuttles.index') }}">Data Armada</a></li>
                                    <li><a class="dropdown-item" href="{{ route('routes.index') }}">Data Rute</a></li>
                                </ul>
                            </li>

                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                    👥 Pengguna
                                </a>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="{{ route('drivers.index') }}">Data Driver</a></li>
                                    <li><a class="dropdown-item" href="{{ route('parents.index') }}">Data Wali Murid</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="{{ route('students.index') }}">Data Siswa</a></li>
                                </ul>
                            </li>

                            <li class="nav-item ms-2 border-start ps-2 d-none d-lg-block"></li> <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('schedules.*') ? 'active' : '' }}" href="{{ route('schedules.index') }}">
                                    📅 Master Jadwal
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('trips.*') ? 'active' : '' }}" href="{{ route('trips.index') }}">
                                    📝 Riwayat Trip
                                </a>
                            </li>
                        @endif

                        @if(auth()->user()->role == 'driver')
                            <li class="nav-item">
                                <a class="nav-link fw-bold text-warning {{ request()->routeIs('driver.dashboard') ? 'active' : '' }}" href="{{ route('driver.dashboard') }}">
                                    🚀 TUGAS SAYA
                                </a>
                            </li>
                        @endif

                         @if(auth()->user()->role == 'parent')
                            <li class="nav-item">
                                <a class="nav-link fw-bold text-warning {{ request()->routeIs('parents.dashboard') ? 'active' : '' }}" href="{{ route('parents.dashboard') }}">
                                    👶 MONITOR ANAK
                                </a>
                            </li>
                        @endif

                    @endauth

                </ul>

                <ul class="navbar-nav ms-auto">
                    @auth
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle active" href="#" role="button" data-bs-toggle="dropdown">
                                👋 Halo, {{ Auth::user()->name }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><span class="dropdown-item-text text-muted small">Role: {{ ucfirst(Auth::user()->role) }}</span></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-danger">
                                            <i class="bi bi-box-arrow-right"></i> Logout
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @else
                        <li class="nav-item">
                            <a class="nav-link active fw-bold" href="{{ route('login') }}">Login Pengguna</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('admin.login') }}">Login Admin</a>
                        </li>
                    @endauth
                </ul>

            </div>
        </div>
    </nav>

    <div class="container">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger shadow-sm">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>