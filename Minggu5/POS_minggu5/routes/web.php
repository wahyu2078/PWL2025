<?php

use App\Http\Controllers\BarangController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\LevelController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WelcomeController;
use Illuminate\Support\Facades\Route;

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

Route::get('/', [WelcomeController::class, 'index']);  // Halaman Welcome

// Rute untuk User
Route::group(['prefix' => 'user'], function () {
    Route::get('/', [UserController::class, 'index']);  // Menampilkan halaman awal user
    Route::post('/list', [UserController::class, 'list']);  // Menampilkan data user dalam bentuk JSON untuk datatables
    Route::get('/create', [UserController::class, 'create']);  // Menampilkan halaman form tambah user
    Route::post('/', [UserController::class, 'store']);  // Menyimpan data user baru
    Route::get('/{id}', [UserController::class, 'show']);  // Menampilkan detail user
    Route::get('/{id}/edit', [UserController::class, 'edit']);  // Menampilkan halaman form edit user
    Route::put('/{id}', [UserController::class, 'update']);  // Menyimpan perubahan data user
    Route::delete('/{id}', [UserController::class, 'destroy']);  // Menghapus data user
});

// Rute untuk Level
Route::get('/level', [LevelController::class, 'index']);
Route::post('/level/list', [LevelController::class, 'list']);

// Rute untuk Kategori
Route::get('/kategori', [KategoriController::class, 'index']);
Route::post('/kategori/list', [KategoriController::class, 'list']);

// Rute untuk Supplier
Route::get('/supplier', [SupplierController::class, 'index']);
Route::post('/supplier/list', [SupplierController::class, 'list']);

// Rute untuk Barang
Route::get('/barang', [BarangController::class, 'index']);
Route::post('/barang/list', [BarangController::class, 'list']);
