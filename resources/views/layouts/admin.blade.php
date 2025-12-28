<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') - TWOBASE</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>

<body>
    <div class="admin-layout">
        <!-- Sidebar -->
        <aside class="admin-sidebar">
            <a href="{{ route('home') }}" class="logo">
                <i class="fas fa-tshirt"></i>
                TWOBASE
            </a>

            <nav class="admin-nav">
                <a href="{{ route('admin.dashboard') }}"
                    class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <i class="fas fa-chart-pie"></i>
                    Dashboard
                </a>
                <a href="{{ route('admin.products.index') }}"
                    class="{{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
                    <i class="fas fa-box"></i>
                    Produk
                </a>
                <a href="{{ route('admin.categories.index') }}"
                    class="{{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                    <i class="fas fa-tags"></i>
                    Kategori
                </a>
                <a href="{{ route('admin.orders.index') }}"
                    class="{{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
                    <i class="fas fa-shopping-bag"></i>
                    Pesanan
                </a>
                <a href="{{ route('admin.reviews.index') }}"
                    class="{{ request()->routeIs('admin.reviews.*') ? 'active' : '' }}">
                    <i class="fas fa-star"></i>
                    Ulasan
                </a>
                <div style="border-top: 1px solid rgba(255,255,255,0.1); margin: 20px 0;"></div>
                <a href="{{ route('home') }}">
                    <i class="fas fa-store"></i>
                    Lihat Toko
                </a>
                <form action="{{ route('logout') }}" method="POST" style="margin: 0;">
                    @csrf
                    <button type="submit"
                        style="width: 100%; text-align: left; padding: 15px 25px; color: rgba(255,255,255,0.7); background: none; border: none; cursor: pointer; display: flex; align-items: center; gap: 15px; font-size: 1rem;">
                        <i class="fas fa-sign-out-alt" style="width: 24px; text-align: center;"></i>
                        Logout
                    </button>
                </form>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="admin-content">
            @if(session('success'))
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    {{ session('error') }}
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    @stack('scripts')
</body>

</html>