<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm mb-4 d-md-none rounded-3 border-bottom">
    <div class="container-fluid">
        
        <button class="btn btn-light border-0 shadow-sm" id="menu-toggle">
            <i class="bi bi-list fs-3 text-primary"></i>
        </button>

        <span class="navbar-brand ms-3 fw-bold" style="font-size: 1.1rem;">
            🚌 Shuttle<span class="text-primary">Admin</span>
        </span>

        <div class="ms-auto d-flex align-items-center">
            <div class="dropdown">
                <a href="#" class="d-flex align-items-center text-decoration-none" data-bs-toggle="dropdown">
                    <div class="bg-light rounded-circle d-flex align-items-center justify-content-center border" style="width: 40px; height: 40px;">
                        <i class="bi bi-person-fill fs-5 text-secondary"></i>
                    </div>
                </a>
                <ul class="dropdown-menu dropdown-menu-end border-0 shadow mt-2">
                    <li><span class="dropdown-item-text text-muted small">{{ Auth::user()->name ?? 'Admin' }}</span></li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="dropdown-item text-danger small">
                                <i class="bi bi-box-arrow-left"></i> Keluar
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>

    </div>
</nav>