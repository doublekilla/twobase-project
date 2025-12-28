<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    /**
     * Display cart page
     */
    public function index()
    {
        $cartItems = Cart::with(['product.primaryImage'])
            ->where('user_id', auth()->id())
            ->get();

        $subtotal = $cartItems->sum(function ($item) {
            return $item->product->current_price * $item->quantity;
        });

        return view('cart', compact('cartItems', 'subtotal'));
    }

    /**
     * Add item to cart
     */
    public function add(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'selected_size' => 'nullable|string',
            'selected_color' => 'nullable|string',
        ]);

        $product = Product::findOrFail($validated['product_id']);

        // Check stock
        if ($product->stock < $validated['quantity']) {
            return back()->with('error', 'Stok tidak mencukupi.');
        }

        // Check if item already in cart
        $existingCart = Cart::where('user_id', auth()->id())
            ->where('product_id', $validated['product_id'])
            ->where('selected_size', $validated['selected_size'] ?? null)
            ->where('selected_color', $validated['selected_color'] ?? null)
            ->first();

        if ($existingCart) {
            $existingCart->increment('quantity', $validated['quantity']);
        } else {
            Cart::create([
                'user_id' => auth()->id(),
                'product_id' => $validated['product_id'],
                'quantity' => $validated['quantity'],
                'selected_size' => $validated['selected_size'] ?? null,
                'selected_color' => $validated['selected_color'] ?? null,
            ]);
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Produk berhasil ditambahkan ke keranjang.',
                'cart_count' => Cart::where('user_id', auth()->id())->sum('quantity'),
            ]);
        }

        return back()->with('success', 'Produk berhasil ditambahkan ke keranjang.');
    }

    /**
     * Update cart item quantity
     */
    public function update(Request $request, Cart $cart)
    {
        // Ensure user owns this cart item
        if ($cart->user_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        // Check stock
        if ($cart->product->stock < $validated['quantity']) {
            return back()->with('error', 'Stok tidak mencukupi.');
        }

        $cart->update(['quantity' => $validated['quantity']]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Keranjang berhasil diperbarui.',
                'subtotal' => number_format($cart->subtotal, 0, ',', '.'),
            ]);
        }

        return back()->with('success', 'Keranjang berhasil diperbarui.');
    }

    /**
     * Remove item from cart
     */
    public function destroy(Cart $cart)
    {
        // Ensure user owns this cart item
        if ($cart->user_id !== auth()->id()) {
            abort(403);
        }

        $cart->delete();

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Produk berhasil dihapus dari keranjang.',
                'cart_count' => Cart::where('user_id', auth()->id())->sum('quantity'),
            ]);
        }

        return back()->with('success', 'Produk berhasil dihapus dari keranjang.');
    }

    /**
     * Get cart count - API
     */
    public function getCount()
    {
        $count = auth()->check()
            ? Cart::where('user_id', auth()->id())->sum('quantity')
            : 0;

        return response()->json(['count' => $count]);
    }

    /**
     * Clear cart
     */
    public function clear()
    {
        Cart::where('user_id', auth()->id())->delete();

        return back()->with('success', 'Keranjang berhasil dikosongkan.');
    }
}
