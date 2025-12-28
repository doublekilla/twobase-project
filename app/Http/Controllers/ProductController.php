<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    /**
     * Display products list - Public
     */
    public function index(Request $request)
    {
        $query = Product::with(['category', 'primaryImage', 'approvedReviews'])
            ->withCount(['approvedReviews as approved_reviews_count'])
            ->where('is_active', true);

        // Filter by category
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // Filter by price range
        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        // Search
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Sort
        $sort = $request->input('sort', 'latest');
        switch ($sort) {
            case 'price_low':
                $query->orderBy('price', 'asc');
                break;
            case 'price_high':
                $query->orderBy('price', 'desc');
                break;
            case 'name':
                $query->orderBy('name', 'asc');
                break;
            default:
                $query->latest();
        }

        $products = $query->paginate(12);
        $categories = Category::where('is_active', true)->get();

        return view('products.index', compact('products', 'categories'));
    }

    /**
     * Display product detail - Public
     */
    public function show(Product $product)
    {
        $product->load(['category', 'images', 'approvedReviews.user']);
        $relatedProducts = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('is_active', true)
            ->limit(4)
            ->get();

        // Get order items that user can review (completed orders, not yet reviewed)
        $unreviewedOrderItems = collect();
        if (auth()->check()) {
            $unreviewedOrderItems = \App\Models\OrderItem::with('order')
                ->whereHas('order', function ($query) {
                    $query->where('user_id', auth()->id())
                        ->where('status', 'completed');
                })
                ->where('product_id', $product->id)
                ->whereDoesntHave('review')
                ->get();
        }

        return view('products.show', compact('product', 'relatedProducts', 'unreviewedOrderItems'));
    }

    /**
     * Display products list - Admin
     */
    public function adminIndex()
    {
        $products = Product::with(['category', 'primaryImage'])
            ->latest()
            ->paginate(10);

        return view('admin.products.index', compact('products'));
    }

    /**
     * Show create form - Admin
     */
    public function create()
    {
        $categories = Category::where('is_active', true)->get();
        return view('admin.products.create', compact('categories'));
    }

    /**
     * Store new product - Admin
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'discount_price' => 'nullable|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'size' => 'nullable|string',
            'color' => 'nullable|string',
            'is_featured' => 'nullable',
            'is_active' => 'nullable',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $validated['slug'] = Str::slug($validated['name']) . '-' . uniqid();
        $validated['is_featured'] = $request->boolean('is_featured');
        $validated['is_active'] = $request->boolean('is_active', true);

        $product = Product::create($validated);

        // Handle images
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $image) {
                $path = $image->store('products', 'public');
                ProductImage::create([
                    'product_id' => $product->id,
                    'image_path' => $path,
                    'is_primary' => $index === 0,
                    'sort_order' => $index,
                ]);
            }
        }

        return redirect()->route('admin.products.index')
            ->with('success', 'Produk berhasil ditambahkan.');
    }

    /**
     * Show edit form - Admin
     */
    public function edit(Product $product)
    {
        $categories = Category::where('is_active', true)->get();
        $product->load('images');
        return view('admin.products.edit', compact('product', 'categories'));
    }

    /**
     * Update product - Admin
     */
    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'discount_price' => 'nullable|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'size' => 'nullable|string',
            'color' => 'nullable|string',
            'is_featured' => 'nullable',
            'is_active' => 'nullable',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $validated['is_featured'] = $request->boolean('is_featured');
        $validated['is_active'] = $request->boolean('is_active', true);

        $product->update($validated);

        // Handle new images
        if ($request->hasFile('images')) {
            $maxOrder = $product->images()->max('sort_order') ?? -1;
            foreach ($request->file('images') as $index => $image) {
                $path = $image->store('products', 'public');
                ProductImage::create([
                    'product_id' => $product->id,
                    'image_path' => $path,
                    'is_primary' => $product->images()->count() === 0 && $index === 0,
                    'sort_order' => $maxOrder + $index + 1,
                ]);
            }
        }

        return redirect()->route('admin.products.index')
            ->with('success', 'Produk berhasil diperbarui.');
    }

    /**
     * Delete product - Admin
     */
    public function destroy(Product $product)
    {
        // Delete images from storage
        foreach ($product->images as $image) {
            Storage::disk('public')->delete($image->image_path);
        }

        $product->delete();

        return redirect()->route('admin.products.index')
            ->with('success', 'Produk berhasil dihapus.');
    }

    /**
     * Delete product image - Admin
     */
    public function deleteImage(ProductImage $image)
    {
        Storage::disk('public')->delete($image->image_path);
        $image->delete();

        return back()->with('success', 'Gambar berhasil dihapus.');
    }

    /**
     * Set primary image - Admin
     */
    public function setPrimaryImage(ProductImage $image)
    {
        // Remove primary from other images
        ProductImage::where('product_id', $image->product_id)
            ->update(['is_primary' => false]);

        $image->update(['is_primary' => true]);

        return back()->with('success', 'Gambar utama berhasil diubah.');
    }

    /**
     * Get featured products - API
     */
    public function getFeatured()
    {
        $products = Product::with(['primaryImage'])
            ->where('is_active', true)
            ->where('is_featured', true)
            ->limit(8)
            ->get();

        return response()->json($products);
    }
}
