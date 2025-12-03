<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>School Shuttle - Antar Jemput Aman & Nyaman</title>
    
    {{-- Bootstrap 5 CSS --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    {{-- Bootstrap Icons --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    {{-- Google Fonts: Poppins --}}
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary: #2563eb;
            --primary-dark: #1e40af;
            --secondary: #fbbf24;
            --dark: #1e293b;
            --light: #f8fafc;
            --gray: #64748b;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            overflow-x: hidden;
            color: var(--dark);
            line-height: 1.6;
        }
        
        /* Smooth Scroll */
        html {
            scroll-behavior: smooth;
        }

        /* Navbar */
        .navbar {
            padding: 0.5rem 0; /* Padding diperkecil sedikit karena logo sudah tinggi */
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
            background-color: rgba(255, 255, 255, 0.95) !important;
        }
        
        .navbar.scrolled {
            box-shadow: 0 2px 20px rgba(0,0,0,0.08);
        }
        
        .navbar-brand {
            font-weight: 800;
            font-size: 1.6rem;
            color: var(--dark) !important;
            transition: transform 0.2s;
            padding: 0;
        }
        
        .navbar-brand:hover {
            transform: scale(1.02);
        }
        
        .nav-link {
            font-weight: 500;
            color: var(--gray) !important;
            margin: 0 0.8rem;
            position: relative;
            transition: color 0.3s;
        }
        
        .nav-link::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: -5px;
            left: 50%;
            background: var(--primary);
            transition: all 0.3s ease;
            transform: translateX(-50%);
        }
        
        .nav-link:hover::after {
            width: 80%;
        }
        
        .nav-link:hover {
            color: var(--primary) !important;
        }
        
        .btn-login {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white;
            padding: 0.7rem 2rem;
            border-radius: 50px;
            font-weight: 600;
            border: none;
            box-shadow: 0 4px 15px rgba(37, 99, 235, 0.3);
            transition: all 0.3s ease;
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 25px rgba(37, 99, 235, 0.4);
            color: white;
        }

        /* Hero Section */
        .hero-section {
            padding: 120px 0 100px;
            background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
            position: relative;
            overflow: hidden;
        }
        
        .hero-section::before {
            content: '';
            position: absolute;
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(37, 99, 235, 0.1) 0%, transparent 70%);
            top: -300px;
            right: -200px;
            border-radius: 50%;
        }
        
        .hero-title {
            font-size: 3.8rem;
            font-weight: 800;
            line-height: 1.15;
            margin-bottom: 1.5rem;
            color: var(--dark);
            animation: fadeInUp 0.8s ease;
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .hero-desc {
            font-size: 1.15rem;
            color: var(--gray);
            margin-bottom: 2.5rem;
            max-width: 550px;
            line-height: 1.8;
            animation: fadeInUp 0.8s ease 0.2s both;
        }
        
        .hero-img {
            max-width: 100%;
            border-radius: 24px;
            box-shadow: 0 25px 50px rgba(0,0,0,0.12);
            animation: float 6s ease-in-out infinite;
            transition: transform 0.3s;
        }
        
        .hero-img:hover {
            transform: scale(1.02);
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-25px); }
        }

        .badge-hero {
            background: linear-gradient(135deg, #fef3c7, #fde68a);
            color: #92400e;
            font-weight: 600;
            padding: 0.6rem 1.5rem;
            border-radius: 50px;
            display: inline-block;
            margin-bottom: 1.5rem;
            animation: fadeInUp 0.8s ease 0.1s both;
            box-shadow: 0 4px 12px rgba(251, 191, 36, 0.2);
        }

        .btn-hero-primary {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white;
            padding: 1rem 2.5rem;
            border-radius: 50px;
            font-weight: 600;
            border: none;
            box-shadow: 0 10px 30px rgba(37, 99, 235, 0.3);
            transition: all 0.3s ease;
            animation: fadeInUp 0.8s ease 0.3s both;
        }
        
        .btn-hero-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 40px rgba(37, 99, 235, 0.4);
            color: white;
        }
        
        .btn-hero-secondary {
            background: white;
            color: var(--dark);
            padding: 1rem 2.5rem;
            border-radius: 50px;
            font-weight: 600;
            border: 2px solid #e2e8f0;
            transition: all 0.3s ease;
            animation: fadeInUp 0.8s ease 0.4s both;
        }
        
        .btn-hero-secondary:hover {
            background: var(--dark);
            color: white;
            border-color: var(--dark);
            transform: translateY(-3px);
        }

        /* Mini Stats */
        .stat-item {
            text-align: center;
            animation: fadeInUp 0.8s ease 0.5s both;
        }
        
        .stat-item h3 {
            font-weight: 800;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            font-size: 3rem;
            margin-bottom: 0.5rem;
        }
        
        .stat-item p {
            font-weight: 600;
            color: var(--gray);
            font-size: 0.95rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Features Section */
        .section-header {
            text-align: center;
            margin-bottom: 4rem;
        }
        
        .section-label {
            color: var(--primary);
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 2px;
            font-size: 0.85rem;
            margin-bottom: 1rem;
        }
        
        .section-title {
            font-weight: 800;
            font-size: 2.8rem;
            color: var(--dark);
            margin-bottom: 1rem;
        }
        
        .feature-card {
            border: none;
            border-radius: 24px;
            padding: 2.5rem 2rem;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            background: white;
            box-shadow: 0 4px 20px rgba(0,0,0,0.04);
            height: 100%;
            position: relative;
            overflow: hidden;
        }
        
        .feature-card::before {
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
        
        .feature-card:hover::before {
            transform: scaleX(1);
        }
        
        .feature-card:hover {
            transform: translateY(-12px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.12);
        }
        
        .icon-box {
            width: 80px;
            height: 80px;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.2rem;
            margin-bottom: 1.8rem;
            transition: all 0.3s ease;
            position: relative;
        }
        
        .feature-card:hover .icon-box {
            transform: scale(1.1) rotate(5deg);
        }
        
        .feature-card h4 {
            font-weight: 700;
            margin-bottom: 1rem;
            color: var(--dark);
            font-size: 1.4rem;
        }
        
        .feature-card p {
            color: var(--gray);
            line-height: 1.7;
            margin: 0;
        }

        /* CTA Section */
        .cta-section {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            padding: 5rem 0;
            position: relative;
            overflow: hidden;
        }
        
        .cta-section::before {
            content: '';
            position: absolute;
            width: 400px;
            height: 400px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 50%;
            top: -200px;
            right: -100px;
        }
        
        .cta-section::after {
            content: '';
            position: absolute;
            width: 300px;
            height: 300px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 50%;
            bottom: -150px;
            left: -50px;
        }
        
        .cta-section h2 {
            font-weight: 800;
            font-size: 2.8rem;
            margin-bottom: 1rem;
        }
        
        .cta-section p {
            font-size: 1.2rem;
            opacity: 0.9;
        }
        
        .btn-cta {
            background: var(--secondary);
            color: var(--dark);
            padding: 1.2rem 3rem;
            border-radius: 50px;
            font-weight: 700;
            border: none;
            box-shadow: 0 10px 30px rgba(251, 191, 36, 0.3);
            transition: all 0.3s ease;
            font-size: 1.1rem;
        }
        
        .btn-cta:hover {
            background: #f59e0b;
            transform: translateY(-3px) scale(1.05);
            box-shadow: 0 15px 40px rgba(251, 191, 36, 0.4);
            color: var(--dark);
        }

        /* Footer */
        footer {
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            color: #cbd5e1;
            padding: 4rem 0 2rem;
        }
        
        footer h4, footer h5 {
            color: white;
            font-weight: 700;
            margin-bottom: 1.5rem;
        }
        
        .footer-link {
            color: #94a3b8;
            text-decoration: none;
            margin-bottom: 0.8rem;
            display: block;
            transition: all 0.3s;
            padding-left: 0;
        }
        
        .footer-link:hover {
            color: white;
            padding-left: 8px;
        }
        
        .newsletter-input {
            border: none;
            border-radius: 50px 0 0 50px;
            padding: 0.8rem 1.2rem;
        }
        
        .newsletter-btn {
            border-radius: 0 50px 50px 0;
            background: var(--secondary);
            color: var(--dark);
            font-weight: 700;
            border: none;
            padding: 0.8rem 1.5rem;
            transition: all 0.3s;
        }
        
        .newsletter-btn:hover {
            background: #f59e0b;
        }

        /* Responsive */
        @media (max-width: 991px) {
            .hero-title {
                font-size: 2.8rem;
            }
            .section-title {
                font-size: 2.2rem;
            }
            .nav-link {
                margin: 0.5rem 0;
            }
        }
        
        @media (max-width: 768px) {
            .hero-title {
                font-size: 2.2rem;
            }
            .hero-section {
                padding: 80px 0 60px;
            }
            .stat-item h3 {
                font-size: 2.2rem;
            }
            .cta-section h2 {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>

    {{-- NAVBAR --}}
    <nav class="navbar navbar-expand-lg bg-white sticky-top" id="mainNav">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="#">
                {{-- PERUBAHAN: Logo Diperbesar menjadi 120px --}}
                <img src="{{ asset('images/logo.png') }}" alt="Logo" style="height: 80px; width: auto; object-fit: contain;">
            </a>
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item"><a class="nav-link" href="#beranda">Beranda</a></li>
                    <li class="nav-item"><a class="nav-link" href="#fitur">Fitur & Layanan</a></li>
                    <li class="nav-item"><a class="nav-link" href="#kontak">Hubungi Kami</a></li>
                </ul>
                
                @if (Route::has('login'))
                    <div class="d-flex">
                        @auth
                            <a href="{{ url('/dashboard') }}" class="btn btn-login">
                                <i class="bi bi-speedometer2 me-2"></i>Dashboard
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="btn btn-login">
                                <i class="bi bi-box-arrow-in-right me-2"></i>Masuk / Login
                            </a>
                        @endauth
                    </div>
                @endif
            </div>
        </div>
    </nav>

    {{-- HERO SECTION --}}
    <section id="beranda" class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <span class="badge-hero">
                        <i class="bi bi-star-fill me-1"></i> Solusi Antar Jemput Sekolah Terpercaya
                    </span>
                    <h1 class="hero-title">
                        Perjalanan Aman, <br> 
                        <span class="text-primary">Hati Orang Tua Tenang</span>
                    </h1>
                    <p class="hero-desc">
                        Platform manajemen antar jemput sekolah modern dengan real-time tracking. Pantau lokasi siswa, notifikasi otomatis, dan armada terpercaya untuk keamanan maksimal.
                    </p>
                    <div class="d-flex gap-3 flex-wrap">
                        <a href="{{ route('login') }}" class="btn btn-hero-primary">
                            <i class="bi bi-rocket-takeoff me-2"></i>Mulai Sekarang
                        </a>
                        <a href="#fitur" class="btn btn-hero-secondary">
                            <i class="bi bi-play-circle me-2"></i>Pelajari Lebih Lanjut
                        </a>
                    </div>
                    
                    {{-- Mini Stats --}}
                    <div class="row mt-5 g-4">
                        <div class="col-4 stat-item">
                            <h3>500+</h3>
                            <p>Siswa Aktif</p>
                        </div>
                        <div class="col-4 stat-item">
                            <h3>20+</h3>
                            <p>Armada Siap</p>
                        </div>
                        <div class="col-4 stat-item">
                            <h3>100%</h3>
                            <p>Aman & Nyaman</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 mt-5 mt-lg-0 text-center">
                    <img src="https://img.freepik.com/free-vector/school-bus-concept-illustration_114360-1144.jpg?w=826&t=st=1701234567~exp=1701235167~hmac=abcdef" alt="School Bus Illustration" class="hero-img">
                </div>
            </div>
        </div>
    </section>

    {{-- FITUR SECTION --}}
    <section id="fitur" class="py-5 bg-light">
        <div class="container py-5">
            <div class="section-header">
                <p class="section-label">Kenapa Memilih Kami?</p>
                <h2 class="section-title">Fitur Unggulan ShuttleApp</h2>
                <p class="text-muted mx-auto" style="max-width: 600px;">Teknologi terkini untuk keamanan dan kenyamanan perjalanan siswa Anda</p>
            </div>

            <div class="row g-4">
                {{-- Fitur 1 --}}
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="icon-box bg-info bg-opacity-10 text-info">
                            <i class="bi bi-geo-alt-fill"></i>
                        </div>
                        <h4>Real-time Tracking</h4>
                        <p>Pantau posisi kendaraan penjemput secara langsung melalui aplikasi dengan GPS akurat dan update setiap detik.</p>
                    </div>
                </div>

                {{-- Fitur 2 --}}
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="icon-box bg-warning bg-opacity-10 text-warning">
                            <i class="bi bi-bell-fill"></i>
                        </div>
                        <h4>Notifikasi Pintar</h4>
                        <p>Dapatkan notifikasi instant via WhatsApp atau push notification saat siswa dijemput, tiba di sekolah, atau sampai di rumah.</p>
                    </div>
                </div>

                {{-- Fitur 3 --}}
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="icon-box bg-success bg-opacity-10 text-success">
                            <i class="bi bi-shield-check"></i>
                        </div>
                        <h4>Driver Terpercaya</h4>
                        <p>Seluruh driver terverifikasi, memiliki lisensi resmi, dan terlatih khusus untuk menjaga keamanan dan kenyamanan siswa.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- CTA SECTION --}}
    <section class="cta-section">
        <div class="container text-center text-white position-relative" style="z-index: 1;">
            <h2 class="mb-3">Siap Bergabung dengan Kami?</h2>
            <p class="lead mb-4 opacity-90">Daftarkan sekolah atau anak Anda sekarang untuk pengalaman antar jemput terbaik dan teraman</p>
            <a href="{{ route('login') }}" class="btn btn-cta">
                <i class="bi bi-arrow-right-circle me-2"></i>Masuk ke Aplikasi
            </a>
        </div>
    </section>

    {{-- FOOTER --}}
    <footer id="kontak">
        <div class="container">
            <div class="row gy-4">
                <div class="col-lg-4 col-md-6">
                    <h4 class="mb-3">
                        <i class="bi bi-bus-front-fill me-2 text-warning"></i> ShuttleApp
                    </h4>
                    <p class="text-secondary">
                        Aplikasi manajemen transportasi sekolah yang menghubungkan Pihak Sekolah, Driver, dan Orang Tua dalam satu platform terintegrasi.
                    </p>
                    <div class="mt-4">
                        <a href="#" class="text-secondary me-3 fs-5"><i class="bi bi-facebook"></i></a>
                        <a href="#" class="text-secondary me-3 fs-5"><i class="bi bi-instagram"></i></a>
                        <a href="#" class="text-secondary me-3 fs-5"><i class="bi bi-twitter"></i></a>
                        <a href="#" class="text-secondary fs-5"><i class="bi bi-linkedin"></i></a>
                    </div>
                </div>
                <div class="col-lg-2 col-md-6">
                    <h5 class="mb-3">Tautan</h5>
                    <a href="#beranda" class="footer-link">Beranda</a>
                    <a href="#fitur" class="footer-link">Fitur</a>
                    <a href="{{ route('login') }}" class="footer-link">Login User</a>
                    <a href="{{ route('admin.login') }}" class="footer-link">Login Admin</a>
                </div>
                <div class="col-lg-3 col-md-6">
                    <h5 class="mb-3">Kontak</h5>
                    <p class="text-secondary mb-2"><i class="bi bi-envelope me-2"></i> info@shuttleapp.com</p>
                    <p class="text-secondary mb-2"><i class="bi bi-telephone me-2"></i> +62 812 3456 7890</p>
                    <p class="text-secondary"><i class="bi bi-geo-alt me-2"></i> Jakarta, Indonesia</p>
                </div>
                <div class="col-lg-3 col-md-6">
                    <h5 class="mb-3">Newsletter</h5>
                    <p class="text-secondary small mb-3">Dapatkan update terbaru langsung ke email Anda</p>
                    <form action="#">
                        <div class="input-group">
                            <input type="email" class="form-control newsletter-input" placeholder="Email Anda" required>
                            <button class="btn newsletter-btn" type="submit">Kirim</button>
                        </div>
                    </form>
                </div>
            </div>
            <hr class="border-secondary mt-5 mb-4">
            <div class="text-center text-secondary">
                <small>&copy; {{ date('Y') }} ShuttleApp Management System. All rights reserved. Made with <i class="bi bi-heart-fill text-danger"></i></small>
            </div>
        </div>
    </footer>

    {{-- Bootstrap JS --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    {{-- Scroll Animation --}}
    <script>
        // Navbar scroll effect
        window.addEventListener('scroll', function() {
            const navbar = document.getElementById('mainNav');
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });
    </script>
</body>
</html>