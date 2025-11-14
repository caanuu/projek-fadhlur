<?php

use App\Http\Controllers\LoginController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\TransaksiMasukController;
use App\Http\Controllers\TransaksiKeluarController;
use App\Http\Controllers\MutasiKondisiController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

// --- RUTE LOGIN (PUBLIK) ---
Route::get('/', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/', [LoginController::class, 'login'])->name('login.process');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');


// --- RUTE YANG MEMBUTUHKAN LOGIN ---
Route::middleware('auth')->group(function () {

    // Dashboard (Bisa diakses semua role)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // --- RUTE UNTUK GUDANG & ADMIN ---
    Route::middleware('role:admin,gudang')->group(function () {

        // Master Barang (minus Hapus)
        Route::get('barang', [BarangController::class, 'index'])->name('barang.index');
        Route::get('barang/create', [BarangController::class, 'create'])->name('barang.create');
        Route::post('barang', [BarangController::class, 'store'])->name('barang.store');
        Route::get('barang/{barang}/edit', [BarangController::class, 'edit'])->name('barang.edit');
        Route::put('barang/{barang}', [BarangController::class, 'update'])->name('barang.update');

        // Laporan Stok
        Route::get('list', [BarangController::class, 'list'])->name('barang.list');
        Route::get('rusak', [BarangController::class, 'rusak'])->name('barang.rusak');

        // Transaksi Masuk
        Route::get('transaksi-masuk', [TransaksiMasukController::class, 'index'])->name('transaksi-masuk.index');
        Route::get('transaksi-masuk/create', [TransaksiMasukController::class, 'create'])->name('transaksi-masuk.create');
        Route::post('transaksi-masuk', [TransaksiMasukController::class, 'store'])->name('transaksi-masuk.store');
        Route::get('/transaksi-masuk/export', [TransaksiMasukController::class, 'export'])->name('transaksi-masuk.export');

        // Mutasi Kondisi
        Route::resource('mutasi-kondisi', MutasiKondisiController::class)->only(['create','store']);
    });

    // --- RUTE UNTUK KASIR & ADMIN ---
    Route::middleware('role:admin,kasir')->group(function () {

        // Transaksi Keluar
        Route::get('transaksi-keluar', [TransaksiKeluarController::class, 'index'])->name('transaksi-keluar.index');
        Route::get('transaksi-keluar/create', [TransaksiKeluarController::class, 'create'])->name('transaksi-keluar.create');
        Route::post('transaksi-keluar', [TransaksiKeluarController::class, 'store'])->name('transaksi-keluar.store');

        Route::get('/transaksi-keluar/export', [TransaksiKeluarController::class, 'export'])->name('transaksi-keluar.export');
        Route::get('/transaksi-keluar/{id}/print', [TransaksiKeluarController::class, 'print'])->name('transaksi-keluar.print');

        // Helper untuk Get Stok (dibutuhkan form transaksi keluar)
        Route::get('/get-stok-barang/{id}', [BarangController::class, 'getStok'])->name('barang.getStok');
    });

    // --- RUTE KHUSUS ADMIN ---
    Route::middleware('role:admin')->group(function () {

        // Hapus Barang
        Route::delete('barang/{barang}', [BarangController::class, 'destroy'])->name('barang.destroy');

        // Laporan Transaksi (Keuangan)
        Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');

        // Note: Admin juga bisa mengakses rute 'admin,gudang' dan 'admin,kasir' di atas.
    });

});
