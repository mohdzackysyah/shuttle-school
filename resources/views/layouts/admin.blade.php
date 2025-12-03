<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Shuttle Sekolah</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    <style>
        :root {
            --primary: #2563eb;
            --primary-dark: #1e40af;
            --secondary: #fbbf24;
            --dark: #1e293b;
            --light: #f8fafc;
            --gray: #64748b;
            --success: #10b981;
            --danger: #ef4444;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
            overflow-x: hidden;
            color: var(--dark);
            line-height: 1.6;
        }

        /* Smooth Scroll */
        html {
            scroll-behavior: smooth;
        }

        /* --- 1. SIDEBAR STYLE --- */
        #sidebar-wrapper {
            min-width: 280px;
            max-width: 280px;
            background: white;
            border-right: 1px solid rgba(37, 99, 235, 0.1);
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            z-index: 1050;
            transition: all 0.3s ease;
            overflow-y: auto;
            box-shadow: 4px 0 20px rgba(0, 0, 0, 0.04);
        }

        /* Sidebar Header/Logo - SAMA SEPERTI NAVBAR */
        .sidebar-heading {
            padding: 2rem 1.5rem;
            font-size: 1.6rem;
            font-weight: 800;
            color: var(--dark);
            display: flex;
            align-items: center;
            gap: 10px;
            border-bottom: 2px solid rgba(37, 99, 235, 0.1);
            background: linear-gradient(135deg, #f0f9ff, #e0f2fe);
            transition: transform 0.2s;
        }

        .sidebar-heading:hover {
            transform: scale(1.02);
        }

        /* Logo Bus Kuning - SAMA PERSIS dengan landing page */
        .sidebar-heading .brand-icon {
            font-size: 2rem;
            color: var(--secondary); /* Kuning #fbbf24 */
        }

        /* Sidebar Profile Card */
        .sidebar-profile {
            padding: 1.5rem;
            background: linear-gradient(135deg, #f0f9ff, #e0f2fe);
            margin: 1rem;
            border-radius: 16px;
            margin-bottom: 1.5rem;
            box-shadow: 0 4px 15px rgba(37, 99, 235, 0.08);
            position: relative;
            overflow: hidden;
        }

        .sidebar-profile::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 3px;
            background: linear-gradient(90deg, var(--primary), var(--secondary));
        }

        .sidebar-profile .profile-avatar {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 1.5rem;
            margin: 0 auto 1rem;
            box-shadow: 0 4px 15px rgba(37, 99, 235, 0.3);
        }

        .sidebar-profile .profile-name {
            font-weight: 700;
            font-size: 1.1rem;
            color: var(--dark);
            text-align: center;
            margin-bottom: 0.3rem;
        }

        .sidebar-profile .profile-role {
            font-size: 0.85rem;
            color: var(--gray);
            text-align: center;
            font-weight: 500;
        }

        /* Sidebar Label */
        .sidebar-label {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: var(--gray);
            padding: 1.5rem 1.5rem 0.8rem;
            font-weight: 700;
        }

        /* Menu Items */
        .list-group-item {
            border: none;
            padding: 0.8rem 1.5rem;
            font-weight: 500;
            color: var(--gray);
            font-size: 0.95rem;
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 4px;
            transition: all 0.3s ease;
            position: relative;
        }

        .list-group-item i {
            font-size: 1.2rem;
            width: 24px;
            text-align: center;
        }

        .list-group-item:hover {
            color: var(--primary);
            background-color: var(--light);
            padding-left: 2rem;
        }

        .list-group-item.active {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white;
            border-radius: 12px;
            margin: 0 1rem;
            width: calc(100% - 2rem);
            box-shadow: 0 4px 15px rgba(37, 99, 235, 0.3);
        }

        .list-group-item.active:hover {
            padding-left: 1.5rem;
        }

        /* --- 2. CONTENT STYLE --- */
        #page-content-wrapper {
            width: 100%;
            margin-left: 280px;
            padding: 2rem;
            transition: all 0.3s ease;
            min-height: 100vh;
        }

        /* --- 3. OVERLAY --- */
        #sidebar-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(30, 41, 59, 0.7);
            backdrop-filter: blur(4px);
            z-index: 1040;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        /* --- 4. RESPONSIVE --- */
        @media (max-width: 768px) {
            #sidebar-wrapper {
                left: -280px;
            }

            #page-content-wrapper {
                margin-left: 0;
                padding: 1rem;
            }

            body.sb-sidenav-toggled #sidebar-wrapper {
                left: 0;
                box-shadow: 5px 0 25px rgba(0, 0, 0, 0.15);
            }

            body.sb-sidenav-toggled #sidebar-overlay {
                display: block;
                opacity: 1;
            }

            body.sb-sidenav-toggled {
                overflow: hidden;
            }

            .sidebar-heading {
                padding: 1.5rem;
                font-size: 1.3rem;
            }

            .sidebar-heading .brand-icon {
                font-size: 1.6rem;
            }
        }

        /* Card Styling */
        .card {
            border: none;
            border-radius: 24px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
            background-color: white;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            overflow: hidden;
            position: relative;
        }

        .card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, var(--primary), var(--secondary));
            transform: scaleX(0);
            transition: transform 0.4s ease;
        }

        .card:hover::before {
            transform: scaleX(1);
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
        }

        .card-header {
            background: linear-gradient(135deg, #f0f9ff, #e0f2fe);
            border-bottom: 2px solid rgba(37, 99, 235, 0.1);
            padding: 1.5rem;
            font-weight: 700;
            font-size: 1.1rem;
            color: var(--dark);
            border-radius: 24px 24px 0 0 !important;
        }

        /* Alert Styling */
        .alert {
            border: none;
            border-radius: 16px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            padding: 1rem 1.2rem;
            margin-bottom: 1.5rem;
            font-weight: 500;
            animation: slideDown 0.4s ease;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .alert-success {
            background: linear-gradient(135deg, #d1fae5, #a7f3d0);
            color: #065f46;
            border-left: 4px solid var(--success);
        }

        .alert-danger {
            background: linear-gradient(135deg, #fee2e2, #fecaca);
            color: #991b1b;
            border-left: 4px solid var(--danger);
        }

        .alert i {
            font-size: 1.2rem;
            margin-right: 0.5rem;
        }

        /* Scrollbar Styling */
        #sidebar-wrapper::-webkit-scrollbar {
            width: 6px;
        }

        #sidebar-wrapper::-webkit-scrollbar-track {
            background: var(--light);
        }

        #sidebar-wrapper::-webkit-scrollbar-thumb {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            border-radius: 10px;
        }

        #sidebar-wrapper::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(135deg, var(--primary-dark), #f59e0b);
        }

        /* Button Styling */
        .btn-primary {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            border: none;
            border-radius: 50px;
            padding: 0.6rem 1.5rem;
            font-weight: 600;
            box-shadow: 0 4px 15px rgba(37, 99, 235, 0.3);
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(37, 99, 235, 0.4);
        }
    </style>
