<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\RegisterController;
use App\Http\Controllers\Api\LoginController;
use App\Http\Controllers\Api\LogoutController;
use App\Http\Controllers\Api\LevelController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\KategoriController;
use App\Http\Controllers\Api\BarangController;
use App\Http\Controllers\Api\PenjualanController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route register dan login
Route::post('/register', RegisterController::class)->name('register');
Route::post('/login', LoginController::class)->name('login');
Route::post('/logout', LogoutController::class)->name('logout');
Route::post('/register1', RegisterController::class)->name('register1');
Route::post('/barang1', [BarangController::class, 'store']);
Route::get('/barang1', [BarangController::class, 'index']);
Route::get('/barang1/{id}', [BarangController::class, 'show']);
Route::post('/penjualan', [PenjualanController::class, 'store']);
Route::get('/penjualan', [PenjualanController::class, 'index']);
Route::get('/penjualan/{id}', [PenjualanController::class, 'show']);


// Route user (hanya bisa diakses kalau sudah login / token valid)
Route::middleware('auth:api')->group(function () {
    // User profile
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // CRUD Levels
    Route::get('levels', [LevelController::class, 'index']);
    Route::post('levels', [LevelController::class, 'store']);
    Route::get('levels/{id}', [LevelController::class, 'show']);
    Route::put('levels/{id}', [LevelController::class, 'update']);
    Route::delete('levels/{id}', [LevelController::class, 'destroy']);

    // CRUD User
    Route::get('users', [UserController::class, 'index']);
    Route::post('users', [UserController::class, 'store']);
    Route::get('users/{id}', [UserController::class, 'show']);
    Route::put('users/{id}', [UserController::class, 'update']);
    Route::delete('users/{id}', [UserController::class, 'destroy']);

    // CRUD Kategori
    Route::get('kategori', [KategoriController::class, 'index']);
    Route::post('kategori', [KategoriController::class, 'store']);
    Route::get('kategori/{id}', [KategoriController::class, 'show']);
    Route::put('kategori/{id}', [KategoriController::class, 'update']);
    Route::delete('kategori/{id}', [KategoriController::class, 'destroy']);

    // CRUD Barang
    Route::get('barang', [BarangController::class, 'index']);
    Route::post('barang', [BarangController::class, 'store']);
    Route::get('barang/{id}', [BarangController::class, 'show']);
    Route::put('barang/{id}', [BarangController::class, 'update']);
    Route::delete('barang/{id}', [BarangController::class, 'destroy']);
});
