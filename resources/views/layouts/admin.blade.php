<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Shuttle Sekolah</title>
    
    {{-- Fonts & Icons --}}
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    <style>
        :root {
            /* Palette Warna */
            --primary: #4f46e5;
            --primary-dark: #3730a3;
            --secondary: #fbbf24;
            --dark: #0f172a;
            --light: #f8fafc;
            --gray: #64748b;
            --success: #10b981;
            --danger: #ef4444;

            /* Ukuran Sidebar */
            --sidebar-width: 280px;
            --sidebar-collapsed-width: 80px;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Poppins', sans-serif;
            background: #f1f5f9;
            overflow-x: hidden;
            color: var(--dark);
            line-height: 1.6;
        }

        /* --- WRAPPER LAYOUT --- */
        #wrapper {
            display: flex;
            width: 100%;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        /* --- SIDEBAR CONTAINER --- */
        #sidebar-wrapper {
            width: var(--sidebar-width);
            min-width: var(--sidebar-width);
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1050;
            background: white;
            border-right: 1px solid rgba(79, 70, 229, 0.1);
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
            display: flex; 
            flex-direction: column;
        }

        /* --- CONTENT WRAPPER --- */
        #page-content-wrapper {
            width: 100%;
            min-height: 100vh;
            margin-left: var(--sidebar-width); 
            padding-top: 10px;
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
            display: flex;
            flex-direction: column;
        }

        .container-fluid {
            padding: 1.5rem 2rem;
            flex: 1;
        }

        /* --- LOGIC: DESKTOP MINIMIZE --- */
        body.sidebar-minimized #sidebar-wrapper {
            width: var(--sidebar-collapsed-width);
            min-width: var(--sidebar-collapsed-width);
        }

        body.sidebar-minimized #page-content-wrapper {
            margin-left: var(--sidebar-collapsed-width);
        }

        /* HILANGKAN LOGO SAAT MINIMIZED */
        body.sidebar-minimized .brand-wrapper {
            display: none !important;
        }
        
        body.sidebar-minimized .sidebar-heading {
            justify-content: center;
            padding: 0;
        }

        /* Logic Minimized Lainnya */
        body.sidebar-minimized .menu-text,
        body.sidebar-minimized .sidebar-label,
        body.sidebar-minimized .profile-info {
            display: none !important;
        }

        body.sidebar-minimized .list-group-item {
            justify-content: center; padding: 0.8rem 0;
        }
        
        body.sidebar-minimized .profile-card {
            background: transparent; border: none; padding: 0;
        }
        
        body.sidebar-minimized .profile-img {
            width: 40px; height: 40px; border-width: 2px;
        }
        
        body.sidebar-minimized .list-group-item:hover::after {
            content: attr(title);
            position: absolute; left: 100%; top: 50%;
            transform: translateY(-50%); background: #1e293b; color: white;
            padding: 6px 12px; border-radius: 6px; font-size: 0.85rem;
            z-index: 9999; margin-left: 15px; white-space: nowrap; pointer-events: none;
        }
        
        body.sidebar-minimized .btn-toggle-sidebar i {
            transform: rotate(180deg);
        }

        /* --- LOGIC: MOBILE HIDDEN --- */
        @media (max-width: 768px) {
            #page-content-wrapper {
                margin-left: 0 !important;
                width: 100%;
            }

            #sidebar-wrapper {
                left: calc(var(--sidebar-width) * -1); 
            }

            body.sidebar-mobile-open #sidebar-wrapper {
                left: 0;
                box-shadow: 5px 0 25px rgba(0, 0, 0, 0.15);
            }
            
            body.sidebar-mobile-open {
                overflow: hidden;
            }
        }

        /* --- COMPONENTS STYLE --- */
        .card {
            border: none; border-radius: 20px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            background-color: white; margin-bottom: 1.5rem;
            position: relative; overflow: hidden;
        }
        .card::before {
            content: ''; position: absolute; top: 0; left: 0; width: 100%; height: 4px;
            background: linear-gradient(90deg, var(--primary), var(--secondary));
        }
        .card-header {
            background-color: white; border-bottom: 1px solid #f1f5f9;
            padding: 1.25rem 1.5rem; font-weight: 700; font-size: 1.1rem;
        }

        .alert {
            border: none; border-radius: 12px; padding: 1rem 1.2rem;
            margin-bottom: 1.5rem; display: flex; align-items: center;
        }
        .alert-success { background: #ecfdf5; color: #047857; border-left: 4px solid var(--success); }
        .alert-danger { background: #fef2f2; color: #b91c1c; border-left: 4px solid var(--danger); }
        
        .btn-primary {
            background: var(--primary); border: none; border-radius: 8px;
            padding: 0.5rem 1.2rem; transition: 0.3s;
        }
        .btn-primary:hover {
            background: var(--primary-dark); transform: translateY(-2px);
        }

        /* Overlay Mobile */
        #sidebar-overlay {
            display: none; position: fixed; top: 0; left: 0; width: 100vw; height: 100vh;
            background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px);
            z-index: 1040; opacity: 0; transition: opacity 0.3s ease;
        }
        body.sidebar-mobile-open #sidebar-overlay { display: block; opacity: 1; }

        /* --- PERBAIKAN FOOTER (LEBIH RAMPING) --- */
        .main-footer {
            background: white;
            border-top: 1px solid #e2e8f0;
            padding: 0.8rem 0; /* Padding diperkecil agar tipis */
            margin-top: auto;
            color: var(--gray);
            font-size: 0.8rem; /* Font diperkecil sedikit */
        }
    </style>
