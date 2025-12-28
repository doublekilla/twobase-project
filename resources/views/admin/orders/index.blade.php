@extends('layouts.admin')

@section('title', 'Kelola Pesanan')

@section('content')
    <div class="admin-header">
        <h1>Kelola Pesanan</h1>
    </div>

    <!-- Filters -->
    <div
        style="background: white; border-radius: 16px; padding: 20px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); margin-bottom: 25px;">
        <form action="{{ route('admin.orders.index') }}" method="GET"
            style="display: flex; gap: 15px; align-items: end; flex-wrap: wrap;">
            <div class="form-group" style="margin: 0; flex: 1; min-width: 200px;">
                <label>Cari No. Pesanan</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Masukkan no. pesanan..."
                    style="padding: 10px 15px;">
            </div>
            <div class="form-group" style="margin: 0; flex: 1; min-width: 150px;">
                <label>Status Pesanan</label>
                <select name="status" style="padding: 10px 15px;">
                    <option value="">Semua Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Processing</option>
                    <option value="shipped" {{ request('status') == 'shipped' ? 'selected' : '' }}>Shipped</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>
            <div class="form-group" style="margin: 0; flex: 1; min-width: 150px;">
                <label>Status Pembayaran</label>
                <select name="payment_status" style="padding: 10px 15px;">
                    <option value="">Semua</option>
                    <option value="paid" {{ request('payment_status') == 'paid' ? 'selected' : '' }}>Paid</option>
                    <option value="refunded" {{ request('payment_status') == 'refunded' ? 'selected' : '' }}>Refunded</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary"
                style="height: fit-content; padding: 10px 20px; font-size: 14px; line-height: 1.5;">Filter</button>
            @if(request()->hasAny(['status', 'payment_status', 'search']))
                <a href="{{ route('admin.orders.index') }}" class="btn btn-outline"
                    style="height: fit-content; padding: 9px 19px; font-size: 14px; line-height: 1.5; box-sizing: border-box;">Reset</a>
            @endif
        </form>
    </div>

    <div class="data-table">
        <table>
            <thead>
                <tr>
                    <th>No. Pesanan</th>
                    <th>Pelanggan</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Pembayaran</th>
                    <th>Tanggal</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                    <tr>
                        <td><strong>{{ $order->order_number }}</strong></td>
                        <td>
                            {{ $order->user->name }}<br>
                            <small style="color: #6c757d;">{{ $order->user->email }}</small>
                        </td>
                        <td><strong>Rp {{ number_format($order->total, 0, ',', '.') }}</strong></td>
                        <td>
                            <span class="status-badge {{ $order->status }}">{{ ucfirst($order->status) }}</span>
                        </td>
                        <td>
                            <span class="status-badge {{ $order->payment_status }}">{{ ucfirst($order->payment_status) }}</span>
                        </td>
                        <td>{{ $order->created_at->format('d M Y H:i') }}</td>
                        <td>
                            <div style="display: flex; gap: 10px;">
                                <a href="{{ route('orders.show', $order) }}" class="btn btn-sm btn-outline">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <button type="button"
                                    onclick="openStatusModal({{ $order->id }}, '{{ $order->status }}', '{{ $order->payment_status }}')"
                                    class="btn btn-sm btn-primary">
                                    <i class="fas fa-edit"></i>
                                </button>
                                @if($order->status !== 'cancelled')
                                    <form action="{{ route('admin.orders.cancel', $order) }}" method="POST"
                                        onsubmit="return confirm('Yakin ingin membatalkan pesanan ini?')">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-danger">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align: center; color: #6c757d; padding: 40px;">
                            Belum ada pesanan
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="pagination">
        {{ $orders->withQueryString()->links() }}
    </div>

    <!-- Status Update Modal -->
    <div id="statusModal"
        style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center;">
        <div style="background: white; border-radius: 16px; padding: 30px; width: 400px; max-width: 90%;">
            <h3 style="margin-bottom: 25px;">Update Status Pesanan</h3>
            <form id="statusForm" method="POST">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <label>Status Pesanan</label>
                    <select name="status" id="modal_status" required>
                        <option value="pending">Pending</option>
                        <option value="processing">Processing</option>
                        <option value="shipped">Shipped</option>
                        <option value="completed">Completed</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Status Pembayaran</label>
                    <select name="payment_status" id="modal_payment_status" required>
                        <option value="paid">Paid</option>
                        <option value="refunded">Refunded</option>
                    </select>
                </div>
                <div style="display: flex; gap: 15px; margin-top: 25px;">
                    <button type="submit" class="btn btn-primary">Simpan</button>
                    <button type="button" onclick="closeStatusModal()" class="btn btn-outline">Batal</button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            function openStatusModal(orderId, status, paymentStatus) {
                document.getElementById('statusForm').action = '/admin/orders/' + orderId + '/status';
                document.getElementById('modal_status').value = status;
                document.getElementById('modal_payment_status').value = paymentStatus;
                document.getElementById('statusModal').style.display = 'flex';
            }

            function closeStatusModal() {
                document.getElementById('statusModal').style.display = 'none';
            }

            document.getElementById('statusModal').addEventListener('click', function (e) {
                if (e.target === this) closeStatusModal();
            });
        </script>
    @endpush
@endsection