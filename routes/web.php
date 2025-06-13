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
    Route::get('register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('register', [RegisteredUserController::class, 'store']);
    Route::get('login', [LoginController::class, 'create'])->name('login');
    Route::post('login', [LoginController::class, 'store']);
});

// Rutas para compradores/vendedores
Route::middleware(['auth', 'person'])->group(function () {
    Route::get('/home', function () {
        return view('home');
    })->name('home');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
});

// Rutas para administradores
Route::middleware(['auth:admin', 'admin'])->prefix('admin')->group(function () {
    Route::get('/', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');
});

// Ruta de logout (accesible para todos los usuarios autenticados)
Route::post('logout', [LoginController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

// Rutas solo para vendedores
Route::middleware(['auth', 'person', 'role:seller'])->prefix('seller')->group(function () {
    Route::resource('listings', ListingController::class);
    Route::get('/sales', [SalesController::class, 'index'])->name('seller.sales');
    Route::get('/sales/{sale}', [SalesController::class, 'show'])->name('seller.sales.show');
    Route::patch('/sales/{sale}', [SalesController::class, 'update'])->name('seller.sales.update');
});

Route::middleware('auth')->group(function () {
    Route::get('email/verify', [VerificationController::class, 'show'])->name('verification.notice');
    Route::get('email/verify/{id}/{hash}', [VerificationController::class, 'verify'])->name('verification.verify');
    Route::post('email/resend', [VerificationController::class, 'resend'])->name('verification.resend');
});

require __DIR__.'/auth.php';
