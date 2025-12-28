<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - TWOBASE</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>

<body>
    <div class="auth-page">
        <div class="auth-card">
            <a href="{{ route('home') }}" class="logo">
                <i class="fas fa-tshirt"></i>
                TWOBASE
            </a>
            <h1>Masuk</h1>
            <p>Selamat datang kembali! Silakan masuk ke akun Anda</p>

            @if(session('error'))
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    {{ session('error') }}
                </div>
            @endif

            <form action="{{ route('login') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" required
                        placeholder="email@example.com">
                    @error('email')
                        <span style="color: #e74c3c; font-size: 0.85rem;">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password" required placeholder="••••••••">
                    @error('password')
                        <span style="color: #e74c3c; font-size: 0.85rem;">{{ $message }}</span>
                    @enderror
                </div>

                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                        <input type="checkbox" name="remember" style="width: 16px; height: 16px;">
                        <span style="color: #6c757d;">Ingat saya</span>
                    </label>
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-sign-in-alt"></i>
                    Masuk
                </button>
            </form>

            <div class="auth-links">
                Belum punya akun? <a href="{{ route('register') }}">Daftar sekarang</a>
            </div>
        </div>
    </div>
</body>

</html>