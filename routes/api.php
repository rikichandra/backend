<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\KategoriProdukController;
use App\Http\Controllers\ProdukController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;




Route::post('/login', [AuthController::class, 'login']);


Route::middleware('auth:sanctum')->group(function () {

    // produk kategori routes
    Route::get('/kategori-produks', [KategoriProdukController::class, 'index']);
    Route::post('/kategori-produks', [KategoriProdukController::class, 'store']);
    Route::put('/kategori-produks/{id}', [KategoriProdukController::class, 'update']);
    Route::delete('/kategori-produks/{id}', [KategoriProdukController::class, 'destroy']);

    // produk routes
    Route::get('/produks', [ProdukController::class, 'index']);
    Route::post('/produks', [ProdukController::class, 'store']);
    Route::put('/produks/{id}', [ProdukController::class, 'update']);
    Route::delete('/produks/{id}', [ProdukController::class, 'destroy']);

    // transaksi routes
    Route::post('/transaksis', [\App\Http\Controllers\TransaksiController::class, 'store']);
    Route::get('/transaksis', [\App\Http\Controllers\TransaksiController::class, 'index']);

    Route::post('/logout', [AuthController::class, 'logout']);
});
