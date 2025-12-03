<div id="sidebar-wrapper">
    <!-- Logo Header dengan Toggle Button -->
    <div class="sidebar-heading d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center">
            <i class="bi bi-bus-front-fill brand-icon"></i>
            <span>Shuttle<span class="text-primary">App</span></span>
        </div>
        <!-- Toggle Button untuk Desktop -->
        <button class="btn btn-toggle d-none d-md-flex" id="sidebar-toggle-desktop" type="button">
            <i class="bi bi-chevron-left"></i>
        </button>
    </div>

    <!-- Profile Card -->
    <div class="sidebar-profile">
        @if(Auth::user()->photo)
            <img src="{{ asset('storage/' . Auth::user()->photo) }}" alt="Profile" class="profile-avatar" style="object-fit: cover;">
        @else
            <div class="profile-avatar">
                {{ substr(Auth::user()->name, 0, 1) }}
            </div>
        @endif
        <div class="profile-name">{{ Auth::user()->name }}</div>
        <div class="profile-role">
            <i class="bi bi-shield-check me-1"></i> Administrator
        </div>
    </div>

    <!-- Menu Items -->
    <div class="list-group list-group-flush flex-grow-1">
        
        <a href="{{ route('admin.dashboard') }}" class="list-group-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <i class="bi bi-grid-1x2-fill"></i> <span class="menu-text">Dashboard</span>
        </a>

        <div class="sidebar-label">Operasional</div>
        
        <a href="{{ route('schedules.index') }}" class="list-group-item {{ request()->routeIs('schedules.*') ? 'active' : '' }}">
            <i class="bi bi-calendar-week"></i> <span class="menu-text">Master Jadwal</span>
        </a>
        
        <a href="{{ route('trips.index') }}" class="list-group-item {{ request()->routeIs('trips.*') ? 'active' : '' }}">
            <i class="bi bi-clipboard-check"></i> <span class="menu-text">Riwayat Trip</span>
        </a>

        <div class="sidebar-label">Data Master</div>
        
        <a href="{{ route('routes.index') }}" class="list-group-item {{ request()->routeIs('routes.*') ? 'active' : '' }}">
            <i class="bi bi-map"></i> <span class="menu-text">Data Rute</span>
        </a>
        
        <a href="{{ route('shuttles.index') }}" class="list-group-item {{ request()->routeIs('shuttles.*') ? 'active' : '' }}">
            <i class="bi bi-bus-front"></i> <span class="menu-text">Data Armada</span>
        </a>
        
        <a href="{{ route('complexes.index') }}" class="list-group-item {{ request()->routeIs('complexes.*') ? 'active' : '' }}">
            <i class="bi bi-buildings"></i> <span class="menu-text">Data Komplek</span>
        </a>

        <div class="sidebar-label">Pengguna</div>
        
        <a href="{{ route('drivers.index') }}" class="list-group-item {{ request()->routeIs('drivers.*') ? 'active' : '' }}">
            <i class="bi bi-person-badge"></i> <span class="menu-text">Data Driver</span>
        </a>
        
        <a href="{{ route('parents.index') }}" class="list-group-item {{ request()->routeIs('parents.*') ? 'active' : '' }}">
            <i class="bi bi-people"></i> <span class="menu-text">Wali Murid</span>
        </a>
        
        <a href="{{ route('students.index') }}" class="list-group-item {{ request()->routeIs('students.*') ? 'active' : '' }}">
            <i class="bi bi-backpack"></i> <span class="menu-text">Data Siswa</span>
        </a>

    </div>

    <!-- Logout Button -->
    <div class="p-3 border-top sidebar-footer">
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-logout w-100 d-flex align-items-center justify-content-center gap-2">
                <i class="bi bi-box-arrow-left"></i> <span class="menu-text">Keluar</span>
            </button>
        </form>
    </div>
</div>

