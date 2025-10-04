<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\KategoriProdukController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;




Route::post('/login', [AuthController::class, 'login']);


Route::middleware('auth:sanctum')->group(function () {

    // produk kategori routes
    Route::get('/kategori-produks', [KategoriProdukController::class, 'index']);
    Route::post('/kategori-produks', [KategoriProdukController::class, 'store']);
    Route::put('/kategori-produks/{id}', [KategoriProdukController::class, 'update']);
    Route::delete('/kategori-produks/{id}', [KategoriProdukController::class, 'destroy']);

    Route::post('/logout', [AuthController::class, 'logout']);
});
