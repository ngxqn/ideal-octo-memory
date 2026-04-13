<?php

use Illuminate\Support\Facades\Route;

// ── User-facing Controllers ──────────────────────────────────────
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;

// ── Admin Controllers ────────────────────────────────────────────
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\CatalogueController;
use App\Http\Controllers\Admin\PricingController;
use App\Http\Controllers\Admin\GoodsReceiptController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\InventoryController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\AuthController as AdminAuthController;

/*
|--------------------------------------------------------------------------
| Public Routes (No Auth Required)
|--------------------------------------------------------------------------
*/

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/about', [PageController::class, 'about'])->name('about');
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');

/*
|--------------------------------------------------------------------------
| Guest Routes (Only When NOT Logged In)
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);

    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
});

/*
|--------------------------------------------------------------------------
| Authenticated Customer Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:customer'])->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/items', [CartController::class, 'store'])->name('cart.items.store');
    Route::put('/cart/items/{cartItem}', [CartController::class, 'update'])->name('cart.items.update');
    Route::delete('/cart/items/{cartItem}', [CartController::class, 'destroy'])->name('cart.items.destroy');
    Route::get('/api/cart/count', [CartController::class, 'getCount'])->name('cart.count');

    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');

    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
});

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/

Route::prefix('admin')->name('admin.')->group(function () {

    Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AdminAuthController::class, 'login']);

    Route::middleware(['auth', 'role:admin'])->group(function () {
        Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');

        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // ── Catalogue Management (Unified Products + Categories) ──
        Route::get('/catalogue', [CatalogueController::class, 'index'])->name('catalogue.index');
        Route::post('/catalogue/products', [CatalogueController::class, 'storeProduct'])->name('catalogue.products.store');
        Route::put('/catalogue/products/{product}', [CatalogueController::class, 'updateProduct'])->name('catalogue.products.update');
        Route::delete('/catalogue/products/{product}', [CatalogueController::class, 'destroyProduct'])->name('catalogue.products.destroy');
        Route::patch('/catalogue/products/{product}/toggle', [CatalogueController::class, 'toggleProductVisibility'])->name('catalogue.products.toggle');
        
        Route::post('/catalogue/categories', [CatalogueController::class, 'storeCategory'])->name('catalogue.categories.store');
        Route::put('/catalogue/categories/{category}', [CatalogueController::class, 'updateCategory'])->name('catalogue.categories.update');
        Route::delete('/catalogue/categories/{category}', [CatalogueController::class, 'destroyCategory'])->name('catalogue.categories.destroy');
        Route::patch('/catalogue/categories/{category}/toggle', [CatalogueController::class, 'toggleCategoryVisibility'])->name('catalogue.categories.toggle');

        Route::get('/pricing', [PricingController::class, 'index'])->name('pricing.index');
        Route::put('/pricing/{product}', [PricingController::class, 'update'])->name('pricing.update');

        Route::get('/goods-receipts', [GoodsReceiptController::class, 'index'])->name('goods-receipts.index');
        Route::post('/goods-receipts', [GoodsReceiptController::class, 'store'])->name('goods-receipts.store');
        Route::get('/goods-receipts/{goodsReceipt}', [GoodsReceiptController::class, 'show'])->name('goods-receipts.show');
        Route::put('/goods-receipts/{goodsReceipt}', [GoodsReceiptController::class, 'update'])->name('goods-receipts.update');
        Route::delete('/goods-receipts/{goodsReceipt}', [GoodsReceiptController::class, 'destroy'])->name('goods-receipts.destroy');
        Route::put('/goods-receipts/{goodsReceipt}/complete', [GoodsReceiptController::class, 'complete'])->name('goods-receipts.complete');

        Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
        Route::put('/orders/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.update-status');

        Route::get('/inventory/report', [InventoryController::class, 'report'])->name('inventory.report');
        Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory.index');

        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
    });
});
