@extends('layouts.app')

@section('title', 'Produk - TWOBASE')

@section('content')
    <section style="padding: 40px 0 80px;">
        <div class="container">
            <div style="display: flex; gap: 30px;">
                <!-- Sidebar Filters -->
                <aside style="width: 280px; flex-shrink: 0;">
                    <div
                        style="background: white; border-radius: 16px; padding: 25px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); position: sticky; top: 120px;">
                        <h3 style="font-size: 1.2rem; font-weight: 700; color: #1d3557; margin-bottom: 25px;">Filter</h3>

                        <form action="{{ route('products.index') }}" method="GET">
                            <!-- Category Filter -->
                            <div style="margin-bottom: 25px;">
                                <label
                                    style="display: block; font-weight: 600; color: #1d3557; margin-bottom: 12px;">Kategori</label>
                                <select name="category"
                                    style="width: 100%; padding: 12px 15px; border: 2px solid #e9ecef; border-radius: 10px; font-size: 0.95rem;">
                                    <option value="">Semua Kategori</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Price Range -->
                            <div style="margin-bottom: 25px;">
                                <label
                                    style="display: block; font-weight: 600; color: #1d3557; margin-bottom: 12px;">Harga</label>
                                <div style="display: flex; flex-direction: column; gap: 10px;">
                                    <input type="number" name="min_price" placeholder="Harga Minimum"
                                        value="{{ request('min_price') }}"
                                        style="width: 100%; padding: 10px 12px; border: 2px solid #e9ecef; border-radius: 8px; box-sizing: border-box;">
                                    <input type="number" name="max_price" placeholder="Harga Maximum"
                                        value="{{ request('max_price') }}"
                                        style="width: 100%; padding: 10px 12px; border: 2px solid #e9ecef; border-radius: 8px; box-sizing: border-box;">
                                </div>
                            </div>

                            <!-- Sort -->
                            <div style="margin-bottom: 25px;">
                                <label
                                    style="display: block; font-weight: 600; color: #1d3557; margin-bottom: 12px;">Urutkan</label>
                                <select name="sort"
                                    style="width: 100%; padding: 12px 15px; border: 2px solid #e9ecef; border-radius: 10px; font-size: 0.95rem;">
                                    <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Terbaru
                                    </option>
                                    <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>Harga:
                                        Rendah ke Tinggi</option>
                                    <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>Harga:
                                        Tinggi ke Rendah</option>
                                    <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>Nama A-Z</option>
                                </select>
                            </div>

                            @if(request('search'))
                                <input type="hidden" name="search" value="{{ request('search') }}">
                            @endif

                            <button type="submit" class="btn btn-primary" style="width: 100%;">
                                <i class="fas fa-filter"></i>
                                Terapkan Filter
                            </button>
                        </form>

                        @if(request()->hasAny(['category', 'min_price', 'max_price', 'sort', 'search']))
                            <a href="{{ route('products.index') }}"
                                style="display: block; text-align: center; margin-top: 15px; color: #6c757d;">
                                <i class="fas fa-times"></i> Reset Filter
                            </a>
                        @endif
                    </div>
                </aside>

                <!-- Products Grid -->
                <div style="flex: 1;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
                        <div>
                            <h1 style="font-size: 1.8rem; font-weight: 700; color: #1d3557; margin-bottom: 5px;">
                                @if(request('search'))
                                    Hasil pencarian: "{{ request('search') }}"
                                @else
                                    Semua Produk
                                @endif
                            </h1>
                            <p style="color: #6c757d;">Menampilkan {{ $products->total() }} produk</p>
                        </div>
                    </div>

                    @if($products->count() > 0)
                        <div class="products-grid">
                            @foreach($products as $product)
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
                                            <span class="current-price">Rp
                                                {{ number_format($product->current_price, 0, ',', '.') }}</span>
                                            @if($product->has_discount)
                                                <span class="original-price">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        </div>

                        <!-- Pagination -->
                        <div class="pagination">
                            @if($products->onFirstPage())
                                <span class="disabled"><i class="fas fa-chevron-left"></i> Prev</span>
                            @else
                                <a href="{{ $products->previousPageUrl() }}"><i class="fas fa-chevron-left"></i> Prev</a>
                            @endif
                            
                            @foreach($products->getUrlRange(1, $products->lastPage()) as $page => $url)
                                @if($page == $products->currentPage())
                                    <span class="active">{{ $page }}</span>
                                @else
                                    <a href="{{ $url }}">{{ $page }}</a>
                                @endif
                            @endforeach
                            
                            @if($products->hasMorePages())
                                <a href="{{ $products->nextPageUrl() }}">Next <i class="fas fa-chevron-right"></i></a>
                            @else
                                <span class="disabled">Next <i class="fas fa-chevron-right"></i></span>
                            @endif
                        </div>
                    @else
                        <div style="text-align: center; padding: 80px 20px; background: white; border-radius: 16px;">
                            <i class="fas fa-search" style="font-size: 4rem; color: #dee2e6; margin-bottom: 20px;"></i>
                            <h3 style="color: #1d3557; margin-bottom: 10px;">Produk tidak ditemukan</h3>
                            <p style="color: #6c757d;">Coba ubah filter atau kata kunci pencarian</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>
@endsection