<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Portal - Shuttle Sekolah</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #1a1d20; height: 100vh; display: flex; align-items: center; justify-content: center; }
        .card-login { max-width: 380px; width: 100%; border: 1px solid #2c3034; box-shadow: 0 0 40px rgba(0,0,0,0.5); border-radius: 12px; background-color: #2c3034; color: #e0e0e0; }
        .form-control { background-color: #212529; border: 1px solid #495057; color: #fff; padding: 12px; }
        .form-control:focus { background-color: #212529; color: #fff; border-color: #0d6efd; box-shadow: none; }
        .btn-admin { background-color: #0d6efd; color: #fff; font-weight: bold; padding: 10px; border: none; }
        .btn-admin:hover { background-color: #0b5ed7; }
    </style>
</head>
<body>
    <div class="card card-login p-4">
        <div class="text-center mb-4">
            <h2 class="mb-2">🛡️</h2>
            <h5 class="fw-bold text-uppercase ls-1 text-white">Admin Panel</h5>
            <p class="text-secondary small">Hanya untuk Administrator</p>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger py-2 small bg-danger text-white border-0 text-center mb-3">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('admin.login.post') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label small text-secondary">Username Admin</label>
                <input type="text" name="username" class="form-control rounded-3" placeholder="Masukkan username" required autofocus>
            </div>
            <div class="mb-4">
                <label class="form-label small text-secondary">Password</label>
                <input type="password" name="password" class="form-control rounded-3" placeholder="******" required>
            </div>
            <button type="submit" class="btn btn-admin w-100 rounded-3 shadow">LOGIN ADMIN</button>
        </form>
        
        <div class="text-center mt-4">
            <a href="{{ route('login') }}" class="text-secondary small text-decoration-none">
                &larr; Kembali ke Login Pengguna
            </a>
        </div>
    </div>
</body>
</html>