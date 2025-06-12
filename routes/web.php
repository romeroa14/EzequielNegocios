<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Livewire\ProductCatalog;

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

// Frontend Routes
Route::get('/', function () {
    return view('home');
})->name('home');

Route::get('/catalogo', function () {
    return view('catalog');
})->name('catalog');

Route::get('/productores', function () {
    return view('producers');
})->name('producers');

// Authentication Routes (Laravel Breeze style)
Route::middleware('guest')->group(function () {
    Route::get('login', function() {
        return view('auth.login');
    })->name('login');

    Route::get('register', function() {
        return view('auth.register');
    })->name('register');
});

Route::middleware('auth')->group(function () {
    Route::post('logout', function() {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect('/');
    })->name('logout');
});
