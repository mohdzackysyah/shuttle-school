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
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    {{-- AOS Animation CSS --}}
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    <style>
        :root {
            --primary: #2563eb; 
            --primary-dark: #1e40af;
            --secondary: #fbbf24;
            --accent: #38bdf8;
            --dark: #0f172a;
            --light: #f8fafc;
            --gray: #64748b;
        }

        html, body {
            overflow-x: hidden;
            width: 100%;
            font-family: 'Poppins', sans-serif;
            color: var(--dark);
            background-color: #fff;
        }

        /* --- Navbar --- */
        .navbar {
            padding: 1rem 0;
            transition: all 0.4s ease;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(0,0,0,0.05);
        }
        
        .navbar.scrolled {
            padding: 0.5rem 0;
            background: rgba(255, 255, 255, 0.98);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        }

        .nav-link {
            font-weight: 500;
            color: var(--gray) !important;
            margin: 0 1rem;
            position: relative;
        }

        .nav-link:hover, .nav-link.active {
            color: var(--primary) !important;
        }

        /* Tombol Login */
        .btn-login {
            background: var(--primary);
            color: white;
            padding: 0.5rem 1.5rem;
            border-radius: 50px;
            font-weight: 600;
            border: none;
            box-shadow: 0 4px 15px rgba(37, 99, 235, 0.2);
            transition: all 0.3s ease;
            white-space: nowrap;
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(37, 99, 235, 0.3);
            background: var(--primary-dark);
            color: white;
        }

        /* --- Hero Section --- */
        .hero-section {
            padding: 140px 0 100px;
            position: relative;
            background: radial-gradient(circle at 10% 20%, #f0f9ff 0%, #fff 90%);
            overflow: hidden;
        }

        .hero-blob {
            position: absolute;
            top: -10%;
            right: -5%;
            width: 600px;
            height: 600px;
            background: linear-gradient(135deg, rgba(37, 99, 235, 0.08), rgba(56, 189, 248, 0.08));
            border-radius: 50%;
            filter: blur(80px);
            z-index: 0;
            animation: pulse-blob 8s infinite alternate;
        }

        @keyframes pulse-blob {
            0% { transform: scale(1); }
            100% { transform: scale(1.1); }
        }

        .hero-content {
            position: relative;
            z-index: 2;
        }

        .hero-title {
            font-size: 3.5rem;
            font-weight: 800;
            line-height: 1.2;
            margin-bottom: 1.5rem;
            color: var(--dark);
        }

        .hero-desc {
            font-size: 1.1rem;
            color: var(--gray);
            margin-bottom: 2.5rem;
            line-height: 1.8;
            max-width: 500px;
        }

        .btn-hero-primary {
            background: var(--primary);
            color: white;
            padding: 1rem 2.5rem;
            border-radius: 50px;
            font-weight: 600;
            border: none;
            box-shadow: 0 10px 30px rgba(37, 99, 235, 0.25);
            transition: all 0.3s ease;
            display: inline-block;
        }

        .btn-hero-secondary {
            background: white;
            color: var(--dark);
            padding: 1rem 2.5rem;
            border-radius: 50px;
            font-weight: 600;
            border: 2px solid #e2e8f0;
            transition: all 0.3s ease;
            margin-left: 10px;
            display: inline-block;
        }

        .btn-hero-primary:hover, .btn-hero-secondary:hover {
            transform: translateY(-3px);
        }

        .hero-img-wrapper {
            position: relative;
            z-index: 2;
            padding: 20px;
        }
        
        .hero-img-frame {
            border-radius: 30px;
            overflow: hidden;
            box-shadow: 0 25px 60px -15px rgba(0, 0, 0, 0.1);
            transform: rotate(-2deg);
            background: white;
            padding: 10px;
            border: 1px solid rgba(0,0,0,0.05);
            transition: transform 0.3s;
        }

        .hero-img {
            width: 100%;
            height: auto;
            border-radius: 20px;
            display: block;
        }

        /* --- Features Section --- */
        .features-section {
            padding: 80px 0;
            background-color: #fff;
        }

        .feature-card {
            background: white;
            padding: 2.5rem 2rem;
            border-radius: 24px;
            transition: all 0.3s;
            height: 100%;
            border: 1px solid #f1f5f9;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }

        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
        }

        .icon-box {
            width: 60px;
            height: 60px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            margin-bottom: 1.5rem;
        }

        .icon-blue { background: #eff6ff; color: var(--primary); }
        .icon-yellow { background: #fffbeb; color: #d97706; }
        .icon-green { background: #ecfdf5; color: #059669; }

        /* --- Footer --- */
        footer {
            background-color: var(--dark);
            color: #94a3b8;
            padding: 60px 0 30px;
        }
        
        .footer-link {
            text-decoration: none;
            color: #94a3b8;
            display: block;
            margin-bottom: 10px;
            transition: 0.3s;
        }
        
        .footer-link:hover { color: white; padding-left: 5px; }

        /* RESPONSIVE */
        @media (max-width: 991px) {
            .navbar-collapse {
                background: white;
                padding: 1rem;
                border-radius: 15px;
                box-shadow: 0 10px 30px rgba(0,0,0,0.1);
                margin-top: 10px;
            }
            .hero-title { font-size: 2.8rem; }
            .hero-img-wrapper { margin-top: 3rem; transform: none; }
            .hero-img-frame { transform: rotate(0deg); }
        }

        @media (max-width: 768px) {
            .hero-section {
                padding: 100px 0 60px;
                text-align: center;
            }
            .hero-title { font-size: 2.2rem; }
            .hero-desc {
                font-size: 1rem;
                margin-left: auto;
                margin-right: auto;
            }
            .hero-blob {
                width: 300px;
                height: 300px;
                right: -100px;
            }
            .d-flex.gap-3.flex-wrap {
                justify-content: center;
            }
            .btn-hero-secondary {
                margin-left: 0;
                margin-top: 10px;
            }
            .stats-container {
                justify-content: center;
            }
            .hero-img-wrapper {
                padding: 0;
                margin-top: 2.5rem;
            }
            .cta-section h2 { font-size: 1.8rem; }
            .btn-cta { width: 100%; }
        }
    </style>
</head>
<body>

    {{-- NAVBAR --}}
    <nav class="navbar navbar-expand-lg fixed-top" id="mainNav">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="#">
                <img src="{{ asset('images/logo.png') }}" alt="Logo" style="height: 50px; width: auto; object-fit: contain;">
            </a>
            <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav mx-auto mb-2 mb-lg-0 text-center">
                    <li class="nav-item"><a class="nav-link" href="#beranda">Beranda</a></li>
                    <li class="nav-item"><a class="nav-link" href="#fitur">Fitur & Layanan</a></li>
                    <li class="nav-item"><a class="nav-link" href="#kontak">Hubungi Kami</a></li>
                </ul>
                <div class="d-flex gap-2 justify-content-center mt-3 mt-lg-0">
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/dashboard') }}" class="btn btn-login w-100">
                                <i class="bi bi-speedometer2 me-2"></i>Dashboard
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="btn btn-login">
                                Login <i class="bi bi-arrow-right ms-1"></i>
                            </a>
                        @endauth
                    @endif
                </div>
            </div>
        </div>
    </nav>

    {{-- HERO SECTION --}}
    <section id="beranda" class="hero-section">
        <div class="hero-blob"></div>
        
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 hero-content order-1 order-lg-1" data-aos="fade-right">
                    <span class="badge bg-warning text-dark px-3 py-2 rounded-pill mb-3 fw-bold shadow-sm">
                        <i class="bi bi-star-fill me-1"></i> Solusi Sekolah Modern
                    </span>
                    <h1 class="hero-title">
                        Perjalanan Aman, <br>
                        <span class="text-primary">Hati Orang Tua Tenang</span>
                    </h1>
                    <p class="hero-desc">
                        Platform manajemen antar jemput sekolah dengan real-time tracking. Pantau lokasi siswa, notifikasi otomatis, dan armada terpercaya.
                    </p>
                    <div class="d-flex gap-3 flex-wrap justify-content-md-start justify-content-center">
                        <a href="{{ route('login') }}" class="btn btn-hero-primary">
                            Mulai Sekarang
                        </a>
                        <a href="#fitur" class="btn btn-hero-secondary">
                            Pelajari Dulu
                        </a>
                    </div>

                    {{-- Mini Stats --}}
                    <div class="d-flex mt-5 gap-4 pt-4 border-top justify-content-center justify-content-lg-start stats-container">
                        <div class="text-center text-lg-start">
                            <h3 class="fw-bold m-0 text-primary">500+</h3>
                            <p class="small text-muted m-0">SISWA AKTIF</p>
                        </div>
                        <div class="border-end mx-2"></div>
                        <div class="text-center text-lg-start">
                            <h3 class="fw-bold m-0 text-primary">100%</h3>
                            <p class="small text-muted m-0">AMAN</p>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6 text-center hero-img-wrapper order-2 order-lg-2" data-aos="fade-left" data-aos-delay="200">
                    <div class="hero-img-frame">
                        <img src="https://img.freepik.com/free-vector/school-bus-concept-illustration_114360-1144.jpg?w=826&t=st=1701234567~exp=1701235167~hmac=abcdef" alt="School Bus Illustration" class="hero-img">
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- FEATURES SECTION --}}
    <section id="fitur" class="features-section">
        <div class="container">
            <div class="text-center mb-5" data-aos="zoom-in"> {{-- Ganti fade-up jadi zoom-in agar lebih kerasa saat naik --}}
                <span class="section-tag">Kenapa Kami?</span>
                <h2 class="fw-bold mb-3">Fitur Unggulan ShuttleApp</h2>
                <p class="text-muted mx-auto" style="max-width: 600px;">
                    Teknologi terkini untuk keamanan dan kenyamanan perjalanan siswa Anda
                </p>
            </div>

            <div class="row g-4">
                {{-- Fitur 1 --}}
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
                    <div class="feature-card">
                        <div class="icon-box icon-blue">
                            <i class="bi bi-geo-alt-fill"></i>
                        </div>
                        <h4 class="fw-bold mb-3">Real-time Tracking</h4>
                        <p class="text-muted mb-0">Pantau posisi kendaraan penjemput secara langsung melalui aplikasi dengan GPS akurat dan update setiap detik.</p>
                    </div>
                </div>

                {{-- Fitur 2 --}}
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
                    <div class="feature-card">
                        <div class="icon-box icon-yellow">
                            <i class="bi bi-bell-fill"></i>
                        </div>
                        <h4 class="fw-bold mb-3">Notifikasi Pintar</h4>
                        <p class="text-muted mb-0">Dapatkan notifikasi instant via WhatsApp atau push notification saat siswa dijemput atau tiba di sekolah.</p>
                    </div>
                </div>

                {{-- Fitur 3 --}}
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="300">
                    <div class="feature-card">
                        <div class="icon-box icon-green">
                            <i class="bi bi-shield-check"></i>
                        </div>
                        <h4 class="fw-bold mb-3">Driver Terpercaya</h4>
                        <p class="text-muted mb-0">Seluruh driver terverifikasi, memiliki lisensi resmi, dan terlatih khusus untuk keamanan siswa.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- CTA SECTION --}}
    <section class="py-5 bg-primary position-relative overflow-hidden text-center text-white" style="background: linear-gradient(135deg, var(--primary), var(--primary-dark));">
        <div class="container position-relative py-5" data-aos="zoom-in">
            <h2 class="fw-bold mb-3">Siap Bergabung dengan Kami?</h2>
            <p class="mb-4 opacity-75 fs-5">Daftarkan sekolah atau anak Anda sekarang.</p>
            <a href="{{ route('login') }}" class="btn btn-warning btn-lg rounded-pill fw-bold px-5 btn-cta">
                Masuk ke Aplikasi
            </a>
        </div>
    </section>

    {{-- FOOTER --}}
    <footer id="kontak">
        <div class="container">
            <div class="row gy-4">
                <div class="col-lg-4 col-md-6">
                    <h4 class="text-white fw-bold mb-4">ShuttleApp</h4>
                    <p class="text-secondary">
                        Aplikasi manajemen transportasi sekolah yang menghubungkan Pihak Sekolah, Driver, dan Orang Tua dalam satu platform.
                    </p>
                </div>
                <div class="col-lg-2 col-md-6">
                    <h5 class="text-white mb-3">Menu</h5>
                    <ul class="list-unstyled">
                        <li><a href="#beranda" class="footer-link">Beranda</a></li>
                        <li><a href="#fitur" class="footer-link">Fitur</a></li>
                        <li><a href="{{ route('login') }}" class="footer-link">Login User</a></li>
                        <li><a href="{{ route('admin.login') }}" class="footer-link">Login Admin</a></li>
                    </ul>
                </div>
                <div class="col-lg-3 col-md-6">
                    <h5 class="text-white mb-3">Kontak</h5>
                    <p class="text-secondary mb-2"><i class="bi bi-envelope me-2"></i> info@shuttleapp.com</p>
                    <p class="text-secondary mb-2"><i class="bi bi-telephone me-2"></i> +62 812 3456 7890</p>
                    <p class="text-secondary"><i class="bi bi-geo-alt me-2"></i> Jakarta, Indonesia</p>
                </div>
                <div class="col-lg-3 col-md-6">
                    <h5 class="text-white mb-3">Sosial Media</h5>
                    <div class="d-flex gap-3">
                        <a href="#" class="text-white fs-5"><i class="bi bi-instagram"></i></a>
                        <a href="#" class="text-white fs-5"><i class="bi bi-facebook"></i></a>
                        <a href="#" class="text-white fs-5"><i class="bi bi-youtube"></i></a>
                    </div>
                </div>
            </div>
            <div class="border-top border-secondary mt-5 pt-4 text-center">
                <small class="text-secondary">&copy; {{ date('Y') }} ShuttleApp System. All rights reserved.</small>
            </div>
        </div>
    </footer>

    {{-- Bootstrap JS --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    {{-- AOS Animation JS --}}
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    
    <script>
        // Init Animation Berulang
        AOS.init({
            once: false,          // WAJIB FALSE: agar animasi berulang setiap kali masuk viewport
            mirror: true,         // WAJIB TRUE: agar elemen animasi saat scroll ke atas (masuk dari atas)
            anchorPlacement: 'top-bottom', // Animasi mulai saat bagian atas elemen menyentuh bagian bawah layar
            offset: 50,           // Jarak trigger (semakin kecil, semakin cepat animasi mulai saat masuk layar)
            duration: 800,        // Durasi animasi
        });

        // Navbar Scroll Effect
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