<?php

use App\Http\Controllers\KategoriController;
use App\Http\Controllers\KeranjangController;
use App\Http\Controllers\ProdukController;
use App\Http\Middleware\AdminMiddleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

// Admin Routes
Route::group(['prefix' => 'admin', 'middleware' => ['auth', AdminMiddleware::class]], function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('kategori', KategoriController::class);
    Route::resource('produk', ProdukController::class);
});

// User Routes
Route::group(['prefix' => 'user'], function () {
    Route::get('home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
    Route::get('shop', function () {return view('user.shop');})->name('shop');
    Route::get('keranjang', [KeranjangController::class, 'index'])->name('keranjang.index');
    Route::post('keranjang', [KeranjangController::class, 'store'])->name('keranjang.store');
    Route::get('keranjang/{id}', [KeranjangController::class, 'show'])->name('keranjang.show');
    Route::get('keranjang/{id}/edit', [KeranjangController::class, 'edit'])->name('keranjang.edit');
    Route::put('keranjang/{id}', [KeranjangController::class, 'update'])->name('keranjang.update');
    Route::delete('keranjang/{id}', [KeranjangController::class, 'destroy'])->name('keranjang.destroy');
    Route::get('/detailpesanan/{id}', function ($id) {
        $produk = App\Models\Produk::find($id);
        return view('user.detailpesanan', compact('produk'));
    })->name('detailpesanan');
});
