<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Livewire\ProductCatalog;
use App\Http\Controllers\SellerDashboardController;
use App\Http\Controllers\SellerProductController;
use App\Http\Controllers\SellerSalesController;
use App\Http\Controllers\SellerProfileController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductListingController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\Seller\DashboardController;
use App\Http\Controllers\Seller\ListingController;
use App\Http\Controllers\Seller\SalesController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\ProducerController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProfileController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Rutas públicas
Route::get('/', function () {
    return view('welcome');
});

// Rutas del catálogo (públicas)
Route::get('/catalog', [CatalogController::class, 'index'])->name('catalog');
Route::get('/products/{product}', [CatalogController::class, 'show'])->name('products.show');

// Rutas de productores (públicas)
Route::get('/producers', [ProducerController::class, 'index'])->name('producers');
Route::get('/producers/{producer}', [ProducerController::class, 'show'])->name('producers.show');

// Rutas que requieren autenticación
Route::middleware(['auth'])->group(function () {
    // Redirigir /home a /catalog
    Route::get('/catalogo', function() {
        return view('catalog');
    })->name('catalog');
    
    // Rutas específicas para compradores
    Route::middleware(['role:buyer'])->group(function () {
        Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
        Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
    });
    
    // Rutas específicas para vendedores
    Route::middleware(['role:seller'])->group(function () {
        Route::get('/seller/dashboard', [HomeController::class, 'sellerDashboard'])->name('seller.dashboard');
        
        // Gestión de productos
        Route::resource('seller/listings', ListingController::class)->names([
            'index' => 'seller.listings.index',
            'create' => 'seller.listings.create',
            'store' => 'seller.listings.store',
            'edit' => 'seller.listings.edit',
            'update' => 'seller.listings.update',
            'destroy' => 'seller.listings.destroy',
        ]);

        // Ventas
        Route::get('/seller/sales', [SalesController::class, 'index'])->name('seller.sales');
    });
});

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('login', [LoginController::class, 'login']);
    Route::get('register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('register', [RegisterController::class, 'register']);
    Route::get('password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('password/reset', [ResetPasswordController::class, 'reset'])->name('password.update');
});

Route::middleware('auth')->group(function () {
    Route::post('logout', [LoginController::class, 'logout'])->name('logout');
    Route::get('email/verify', [VerificationController::class, 'show'])->name('verification.notice');
    Route::get('email/verify/{id}/{hash}', [VerificationController::class, 'verify'])->name('verification.verify');
    Route::post('email/resend', [VerificationController::class, 'resend'])->name('verification.resend');

    // Rutas del perfil
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
});

// Seller Routes
Route::middleware(['auth', 'role:seller'])->prefix('seller')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('seller.dashboard');
    
    // // Products Management
    // Route::resource('products', SellerProductController::class)->names([
    //     'index' => 'seller.products.index',
    //     'create' => 'seller.products.create',
    //     'store' => 'seller.products.store',
    //     'edit' => 'seller.products.edit',
    //     'update' => 'seller.products.update',
    //     'destroy' => 'seller.products.destroy',
    // ]);
    // Route::get('categories/{category}/subcategories', [SellerProductController::class, 'getSubcategories'])
    //     ->name('seller.categories.subcategories');

    // // Product Listings
    // Route::resource('listings', ListingController::class)->names([
    //     'index' => 'seller.listings.index',
    //     'create' => 'seller.listings.create',
    //     'store' => 'seller.listings.store',
    //     'edit' => 'seller.listings.edit',
    //     'update' => 'seller.listings.update',
    //     'destroy' => 'seller.listings.destroy',
    // ]);

    // Route::get('/sales', [SalesController::class, 'index'])->name('seller.sales');
    // Route::get('/profile', [SellerProfileController::class, 'edit'])->name('seller.profile');
});

require __DIR__.'/auth.php';
