<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SIM Keuangan Pembayaran Santri Darul Muttaqin</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #15803d 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
        }
        .login-card {
            background: #ffffff;
            border-radius: 1rem;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.2), 0 8px 10px -6px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 440px;
            overflow: hidden;
        }
        .login-header {
            background: #f0fdf4;
            border-bottom: 1px solid #dcfce7;
            padding: 2rem 2rem 1.5rem;
            text-align: center;
        }
        .btn-success {
            background-color: #15803d;
            border-color: #15803d;
            padding: 0.75rem;
            font-weight: 600;
        }
        .btn-success:hover {
            background-color: #166534;
            border-color: #166534;
        }
    </style>
</head>
<body>

    <div class="login-card">
        <div class="login-header">
            <div class="bg-success text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3 shadow" style="width: 56px; height: 56px;">
                <i class="bi bi-bank2 fs-3"></i>
            </div>
            <h5 class="fw-bold text-dark mb-1">Pondok Pesantren Darul Muttaqin</h5>
            <p class="text-muted small mb-0">Sistem Informasi Manajemen Keuangan Pembayaran Santri</p>
        </div>

        <div class="p-4 p-md-4 pt-md-3">
            @if(session('info'))
                <div class="alert alert-info py-2 small mb-3">{{ session('info') }}</div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger py-2 small mb-3">
                    {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('login.post') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label small fw-semibold text-secondary">Username</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light text-muted"><i class="bi bi-person"></i></span>
                        <input type="text" name="username" class="form-control" placeholder="Masukkan username" value="{{ old('username') }}" required autofocus>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-semibold text-secondary">Password</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light text-muted"><i class="bi bi-lock"></i></span>
                        <input type="password" name="password" class="form-control" placeholder="Masukkan password" required>
                    </div>
                </div>

                <div class="d-flex align-items-center justify-content-between mb-4">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="remember" id="remember">
                        <label class="form-check-label small text-muted" for="remember">Ingat Saya</label>
                    </div>
                </div>

                <button type="submit" class="btn btn-success w-100 mb-3 shadow-sm">
                    <i class="bi bi-box-arrow-in-right me-1"></i> Masuk ke Sistem
                </button>
            </form>

            <div class="border-top pt-3 text-center">
                <small class="text-muted d-block mb-2">Akun Demo Pengujian:</small>
                <div class="d-flex gap-2 justify-content-center">
                    <span class="badge bg-light text-dark border p-2 text-start">
                        <strong>Bendahara:</strong><br>
                        User: <code>bendahara</code> | Pass: <code>admin123</code>
                    </span>
                    <span class="badge bg-light text-dark border p-2 text-start">
                        <strong>Wali Santri:</strong><br>
                        User: <code>wali_2026001</code> | Pass: <code>2026001</code>
                    </span>
                </div>
            </div>
        </div>
    </div>

</body>
</html>

