<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'TWOBASE') - Belanja Fashion Online Terbaik</title>
    <meta name="description"
        content="@yield('description', 'TWOBASE - Toko fashion online terlengkap dengan koleksi pakaian pria dan wanita terbaru. Diskon hingga 70%!')">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Styles -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">

    @stack('styles')
</head>

<body>
    <!-- Top Navigation Bar -->
    <nav class="navbar">
        <div class="navbar-top">
            <div class="container">
                <div class="navbar-top-left">
                    <span><i class="fas fa-truck"></i> Gratis Ongkir untuk pembelian di atas Rp 500.000</span>
                </div>
                <div class="navbar-top-right">
                    <a href="#">Pusat Bantuan</a>
                </div>
            </div>
        </div>

        <div class="navbar-main">
            <div class="container">
                <a href="{{ route('home') }}" class="logo">
                    <i class="fas fa-tshirt"></i>
                    TWOBASE
                </a>

                <form action="{{ route('products.index') }}" method="GET" class="search-bar">
                    <input type="text" name="search" placeholder="Cari produk fashion terbaik..."
                        value="{{ request('search') }}">
                    <button type="submit">
                        <i class="fas fa-search"></i>
                    </button>
                </form>

                <div class="navbar-actions">
                    @auth
                        <a href="{{ route('cart.index') }}" class="nav-icon" title="Keranjang">
                            <i class="fas fa-shopping-cart"></i>
                            <span class="badge"
                                id="cart-count">{{ \App\Models\Cart::where('user_id', auth()->id())->sum('quantity') }}</span>
                        </a>

                        <div class="dropdown" style="position: relative;">
                            <a href="#" class="nav-icon" onclick="toggleDropdown(event)" title="Akun">
                                <i class="fas fa-user"></i>
                            </a>
                            <div class="dropdown-menu" id="userDropdown"
                                style="display: none; position: absolute; right: 0; top: 50px; background: white; border-radius: 12px; box-shadow: 0 10px 40px rgba(0,0,0,0.15); min-width: 200px; z-index: 1000;">
                                <div style="padding: 15px 20px; border-bottom: 1px solid #e9ecef;">
                                    <strong>{{ auth()->user()->name }}</strong>
                                    <p style="font-size: 0.85rem; color: #6c757d; margin: 0;">{{ auth()->user()->email }}
                                    </p>
                                </div>
                                <a href="{{ route('orders.index') }}"
                                    style="display: block; padding: 12px 20px; color: #1d3557;"><i class="fas fa-box"
                                        style="width: 20px;"></i> Pesanan Saya</a>
                                <a href="{{ route('profile') }}"
                                    style="display: block; padding: 12px 20px; color: #1d3557;"><i class="fas fa-user-cog"
                                        style="width: 20px;"></i> Profil</a>
                                @if(auth()->user()->isAdmin())
                                    <a href="{{ route('admin.dashboard') }}"
                                        style="display: block; padding: 12px 20px; color: #e63946;"><i class="fas fa-cog"
                                            style="width: 20px;"></i> Admin Panel</a>
                                @endif
                                <form action="{{ route('logout') }}" method="POST" style="margin: 0;">
                                    @csrf
                                    <button type="submit"
                                        style="width: 100%; text-align: left; padding: 12px 20px; color: #e74c3c; border-top: 1px solid #e9ecef; background: none; cursor: pointer; font-size: 1rem;"><i
                                            class="fas fa-sign-out-alt" style="width: 20px;"></i> Logout</button>
                                </form>
                            </div>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-outline">Masuk</a>
                        <a href="{{ route('register') }}" class="btn btn-primary">Daftar</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Flash Messages -->
    @if(session('success'))
        <div class="container" style="margin-top: 20px;">
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                {{ session('success') }}
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="container" style="margin-top: 20px;">
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i>
                {{ session('error') }}
            </div>
        </div>
    @endif

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-col">
                    <a href="{{ route('home') }}" class="logo" style="margin-bottom: 20px;">
                        <i class="fas fa-tshirt"></i>
                        TWOBASE
                    </a>
                    <p>TWOBASE adalah toko fashion online terpercaya dengan koleksi pakaian pria dan wanita
                        berkualitas tinggi. Kami berkomitmen memberikan pengalaman belanja terbaik dengan harga
                        terjangkau.</p>
                </div>
                <div class="footer-col">
                    <h4>Kategori</h4>
                    <ul>
                        <li><a href="{{ route('products.index') }}?category=1">Kemeja Pria</a></li>
                        <li><a href="{{ route('products.index') }}?category=2">Kaos Pria</a></li>
                        <li><a href="{{ route('products.index') }}?category=3">Celana Pria</a></li>
                        <li><a href="{{ route('products.index') }}?category=4">Dress Wanita</a></li>
                        <li><a href="{{ route('products.index') }}?category=5">Blouse Wanita</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h4>Layanan Pelanggan</h4>
                    <ul>
                        <li><a href="#">Cara Belanja</a></li>
                        <li><a href="#">Pengiriman</a></li>
                        <li><a href="#">Pengembalian</a></li>
                        <li><a href="#">FAQ</a></li>
                        <li><a href="#">Hubungi Kami</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h4>Ikuti Kami</h4>
                    <ul>
                        <li><a href="#"><i class="fab fa-instagram"></i> Instagram</a></li>
                        <li><a href="#"><i class="fab fa-facebook"></i> Facebook</a></li>
                        <li><a href="#"><i class="fab fa-twitter"></i> Twitter</a></li>
                        <li><a href="#"><i class="fab fa-tiktok"></i> TikTok</a></li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; {{ date('Y') }} TWOBASE. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script>
        function toggleDropdown(event) {
            event.preventDefault();
            const dropdown = document.getElementById('userDropdown');
            dropdown.style.display = dropdown.style.display === 'none' ? 'block' : 'none';
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', function (event) {
            const dropdown = document.getElementById('userDropdown');
            if (dropdown && !event.target.closest('.dropdown')) {
                dropdown.style.display = 'none';
            }
        });

        // Format currency
        function formatCurrency(amount) {
            return 'Rp ' + amount.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        }
    </script>

    @stack('scripts')
</body>

</html>