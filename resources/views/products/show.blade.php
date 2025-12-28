@extends('layouts.app')

@section('title', $product->name . ' - TWOBASE')

@section('content')
    <section class="product-detail">
        <div class="container">
            <div class="product-detail-grid">
                <!-- Product Gallery -->
                <div class="product-gallery">
                    <div class="gallery-thumbs">
                        @if($product->images->count() > 0)
                            @foreach($product->images as $index => $image)
                                <div onclick="setMainImage('{{ asset('storage/' . $image->image_path) }}')"
                                    style="width: 80px; height: 80px; background: #f8f9fa; border-radius: 8px; overflow: hidden; cursor: pointer; {{ $image->is_primary ? 'border: 2px solid #e63946;' : 'border: 2px solid transparent;' }}">
                                    <img src="{{ asset('storage/' . $image->image_path) }}" alt="{{ $product->name }}"
                                        style="width: 100%; height: 100%; object-fit: cover;">
                                </div>
                            @endforeach
                        @else
                            @for($i = 1; $i <= 4; $i++)
                                <div
                                    style="width: 80px; height: 80px; background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); border-radius: 8px; display: flex; align-items: center; justify-content: center; {{ $i == 1 ? 'border: 2px solid #e63946;' : '' }}">
                                    <i class="fas fa-tshirt" style="font-size: 1.5rem; color: #dee2e6;"></i>
                                </div>
                            @endfor
                        @endif
                    </div>
                    <div class="gallery-main">
                        @if($product->primaryImage)
                            <img id="mainProductImage" src="{{ asset('storage/' . $product->primaryImage->image_path) }}"
                                alt="{{ $product->name }}"
                                style="width: 100%; height: 500px; object-fit: cover; border-radius: 12px;">
                        @else
                            <div
                                style="width: 100%; height: 500px; background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-tshirt" style="font-size: 8rem; color: #dee2e6;"></i>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Product Info -->
                <div class="product-detail-info">
                    <div style="margin-bottom: 15px;">
                        <a href="{{ route('products.index') }}?category={{ $product->category_id }}"
                            style="color: #e63946; font-weight: 600; font-size: 0.9rem; text-transform: uppercase;">
                            {{ $product->category->name }}
                        </a>
                    </div>

                    <h1>{{ $product->name }}</h1>

                    <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 20px;">
                        <div style="color: #f39c12;">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="fas fa-star{{ $i <= round($product->average_rating) ? '' : '-half-alt' }}"></i>
                            @endfor
                        </div>
                        <span style="color: #6c757d;">({{ $product->approvedReviews->count() }} ulasan)</span>
                        <span style="color: {{ $product->stock > 0 ? '#2ecc71' : '#e74c3c' }}; font-weight: 600;">
                            {{ $product->stock > 0 ? 'Stok: ' . $product->stock : 'Habis' }}
                        </span>
                    </div>

                    <div class="product-detail-price">
                        <span class="current">Rp {{ number_format($product->current_price, 0, ',', '.') }}</span>
                        @if($product->has_discount)
                            <span class="original">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                            <span class="discount">-{{ $product->discount_percentage }}%</span>
                        @endif
                    </div>

                    <p style="color: #6c757d; line-height: 1.8; margin-bottom: 30px;">
                        {{ $product->description }}
                    </p>

                    @auth
                        <form action="{{ route('cart.add') }}" method="POST" id="addToCartForm"
                            onsubmit="return validateCart()">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}">

                            <div class="product-options">
                                @if($product->size)
                                    <div class="option-group">
                                        <label>Ukuran <span style="color: #e63946;">*</span></label>
                                        <div class="size-options" id="sizeOptions">
                                            @foreach($product->size_options as $size)
                                                <label class="size-option" style="cursor: pointer;">
                                                    <input type="radio" name="selected_size" value="{{ $size }}" style="display: none;">
                                                    {{ trim($size) }}
                                                </label>
                                            @endforeach
                                        </div>
                                        <p id="sizeError"
                                            style="color: #e63946; font-size: 0.85rem; margin-top: 8px; display: none;">
                                            <i class="fas fa-exclamation-circle"></i> Silakan pilih ukuran
                                        </p>
                                    </div>
                                @endif

                                @if($product->color)
                                    <div class="option-group">
                                        <label>Warna <span style="color: #e63946;">*</span></label>
                                        <div class="color-options" id="colorOptions">
                                            @foreach($product->color_options as $color)
                                                <label class="size-option" style="cursor: pointer;">
                                                    <input type="radio" name="selected_color" value="{{ $color }}" style="display: none;">
                                                    {{ trim($color) }}
                                                </label>
                                            @endforeach
                                        </div>
                                        <p id="colorError"
                                            style="color: #e63946; font-size: 0.85rem; margin-top: 8px; display: none;">
                                            <i class="fas fa-exclamation-circle"></i> Silakan pilih warna
                                        </p>
                                    </div>
                                @endif

                                <div class="option-group">
                                    <label>Jumlah</label>
                                    <div class="quantity-selector">
                                        <button type="button" onclick="decrementQty()">-</button>
                                        <input type="number" name="quantity" id="quantity" value="1" min="1"
                                            max="{{ $product->stock }}" readonly>
                                        <button type="button" onclick="incrementQty()">+</button>
                                    </div>
                                </div>
                            </div>

                            <div class="add-to-cart-section">
                                <button type="submit" id="addToCartBtn" class="btn btn-primary btn-lg" {{ $product->stock < 1 ? 'disabled' : '' }}>
                                    <i class="fas fa-shopping-cart"></i>
                                    Tambah ke Keranjang
                                </button>
                            </div>
                        </form>
                    @else
                        <div style="background: #f8f9fa; padding: 20px; border-radius: 12px; text-align: center;">
                            <p style="margin-bottom: 15px;">Silakan login untuk membeli produk ini</p>
                            <a href="{{ route('login') }}" class="btn btn-primary">Login Sekarang</a>
                        </div>
                    @endauth
                </div>
            </div>

            <!-- Reviews Section -->
            <div id="reviews" style="margin-top: 60px;">
                <h2 style="font-size: 1.5rem; font-weight: 700; color: #1d3557; margin-bottom: 30px;">Ulasan Pelanggan</h2>

                @auth
                    @if($unreviewedOrderItems->count() > 0)
                        @foreach($unreviewedOrderItems as $orderItem)
                            <div style="background: white; border-radius: 16px; padding: 30px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); margin-bottom: 20px;">
                                <h3 style="font-size: 1.1rem; font-weight: 600; margin-bottom: 15px;">
                                    <i class="fas fa-star" style="color: #f39c12;"></i> Tulis Ulasan
                                </h3>
                                <p style="color: #6c757d; font-size: 0.9rem; margin-bottom: 20px; background: #f8f9fa; padding: 10px; border-radius: 8px;">
                                    <i class="fas fa-shopping-bag"></i> 
                                    Pesanan #{{ $orderItem->order->order_number }} - {{ $orderItem->order->created_at->format('d M Y') }}
                                </p>
                                <form action="{{ route('reviews.store', $product) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="order_item_id" value="{{ $orderItem->id }}">
                                    <div style="margin-bottom: 20px;">
                                        <label style="display: block; font-weight: 600; margin-bottom: 10px;">Rating</label>
                                        <div class="star-rating-input" style="display: flex; gap: 8px;">
                                            @for($i = 1; $i <= 5; $i++)
                                                <label style="cursor: pointer; font-size: 2rem;">
                                                    <input type="radio" name="rating" value="{{ $i }}" style="display: none;" {{ $i == 5 ? 'checked' : '' }}>
                                                    <i class="fas fa-star star-icon" data-rating="{{ $i }}"
                                                        style="color: {{ $i <= 5 ? '#f39c12' : '#dee2e6' }}; transition: all 0.2s;"></i>
                                                </label>
                                            @endfor
                                        </div>
                                    </div>
                                    <div style="margin-bottom: 20px;">
                                        <label style="display: block; font-weight: 600; margin-bottom: 10px;">Komentar</label>
                                        <textarea name="comment" rows="4"
                                            style="width: 100%; padding: 15px; border: 2px solid #e9ecef; border-radius: 12px;"
                                            placeholder="Bagikan pengalaman Anda dengan produk ini..."></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-paper-plane"></i> Kirim Ulasan
                                    </button>
                                </form>
                            </div>
                        @endforeach
                    @else
                        @if($product->reviews()->where('user_id', auth()->id())->exists())
                            <div style="background: #d4edda; border: 1px solid #28a745; border-radius: 12px; padding: 20px; margin-bottom: 30px;">
                                <p style="color: #155724; margin: 0;">
                                    <i class="fas fa-check-circle"></i> 
                                    Anda sudah memberikan ulasan untuk semua pembelian produk ini.
                                </p>
                            </div>
                        @else
                            <div style="background: #fff3cd; border: 1px solid #ffc107; border-radius: 12px; padding: 20px; margin-bottom: 30px;">
                                <p style="color: #856404; margin: 0;">
                                    <i class="fas fa-info-circle"></i> 
                                    Anda dapat memberikan ulasan setelah pesanan Anda selesai.
                                </p>
                            </div>
                        @endif
                    @endif
                @endauth

                <div style="display: grid; gap: 20px;">
                    @forelse($product->approvedReviews as $review)
                        <div
                            style="background: white; border-radius: 16px; padding: 25px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
                            <div style="display: flex; justify-content: space-between; margin-bottom: 15px;">
                                <div>
                                    <strong>{{ $review->user->name }}</strong>
                                    <div style="color: #f39c12; margin-top: 5px;">
                                        @for($i = 1; $i <= 5; $i++)
                                            <i class="fas fa-star{{ $i <= $review->rating ? '' : '-o' }}"></i>
                                        @endfor
                                    </div>
                                </div>
                                <span
                                    style="color: #6c757d; font-size: 0.9rem;">{{ $review->created_at->format('d M Y') }}</span>
                            </div>
                            <p style="color: #6c757d; line-height: 1.7;">{{ $review->comment }}</p>
                        </div>
                    @empty
                        <div style="text-align: center; padding: 50px; background: white; border-radius: 16px;">
                            <i class="fas fa-comments" style="font-size: 3rem; color: #dee2e6; margin-bottom: 15px;"></i>
                            <p style="color: #6c757d;">Belum ada ulasan untuk produk ini</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Related Products -->
            @if($relatedProducts->count() > 0)
                <div style="margin-top: 60px;">
                    <h2 style="font-size: 1.5rem; font-weight: 700; color: #1d3557; margin-bottom: 30px;">Produk Terkait</h2>
                    <div class="products-grid">
                        @foreach($relatedProducts as $related)
                            <a href="{{ route('products.show', $related) }}" class="product-card">
                                <div class="product-image">
                                    <div
                                        style="width: 100%; height: 100%; background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); display: flex; align-items: center; justify-content: center;">
                                        <i class="fas fa-tshirt" style="font-size: 4rem; color: #dee2e6;"></i>
                                    </div>
                                </div>
                                <div class="product-info">
                                    <div class="product-category">{{ $related->category->name }}</div>
                                    <h3 class="product-name">{{ $related->name }}</h3>
                                    <div class="product-price">
                                        <span class="current-price">Rp
                                            {{ number_format($related->current_price, 0, ',', '.') }}</span>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </section>

    @push('scripts')
        <script>
            function incrementQty() {
                const input = document.getElementById('quantity');
                const max = parseInt(input.getAttribute('max'));
                if (parseInt(input.value) < max) {
                    input.value = parseInt(input.value) + 1;
                }
            }

            function decrementQty() {
                const input = document.getElementById('quantity');
                if (parseInt(input.value) > 1) {
                    input.value = parseInt(input.value) - 1;
                }
            }

            // Change main product image
            function setMainImage(src) {
                const mainImg = document.getElementById('mainProductImage');
                if (mainImg) {
                    mainImg.src = src;
                }
            }

            // Validate cart before submit
            function validateCart() {
                let isValid = true;

                // Check size selection
                const sizeOptions = document.getElementById('sizeOptions');
                const sizeError = document.getElementById('sizeError');
                if (sizeOptions) {
                    const sizeSelected = document.querySelector('input[name="selected_size"]:checked');
                    if (!sizeSelected) {
                        sizeError.style.display = 'block';
                        isValid = false;
                    } else {
                        sizeError.style.display = 'none';
                    }
                }

                // Check color selection
                const colorOptions = document.getElementById('colorOptions');
                const colorError = document.getElementById('colorError');
                if (colorOptions) {
                    const colorSelected = document.querySelector('input[name="selected_color"]:checked');
                    if (!colorSelected) {
                        colorError.style.display = 'block';
                        isValid = false;
                    } else {
                        colorError.style.display = 'none';
                    }
                }

                if (!isValid) {
                    // Show alert notification
                    let message = 'Mohon lengkapi pilihan berikut:\n';
                    if (sizeOptions && !document.querySelector('input[name="selected_size"]:checked')) {
                        message += '• Pilih Ukuran\n';
                    }
                    if (colorOptions && !document.querySelector('input[name="selected_color"]:checked')) {
                        message += '• Pilih Warna\n';
                    }
                    alert(message);

                    // Scroll to the first error
                    const firstError = document.querySelector('#sizeError[style*="block"], #colorError[style*="block"]');
                    if (firstError) {
                        firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
                }

                return isValid;
            }

            // Size/color selection
            document.querySelectorAll('.size-option').forEach(option => {
                option.addEventListener('click', function () {
                    const parent = this.closest('.size-options, .color-options');
                    parent.querySelectorAll('.size-option').forEach(o => o.classList.remove('active'));
                    this.classList.add('active');
                });
            });

            // Interactive Star Rating
            const starContainer = document.querySelector('.star-rating-input');
            if (starContainer) {
                const stars = starContainer.querySelectorAll('.star-icon');
                let currentRating = 5;

                function updateStars(rating) {
                    stars.forEach((star, index) => {
                        if (index < rating) {
                            star.style.color = '#f39c12';
                            star.style.transform = 'scale(1.1)';
                        } else {
                            star.style.color = '#dee2e6';
                            star.style.transform = 'scale(1)';
                        }
                    });
                }

                stars.forEach((star, index) => {
                    // Hover effect
                    star.addEventListener('mouseenter', () => {
                        updateStars(index + 1);
                    });

                    // Click to select
                    star.addEventListener('click', () => {
                        currentRating = index + 1;
                        const radio = star.closest('label').querySelector('input[type="radio"]');
                        radio.checked = true;
                        updateStars(currentRating);
                    });
                });

                // Reset on mouse leave
                starContainer.addEventListener('mouseleave', () => {
                    updateStars(currentRating);
                });

                // Initialize with 5 stars selected
                updateStars(5);
            }
        </script>
    @endpush
@endsection