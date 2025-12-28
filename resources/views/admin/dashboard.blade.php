@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
    <div class="admin-header">
        <h1>Dashboard</h1>
        <p style="color: #6c757d;">Selamat datang, {{ auth()->user()->name }}!</p>
    </div>

    <!-- Stats Grid -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="icon primary">
                <i class="fas fa-box"></i>
            </div>
            <div class="info">
                <h3>{{ $stats['total_products'] }}</h3>
                <p>Total Produk</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="icon success">
                <i class="fas fa-shopping-bag"></i>
            </div>
            <div class="info">
                <h3>{{ $stats['total_orders'] }}</h3>
                <p>Total Pesanan</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="icon warning">
                <i class="fas fa-clock"></i>
            </div>
            <div class="info">
                <h3>{{ $stats['pending_orders'] }}</h3>
                <p>Pesanan Pending</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="icon info">
                <i class="fas fa-money-bill-wave"></i>
            </div>
            <div class="info">
                <h3>Rp {{ number_format($stats['total_revenue'], 0, ',', '.') }}</h3>
                <p>Total Pendapatan</p>
            </div>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 30px;">
        <!-- Recent Orders -->
        <div class="data-table">
            <div
                style="padding: 20px; border-bottom: 1px solid #e9ecef; display: flex; justify-content: space-between; align-items: center;">
                <h2 style="font-size: 1.2rem; font-weight: 700; color: #1d3557;">Pesanan Terbaru</h2>
                <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-outline">Lihat Semua</a>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>No. Pesanan</th>
                        <th>Pelanggan</th>
                        <th>Total</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentOrders as $order)
                        <tr>
                            <td><strong>{{ $order->order_number }}</strong></td>
                            <td>{{ $order->user->name }}</td>
                            <td>Rp {{ number_format($order->total, 0, ',', '.') }}</td>
                            <td>
                                <span class="status-badge {{ $order->status }}">{{ ucfirst($order->status) }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" style="text-align: center; color: #6c757d;">Belum ada pesanan</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Low Stock Products -->
        <div class="data-table">
            <div style="padding: 20px; border-bottom: 1px solid #e9ecef;">
                <h2 style="font-size: 1.2rem; font-weight: 700; color: #1d3557;">Stok Menipis</h2>
            </div>
            <div style="padding: 15px;">
                @forelse($lowStockProducts as $product)
                    <div
                        style="display: flex; justify-content: space-between; align-items: center; padding: 12px 0; border-bottom: 1px solid #e9ecef;">
                        <div>
                            <strong style="color: #1d3557;">{{ $product->name }}</strong>
                            <p style="color: #6c757d; font-size: 0.85rem; margin: 0;">{{ $product->category->name ?? '-' }}</p>
                        </div>
                        <span class="status-badge cancelled">{{ $product->stock }} tersisa</span>
                    </div>
                @empty
                    <p style="text-align: center; color: #6c757d; padding: 20px;">Semua stok aman</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Top Selling Section -->
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px; margin-top: 30px;">
        <!-- Top Selling Products -->
        <div class="data-table">
            <div style="padding: 20px; border-bottom: 1px solid #e9ecef;">
                <h2 style="font-size: 1.2rem; font-weight: 700; color: #1d3557;">
                    <i class="fas fa-fire" style="color: #e63946;"></i> Produk Terlaris
                </h2>
            </div>
            <div style="padding: 15px;">
                @forelse($topProducts as $index => $product)
                    <div
                        style="display: flex; justify-content: space-between; align-items: center; padding: 12px 0; border-bottom: 1px solid #e9ecef;">
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <span
                                style="width: 28px; height: 28px; border-radius: 50%; background: {{ $index == 0 ? '#ffd700' : ($index == 1 ? '#c0c0c0' : ($index == 2 ? '#cd7f32' : '#e9ecef')) }}; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.85rem; color: {{ $index < 3 ? '#1d3557' : '#6c757d' }};">
                                {{ $index + 1 }}
                            </span>
                            <strong style="color: #1d3557;">{{ $product->name }}</strong>
                        </div>
                        <span class="status-badge processing">{{ $product->total_sold }} terjual</span>
                    </div>
                @empty
                    <p style="text-align: center; color: #6c757d; padding: 20px;">Belum ada data penjualan</p>
                @endforelse
            </div>
        </div>

        <!-- Top Selling Categories -->
        <div class="data-table">
            <div style="padding: 20px; border-bottom: 1px solid #e9ecef;">
                <h2 style="font-size: 1.2rem; font-weight: 700; color: #1d3557;">
                    <i class="fas fa-crown" style="color: #ffc107;"></i> Kategori Terlaris
                </h2>
            </div>
            <div style="padding: 15px;">
                @forelse($topCategories as $index => $category)
                    <div
                        style="display: flex; justify-content: space-between; align-items: center; padding: 12px 0; border-bottom: 1px solid #e9ecef;">
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <span
                                style="width: 28px; height: 28px; border-radius: 50%; background: {{ $index == 0 ? '#ffd700' : ($index == 1 ? '#c0c0c0' : ($index == 2 ? '#cd7f32' : '#e9ecef')) }}; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.85rem; color: {{ $index < 3 ? '#1d3557' : '#6c757d' }};">
                                {{ $index + 1 }}
                            </span>
                            <strong style="color: #1d3557;">{{ $category->name }}</strong>
                        </div>
                        <span class="status-badge completed">{{ $category->total_sold }} terjual</span>
                    </div>
                @empty
                    <p style="text-align: center; color: #6c757d; padding: 20px;">Belum ada data penjualan</p>
                @endforelse
            </div>
        </div>
    </div>
@endsection