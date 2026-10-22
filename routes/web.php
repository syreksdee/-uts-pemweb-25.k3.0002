<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\KasirController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\PelangganController;

/*
|--------------------------------------------------------------------------
| Web Routes - SiToko — Toko Online Sederhana 002
|--------------------------------------------------------------------------
*/

// Redirect root ke Toko
Route::get('/', function () {
    return redirect()->route('toko.index');
});

// Autentikasi Web
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::match(['get', 'post'], '/logout', [AuthController::class, 'logout'])->name('logout');

// Quick Demo Login (Mempermudah Pengujian / Penilaian Dosen)
Route::get('/demo-login/{role}', [AuthController::class, 'quickLogin'])->name('demo.login');

// Toko Online (Katalog Publik & Detail Produk)
Route::get('/toko', [PelangganController::class, 'index'])->name('toko.index');
Route::get('/toko/produk/{id}', [PelangganController::class, 'show'])->name('toko.produk.show');

// Grup Rute Terotentikasi (Semua Pengguna yang sudah Login)
Route::middleware('role')->group(function () {
    // Profil & Ubah Foto Profil (Requirement 3a: jpg/png, <= 2MB, simpan path di DB)
    Route::get('/profil', [AuthController::class, 'showProfile'])->name('profile.show');
    Route::put('/profil', [AuthController::class, 'updateProfile'])->name('profile.update');

    // Modul Pelanggan: Keranjang, Checkout, dan Pembayaran (Requirement 3d)
    Route::get('/keranjang', [PelangganController::class, 'keranjang'])->name('toko.keranjang');
    Route::post('/checkout', [PelangganController::class, 'checkout'])->name('toko.checkout');
    Route::get('/pesanan/{id}/bayar', [PelangganController::class, 'bayar'])->name('toko.bayar');
    Route::post('/pesanan/{id}/bayar-simulasi', [PelangganController::class, 'simulatePayment'])->name('toko.bayar.simulasi');
    Route::get('/pesanan-saya', [PelangganController::class, 'pesananSaya'])->name('toko.pesanan');

    // Modul Kasir (UKasir: Desktop POS, penjualan & kurangi stok - Requirement 3c & 4a)
    Route::prefix('kasir')->name('kasir.')->middleware('role:kasir,admin')->group(function () {
        Route::get('/', [KasirController::class, 'pos'])->name('pos');
        Route::post('/checkout', [KasirController::class, 'checkout'])->name('checkout');
        Route::get('/struk/{id}', [KasirController::class, 'struk'])->name('struk');
        Route::get('/riwayat', [KasirController::class, 'riwayat'])->name('riwayat');
    });

    // Modul Gudang (UGudang: CRUD Barang & Kategori dengan Search & Pagination - Requirement 4b & 8)
    Route::prefix('gudang')->name('gudang.')->middleware('role:gudang,admin')->group(function () {
        Route::resource('barang', BarangController::class);
        Route::resource('kategori', KategoriController::class);
    });

    // Modul Admin (UAdmin: Read/view laporan, atur kasir & pelanggan - Requirement 3b & 4c)
    Route::prefix('admin')->name('admin.')->middleware('role:admin')->group(function () {
        Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
        Route::get('/laporan', [AdminController::class, 'laporan'])->name('laporan');
        Route::get('/transaksi/{id}', [AdminController::class, 'detailTransaksi'])->name('transaksi.detail');

        // Manajemen Kasir
        Route::get('/kasir', [AdminController::class, 'manageKasir'])->name('kasir.index');
        Route::post('/kasir', [AdminController::class, 'storeKasir'])->name('kasir.store');
        Route::delete('/kasir/{id}', [AdminController::class, 'deleteKasir'])->name('kasir.destroy');

        // Manajemen Pelanggan
        Route::get('/pelanggan', [AdminController::class, 'managePelanggan'])->name('pelanggan.index');
        Route::post('/pelanggan', [AdminController::class, 'storePelanggan'])->name('pelanggan.store');
        Route::delete('/pelanggan/{id}', [AdminController::class, 'deletePelanggan'])->name('pelanggan.destroy');
    });
});
