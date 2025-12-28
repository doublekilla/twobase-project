@extends('layouts.app')

@section('title', 'TWOBASE - Belanja Fashion Online Terbaik')

@section('content')
<!-- Hero Section -->
<section class="hero">
    <div class="container">
        <div class="hero-content">
            <h1>Temukan <span>Gaya Terbaikmu</span> Disini</h1>
            <p>Koleksi fashion terlengkap dengan kualitas premium dan harga terjangkau. Gratis ongkir untuk pembelian pertama!</p>
            <div style="display: flex; gap: 15px;">
                <a href="{{ route('products.index') }}" class="btn btn-primary btn-lg">
                    <i class="fas fa-shopping-bag"></i>
                    Belanja Sekarang
                </a>
                <a href="{{ route('products.index') }}?featured=1" class="btn btn-outline btn-lg" style="border-color: white; color: white;">
                    Lihat Koleksi
                </a>
            </div>
            <div class="hero-stats">
                <div class="stat-item">
                    <div class="number">10K+</div>
                    <div class="label">Produk</div>
                </div>
                <div class="stat-item">
                    <div class="number">50K+</div>
                    <div class="label">Pelanggan</div>
                </div>
                <div class="stat-item">
                    <div class="number">98%</div>
                    <div class="label">Puas</div>
                </div>
            </div>
        </div>
        <div class="hero-image">
            @if($featuredProducts->first() && $featuredProducts->first()->primaryImage)
                <img src="{{ asset('storage/' . $featuredProducts->first()->primaryImage->image_path) }}" 
                    alt="Fashion Terbaik" 
                    style="width: 100%; height: 400px; object-fit: cover; border-radius: 20px; box-shadow: 0 20px 60px rgba(0,0,0,0.3);">
            @else
                <div style="width: 100%; height: 400px; background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); border-radius: 20px; display: flex; align-items: center; justify-content: center;">
                    <div style="text-align: center; color: #6c757d;">
                        <i class="fas fa-tshirt" style="font-size: 5rem; margin-bottom: 20px; color: #e63946;"></i>
                        <p style="font-size: 1.2rem;">Fashion Terbaik</p>
                    </div>
                </div>
            @endif
            @if($featuredProducts->first() && $featuredProducts->first()->has_discount)
                <div class="promo-badge">
                    <div class="discount">{{ $featuredProducts->first()->discount_percentage }}%</div>
                    <div class="text">DISKON</div>
                </div>
            @endif
        </div>
    </div>
</section>

<!-- Categories Section -->
<section class="categories-section">
    <div class="container">
        <div class="section-header">
            <h2>Kategori Populer</h2>
            <p>Temukan koleksi fashion berdasarkan kategori favoritmu</p>
        </div>
        <div class="categories-marquee" style="overflow: hidden; position: relative;">
            <div class="categories-track" style="display: flex; gap: 20px; animation: scrollCategories 20s linear infinite;">
                @foreach($categories as $category)
                    <a href="{{ route('products.index') }}?category={{ $category->id }}" class="category-card" 
                        style="flex-shrink: 0; min-width: 140px;">
                        <div class="icon">
                            @switch($category->slug)
                                @case('kemeja-pria')
                                    <i class="fas fa-user-tie"></i>
                                    @break
                                @case('kaos-pria')
                                    <i class="fas fa-tshirt"></i>
                                    @break
                                @case('celana-pria')
                                    <i class="fas fa-socks"></i>
                                    @break
                                @case('dress-wanita')
                                    <i class="fas fa-female"></i>
                                    @break
                                @case('blouse-wanita')
                                    <i class="fas fa-vest"></i>
                                    @break
                                @default
                                    <i class="fas fa-gem"></i>
                            @endswitch
                        </div>
                        <h3>{{ $category->name }}</h3>
                        <span>{{ $category->products_count }} Produk</span>
                    </a>
                @endforeach
                {{-- Duplicate for seamless loop --}}
                @foreach($categories as $category)
                    <a href="{{ route('products.index') }}?category={{ $category->id }}" class="category-card" 
                        style="flex-shrink: 0; min-width: 140px;">
                        <div class="icon">
                            @switch($category->slug)
                                @case('kemeja-pria')
                                    <i class="fas fa-user-tie"></i>
                                    @break
                                @case('kaos-pria')
                                    <i class="fas fa-tshirt"></i>
                                    @break
                                @case('celana-pria')
                                    <i class="fas fa-socks"></i>
                                    @break
                                @case('dress-wanita')
                                    <i class="fas fa-female"></i>
                                    @break
                                @case('blouse-wanita')
                                    <i class="fas fa-vest"></i>
                                    @break
                                @default
                                    <i class="fas fa-gem"></i>
                            @endswitch
                        </div>
                        <h3>{{ $category->name }}</h3>
                        <span>{{ $category->products_count }} Produk</span>
                    </a>
                @endforeach
            </div>
        </div>
    </div>
