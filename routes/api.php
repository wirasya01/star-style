<?php

use App\Http\Controllers\Api\AuthController;
// import controller nya
use App\Http\Controllers\Api\KategoriController;
use App\Http\Controllers\Api\ProdukController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/profile', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// auth
Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);
Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
// kategori
Route::apiResource('kategori', KategoriController::class);
// produk
Route::apiResource('produk', ProdukController::class);

Route::post('/midtrans/create-transaction/{id}', [MidtransController::class, 'createTransaction']);

