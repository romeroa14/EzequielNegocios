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
use App\Http\Controllers\ProductPresentationController;
use App\Http\Controllers\CookieController;
use App\Http\Controllers\WelcomeController;
use App\Http\Controllers\MarketController;

// Rutas públicas
Route::get('/', function() {
    return view('catalog');
})->name('welcome');

Route::get('/welcome', function() {
    // Obtener las tasas de cambio BCV
    $exchangeRates = [
        'usd' => [
            'rate' => \App\Models\ExchangeRate::where('currency_code', 'USD')->latest()->first()?->rate ?? null
        ],
        'eur' => [
            'rate' => \App\Models\ExchangeRate::where('currency_code', 'EUR')->latest()->first()?->rate ?? null
        ]
    ];
    
    return view('welcome', compact('exchangeRates'));
})->name('welcome.page');

Route::get('/home', function() {
    return view('catalog');
})->name('home');

// Rutas del catálogo (públicas)
Route::get('/catalogo', function() {
    return view('catalog');
})->name('catalogo');

// Endpoint AJAX para detalles del producto
Route::get('/product-details/{productId}', function($productId) {
    $product = \App\Models\ProductListing::with([
        'product.productCategory',
        'product.productSubcategory', 
        'product.productLine',
        'product.brand',
        'product.productPresentation',
        'person',
        'state',
        'municipality',
        'parish'
    ])
    ->where('id', $productId)
    ->where('status', 'active')
    ->first();
    
    if (!$product) {
        return response()->json(['error' => 'Producto no encontrado'], 404);
    }
    
    return response()->json($product);
})->name('product.details');

// Rutas de productos (públicas)
Route::get('/productos', function() {
    return view('products.products');
})->name('productos');

// Rutas de productores (públicas)
Route::get('/productores', [ProducerController::class, 'index'])->name('productores.index');
Route::get('/productores/{producer}', [ProducerController::class, 'show'])->name('productores.show');
Route::post('/productores/{producer}/contact', [ProducerController::class, 'contact'])->name('productores.contact');

// Route::get('/producers', [ProducerController::class, 'index'])->name('producers');
// Route::get('/producers/{producer}', [ProducerController::class, 'show'])->name('producers.show');

// Contactar productor (público, puedes hacer que envíe un email o redirija a WhatsApp)
Route::post('/contact-producer/{producer}', [ProducerController::class, 'contact'])->name('producers.contact');

// Rutas de políticas y cookies
Route::get('/politica-privacidad', [CookieController::class, 'privacyPolicy'])->name('privacy-policy');
Route::get('/politica-cookies', [CookieController::class, 'cookiePolicy'])->name('cookie-policy');

// Rutas de precios de mercado (públicas)
Route::get('/mercado', [MarketController::class, 'index'])->name('market.index');
Route::get('/mercado/semanal', [MarketController::class, 'weekly'])->name('market.weekly');
Route::get('/mercado/producto/{product}/historial', [MarketController::class, 'productHistory'])->name('market.product.history');

// Webhooks para automatización (usando grupo API sin CSRF)
Route::middleware('api')->group(function () {
    Route::post('/webhook/bcv/update-rates', [App\Http\Controllers\WebhookController::class, 'updateBcvRates'])->name('webhook.bcv.update-rates');
    Route::get('/webhook/health', [App\Http\Controllers\WebhookController::class, 'healthCheck'])->name('webhook.health');
    Route::post('/webhook/bcv/cleanup', [App\Http\Controllers\WebhookController::class, 'cleanupBcvRates'])->name('webhook.bcv.cleanup');
});
Route::get('/preferencias-cookies', [CookieController::class, 'showPreferences'])->name('cookie.preferences.show');
Route::post('/cookie-preferences', [CookieController::class, 'storePreferences'])->name('cookie.preferences');
Route::patch('/cookie-preferences', [CookieController::class, 'updatePreferences'])->name('cookie.preferences.update');

// Rutas de autenticación
Route::middleware('guest')->group(function () {
    // Las rutas de autenticación se manejan en routes/auth.php
});

// Rutas de Google OAuth
Route::get('/auth/google', [App\Http\Controllers\Auth\GoogleController::class, 'redirectToGoogle'])->name('google.redirect');
Route::get('/auth/google/callback', [App\Http\Controllers\Auth\GoogleController::class, 'handleGoogleCallback'])->name('google.callback');

// Rutas para completar perfil (requieren autenticación)
Route::middleware(['auth'])->group(function () {
    Route::get('/profile/complete', [App\Http\Controllers\Auth\GoogleController::class, 'showCompleteProfile'])->name('profile.complete');
    Route::post('/profile/complete', [App\Http\Controllers\Auth\GoogleController::class, 'completeProfile'])->name('profile.complete.store');
});



// Rutas para usuarios autenticados
Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/profile/municipalities/{state_id}', [ProfileController::class, 'getMunicipalities'])->name('profile.municipalities');
    Route::get('/profile/parishes/{municipality_id}', [ProfileController::class, 'getParishes'])->name('profile.parishes');
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
Route::middleware(['auth', 'role:seller'])->prefix('seller')->name('seller.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Seller\DashboardController::class, 'index'])->name('dashboard');
    Route::view('/products', 'seller.products')->name('products.index');
    Route::view('/listings', 'seller.listings')->name('listings.index');
});

// Rutas para los selects en cascada de ubicación
Route::get('/get-municipalities/{state_id}', [RegisteredUserController::class, 'getMunicipalities'])->name('get.municipalities');
Route::get('/get-parishes/{municipality_id}', [RegisteredUserController::class, 'getParishes'])->name('get.parishes');

require __DIR__.'/auth.php';

Route::get('/productor/{listing}', function ($listing) {
    return view('productor.show', ['listing' => $listing]);
})->name('productor.show');
