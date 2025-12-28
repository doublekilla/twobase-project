<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class OrderController extends Controller
{
    /**
     * Show checkout page
     */
    public function checkout(Request $request)
    {
        $selectedItems = $request->input('selected_items', []);

        $query = Cart::with(['product.primaryImage'])
            ->where('user_id', auth()->id());

        // Filter by selected items if provided
        if (!empty($selectedItems)) {
            $query->whereIn('id', $selectedItems);
        }

        $cartItems = $query->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')
                ->with('error', 'Pilih minimal 1 produk untuk checkout.');
        }

        $subtotal = $cartItems->sum(function ($item) {
            return $item->product->current_price * $item->quantity;
        });

        $shippingCost = 15000; // Fixed shipping cost
        $total = $subtotal + $shippingCost;

        // Store selected items in session for order processing
        session(['checkout_items' => $cartItems->pluck('id')->toArray()]);

        return view('checkout', compact('cartItems', 'subtotal', 'shippingCost', 'total'));
    }

    /**
     * Process order
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'shipping_address' => 'required|string',
            'shipping_phone' => 'required|string|max:20',
            'payment_method' => 'required|in:cod,midtrans',
            'notes' => 'nullable|string',
        ]);

        // Get selected items from session
        $checkoutItems = session('checkout_items', []);

        $query = Cart::with('product')
            ->where('user_id', auth()->id());

        // Only process selected items
        if (!empty($checkoutItems)) {
            $query->whereIn('id', $checkoutItems);
        }

        $cartItems = $query->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')
                ->with('error', 'Pilih minimal 1 produk untuk checkout.');
        }

        // Validate stock
        foreach ($cartItems as $item) {
            if ($item->product->stock < $item->quantity) {
                return back()->with('error', "Stok {$item->product->name} tidak mencukupi.");
            }
        }

        DB::beginTransaction();
        try {
            $subtotal = $cartItems->sum(function ($item) {
                return $item->product->current_price * $item->quantity;
            });

            $shippingCost = 15000;
            $total = $subtotal + $shippingCost;

            // Set status based on payment method
            $paymentStatus = $validated['payment_method'] === 'cod' ? 'paid' : 'unpaid';
            $orderStatus = $validated['payment_method'] === 'cod' ? 'processing' : 'pending';

            // Create order
            $order = Order::create([
                'user_id' => auth()->id(),
                'subtotal' => $subtotal,
                'shipping_cost' => $shippingCost,
                'total' => $total,
                'status' => $orderStatus,
                'payment_status' => $paymentStatus,
                'payment_method' => $validated['payment_method'],
                'customer_name' => auth()->user()->name,
                'customer_email' => auth()->user()->email,
                'customer_phone' => $validated['shipping_phone'],
                'shipping_address' => $validated['shipping_address'],
                'shipping_phone' => $validated['shipping_phone'],
                'notes' => $validated['notes'] ?? null,
            ]);

            // Create order items and update stock
            foreach ($cartItems as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'product_name' => $item->product->name,
                    'product_price' => $item->product->current_price,
                    'quantity' => $item->quantity,
                    'size' => $item->selected_size,
                    'color' => $item->selected_color,
                    'subtotal' => $item->product->current_price * $item->quantity,
                ]);

                // Decrease stock
                $item->product->decrement('stock', $item->quantity);
            }

            // Clear only selected cart items
            Cart::whereIn('id', $cartItems->pluck('id'))->delete();

            // Clear checkout session
            session()->forget('checkout_items');

            DB::commit();

            return redirect()->route('orders.show', $order)
                ->with('success', 'Pesanan berhasil dibuat! Nomor pesanan: ' . $order->order_number);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan saat memproses pesanan.');
        }
    }

    /**
     * User order history
     */
    public function index(Request $request)
    {
        $query = Order::where('user_id', auth()->id())
            ->with(['items.product.primaryImage'])
            ->latest();

        // Filter by status if provided
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $orders = $query->paginate(10)->withQueryString();

        return view('user.orders', compact('orders'));
    }

    /**
     * Show order detail
     */
    public function show(Order $order)
    {
        // Ensure user owns this order or is admin
        if ($order->user_id !== auth()->id() && !auth()->user()->isAdmin()) {
            abort(403);
        }

        $order->load(['items.product.primaryImage', 'user']);

        return view('user.order-detail', compact('order'));
    }

    /**
     * Upload payment proof for bank transfer
     */
    public function uploadPaymentProof(Request $request, Order $order)
    {
        // Ensure user owns this order
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        // Only allow upload for transfer payment method
        if ($order->payment_method !== 'transfer') {
            return back()->with('error', 'Upload bukti pembayaran hanya untuk metode transfer bank.');
        }

        // Only allow if not already paid
        if ($order->payment_status === 'paid') {
            return back()->with('error', 'Pesanan sudah dibayar.');
        }

        $request->validate([
            'payment_proof' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        // Delete old payment proof if exists
        if ($order->payment_proof) {
            Storage::disk('public')->delete($order->payment_proof);
        }

        // Store new payment proof
        $path = $request->file('payment_proof')->store('payment_proofs', 'public');

        $order->update(['payment_proof' => $path]);

        return back()->with('success', 'Bukti pembayaran berhasil diunggah. Menunggu konfirmasi admin.');
    }

    /**
     * Admin - List all orders
     */
    public function adminIndex(Request $request)
    {
        $query = Order::with(['user', 'items'])
            ->latest();

        // Search by order number
        if ($request->filled('search')) {
            $query->where('order_number', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        $orders = $query->paginate(15);

        return view('admin.orders.index', compact('orders'));
    }

    /**
     * Admin - Update order status
     */
    public function updateStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,processing,shipped,completed,cancelled',
            'payment_status' => 'required|in:unpaid,paid,refunded',
        ]);

        // Store old status to check if changing to cancelled
        $oldStatus = $order->status;
        $newStatus = $validated['status'];

        // If status changed to cancelled and was not cancelled before, restore stock
        if ($newStatus === 'cancelled' && $oldStatus !== 'cancelled') {
            foreach ($order->items as $item) {
                if ($item->product) {
                    $item->product->increment('stock', $item->quantity);
                }
            }
        }

        // If status changed from cancelled to other status, reduce stock again
        if ($oldStatus === 'cancelled' && $newStatus !== 'cancelled') {
            foreach ($order->items as $item) {
                if ($item->product) {
                    $item->product->decrement('stock', $item->quantity);
                }
            }
        }

        $order->update($validated);

        return back()->with('success', 'Status pesanan berhasil diperbarui.');
    }

    /**
     * Admin - Cancel order and restore stock
     */
    public function cancel(Order $order)
    {
        if ($order->status === 'cancelled') {
            return back()->with('error', 'Pesanan sudah dibatalkan.');
        }

        DB::beginTransaction();
        try {
            // Restore stock
            foreach ($order->items as $item) {
                Product::where('id', $item->product_id)
                    ->increment('stock', $item->quantity);
            }

            $order->update([
                'status' => 'cancelled',
                'payment_status' => 'refunded',
            ]);

            DB::commit();

            return back()->with('success', 'Pesanan berhasil dibatalkan dan stok dikembalikan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan saat membatalkan pesanan.');
        }
    }

    /**
     * User - Cancel pending order
     */
    public function userCancel(Order $order)
    {
        // Ensure user owns this order
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        // Only allow cancel for pending orders
        if ($order->status !== 'pending') {
            return back()->with('error', 'Pesanan tidak dapat dibatalkan karena sudah diproses oleh admin.');
        }

        DB::beginTransaction();
        try {
            // Restore stock
            foreach ($order->items as $item) {
                Product::where('id', $item->product_id)
                    ->increment('stock', $item->quantity);
            }

            $order->update([
                'status' => 'cancelled',
            ]);

            DB::commit();

            return redirect()->route('orders.index')
                ->with('success', 'Pesanan berhasil dibatalkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan saat membatalkan pesanan.');
        }
    }
}