</head>
<body>

    <div id="sidebar-overlay"></div>

    <div class="d-flex" id="wrapper">
        
        @include('layouts.partials.sidebar-admin')

        <div id="page-content-wrapper">
            
            @include('layouts.partials.topbar')

            <div class="container-fluid">
                @if(session('success'))
                    <div class="alert alert-success border-0 shadow-sm rounded-3">
                        <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                    </div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger border-0 shadow-sm rounded-3">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
                    </div>
                @endif

                @yield('content')
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Ambil elemen
        const body = document.body;
        const toggleButton = document.getElementById("menu-toggle");
        const overlay = document.getElementById("sidebar-overlay");

        // Fungsi Buka/Tutup Menu
        function toggleMenu(e) {
            if(e) e.preventDefault();
            body.classList.toggle("sb-sidenav-toggled");
        }

        // Event Listener Tombol Hamburger
        if(toggleButton){
            toggleButton.addEventListener("click", toggleMenu);
        }

        // Event Listener Overlay
        if(overlay){
            overlay.addEventListener("click", toggleMenu);
        }

        // Auto close menu saat klik menu item di mobile
        const menuItems = document.querySelectorAll('#sidebar-wrapper .list-group-item');
        menuItems.forEach(item => {
            item.addEventListener('click', function() {
                if(window.innerWidth <= 768) {
                    toggleMenu();
                }
            });
        });
    </script>
</body>
</html>
