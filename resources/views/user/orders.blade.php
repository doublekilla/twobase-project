@extends('layouts.app')

@section('title', 'Pesanan Saya - TWOBASE')

@section('content')
    <section style="padding: 40px 0 80px;">
        <div class="container">
            <h1 style="font-size: 2rem; font-weight: 700; color: #1d3557; margin-bottom: 30px;">Pesanan Saya</h1>

            <!-- Filter Tabs -->
            <div style="display: flex; gap: 10px; flex-wrap: wrap; margin-bottom: 25px;">
                <a href="{{ route('orders.index') }}" 
                    class="btn {{ !request('status') ? 'btn-primary' : 'btn-outline' }}" style="font-size: 0.9rem;">
                    <i class="fas fa-list"></i> Semua
                </a>
                <a href="{{ route('orders.index', ['status' => 'pending']) }}" 
                    class="btn {{ request('status') == 'pending' ? 'btn-primary' : 'btn-outline' }}" style="font-size: 0.9rem;">
                    <i class="fas fa-clock"></i> Menunggu
                </a>
                <a href="{{ route('orders.index', ['status' => 'processing']) }}" 
                    class="btn {{ request('status') == 'processing' ? 'btn-primary' : 'btn-outline' }}" style="font-size: 0.9rem;">
                    <i class="fas fa-cog"></i> Diproses
                </a>
                <a href="{{ route('orders.index', ['status' => 'shipped']) }}" 
                    class="btn {{ request('status') == 'shipped' ? 'btn-primary' : 'btn-outline' }}" style="font-size: 0.9rem;">
                    <i class="fas fa-truck"></i> Dikirim
                </a>
                <a href="{{ route('orders.index', ['status' => 'completed']) }}" 
                    class="btn {{ request('status') == 'completed' ? 'btn-primary' : 'btn-outline' }}" style="font-size: 0.9rem;">
                    <i class="fas fa-check-circle"></i> Selesai
                </a>
                <a href="{{ route('orders.index', ['status' => 'cancelled']) }}" 
                    class="btn {{ request('status') == 'cancelled' ? 'btn-primary' : 'btn-outline' }}" style="font-size: 0.9rem;">
                    <i class="fas fa-times-circle"></i> Dibatalkan
                </a>
            </div>

            @if($orders->count() > 0)
                <!-- Order Cards (Mobile Friendly) -->
                <div style="display: flex; flex-direction: column; gap: 15px;">
                    @foreach($orders as $order)
                        <div style="background: white; border-radius: 12px; padding: 20px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
                            <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 10px; margin-bottom: 15px;">
                                <div>
                                    <div style="font-weight: 700; color: #1d3557; font-size: 1.1rem;">{{ $order->order_number }}</div>
                                    <div style="color: #6c757d; font-size: 0.85rem;">{{ $order->created_at->format('d M Y, H:i') }}</div>
                                </div>
                                <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                                    <span class="status-badge {{ $order->status }}">{{ $order->getStatusLabel() }}</span>
                                    <span class="status-badge {{ $order->payment_status }}">{{ $order->getPaymentStatusLabel() }}</span>
                                </div>
                            </div>
                            
                            <!-- Order Items Preview -->
                            <div style="border-top: 1px solid #eee; padding-top: 15px; margin-bottom: 15px;">
                                @foreach($order->items->take(2) as $item)
                                    <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 10px;">
                                        <div style="width: 50px; height: 50px; background: #f8f9fa; border-radius: 8px; overflow: hidden; flex-shrink: 0;">
                                            @if($item->product && $item->product->primaryImage)
                                                <img src="{{ asset('storage/' . $item->product->primaryImage->image_path) }}" 
                                                    style="width: 100%; height: 100%; object-fit: cover;">
                                            @else
                                                <div style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center;">
                                                    <i class="fas fa-tshirt" style="color: #dee2e6;"></i>
                                                </div>
                                            @endif
                                        </div>
                                        <div style="flex: 1; min-width: 0;">
                                            <div style="font-weight: 600; color: #1d3557; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                                {{ $item->product_name }}
                                            </div>
                                            <div style="font-size: 0.85rem; color: #6c757d;">
                                                {{ $item->quantity }}x Rp {{ number_format($item->product_price, 0, ',', '.') }}
                                            </div>
                                        </div>
                                        @if($order->status === 'completed' && $item->product)
                                            <a href="{{ route('products.show', $item->product) }}#reviews" 
                                                class="btn btn-outline" style="font-size: 0.75rem; padding: 6px 12px;">
                                                <i class="fas fa-star"></i> Ulas Produk
                                            </a>
                                        @endif
                                    </div>
                                @endforeach
                                @if($order->items->count() > 2)
                                    <div style="color: #6c757d; font-size: 0.85rem;">
                                        +{{ $order->items->count() - 2 }} produk lainnya
                                    </div>
                                @endif
                            </div>
                            
                            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
                                <div>
                                    <span style="color: #6c757d; font-size: 0.9rem;">Total:</span>
                                    <span style="font-weight: 700; color: #e63946; font-size: 1.1rem;">
                                        Rp {{ number_format($order->total, 0, ',', '.') }}
                                    </span>
                                </div>
                                <a href="{{ route('orders.show', $order) }}" class="btn btn-primary" style="font-size: 0.9rem;">
                                    <i class="fas fa-eye"></i> Lihat Detail
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="pagination" style="margin-top: 30px;">
                    @if($orders->onFirstPage())
                        <span class="disabled"><i class="fas fa-chevron-left"></i> Prev</span>
                    @else
                        <a href="{{ $orders->previousPageUrl() }}"><i class="fas fa-chevron-left"></i> Prev</a>
                    @endif
                    
                    @foreach($orders->getUrlRange(1, $orders->lastPage()) as $page => $url)
                        @if($page == $orders->currentPage())
                            <span class="active">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}">{{ $page }}</a>
                        @endif
                    @endforeach
                    
                    @if($orders->hasMorePages())
                        <a href="{{ $orders->nextPageUrl() }}">Next <i class="fas fa-chevron-right"></i></a>
                    @else
                        <span class="disabled">Next <i class="fas fa-chevron-right"></i></span>
                    @endif
                </div>
            @else
                <div style="text-align: center; padding: 80px 20px; background: white; border-radius: 16px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
                    <i class="fas fa-box-open" style="font-size: 5rem; color: #dee2e6; margin-bottom: 25px;"></i>
                    <h2 style="color: #1d3557; margin-bottom: 15px;">
                        @if(request('status'))
                            Tidak Ada Pesanan {{ ucfirst(request('status')) }}
                        @else
                            Belum Ada Pesanan
                        @endif
                    </h2>
                    <p style="color: #6c757d; margin-bottom: 25px;">
                        @if(request('status'))
                            Anda tidak memiliki pesanan dengan status ini.
                        @else
                            Anda belum memiliki pesanan. Mulai berbelanja sekarang!
                        @endif
                    </p>
                    <a href="{{ route('products.index') }}" class="btn btn-primary btn-lg">
                        <i class="fas fa-shopping-bag"></i>
                        Mulai Belanja
                    </a>
                </div>
            @endif
        </div>
    </section>
@endsection