</head>
<body>

    <div id="sidebar-overlay"></div>

    <div id="wrapper">
        @include('layouts.partials.sidebar-admin')

        <div id="page-content-wrapper">
            @include('layouts.partials.topbar')

            <div class="container-fluid px-4 py-3">
                @if(session('success'))
                    <div class="alert alert-success">
                        <i class="bi bi-check-circle-fill me-2 fs-5"></i>
                        <div>{{ session('success') }}</div>
                    </div>
                @endif
                
                @if(session('error'))
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
                        <div>{{ session('error') }}</div>
                    </div>
                @endif

                @yield('content')
            </div>

            {{-- Footer yang Lebih Ramping --}}
            <footer class="main-footer">
                <div class="container-fluid text-center">
                    <p class="mb-0">
                        &copy; {{ date('Y') }} <strong>GoSchool</strong>. 
                        <span class="d-none d-sm-inline opacity-75">All rights reserved.</span>
                    </p>
                </div>
            </footer>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const body = document.body;
            
            // Toggle Desktop
            const desktopToggle = document.getElementById('sidebar-toggle-desktop');
            if (desktopToggle) {
                desktopToggle.addEventListener('click', function() {
                    body.classList.toggle('sidebar-minimized');
                    localStorage.setItem('sidebarMinimized', body.classList.contains('sidebar-minimized'));
                });
            }
            if (localStorage.getItem('sidebarMinimized') === 'true') {
                body.classList.add('sidebar-minimized');
            }

            // Toggle Mobile
            const mobileToggle = document.getElementById('sidebar-toggle-mobile');
            const overlay = document.getElementById('sidebar-overlay');
            const legacyToggle = document.getElementById('menu-toggle'); 
            const activeMobileBtn = mobileToggle || legacyToggle;

            if (activeMobileBtn) {
                activeMobileBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    body.classList.toggle('sidebar-mobile-open');
                });
            }

            if (overlay) {
                overlay.addEventListener('click', function() {
                    body.classList.remove('sidebar-mobile-open');
                });
            }

            const menuLinks = document.querySelectorAll('#sidebar-wrapper .list-group-item');
            menuLinks.forEach(link => {
                link.addEventListener('click', function() {
                    if (window.innerWidth <= 768) {
                        body.classList.remove('sidebar-mobile-open');
                    }
                });
            });

            window.addEventListener('resize', function() {
                if (window.innerWidth > 768) {
                    body.classList.remove('sidebar-mobile-open');
                }
            });
        });
    </script>
</body>
</html>