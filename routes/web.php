<?php

use App\Http\Controllers\KategoriController;
use App\Http\Controllers\KeranjangController;
use App\Http\Controllers\MidtransController;
use App\Http\Controllers\PembayaranController;
use App\Http\Controllers\ProdukController;
use App\Http\Controllers\ProfileController;
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
    Route::resource('pembayaran', PembayaranController::class);

    // Admin pesanan index route
    Route::get('pesanan', [App\Http\Controllers\PesananController::class, 'adminIndex'])->name('admin.pesanan.index');
});

// User Routes
Route::group(['prefix' => 'user', 'middleware' => 'auth'], function () {
    Route::get('home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
    Route::get('shop', [App\Http\Controllers\ProdukController::class, 'userShop'])->name('shop');
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
    Route::get('pembayaran', [PembayaranController::class, 'showPaymentPage'])->name('pembayaran.show');
    Route::delete('pembayaran', [PembayaranController::class, 'showPaymentPage'])->name('pembayaran.destroy');
    Route::post('pembayaran', [PembayaranController::class, 'storeUserPayment'])->name('pembayaran.store');

    Route::get('/checkout/{id}', [MidtransController::class, 'checkoutPage'])->name('checkout.page');
    // Jika kamu simpan di web.php:
    Route::post('/api/midtrans/create-transaction', [MidtransController::class, 'createTransaction']);
    Route::post('/midtrans/callback', [MidtransController::class, 'callback']);

    // Added routes for PesananController
    Route::get('pesanan', [App\Http\Controllers\PesananController::class, 'index'])->name('pesanan.index');
    Route::get('pesanan/{id}', [App\Http\Controllers\PesananController::class, 'show'])->name('pesanan.show');

    // Profile route
    Route::middleware(['auth'])->group(function () {
        Route::get('profile', [ProfileController::class, 'show'])->name('profile.show');
        Route::get('profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('profile/update', [ProfileController::class, 'update'])->name('profile.update');
    });
});
