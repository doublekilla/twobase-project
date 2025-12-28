@extends('layouts.admin')

@section('title', 'Kelola Produk')

@section('content')
    <div class="admin-header">
        <h1>Kelola Produk</h1>
        <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Tambah Produk
        </a>
    </div>

    <div class="data-table">
        <table>
            <thead>
                <tr>
                    <th>Produk</th>
                    <th>Kategori</th>
                    <th>Harga</th>
                    <th>Stok</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $product)
                    <tr>
                        <td>
                            <div style="display: flex; align-items: center; gap: 15px;">
                                <div
                                    style="width: 50px; height: 50px; background: #f8f9fa; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-tshirt" style="color: #dee2e6;"></i>
                                </div>
                                <div>
                                    <strong style="color: #1d3557;">{{ $product->name }}</strong>
                                    @if($product->is_featured)
                                        <span class="badge badge-hot" style="margin-left: 5px;">Featured</span>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td>{{ $product->category->name }}</td>
                        <td>
                            <strong>Rp {{ number_format($product->current_price, 0, ',', '.') }}</strong>
                            @if($product->has_discount)
                                <br><small style="color: #6c757d; text-decoration: line-through;">Rp
                                    {{ number_format($product->price, 0, ',', '.') }}</small>
                            @endif
                        </td>
                        <td>
                            <span
                                class="{{ $product->stock <= 5 ? 'status-badge cancelled' : '' }}">{{ $product->stock }}</span>
                        </td>
                        <td>
                            <span class="status-badge {{ $product->is_active ? 'delivered' : 'cancelled' }}">
                                {{ $product->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </td>
                        <td>
                            <div style="display: flex; gap: 10px;">
                                <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-sm btn-outline">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.products.destroy', $product) }}" method="POST"
                                    onsubmit="return confirm('Yakin ingin menghapus produk ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align: center; color: #6c757d; padding: 40px;">
                            <i class="fas fa-box-open" style="font-size: 2rem; margin-bottom: 10px;"></i>
                            <p>Belum ada produk</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

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
@endsection