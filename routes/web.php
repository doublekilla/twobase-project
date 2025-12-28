<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReviewController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Public Routes
Route::get('/', [HomeController::class, 'index'])->name('home');

// Midtrans Webhook (no CSRF)
Route::post('/payment/notification', [App\Http\Controllers\PaymentController::class, 'handleNotification'])->name('payment.notification')->withoutMiddleware(['web']);

Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');

// Auth Routes (Guest only)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// Authenticated User Routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Profile
    Route::get('/profile', [AuthController::class, 'profile'])->name('profile');
    Route::put('/profile', [AuthController::class, 'updateProfile'])->name('profile.update');

    // Cart
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
    Route::put('/cart/{cart}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/{cart}', [CartController::class, 'destroy'])->name('cart.destroy');
    Route::delete('/cart', [CartController::class, 'clear'])->name('cart.clear');

    // Checkout & Orders
    Route::get('/checkout', [OrderController::class, 'checkout'])->name('checkout');
    Route::post('/checkout', [OrderController::class, 'store'])->name('checkout.store');
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::post('/orders/{order}/payment-proof', [OrderController::class, 'uploadPaymentProof'])->name('orders.uploadPaymentProof');
    Route::post('/orders/{order}/cancel', [OrderController::class, 'userCancel'])->name('orders.cancel');

    // Midtrans Payment Routes
    Route::post('/payment/{order}/snap-token', [App\Http\Controllers\PaymentController::class, 'createSnapToken'])->name('payment.snap-token');
    Route::post('/payment/{order}/update-status', [App\Http\Controllers\PaymentController::class, 'updateStatus'])->name('payment.update-status');
    Route::get('/payment/success', [App\Http\Controllers\PaymentController::class, 'success'])->name('payment.success');
    Route::get('/payment/pending', [App\Http\Controllers\PaymentController::class, 'pending'])->name('payment.pending');
    Route::get('/payment/error', [App\Http\Controllers\PaymentController::class, 'error'])->name('payment.error');

    // Reviews
    Route::post('/products/{product}/reviews', [ReviewController::class, 'store'])->name('reviews.store');
    // Redirect GET requests to reviews back to product page
    Route::get('/products/{product}/reviews', function ($product) {
        return redirect()->route('products.show', $product);
    });
});

// Admin Routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [HomeController::class, 'adminDashboard'])->name('dashboard');

    // Categories Management
    Route::resource('categories', CategoryController::class)->except(['show']);

    // Products Management
    Route::get('/products', [ProductController::class, 'adminIndex'])->name('products.index');
    Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');
    Route::post('/products', [ProductController::class, 'store'])->name('products.store');
    Route::get('/products/{product:id}/edit', [ProductController::class, 'edit'])->name('products.edit');
    Route::put('/products/{product:id}', [ProductController::class, 'update'])->name('products.update');
    Route::delete('/products/{product:id}', [ProductController::class, 'destroy'])->name('products.destroy');
    Route::delete('/products/images/{image}', [ProductController::class, 'deleteImage'])->name('products.deleteImage');
    Route::put('/products/images/{image}/primary', [ProductController::class, 'setPrimaryImage'])->name('products.setPrimaryImage');

    // Orders Management
    Route::get('/orders', [OrderController::class, 'adminIndex'])->name('orders.index');
    Route::put('/orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.updateStatus');
    Route::post('/orders/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');

    // Reviews Management
    Route::get('/reviews', [ReviewController::class, 'adminIndex'])->name('reviews.index');
    Route::put('/reviews/{review}/approve', [ReviewController::class, 'approve'])->name('reviews.approve');
    Route::delete('/reviews/{review}', [ReviewController::class, 'destroy'])->name('reviews.destroy');
});

// API Routes (for AJAX)
Route::prefix('api')->group(function () {
    Route::get('/cart/count', [CartController::class, 'getCount'])->name('api.cart.count');
    Route::get('/categories', [CategoryController::class, 'getCategories'])->name('api.categories');
    Route::get('/products/featured', [ProductController::class, 'getFeatured'])->name('api.products.featured');
});
