<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Display the homepage
     */
    public function index()
    {
        $featuredProducts = Product::with(['primaryImage', 'category', 'approvedReviews'])
            ->withCount(['approvedReviews as approved_reviews_count'])
            ->where('is_active', true)
            ->where('is_featured', true)
            ->limit(8)
            ->get();

        $latestProducts = Product::with(['primaryImage', 'category', 'approvedReviews'])
            ->withCount(['approvedReviews as approved_reviews_count'])
            ->where('is_active', true)
            ->latest()
            ->limit(8)
            ->get();

        $categories = Category::where('is_active', true)
            ->withCount('products')
            ->get();

        return view('home', compact('featuredProducts', 'latestProducts', 'categories'));
    }

    /**
     * Admin Dashboard
     */
    public function adminDashboard()
    {
        $stats = [
            'total_products' => Product::count(),
            'total_categories' => Category::count(),
            'total_orders' => Order::count(),
            'total_users' => User::where('role', 'user')->count(),
            'pending_orders' => Order::where('status', 'pending')->count(),
            'total_revenue' => Order::where('payment_status', 'paid')->sum('total'),
        ];

        $recentOrders = Order::with('user')
            ->latest()
            ->limit(5)
            ->get();

        $lowStockProducts = Product::where('stock', '<=', 5)
            ->where('is_active', true)
            ->limit(5)
            ->get();

        // Top selling products (by quantity sold)
        $topProducts = \DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->where('orders.status', '!=', 'cancelled')
            ->select('products.id', 'products.name', \DB::raw('SUM(order_items.quantity) as total_sold'))
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('total_sold')
            ->limit(5)
            ->get();

        // Top selling categories (by quantity sold)
        $topCategories = \DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->where('orders.status', '!=', 'cancelled')
            ->select('categories.id', 'categories.name', \DB::raw('SUM(order_items.quantity) as total_sold'))
            ->groupBy('categories.id', 'categories.name')
            ->orderByDesc('total_sold')
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentOrders', 'lowStockProducts', 'topProducts', 'topCategories'));
    }
}
