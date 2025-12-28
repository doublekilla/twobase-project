@extends('layouts.app')

@section('title', 'Keranjang Belanja - TWOBASE')

@section('content')
<section class="cart-page">
    <div class="container">
        <h1>Keranjang Belanja</h1>
        
        @if($cartItems->count() > 0)
            <form action="{{ route('checkout') }}" method="GET" id="checkoutForm">
                <div class="cart-grid">
                    <div class="cart-items">
                        <!-- Select All -->
                        <div style="background: white; padding: 15px 20px; border-radius: 12px; margin-bottom: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
                            <label style="display: flex; align-items: center; gap: 12px; cursor: pointer; font-weight: 600;">
                                <input type="checkbox" id="selectAll" onchange="toggleSelectAll()" style="width: 20px; height: 20px; accent-color: #e63946;">
                                Pilih Semua ({{ $cartItems->count() }} produk)
                            </label>
                        </div>
                        
                        @foreach($cartItems as $item)
                            <div class="cart-item" data-item-id="{{ $item->id }}" data-price="{{ $item->product->current_price }}" data-quantity="{{ $item->quantity }}">
                                <div style="display: flex; align-items: center; gap: 15px;">
                                    <input type="checkbox" name="selected_items[]" value="{{ $item->id }}" 
                                        class="cart-checkbox" onchange="updateSummary()" 
                                        style="width: 20px; height: 20px; accent-color: #e63946; cursor: pointer;">
                                    <div class="cart-item-image">
                                        @if($item->product->primaryImage)
                                            <img src="{{ asset('storage/' . $item->product->primaryImage->image_path) }}" alt="{{ $item->product->name }}" style="width: 100%; height: 100%; object-fit: cover;">
                                        @else
                                            <div style="width: 100%; height: 100%; background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); display: flex; align-items: center; justify-content: center;">
                                                <i class="fas fa-tshirt" style="font-size: 2rem; color: #dee2e6;"></i>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="cart-item-info">
                                    <h3>{{ $item->product->name }}</h3>
                                    <div class="cart-item-details">
                                        @if($item->selected_size)
                                            Ukuran: {{ $item->selected_size }}
                                        @endif
                                        @if($item->selected_color)
                                            | Warna: {{ $item->selected_color }}
                                        @endif
                                    </div>
                                    <div class="cart-item-price">Rp {{ number_format($item->product->current_price, 0, ',', '.') }}</div>
                                </div>
                                <div class="cart-item-actions">
                                    <div class="quantity-selector" style="transform: scale(0.85);">
                                        <button type="button" class="qty-btn" data-action="decrease" data-item-id="{{ $item->id }}" data-url="{{ route('cart.update', $item) }}">-</button>
                                        <input type="number" class="qty-input" value="{{ $item->quantity }}" min="1" max="{{ $item->product->stock }}" data-item-id="{{ $item->id }}" data-url="{{ route('cart.update', $item) }}" readonly>
                                        <button type="button" class="qty-btn" data-action="increase" data-item-id="{{ $item->id }}" data-url="{{ route('cart.update', $item) }}">+</button>
                                    </div>
                                    <button type="button" class="btn btn-sm delete-btn" style="color: #e74c3c;" data-url="{{ route('cart.destroy', $item) }}">
                                        <i class="fas fa-trash"></i> Hapus
                                    </button>
                                </div>
                            </div>
                        @endforeach
                        
                        <div style="margin-top: 20px; display: flex; justify-content: space-between;">
                            <a href="{{ route('products.index') }}" class="btn btn-outline">
                                <i class="fas fa-arrow-left"></i> Lanjut Belanja
                            </a>
                            <button type="button" class="btn clear-cart-btn" style="color: #e74c3c;" data-url="{{ route('cart.clear') }}">
                                <i class="fas fa-trash-alt"></i> Kosongkan Keranjang
                            </button>
                        </div>
                    </div>
                    
                    <div class="cart-summary">
                        <h2>Ringkasan Belanja</h2>
                        <div class="summary-row">
                            <span>Item dipilih</span>
                            <span id="selectedCount">0 barang</span>
                        </div>
                        <div class="summary-row">
                            <span>Subtotal</span>
                            <span id="subtotalDisplay">Rp 0</span>
                        </div>
                        <div class="summary-row">
                            <span>Ongkos Kirim</span>
                            <span id="shippingDisplay">Rp 0</span>
                        </div>
                        <div class="summary-row total">
                            <span>Total</span>
                            <span id="totalDisplay">Rp 0</span>
                        </div>
                        <button type="submit" id="checkoutBtn" class="btn btn-primary btn-lg" style="width: 100%; margin-top: 20px;" disabled>
                            <i class="fas fa-credit-card"></i>
                            Checkout
                        </button>
                        <p id="checkoutWarning" style="color: #e63946; font-size: 0.85rem; text-align: center; margin-top: 10px;">
                            <i class="fas fa-info-circle"></i> Pilih minimal 1 produk untuk checkout
                        </p>
                    </div>
                </div>
            </form>
        @else
            <div style="text-align: center; padding: 80px 20px; background: white; border-radius: 16px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
                <i class="fas fa-shopping-cart" style="font-size: 5rem; color: #dee2e6; margin-bottom: 25px;"></i>
                <h2 style="color: #1d3557; margin-bottom: 15px;">Keranjang Belanja Kosong</h2>
                <p style="color: #6c757d; margin-bottom: 25px;">Anda belum menambahkan produk apapun ke keranjang</p>
                <a href="{{ route('products.index') }}" class="btn btn-primary btn-lg">
                    <i class="fas fa-shopping-bag"></i>
                    Mulai Belanja
                </a>
            </div>
        @endif
    </div>
