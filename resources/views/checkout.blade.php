@extends('layouts.app')

@section('title', 'Checkout - TWOBASE')

@section('content')
    <section class="checkout-page">
        <div class="container">
            <h1 style="font-size: 2rem; font-weight: 700; color: #1d3557; margin-bottom: 30px;">Checkout</h1>

            <form action="{{ route('checkout.store') }}" method="POST">
                @csrf
                <div class="checkout-grid">
                    <div class="checkout-form">
                        <div class="form-section">
                            <h3><i class="fas fa-map-marker-alt"></i> Alamat Pengiriman</h3>

                            <div class="form-group">
                                <label for="shipping_address">Alamat Lengkap *</label>
                                <textarea name="shipping_address" id="shipping_address" required
                                    placeholder="Nama jalan, nomor rumah, RT/RW, Kelurahan, Kecamatan, Kota, Provinsi, Kode Pos">{{ auth()->user()->address }}</textarea>
                                @error('shipping_address')
                                    <span style="color: #e74c3c; font-size: 0.85rem;">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="shipping_phone">Nomor Telepon *</label>
                                <input type="tel" name="shipping_phone" id="shipping_phone"
                                    value="{{ auth()->user()->phone }}" required placeholder="08xxxxxxxxxx" pattern="[0-9]*"
                                    inputmode="numeric" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                                @error('shipping_phone')
                                    <span style="color: #e74c3c; font-size: 0.85rem;">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="notes">Catatan (opsional)</label>
                                <textarea name="notes" id="notes" rows="3"
                                    placeholder="Catatan untuk penjual atau kurir..."></textarea>
                            </div>
                        </div>

                        <div class="form-section">
                            <h3><i class="fas fa-credit-card"></i> Metode Pembayaran</h3>
                            <div style="display: grid; gap: 15px;">
                                <label
                                    style="display: flex; align-items: center; gap: 15px; padding: 20px; background: #f8f9fa; border-radius: 12px; cursor: pointer; border: 2px solid #e9ecef;">
                                    <input type="radio" name="payment_method" value="cod" checked>
                                    <div>
                                        <strong>COD (Bayar di Tempat)</strong>
                                        <p style="color: #6c757d; margin: 0; font-size: 0.9rem;">Bayar saat barang sampai
                                        </p>
                                    </div>
                                </label>
                                <label
                                    style="display: flex; align-items: center; gap: 15px; padding: 20px; background: #f8f9fa; border-radius: 12px; cursor: pointer; border: 2px solid #e9ecef;">
                                    <input type="radio" name="payment_method" value="midtrans">
                                    <div>
                                        <strong>Pembayaran Online</strong>
                                        <p style="color: #6c757d; margin: 0; font-size: 0.9rem;">Kartu Kredit, GoPay, OVO,
                                            DANA, ShopeePay, Virtual Account</p>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="cart-summary">
                        <h2>Ringkasan Pesanan</h2>

                        <div style="margin-bottom: 20px; max-height: 300px; overflow-y: auto;">
                            @foreach($cartItems as $item)
                                <div style="display: flex; gap: 15px; padding: 15px 0; border-bottom: 1px solid #e9ecef;">
                                    <div
                                        style="width: 60px; height: 60px; background: #f8f9fa; border-radius: 8px; flex-shrink: 0; display: flex; align-items: center; justify-content: center;">
                                        <i class="fas fa-tshirt" style="color: #dee2e6;"></i>
                                    </div>
                                    <div style="flex: 1;">
                                        <h4 style="font-size: 0.9rem; font-weight: 600; margin-bottom: 5px;">
                                            {{ $item->product->name }}
                                        </h4>
                                        <p style="color: #6c757d; font-size: 0.8rem; margin: 0;">
                                            {{ $item->quantity }}x @ Rp
                                            {{ number_format($item->product->current_price, 0, ',', '.') }}
                                        </p>
                                    </div>
                                    <div style="font-weight: 600; color: #1d3557;">
                                        Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="summary-row">
                            <span>Subtotal</span>
                            <span>Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                        </div>
                        <div class="summary-row">
                            <span>Ongkos Kirim</span>
                            <span>Rp {{ number_format($shippingCost, 0, ',', '.') }}</span>
                        </div>
                        <div class="summary-row total">
                            <span>Total Pembayaran</span>
                            <span>Rp {{ number_format($total, 0, ',', '.') }}</span>
                        </div>

                        <button type="submit" class="btn btn-primary btn-lg" style="width: 100%; margin-top: 20px;">
                            <i class="fas fa-check"></i>
                            Buat Pesanan
                        </button>

                        <a href="{{ route('cart.index') }}"
                            style="display: block; text-align: center; margin-top: 15px; color: #6c757d;">
                            <i class="fas fa-arrow-left"></i> Kembali ke Keranjang
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </section>
@endsection