@extends('layouts.admin')

@section('title', 'Kelola Ulasan')

@section('content')
    <div class="admin-header">
        <h1>Kelola Ulasan</h1>
    </div>

    <div class="data-table">
        <table>
            <thead>
                <tr>
                    <th>Produk</th>
                    <th>Pelanggan</th>
                    <th>Rating</th>
                    <th>Komentar</th>
                    <th>Status</th>
                    <th>Tanggal</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($reviews as $review)
                    <tr>
                        <td><strong style="color: #1d3557;">{{ $review->product->name }}</strong></td>
                        <td>{{ $review->user->name }}</td>
                        <td>
                            <div style="color: #f39c12;">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="fas fa-star{{ $i <= $review->rating ? '' : '-o' }}"></i>
                                @endfor
                            </div>
                        </td>
                        <td style="max-width: 300px;">{{ Str::limit($review->comment, 100) }}</td>
                        <td>
                            <span class="status-badge {{ $review->is_approved ? 'delivered' : 'pending' }}">
                                {{ $review->is_approved ? 'Disetujui' : 'Pending' }}
                            </span>
                        </td>
                        <td>{{ $review->created_at->format('d M Y') }}</td>
                        <td>
                            <div style="display: flex; gap: 10px;">
                                @if(!$review->is_approved)
                                    <form action="{{ route('admin.reviews.approve', $review) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit" class="btn btn-sm btn-success">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    </form>
                                @endif
                                <form action="{{ route('admin.reviews.destroy', $review) }}" method="POST"
                                    onsubmit="return confirm('Yakin ingin menghapus ulasan ini?')">
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
                        <td colspan="7" style="text-align: center; color: #6c757d; padding: 40px;">
                            Belum ada ulasan
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="pagination">
        {{ $reviews->withQueryString()->links() }}
    </div>
@endsection