<style>
    /* Toggle Button untuk Minimize Sidebar */
    .btn-toggle {
        width: 35px;
        height: 35px;
        background: linear-gradient(135deg, #f0f9ff, #e0f2fe);
        border: none;
        border-radius: 10px;
        color: #2563eb;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
        font-size: 1.2rem;
    }

    .btn-toggle:hover {
        background: linear-gradient(135deg, #2563eb, #1e40af);
        color: white;
        transform: scale(1.05);
    }

    /* Sidebar Minimized State */
    body.sidebar-minimized #sidebar-wrapper {
        min-width: 80px;
        max-width: 80px;
    }

    body.sidebar-minimized #page-content-wrapper {
        margin-left: 80px;
    }

    body.sidebar-minimized .sidebar-heading span,
    body.sidebar-minimized .menu-text,
    body.sidebar-minimized .sidebar-label,
    body.sidebar-minimized .sidebar-profile .profile-name,
    body.sidebar-minimized .sidebar-profile .profile-role {
        display: none;
    }

    body.sidebar-minimized .sidebar-heading {
        justify-content: center;
        padding: 1.5rem 1rem;
    }

    body.sidebar-minimized .brand-icon {
        font-size: 2.5rem;
    }

    body.sidebar-minimized .sidebar-profile {
        padding: 1rem 0.5rem;
    }

    body.sidebar-minimized .profile-avatar {
        width: 45px;
        height: 45px;
        font-size: 1.2rem;
        margin: 0 auto;
    }

    body.sidebar-minimized .list-group-item {
        justify-content: center;
        padding: 0.8rem 0;
    }

    body.sidebar-minimized .list-group-item i {
        font-size: 1.5rem;
        margin: 0;
    }

    body.sidebar-minimized .list-group-item.active {
        margin: 0 0.5rem;
        width: calc(100% - 1rem);
    }

    body.sidebar-minimized .sidebar-footer {
        padding: 1rem 0.5rem;
    }

    body.sidebar-minimized .btn-logout {
        width: 50px;
        height: 50px;
        padding: 0;
        border-radius: 50%;
    }

    body.sidebar-minimized .btn-logout i {
        font-size: 1.3rem;
    }

    body.sidebar-minimized .btn-toggle i {
        transform: rotate(180deg);
    }

    /* Logout Button */
    .btn-logout {
        background: linear-gradient(135deg, #ef4444, #dc2626);
        color: white;
        border: none;
        border-radius: 50px;
        padding: 0.8rem 1.5rem;
        font-weight: 600;
        box-shadow: 0 4px 15px rgba(239, 68, 68, 0.3);
        transition: all 0.3s ease;
    }

    .btn-logout:hover {
        background: linear-gradient(135deg, #dc2626, #b91c1c);
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(239, 68, 68, 0.4);
        color: white;
    }

    .btn-logout:active {
        transform: translateY(0);
    }

    /* Profile Avatar sebagai Image */
    .sidebar-profile img.profile-avatar {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        margin: 0 auto 1rem;
        box-shadow: 0 4px 15px rgba(37, 99, 235, 0.3);
        display: block;
    }

    /* Menu Text Transition */
    .menu-text {
        transition: opacity 0.3s ease;
    }

    /* Responsive - Hide toggle button on mobile */
    @media (max-width: 768px) {
        .btn-toggle {
            display: none !important;
        }

        body.sidebar-minimized #sidebar-wrapper {
            min-width: 280px;
            max-width: 280px;
        }

        body.sidebar-minimized #page-content-wrapper {
            margin-left: 0;
        }

        body.sidebar-minimized .sidebar-heading span,
        body.sidebar-minimized .menu-text,
        body.sidebar-minimized .sidebar-label,
        body.sidebar-minimized .sidebar-profile .profile-name,
        body.sidebar-minimized .sidebar-profile .profile-role {
            display: block;
        }
    }

    /* Tooltip untuk Minimized Sidebar */
    body.sidebar-minimized .list-group-item {
        position: relative;
    }

    body.sidebar-minimized .list-group-item:hover::after {
        content: attr(title);
        position: absolute;
        left: 100%;
        top: 50%;
        transform: translateY(-50%);
        background: #1e293b;
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 8px;
        white-space: nowrap;
        margin-left: 1rem;
        font-size: 0.85rem;
        z-index: 1000;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
    }
</style>

<script>
    // Sidebar Toggle untuk Desktop
    document.addEventListener('DOMContentLoaded', function() {
        const toggleBtn = document.getElementById('sidebar-toggle-desktop');
        const body = document.body;

        // Load saved state from localStorage
        const savedState = localStorage.getItem('sidebarMinimized');
        if (savedState === 'true') {
            body.classList.add('sidebar-minimized');
        }

        if (toggleBtn) {
            toggleBtn.addEventListener('click', function() {
                body.classList.toggle('sidebar-minimized');
                
                // Save state to localStorage
                localStorage.setItem('sidebarMinimized', body.classList.contains('sidebar-minimized'));
            });
        }

        // Add title attribute for tooltips when minimized
        const menuItems = document.querySelectorAll('.list-group-item');
        menuItems.forEach(item => {
            const menuText = item.querySelector('.menu-text');
            if (menuText) {
                item.setAttribute('title', menuText.textContent.trim());
            }
        });
    });
</script>
