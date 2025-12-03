<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Pengguna - Shuttle Sekolah</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { 
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%); 
            height: 100vh; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
        }
        .card-login { 
            max-width: 400px; 
            width: 100%; 
            border: none; 
            box-shadow: 0 10px 20px rgba(0,0,0,0.1); 
            border-radius: 15px; 
        }
        /* Menghilangkan panah spinner pada input number di beberapa browser */
        input::-webkit-outer-spin-button,
        input::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }
        input[type=number] {
            -moz-appearance: textfield;
        }
    </style>
</head>
<body>
    <div class="card card-login bg-white p-4">
        <div class="text-center mb-4">
            <h1 class="mb-2">🚍</h1>
            <h4 class="fw-bold text-dark">Shuttle Sekolah</h4>
            <p class="text-muted small">Portal Masuk Driver & Wali Murid</p>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger py-2 small text-center mb-3 rounded-3">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('login.post') }}" method="POST">
            @csrf <div class="mb-3">
                <label class="form-label fw-bold small text-secondary">Nomor WhatsApp / HP</label>
                <input type="tel" name="phone" class="form-control form-control-lg bg-light" 
                       placeholder="Contoh: 0812xxx" required autofocus autocomplete="username">
            </div>
            <div class="mb-4">
                <label class="form-label fw-bold small text-secondary">Password</label>
                <input type="password" name="password" class="form-control form-control-lg bg-light" 
                       placeholder="******" required autocomplete="current-password">
            </div>
            <button type="submit" class="btn btn-primary w-100 fw-bold rounded-3 py-2 shadow-sm">
                MASUK SEBAGAI PENGGUNA
            </button>
        </form>
        
        <div class="text-center mt-4 pt-3 border-top">
            <small class="text-muted">Admin Sekolah? <a href="{{ route('admin.login') }}" class="text-decoration-none">Login disini</a></small>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>