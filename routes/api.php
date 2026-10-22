<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\IdentitasApiController;
use App\Http\Controllers\Api\AuthApiController;
use App\Http\Controllers\Api\BarangApiController;

/*
|--------------------------------------------------------------------------
| REST API Routes - SiToko — Toko Online Sederhana 002
|--------------------------------------------------------------------------
*/

// Endpoint Identitas Wajib (Requirement 10)
Route::get('/identitas', [IdentitasApiController::class, 'index']);

// Autentikasi API
Route::post('/login', [AuthApiController::class, 'login']);

// Endpoint dengan Proteksi Token (Semua role terautentikasi)
Route::middleware('api.auth')->group(function () {
    Route::get('/user', [AuthApiController::class, 'me']);

    // 1. GET Daftar Barang (Pencarian & Pagination)
    Route::get('/barang', [BarangApiController::class, 'index']);

    // 2. GET Detail Barang
    Route::get('/barang/{id}', [BarangApiController::class, 'show']);

    // Role-Privilege Matrix: Khusus UGudang & UAdmin untuk CRUD/Manajemen Barang
    Route::middleware('api.auth:admin,gudang')->group(function () {
        // 3. POST Tambah Barang (201 Created)
        Route::post('/barang', [BarangApiController::class, 'store']);

        // 4. PUT / PATCH Ubah Barang (200 OK)
        Route::put('/barang/{id}', [BarangApiController::class, 'update']);
        Route::patch('/barang/{id}', [BarangApiController::class, 'update']);

        // 5. DELETE Hapus Barang (200 OK)
        Route::delete('/barang/{id}', [BarangApiController::class, 'destroy']);
    });
});
