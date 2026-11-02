<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\JenisTagihanController;
use App\Http\Controllers\KwitansiController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\PembayaranController;
use App\Http\Controllers\SantriController;
use App\Http\Controllers\TagihanController;
use App\Http\Controllers\WebhookController;
use Illuminate\Support\Facades\Route;

// Redirect Home
Route::get('/', function () {
    return redirect()->route('login');
});

// Autentikasi
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Webhook Midtrans (Bypass CSRF)
Route::post('/midtrans/webhook', [WebhookController::class, 'handle'])->name('midtrans.webhook');
Route::post('/api/midtrans/webhook', [WebhookController::class, 'handle']);

// Shared Routes (Kwitansi)
Route::middleware('auth')->group(function () {
    Route::get('/kwitansi/{id}', [KwitansiController::class, 'show'])->name('kwitansi.show');
    Route::get('/kwitansi/{id}/cetak', [KwitansiController::class, 'cetak'])->name('kwitansi.cetak');
});

// Group Role Bendahara
Route::middleware(['auth', 'role:bendahara'])->prefix('bendahara')->name('bendahara.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'bendahara'])->name('dashboard');

    // CRUD Santri
    Route::resource('santri', SantriController::class);

    // Master Jenis Tagihan
    Route::resource('jenis-tagihan', JenisTagihanController::class)->except(['create', 'show', 'edit']);

    // Tagihan & Generator Massal
    Route::get('/tagihan/generate-massal', [TagihanController::class, 'showGenerateMassal'])->name('tagihan.generate.show');
    Route::post('/tagihan/generate-massal', [TagihanController::class, 'generateMassal'])->name('tagihan.generate.post');
    Route::resource('tagihan', TagihanController::class)->except(['edit', 'update', 'show']);

    // Pembayaran & Kasir Tunai
    Route::get('/pembayaran', [PembayaranController::class, 'index'])->name('pembayaran.index');
    Route::get('/pembayaran/kasir-tunai', [PembayaranController::class, 'createKasirTunai'])->name('pembayaran.kasir.create');
    Route::post('/pembayaran/kasir-tunai', [PembayaranController::class, 'storeKasirTunai'])->name('pembayaran.kasir.store');
    Route::post('/pembayaran/{id}/sync-status', [PembayaranController::class, 'syncStatus'])->name('pembayaran.sync');
    Route::post('/pembayaran/{id}/simulate-settlement', [PembayaranController::class, 'simulateSettlement'])->name('pembayaran.simulate');

    // Laporan Keuangan & Tunggakan (Novelty Bab I)
    Route::get('/laporan/pemasukan', [LaporanController::class, 'pemasukan'])->name('laporan.pemasukan');
    Route::get('/laporan/pemasukan/cetak', [LaporanController::class, 'cetakPemasukan'])->name('laporan.pemasukan.cetak');
    Route::get('/laporan/tunggakan', [LaporanController::class, 'tunggakan'])->name('laporan.tunggakan');
    Route::get('/laporan/tunggakan/cetak', [LaporanController::class, 'cetakTunggakan'])->name('laporan.tunggakan.cetak');
});

// Group Role Wali Santri
Route::middleware(['auth', 'role:wali_santri'])->prefix('wali')->name('wali.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'wali'])->name('dashboard');
    Route::post('/tagihan/{id}/pay', [PembayaranController::class, 'getSnapToken'])->name('tagihan.pay');
});