</section>

<style>
@keyframes scrollCategories {
    0% {
        transform: translateX(0);
    }
    100% {
        transform: translateX(-50%);
    }
}

.categories-marquee:hover .categories-track {
    animation-play-state: paused;
}
</style>

<!-- Featured Products Section -->
<section class="products-section">
    <div class="container">
        <div class="section-header">
            <h2>Produk Unggulan</h2>
            <p>Koleksi terbaik pilihan kami untuk kamu</p>
        </div>
        <div class="products-grid">
            @foreach($featuredProducts as $product)
                <a href="{{ route('products.show', $product) }}" class="product-card">
                    <div class="product-image">
                        @if($product->primaryImage)
                            <img src="{{ asset('storage/' . $product->primaryImage->image_path) }}" alt="{{ $product->name }}">
                        @else
                            <div style="width: 100%; height: 100%; background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-tshirt" style="font-size: 4rem; color: #dee2e6;"></i>
                            </div>
                        @endif
                        <div class="product-badges">
                            @if($product->has_discount)
                                <span class="badge badge-sale">-{{ $product->discount_percentage }}%</span>
                            @endif
                            @if($product->is_featured)
                                <span class="badge badge-hot">HOT</span>
                            @endif
                        </div>
                    </div>
                    <div class="product-info">
                        <div class="product-category">{{ $product->category->name }}</div>
                        <h3 class="product-name">{{ $product->name }}</h3>
                        <div class="product-rating">
                            <span class="stars">
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= round($product->average_rating))
                                        <i class="fas fa-star" style="color: #f39c12;"></i>
                                    @else
                                        <i class="far fa-star" style="color: #dee2e6;"></i>
                                    @endif
                                @endfor
                            </span>
                            <span>({{ $product->approved_reviews_count ?? 0 }})</span>
                        </div>
                        <div class="product-price">
                            <span class="current-price">Rp {{ number_format($product->current_price, 0, ',', '.') }}</span>
                            @if($product->has_discount)
                                <span class="original-price">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                            @endif
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
        <div style="text-align: center; margin-top: 40px;">
            <a href="{{ route('products.index') }}" class="btn btn-primary btn-lg">Lihat Semua Produk</a>
        </div>
    </div>
</section>

<!-- Latest Products Section -->
<section class="products-section" style="background: #f8f9fa;">
    <div class="container">
        <div class="section-header">
            <h2>Produk Terbaru</h2>
            <p>Update koleksi terbaru yang baru saja hadir</p>
        </div>
        <div class="products-grid">
            @foreach($latestProducts as $product)
                <a href="{{ route('products.show', $product) }}" class="product-card">
                    <div class="product-image">
                        @if($product->primaryImage)
                            <img src="{{ asset('storage/' . $product->primaryImage->image_path) }}" alt="{{ $product->name }}">
                        @else
                            <div style="width: 100%; height: 100%; background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-tshirt" style="font-size: 4rem; color: #dee2e6;"></i>
                            </div>
                        @endif
                        <div class="product-badges">
                            <span class="badge badge-new">NEW</span>
                            @if($product->has_discount)
                                <span class="badge badge-sale">-{{ $product->discount_percentage }}%</span>
                            @endif
                        </div>
                    </div>
                    <div class="product-info">
                        <div class="product-category">{{ $product->category->name }}</div>
                        <h3 class="product-name">{{ $product->name }}</h3>
                        <div class="product-rating">
                            <span class="stars">
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= round($product->average_rating))
                                        <i class="fas fa-star" style="color: #f39c12;"></i>
                                    @else
                                        <i class="far fa-star" style="color: #dee2e6;"></i>
                                    @endif
                                @endfor
                            </span>
                            <span>({{ $product->approved_reviews_count ?? 0 }})</span>
                        </div>
                        <div class="product-price">
                            <span class="current-price">Rp {{ number_format($product->current_price, 0, ',', '.') }}</span>
                            @if($product->has_discount)
                                <span class="original-price">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                            @endif
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</section>

<!-- Promo Banner -->
<section style="padding: 80px 0; background: linear-gradient(135deg, #e63946 0%, #c62836 100%); color: white;">
    <div class="container" style="text-align: center;">
        <h2 style="font-size: 2.5rem; margin-bottom: 15px;">Promo Spesial 12.12</h2>
        <p style="font-size: 1.2rem; opacity: 0.9; margin-bottom: 30px;">Diskon hingga 70% + Gratis Ongkir</p>
        <a href="{{ route('products.index') }}" class="btn btn-lg" style="background: white; color: #e63946;">
            <i class="fas fa-fire"></i>
            Belanja Sekarang
        </a>
    </div>
</section>
@endsection
