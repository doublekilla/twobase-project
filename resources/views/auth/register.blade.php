<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar - TWOBASE</title>
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
            <h1>Daftar</h1>
            <p>Buat akun baru dan mulai berbelanja</p>

            <form action="{{ route('register') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="name">Nama Lengkap</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required placeholder="John Doe">
                    @error('name')
                        <span style="color: #e74c3c; font-size: 0.85rem;">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" required
                        placeholder="email@example.com">
                    @error('email')
                        <span style="color: #e74c3c; font-size: 0.85rem;">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="phone">Nomor Telepon (opsional)</label>
                    <input type="text" name="phone" id="phone" value="{{ old('phone') }}" placeholder="08xxxxxxxxxx">
                    @error('phone')
                        <span style="color: #e74c3c; font-size: 0.85rem;">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password" required placeholder="Minimal 8 karakter">
                    @error('password')
                        <span style="color: #e74c3c; font-size: 0.85rem;">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password_confirmation">Konfirmasi Password</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" required
                        placeholder="Ulangi password">
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-user-plus"></i>
                    Daftar
                </button>
            </form>

            <div class="auth-links">
                Sudah punya akun? <a href="{{ route('login') }}">Masuk disini</a>
            </div>
        </div>
    </div>
</body>

</html>