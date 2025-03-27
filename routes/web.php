<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', [ProductController::class, 'index'])->name('home');
Route::get('/products/{product:slug}', [ProductController::class, 'show'])->name('product.show');


Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', function () {
        return Inertia::render('dashboard');
    })->name('dashboard');
});

Route::controller(CartController::class)->prefix('cart')->name('cart.')->group(function () {
    Route::get('/', 'index')->name('index');
    Route::post('/add/{product}', 'store')->name('store');
    Route::post('/checkout', 'checkout')->name('checkout');
    Route::put('/{product}', 'update')->name('update');
    Route::delete('/{product}', 'destroy')->name('delete');
});

require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