</section>

@push('scripts')
<script>
    const SHIPPING_COST = 15000;
    
    function formatRupiah(num) {
        return 'Rp ' + num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }
    
    function toggleSelectAll() {
        const selectAll = document.getElementById('selectAll');
        const checkboxes = document.querySelectorAll('.cart-checkbox');
        checkboxes.forEach(cb => cb.checked = selectAll.checked);
        updateSummary();
    }
    
    function updateSummary() {
        const checkboxes = document.querySelectorAll('.cart-checkbox:checked');
        const selectAll = document.getElementById('selectAll');
        const allCheckboxes = document.querySelectorAll('.cart-checkbox');
        
        // Update select all checkbox
        selectAll.checked = checkboxes.length === allCheckboxes.length && allCheckboxes.length > 0;
        
        let subtotal = 0;
        let itemCount = 0;
        
        checkboxes.forEach(cb => {
            const cartItem = cb.closest('.cart-item');
            const price = parseFloat(cartItem.dataset.price);
            // Get quantity from the input field instead of data attribute
            const quantityInput = cartItem.querySelector('input[name="quantity"]');
            const quantity = quantityInput ? parseInt(quantityInput.value) : parseInt(cartItem.dataset.quantity);
            subtotal += price * quantity;
            itemCount += quantity;
        });
        
        const shipping = checkboxes.length > 0 ? SHIPPING_COST : 0;
        const total = subtotal + shipping;
        
        document.getElementById('selectedCount').textContent = itemCount + ' barang';
        document.getElementById('subtotalDisplay').textContent = formatRupiah(subtotal);
        document.getElementById('shippingDisplay').textContent = formatRupiah(shipping);
        document.getElementById('totalDisplay').textContent = formatRupiah(total);
        
        // Enable/disable checkout button
        const checkoutBtn = document.getElementById('checkoutBtn');
        const checkoutWarning = document.getElementById('checkoutWarning');
        if (checkboxes.length > 0) {
            checkoutBtn.disabled = false;
            checkoutWarning.style.display = 'none';
        } else {
            checkoutBtn.disabled = true;
            checkoutWarning.style.display = 'block';
        }
    }
    
    // Handle quantity buttons
    document.querySelectorAll('.qty-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const action = this.dataset.action;
            const url = this.dataset.url;
            const input = this.parentNode.querySelector('.qty-input');
            let quantity = parseInt(input.value);
            
            if (action === 'increase') {
                quantity = Math.min(quantity + 1, parseInt(input.max));
            } else if (action === 'decrease') {
                quantity = Math.max(quantity - 1, 1);
            }
            
            // Create and submit form
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = url;
            form.innerHTML = `
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <input type="hidden" name="_method" value="PUT">
                <input type="hidden" name="quantity" value="${quantity}">
            `;
            document.body.appendChild(form);
            form.submit();
        });
    });
    
    // Handle delete buttons
    document.querySelectorAll('.delete-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            if (confirm('Hapus item ini dari keranjang?')) {
                const url = this.dataset.url;
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = url;
                form.innerHTML = `
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="_method" value="DELETE">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        });
    });
    
    // Handle clear cart button
    document.querySelector('.clear-cart-btn')?.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        if (confirm('Apakah Anda yakin ingin menghapus semua produk dari keranjang?')) {
            const url = this.dataset.url;
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = url;
            form.innerHTML = `
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <input type="hidden" name="_method" value="DELETE">
            `;
            document.body.appendChild(form);
            form.submit();
        }
    });
    
    // Auto-select all items on page load
    document.addEventListener('DOMContentLoaded', function() {
        const selectAll = document.getElementById('selectAll');
        if (selectAll) {
            selectAll.checked = true;
            toggleSelectAll();
        }
    });
</script>
@endpush
@endsection
