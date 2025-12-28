<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\Notification;

class PaymentController extends Controller
{
    public function __construct()
    {
        // Set Midtrans configuration
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = config('midtrans.is_sanitized');
        Config::$is3ds = config('midtrans.is_3ds');
    }

    /**
     * Create Midtrans Snap Token for payment
     */
    public function createSnapToken(Order $order)
    {
        // Verify user owns this order
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        // Only process unpaid orders
        if ($order->payment_status !== 'unpaid') {
            return response()->json(['error' => 'Order already paid'], 400);
        }

        // Use unique order ID for Midtrans (order_number + timestamp)
        $midtransOrderId = $order->order_number . '-' . time();

        $params = [
            'transaction_details' => [
                'order_id' => $midtransOrderId,
                'gross_amount' => (int) $order->total,
            ],
            'customer_details' => [
                'first_name' => $order->customer_name ?? auth()->user()->name,
                'email' => $order->customer_email ?? auth()->user()->email,
                'phone' => $order->customer_phone ?? $order->shipping_phone,
            ],
            'item_details' => $this->getItemDetails($order),
            // Only enable QRIS and Virtual Account
            'enabled_payments' => [
                'other_qris',
                'bca_va',
                'bni_va',
                'bri_va',
                'permata_va',
                'echannel', // Mandiri Bill
            ],
        ];

        try {
            $snapToken = Snap::getSnapToken($params);

            // Save snap token to order
            $order->update(['snap_token' => $snapToken]);

            return response()->json([
                'snap_token' => $snapToken,
                'client_key' => config('midtrans.client_key'),
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Get item details for Midtrans
     */
    private function getItemDetails(Order $order)
    {
        $items = [];

        foreach ($order->items as $item) {
            $items[] = [
                'id' => $item->product_id,
                'price' => (int) $item->product_price,
                'quantity' => $item->quantity,
                'name' => substr($item->product_name, 0, 50), // Max 50 chars
            ];
        }

        // Add shipping cost if applicable
        if ($order->shipping_cost > 0) {
            $items[] = [
                'id' => 'SHIPPING',
                'price' => (int) $order->shipping_cost,
                'quantity' => 1,
                'name' => 'Biaya Pengiriman',
            ];
        }

        return $items;
    }

    /**
     * Handle Midtrans webhook notification
     */
    public function handleNotification(Request $request)
    {
        try {
            $notification = new Notification();

            $orderId = $notification->order_id;
            $transactionStatus = $notification->transaction_status;
            $fraudStatus = $notification->fraud_status;
            $paymentType = $notification->payment_type;

            // Parse order_number from order_id (format: ORDER_NUMBER-TIMESTAMP)
            $orderNumber = explode('-', $orderId)[0] . '-' . explode('-', $orderId)[1];

            // Find order by order_number
            $order = Order::where('order_number', $orderNumber)->first();

            if (!$order) {
                return response()->json(['message' => 'Order not found'], 404);
            }

            // Handle transaction status
            if ($transactionStatus == 'capture') {
                if ($fraudStatus == 'accept') {
                    $this->updateOrderPayment($order, 'paid', $paymentType);
                }
            } elseif ($transactionStatus == 'settlement') {
                $this->updateOrderPayment($order, 'paid', $paymentType);
            } elseif ($transactionStatus == 'pending') {
                // Do nothing, payment is pending
            } elseif (in_array($transactionStatus, ['deny', 'expire', 'cancel'])) {
                $order->update(['payment_status' => 'unpaid']);
            }

            return response()->json(['message' => 'Notification handled']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Update order payment status
     */
    private function updateOrderPayment(Order $order, string $status, string $paymentType)
    {
        $order->update([
            'payment_status' => $status,
            'payment_method' => $paymentType,
            'status' => 'processing', // Move to processing after payment
        ]);
    }

    /**
     * Update payment status from client
     */
    public function updateStatus(Request $request, Order $order)
    {
        // Verify user owns this order
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        $status = $request->input('status');
        $paymentType = $request->input('payment_type', 'midtrans');

        if ($status === 'success') {
            $order->update([
                'payment_status' => 'paid',
                'status' => 'processing',
            ]);
        }

        return response()->json(['message' => 'Status updated']);
    }

    /**
     * Payment success page
     */
    public function success(Request $request)
    {
        return redirect()->route('orders.index')
            ->with('success', 'Pembayaran berhasil! Pesanan Anda sedang diproses.');
    }

    /**
     * Payment pending page
     */
    public function pending(Request $request)
    {
        return redirect()->route('orders.index')
            ->with('info', 'Menunggu pembayaran. Silakan selesaikan pembayaran Anda.');
    }

    /**
     * Payment error page
     */
    public function error(Request $request)
    {
        return redirect()->route('orders.index')
            ->with('error', 'Pembayaran gagal. Silakan coba lagi.');
    }
}
