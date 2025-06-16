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
use App\Http\Controllers\Auth\RegisteredUserController;
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
use App\Http\Controllers\Buyer\DashboardController as BuyerDashboardController;

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
Route::get('/catalog', function() {
    return view('catalog');
})->name('catalog');

// Rutas de productos (públicas)
Route::get('/products', function() {
    return view('products.products');
})->name('products');

// Rutas de productores (públicas)
Route::get('/producers', function() {
    return view('producers.producers');
})->name('producers');

Route::get('/producers/{producer}', function() {
    return view('producers.producer');
})->name('producers.show');

// Route::get('/producers', [ProducerController::class, 'index'])->name('producers');
// Route::get('/producers/{producer}', [ProducerController::class, 'show'])->name('producers.show');

// Contactar productor (público, puedes hacer que envíe un email o redirija a WhatsApp)
Route::post('/contact-producer/{producer}', [ProducerController::class, 'contact'])->name('producers.contact');

// Rutas de autenticación
Route::middleware('guest')->group(function () {
    // Las rutas de autenticación se manejan en routes/auth.php
});

// Rutas para usuarios autenticados
Route::middleware(['auth'])->group(function () {
    Route::get('/home', function () {
        return view('home');
    })->name('home');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
});

// Rutas de verificación de email
Route::middleware('auth')->group(function () {
    Route::get('email/verify', [VerificationController::class, 'show'])->name('verification.notice');
    Route::get('email/verify/{id}/{hash}', [VerificationController::class, 'verify'])->name('verification.verify');
    Route::post('email/resend', [VerificationController::class, 'resend'])->name('verification.resend');
});

// Ruta de logout
Route::post('logout', [LoginController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

// Rutas para compradores
Route::middleware(['auth', 'role:buyer'])->group(function () {
    Route::get('/buyer/dashboard', [BuyerDashboardController::class, 'index'])->name('buyer.dashboard');
});

// Rutas para vendedores
Route::middleware(['auth', 'role:seller'])->group(function () {
    Route::get('/seller/dashboard', [\App\Http\Controllers\Seller\DashboardController::class, 'index'])->name('seller.dashboard');
    
    // Nueva ruta Livewire para gestión de productos
    Route::view('/seller/products', 'seller.products')->name('seller.products.index');
    
    // Rutas antiguas de productos (comentadas porque ahora se usa Livewire)
    // Route::get('/seller/products', [\App\Http\Controllers\Seller\ProductController::class, 'index'])->name('seller.products.index');
    // Route::get('/seller/products/create', [\App\Http\Controllers\Seller\ProductController::class, 'create'])->name('seller.products.create');
    // Route::post('/seller/products', [\App\Http\Controllers\Seller\ProductController::class, 'store'])->name('seller.products.store');
    // Route::get('/seller/products/{listing}/edit', [\App\Http\Controllers\Seller\ProductController::class, 'edit'])->name('seller.products.edit');
    // Route::put('/seller/products/{listing}', [\App\Http\Controllers\Seller\ProductController::class, 'update'])->name('seller.products.update');
    // Route::delete('/seller/products/{listing}', [\App\Http\Controllers\Seller\ProductController::class, 'destroy'])->name('seller.products.destroy');
    // Route::patch('/seller/products/{listing}/toggle-status', [\App\Http\Controllers\Seller\ProductController::class, 'toggleStatus'])->name('seller.products.toggle-status');
    
    // Ruta para obtener subcategorías (puede mantenerse si la usas vía AJAX)
    Route::get('/seller/categories/{category}/subcategories', [\App\Http\Controllers\Seller\ProductController::class, 'getSubcategories'])
        ->name('seller.categories.subcategories');
});

require __DIR__.'/auth.php';
