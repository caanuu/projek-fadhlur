<?php

use App\Http\Controllers\LoginController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\TransaksiMasukController;
use App\Http\Controllers\TransaksiKeluarController;
use App\Http\Controllers\MutasiKondisiController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// --- PUBLIC ROUTES ---
Route::get('/', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/', [LoginController::class, 'login'])->name('login.process');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// --- PROTECTED ROUTES ---
Route::middleware('auth')->group(function () {

    // Redirect Home berdasarkan Role
    Route::get('/home', function () {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($user->isAdmin())
            return redirect()->route('dashboard');
        if ($user->isGudang())
            return redirect()->route('transaksi-masuk.index');
        if ($user->isKasir())
            return redirect()->route('transaksi-keluar.index');

        return redirect()->route('login');
    })->name('home');

    // === GROUP 1: AKSES DATA BARANG (Admin, Gudang, Kasir) ===
    Route::middleware('role:admin,gudang,kasir')->group(function () {
        Route::get('barang/export', [BarangController::class, 'export'])->name('barang.export');
        Route::resource('barang', BarangController::class);
        Route::get('/get-stok-barang/{id}', [BarangController::class, 'getStok'])->name('barang.getStok');
    });

    // === GROUP 2: BARANG MASUK & MUTASI & SUPPLIER (Admin, Gudang) ===
    Route::middleware('role:admin,gudang')->group(function () {
        Route::get('transaksi-masuk/export', [TransaksiMasukController::class, 'export'])->name('transaksi-masuk.export');
        Route::resource('transaksi-masuk', TransaksiMasukController::class)->only(['index', 'create', 'store']);

        // TAMBAHAN: Route Supplier
        Route::resource('suppliers', \App\Http\Controllers\SupplierController::class);

        Route::get('list', [BarangController::class, 'list'])->name('barang.list');
        Route::get('rusak', [BarangController::class, 'rusak'])->name('barang.rusak');
        Route::resource('mutasi-kondisi', MutasiKondisiController::class)->only(['create', 'store']);
    });

    // === GROUP 3: BARANG KELUAR & CUSTOMER (Admin, Kasir) ===
    Route::middleware('role:admin,kasir')->group(function () {
        Route::get('transaksi-keluar/export', [TransaksiKeluarController::class, 'export'])->name('transaksi-keluar.export');
        Route::get('transaksi-keluar/{id}/print', [TransaksiKeluarController::class, 'print'])->name('transaksi-keluar.print');
        Route::resource('transaksi-keluar', TransaksiKeluarController::class)->only(['index', 'create', 'store', 'show']);

        // TAMBAHAN: Route Customer
        Route::resource('customers', \App\Http\Controllers\CustomerController::class);
    });

    // === GROUP 4: DASHBOARD & LAPORAN (Admin Only) ===
    Route::middleware('role:admin')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
    });
});
