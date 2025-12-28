<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Product;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    /**
     * Store a new review
     */
    public function store(Request $request, Product $product)
    {
        $validated = $request->validate([
            'order_item_id' => 'required|exists:order_items,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        // Get the order item
        $orderItem = \App\Models\OrderItem::with('order')->find($validated['order_item_id']);

        // Verify user owns this order and order is completed
        if (
            !$orderItem ||
            $orderItem->order->user_id !== auth()->id() ||
            $orderItem->order->status !== 'completed' ||
            $orderItem->product_id !== $product->id
        ) {
            return back()->with('error', 'Tidak dapat memberikan ulasan untuk item ini.');
        }

        // Check if already reviewed this order item
        $existingReview = Review::where('order_item_id', $orderItem->id)->first();

        if ($existingReview) {
            return back()->with('error', 'Anda sudah memberikan ulasan untuk pembelian ini.');
        }

        Review::create([
            'user_id' => auth()->id(),
            'product_id' => $product->id,
            'order_item_id' => $orderItem->id,
            'rating' => $validated['rating'],
            'comment' => $validated['comment'],
            'is_approved' => true, // Auto-approve for verified purchasers
        ]);

        return back()->with('success', 'Ulasan berhasil dikirim. Terima kasih!');
    }

    /**
     * Admin - List all reviews
     */
    public function adminIndex(Request $request)
    {
        $query = Review::with(['user', 'product'])
            ->latest();

        if ($request->filled('status')) {
            $query->where('is_approved', $request->status === 'approved');
        }

        $reviews = $query->paginate(15);

        return view('admin.reviews.index', compact('reviews'));
    }

    /**
     * Admin - Approve review
     */
    public function approve(Review $review)
    {
        $review->update(['is_approved' => true]);

        return back()->with('success', 'Review berhasil disetujui.');
    }

    /**
     * Admin - Reject/Delete review
     */
    public function destroy(Review $review)
    {
        $review->delete();

        return back()->with('success', 'Review berhasil dihapus.');
    }
}
