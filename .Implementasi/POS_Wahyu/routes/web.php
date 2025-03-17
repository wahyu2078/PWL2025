<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\CategoryController;

// Halaman Home
Route::get('/', [HomeController::class, 'index']);

// Halaman Products (Dengan Prefix)
Route::prefix('category')->group(function () {
    Route::get('/food-beverage', [ProductController::class, 'foodBeverage']);
    Route::get('/beauty-health', [ProductController::class, 'beautyHealth']);
    Route::get('/home-care', [ProductController::class, 'homeCare']);
    Route::get('/baby-kid', [ProductController::class, 'babyKid']);
});

// Halaman User (Dengan Parameter)
Route::get('/user/{id}/name/{name}', [UserController::class, 'show']);

// Halaman Kategori
Route::get('/kategori', [KategoriController::class, 'index']);

// Halaman Produk
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');
Route::post('/products', [ProductController::class, 'store'])->name('products.store');

// Halaman Category
Route::get('/category/{category}', [ProductController::class, 'index']);
Route::get('/category/{id}', [CategoryController::class, 'show'])->name('category.show');
