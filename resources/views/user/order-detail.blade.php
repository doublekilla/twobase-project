@extends('layouts.app')

@section('title', 'Detail Pesanan - TWOBASE')

@section('content')
    <section style="padding: 40px 0 80px;">
        <div class="container">
            <div
                style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; flex-wrap: wrap; gap: 15px;">
                <div>
                    <a href="{{ route('orders.index') }}"
                        style="color: #6c757d; margin-bottom: 10px; display: inline-block;">
                        <i class="fas fa-arrow-left"></i> Kembali ke Pesanan
                    </a>
                    <h1 style="font-size: 2rem; font-weight: 700; color: #1d3557;">Pesanan #{{ $order->order_number }}</h1>
                </div>
                <div style="display: flex; align-items: center; gap: 15px; flex-wrap: wrap;">
                    @if($order->status === 'pending')
                        <form action="{{ route('orders.cancel', $order) }}" method="POST"
                            onsubmit="return confirm('Apakah Anda yakin ingin membatalkan pesanan ini?');">
                            @csrf
                            <button type="submit" class="btn"
                                style="background: #e74c3c; color: white; padding: 8px 16px; border-radius: 8px;">
                                <i class="fas fa-times-circle"></i> Batalkan Pesanan
                            </button>
                        </form>
                    @endif
                    <span class="status-badge {{ $order->status }}" style="font-size: 1rem; padding: 8px 16px;">
                        {{ $order->getStatusLabel() }}
                    </span>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 30px;">
                <!-- Order Items -->
                <div>
                    <div
                        style="background: white; border-radius: 16px; padding: 30px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); margin-bottom: 30px;">
                        <h2 style="font-size: 1.2rem; font-weight: 700; color: #1d3557; margin-bottom: 25px;">Item Pesanan
                        </h2>

                        @foreach($order->items as $item)
                            <div style="display: flex; gap: 20px; padding: 20px 0; border-bottom: 1px solid #e9ecef;">
                                <div
                                    style="width: 80px; height: 80px; background: #f8f9fa; border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; overflow: hidden;">
                                    @if($item->product && $item->product->primaryImage)
                                        <img src="{{ asset('storage/' . $item->product->primaryImage->image_path) }}" 
                                            alt="{{ $item->product_name }}" style="width: 100%; height: 100%; object-fit: cover;">
                                    @else
                                        <i class="fas fa-tshirt" style="font-size: 2rem; color: #dee2e6;"></i>
                                    @endif
                                </div>
                                <div style="flex: 1;">
                                    <h3 style="font-size: 1rem; font-weight: 600; color: #1d3557; margin-bottom: 5px;">
                                        {{ $item->product_name }}
                                        @if(!$item->product)
                                            <span style="font-size: 0.75rem; color: #6c757d; font-weight: normal;">(Produk tidak tersedia)</span>
                                        @endif
                                    </h3>
                                    <p style="color: #6c757d; font-size: 0.9rem; margin-bottom: 5px;">
                                        @if($item->size) Ukuran: {{ $item->size }} @endif
                                        @if($item->color) | Warna: {{ $item->color }} @endif
                                    </p>
                                    <p style="color: #6c757d; font-size: 0.9rem;">
                                        {{ $item->quantity }} x Rp {{ number_format($item->product_price, 0, ',', '.') }}
                                    </p>
                                </div>
                                <div style="font-weight: 700; color: #e63946;">
                                    Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Shipping Info -->
                    <div
                        style="background: white; border-radius: 16px; padding: 30px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); margin-bottom: 30px;">
                        <h2 style="font-size: 1.2rem; font-weight: 700; color: #1d3557; margin-bottom: 20px;">
                            <i class="fas fa-map-marker-alt"></i> Alamat Pengiriman
                        </h2>
                        <p style="line-height: 1.8; color: #6c757d;">
                            <strong style="color: #1d3557;">{{ $order->user->name }}</strong><br>
                            {{ $order->shipping_phone }}<br>
                            {{ $order->shipping_address }}
                        </p>
                        @if($order->notes)
                            <div style="margin-top: 15px; padding: 15px; background: #f8f9fa; border-radius: 10px;">
                                <strong style="color: #1d3557;">Catatan:</strong>
                                <p style="color: #6c757d; margin: 5px 0 0;">{{ $order->notes }}</p>
                            </div>
                        @endif
                    </div>

                    <!-- Payment Proof Section for Bank Transfer -->
                    @if($order->payment_method === 'transfer')
                        <div
                            style="background: white; border-radius: 16px; padding: 30px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
                            <h2 style="font-size: 1.2rem; font-weight: 700; color: #1d3557; margin-bottom: 20px;">
                                <i class="fas fa-credit-card"></i> Pembayaran Transfer Bank
                            </h2>

                            <!-- Bank Info -->
                            <div
                                style="background: linear-gradient(135deg, #1d3557 0%, #457b9d 100%); color: white; padding: 20px; border-radius: 12px; margin-bottom: 20px;">
                                <p style="opacity: 0.9; margin-bottom: 10px;">Transfer ke rekening berikut:</p>
                                <div style="display: grid; gap: 10px;">
                                    <div>
                                        <strong>Bank BCA</strong><br>
                                        No. Rek: <strong>1234567890</strong><br>
                                        A/N: <strong>TWOBASE Indonesia</strong>
                                    </div>
                                </div>
                                <div style="margin-top: 15px; padding-top: 15px; border-top: 1px solid rgba(255,255,255,0.2);">
                                    Total Transfer: <strong style="font-size: 1.3rem;">Rp
                                        {{ number_format($order->total, 0, ',', '.') }}</strong>
                                </div>
                            </div>

                            <!-- Upload Payment Proof -->
                            @if($order->payment_status !== 'paid')
                                <div style="background: #f8f9fa; padding: 20px; border-radius: 12px;">
                                    <h3 style="font-size: 1rem; font-weight: 600; color: #1d3557; margin-bottom: 15px;">
                                        <i class="fas fa-upload"></i> Upload Bukti Pembayaran
                                    </h3>

                                    <form action="{{ route('orders.uploadPaymentProof', $order) }}" method="POST"
                                        enctype="multipart/form-data">
                                        @csrf
                                        <div class="form-group" style="margin-bottom: 15px;">
                                            <input type="file" name="payment_proof" accept="image/*" required
                                                style="padding: 12px; border: 2px dashed #dee2e6; border-radius: 10px; width: 100%; background: white;">
                                            <small style="color: #6c757d;">Format: JPEG, PNG, WebP. Maks 2MB</small>
                                            @error('payment_proof')
                                                <span
                                                    style="color: #e74c3c; font-size: 0.85rem; display: block; margin-top: 5px;">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-upload"></i> Upload Bukti Pembayaran
                                        </button>
                                    </form>
                                </div>
                            @endif

                            <!-- Show uploaded proof -->
                            @if($order->payment_proof)
                                <div style="margin-top: 20px;">
                                    <h3 style="font-size: 1rem; font-weight: 600; color: #1d3557; margin-bottom: 15px;">
                                        <i class="fas fa-image"></i> Bukti Pembayaran yang Diunggah
                                    </h3>
                                    <div style="background: #f8f9fa; padding: 15px; border-radius: 12px; display: inline-block;">
                                        <img src="{{ asset('storage/' . $order->payment_proof) }}" alt="Bukti Pembayaran"
                                            style="max-width: 300px; border-radius: 8px; border: 2px solid #e9ecef;">
                                    </div>
                                    @if($order->payment_status === 'unpaid')
                                        <p style="color: #f39c12; margin-top: 10px;">
                                            <i class="fas fa-clock"></i> Menunggu konfirmasi admin...
                                        </p>
                                    @elseif($order->payment_status === 'paid')
                                        <p style="color: #2ecc71; margin-top: 10px;">
                                            <i class="fas fa-check-circle"></i> Pembayaran telah dikonfirmasi
                                        </p>
                                    @endif
                                </div>
                            @endif
                        </div>
                    @endif

                    {{-- Midtrans Payment Section for non-COD, non-transfer --}}
                    @if($order->payment_method !== 'cod' && $order->payment_method !== 'transfer' && $order->payment_status === 'unpaid')
                        <div
                            style="background: white; border-radius: 16px; padding: 30px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
                            <h2 style="font-size: 1.2rem; font-weight: 700; color: #1d3557; margin-bottom: 20px;">
                                <i class="fas fa-credit-card"></i> Pembayaran Online
                            </h2>

                            <div
                                style="background: linear-gradient(135deg, #1d3557 0%, #457b9d 100%); color: white; padding: 20px; border-radius: 12px; margin-bottom: 20px;">
                                <p style="opacity: 0.9; margin-bottom: 10px;">Total yang harus dibayar:</p>
                                <strong style="font-size: 1.5rem;">Rp {{ number_format($order->total, 0, ',', '.') }}</strong>
                            </div>

                            <button type="button" id="pay-button" class="btn btn-primary"
                                style="width: 100%; padding: 15px; font-size: 1.1rem;">
                                <i class="fas fa-lock"></i> Bayar Sekarang
                            </button>
                            <p style="text-align: center; color: #6c757d; margin-top: 15px; font-size: 0.85rem;">
                                <i class="fas fa-shield-alt"></i> Pembayaran aman melalui Midtrans
                            </p>
                        </div>

                        <script src="https://app.sandbox.midtrans.com/snap/snap.js"
                            data-client-key="{{ config('midtrans.client_key') }}"></script>
                        <script>
                            document.getElementById('pay-button').addEventListener('click', function () {
                                this.disabled = true;
                                this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memproses...';

                                fetch('{{ route("payment.snap-token", $order) }}', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                    }
                                })
                                    .then(response => response.json())
                                    .then(data => {
                                        if (data.snap_token) {
                                            snap.pay(data.snap_token, {
                                                onSuccess: function (result) {
                                                    // Update payment status before redirecting
                                                    fetch('{{ route("payment.update-status", $order) }}', {
                                                        method: 'POST',
                                                        headers: {
                                                            'Content-Type': 'application/json',
                                                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                                        },
                                                        body: JSON.stringify({ status: 'success', payment_type: result.payment_type })
                                                    }).then(() => {
                                                        window.location.href = '{{ route("payment.success") }}';
                                                    });
                                                },
                                                onPending: function (result) {
                                                    window.location.href = '{{ route("payment.pending") }}';
                                                },
                                                onError: function (result) {
                                                    window.location.href = '{{ route("payment.error") }}';
                                                },
                                                onClose: function () {
                                                    document.getElementById('pay-button').disabled = false;
                                                    document.getElementById('pay-button').innerHTML = '<i class="fas fa-lock"></i> Bayar Sekarang';
                                                }
                                            });
                                        } else {
                                            alert('Gagal memuat pembayaran: ' + (data.error || 'Unknown error'));
                                            document.getElementById('pay-button').disabled = false;
                                            document.getElementById('pay-button').innerHTML = '<i class="fas fa-lock"></i> Bayar Sekarang';
                                        }
                                    })
                                    .catch(error => {
                                        alert('Terjadi kesalahan: ' + error.message);
                                        document.getElementById('pay-button').disabled = false;
                                        document.getElementById('pay-button').innerHTML = '<i class="fas fa-lock"></i> Bayar Sekarang';
                                    });
                            });
                        </script>
                    @endif
                </div>

                <!-- Order Summary -->
                <div>
                    <div class="cart-summary">
                        <h2>Ringkasan Pesanan</h2>

                        <div class="summary-row">
                            <span>Subtotal</span>
                            <span>Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span>
                        </div>
                        <div class="summary-row">
                            <span>Ongkos Kirim</span>
                            <span>Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</span>
                        </div>
                        <div class="summary-row total">
                            <span>Total</span>
                            <span>Rp {{ number_format($order->total, 0, ',', '.') }}</span>
                        </div>

                        <div style="margin-top: 25px; padding-top: 20px; border-top: 1px solid #e9ecef;">
                            <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                                <span style="color: #6c757d;">Metode Pembayaran</span>
                                <span style="font-weight: 600;">
                                    @switch($order->payment_method)
                                        @case('transfer')
                                            Transfer Bank
                                            @break
                                        @case('midtrans')
                                            💳 Pembayaran Online
                                            @break
                                        @case('cod')
                                        @default
                                            COD (Bayar di Tempat)
                                    @endswitch
                                </span>
                            </div>
                            <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                                <span style="color: #6c757d;">Status Pembayaran</span>
                                <span
                                    class="status-badge {{ $order->payment_status }}">{{ ucfirst($order->payment_status) }}</span>
                            </div>
                            <div style="display: flex; justify-content: space-between;">
                                <span style="color: #6c757d;">Tanggal Pesanan</span>
                                <span style="font-weight: 600;">{{ $order->created_at->format('d M Y, H:i') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection