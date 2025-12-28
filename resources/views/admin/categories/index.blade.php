@extends('layouts.admin')

@section('title', 'Kelola Kategori')

@section('content')
    <div class="admin-header">
        <h1>Kelola Kategori</h1>
        <a href="{{ route('admin.categories.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Tambah Kategori
        </a>
    </div>

    <div class="data-table">
        <table>
            <thead>
                <tr>
                    <th>Nama Kategori</th>
                    <th>Deskripsi</th>
                    <th>Jumlah Produk</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($categories as $category)
                    <tr>
                        <td><strong style="color: #1d3557;">{{ $category->name }}</strong></td>
                        <td style="max-width: 300px;">{{ Str::limit($category->description, 80) }}</td>
                        <td>{{ $category->products_count }} produk</td>
                        <td>
                            <span class="status-badge {{ $category->is_active ? 'delivered' : 'cancelled' }}">
                                {{ $category->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </td>
                        <td>
                            <div style="display: flex; gap: 10px;">
                                <a href="{{ route('admin.categories.edit', $category) }}" class="btn btn-sm btn-outline">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.categories.destroy', $category) }}" method="POST"
                                    onsubmit="return confirm('Yakin ingin menghapus kategori ini? Semua produk di kategori ini juga akan terhapus.')">
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
                        <td colspan="5" style="text-align: center; color: #6c757d; padding: 40px;">
                            Belum ada kategori
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="pagination">
        {{ $categories->links() }}
    </div>
@endsection