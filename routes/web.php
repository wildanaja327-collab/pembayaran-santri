<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\JenisTagihanController;
use App\Http\Controllers\KwitansiController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\PembayaranController;
use App\Http\Controllers\ProgramController;
use App\Http\Controllers\SantriController;
use App\Http\Controllers\TagihanController;
use App\Http\Controllers\TahunAjaranController;
use App\Http\Controllers\UserController;
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

// Akses Cetak Kwitansi Resmi (Bisa diakses tanpa login jika membawa token valid dari WhatsApp, atau oleh user yang login)
Route::get('/kwitansi/{id}/cetak', [KwitansiController::class, 'cetak'])->name('kwitansi.cetak');
Route::get('/kwitansi/publik/{id}', [KwitansiController::class, 'cetakPublik'])->name('kwitansi.publik');

// Shared Routes (Kwitansi untuk semua user yang login)
Route::middleware('auth')->group(function () {
    Route::get('/kwitansi/{id}', [KwitansiController::class, 'show'])->name('kwitansi.show');
    Route::post('/kwitansi/{id}/kirim-wa', [KwitansiController::class, 'kirimWa'])->name('kwitansi.kirim-wa');
});

// ==========================================
// 1. GROUP ROLE: ADMIN (Administrator Sistem)
// ==========================================
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

    // Audit Trail & Log Aktivitas Sistem
    Route::get('/activity-logs', [AdminController::class, 'activityLogs'])->name('activity-logs');

    // Konfigurasi Payment Gateway Midtrans (Khusus Administrator)
    Route::get('/midtrans', [AdminController::class, 'midtransConfig'])->name('midtrans.index');
    Route::put('/midtrans', [AdminController::class, 'updateMidtransConfig'])->name('midtrans.update');
    Route::post('/midtrans/test-connection', [AdminController::class, 'testMidtransConnection'])->name('midtrans.test');
    Route::post('/midtrans/simulate-webhook', [AdminController::class, 'simulateWebhook'])->name('midtrans.simulate-webhook');

    // Manajemen WhatsApp Gateway (Fonnte)
    Route::get('/whatsapp', [AdminController::class, 'whatsappConfig'])->name('whatsapp.index');
    Route::put('/whatsapp', [AdminController::class, 'updateWhatsappConfig'])->name('whatsapp.update');
    Route::post('/whatsapp/test-send', [AdminController::class, 'testWhatsappSend'])->name('whatsapp.test-send');
    Route::post('/whatsapp/check-device', [AdminController::class, 'checkWhatsappDevice'])->name('whatsapp.check-device');

    // Manajemen Pengguna (Users: Admin, Bendahara, Wali)
    Route::resource('users', UserController::class)->except(['create', 'show', 'edit']);
    Route::post('/users/{id}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');
    Route::post('/users/{id}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');

    // Manajemen Periode Tahun Ajaran
    Route::resource('tahun-ajaran', TahunAjaranController::class)->except(['create', 'show', 'edit']);
    Route::post('/tahun-ajaran/{id}/set-aktif', [TahunAjaranController::class, 'setAktif'])->name('tahun-ajaran.set-aktif');

    // Konfigurasi Sistem, Identitas Pesantren & Gateway
    Route::get('/pengaturan', [AdminController::class, 'pengaturan'])->name('pengaturan');
    Route::put('/pengaturan', [AdminController::class, 'updatePengaturan'])->name('pengaturan.update');

    // Master Program Unggulan
    Route::resource('program', ProgramController::class)->except(['create', 'show', 'edit']);

    // Data Santri
    Route::resource('santri', SantriController::class);
});

// ==========================================
// 2. GROUP ROLE: BENDAHARA (Finance Officer)
// ==========================================
Route::middleware(['auth', 'role:bendahara'])->prefix('bendahara')->name('bendahara.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'bendahara'])->name('dashboard');

    // AJAX Live Search Endpoints
    Route::get('/santri/search-ajax', [SantriController::class, 'searchAjax'])->name('santri.search-ajax');
    Route::get('/tagihan/search-ajax', [TagihanController::class, 'searchAjax'])->name('tagihan.search-ajax');

    // Direktori Santri
    Route::resource('santri', SantriController::class);

    // Master Jenis Tagihan
    Route::resource('jenis-tagihan', JenisTagihanController::class)->except(['create', 'show', 'edit']);

    // Tagihan & Generator Massal
    Route::get('/tagihan/generate-massal', [TagihanController::class, 'showGenerateMassal'])->name('tagihan.generate.show');
    Route::post('/tagihan/generate-massal', [TagihanController::class, 'generateMassal'])->name('tagihan.generate.post');
    Route::post('/tagihan/{id}/kirim-reminder-wa', [TagihanController::class, 'kirimReminderWa'])->name('tagihan.kirim-reminder-wa');
    Route::post('/tagihan/{id}/update-alasan', [TagihanController::class, 'updateAlasan'])->name('tagihan.update-alasan');
    Route::resource('tagihan', TagihanController::class)->except(['edit', 'update']);

    // Transaksi Pembayaran, Rekonsiliasi Gateway & Kasir Tunai
    Route::get('/pembayaran', [PembayaranController::class, 'index'])->name('pembayaran.index');
    Route::get('/pembayaran/rekonsiliasi', [PembayaranController::class, 'rekonsiliasi'])->name('pembayaran.rekonsiliasi');
    Route::post('/pembayaran/{id}/cancel', [PembayaranController::class, 'cancelTransaction'])->name('pembayaran.cancel');
    Route::get('/pembayaran/kasir-tunai', [PembayaranController::class, 'createKasirTunai'])->name('pembayaran.kasir.create');
    Route::post('/pembayaran/kasir-tunai', [PembayaranController::class, 'storeKasirTunai'])->name('pembayaran.kasir.store');
    Route::post('/pembayaran/{id}/sync-status', [PembayaranController::class, 'syncStatus'])->name('pembayaran.sync');
    Route::post('/pembayaran/{id}/simulate-settlement', [PembayaranController::class, 'simulateSettlement'])->name('pembayaran.simulate');

    // Laporan Keuangan, Export Excel/CSV & Rekapan Tunggakan
    Route::get('/laporan/pemasukan', [LaporanController::class, 'pemasukan'])->name('laporan.pemasukan');
    Route::get('/laporan/pemasukan/export-excel', [LaporanController::class, 'exportPemasukanExcel'])->name('laporan.pemasukan.excel');
    Route::get('/laporan/pemasukan/export-csv', [LaporanController::class, 'exportPemasukanCsv'])->name('laporan.pemasukan.csv');
    Route::get('/laporan/pemasukan/cetak', [LaporanController::class, 'cetakPemasukan'])->name('laporan.pemasukan.cetak');
    Route::get('/laporan/tunggakan', [LaporanController::class, 'tunggakan'])->name('laporan.tunggakan');
    Route::get('/laporan/tunggakan/export-excel', [LaporanController::class, 'exportTunggakanExcel'])->name('laporan.tunggakan.excel');
    Route::get('/laporan/tunggakan/export-csv', [LaporanController::class, 'exportTunggakanCsv'])->name('laporan.tunggakan.csv');
    Route::get('/laporan/tunggakan/cetak', [LaporanController::class, 'cetakTunggakan'])->name('laporan.tunggakan.cetak');
});

// ==========================================
// 3. GROUP ROLE: WALI SANTRI (Portal Wali)
// ==========================================
Route::middleware(['auth', 'role:wali_santri'])->prefix('wali')->name('wali.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'wali'])->name('dashboard');
    Route::post('/tagihan/{id}/pay', [PembayaranController::class, 'getSnapToken'])->name('tagihan.pay');
    Route::post('/tagihan/{id}/ajukan-penundaan', [DashboardController::class, 'ajukanPenundaanWali'])->name('tagihan.ajukan-penundaan');
    Route::post('/pembayaran/{id}/sync', [PembayaranController::class, 'syncWaliStatus'])->name('pembayaran.sync');
    Route::post('/pembayaran/{id}/simulate-settlement', [PembayaranController::class, 'simulateSettlementWali'])->name('pembayaran.simulate');

    // Detail per santri
    Route::get('/santri/{nis}', [DashboardController::class, 'waliSantriDetail'])->name('santri.show');

    // Riwayat pembayaran lengkap (all history + filter + paginate)
    Route::get('/riwayat', [DashboardController::class, 'waliRiwayat'])->name('riwayat');

    // Panduan lengkap pembayaran bergambar
    Route::get('/panduan', [DashboardController::class, 'waliPanduan'])->name('panduan');

    // Profil & akun wali
    Route::get('/profil', [DashboardController::class, 'waliProfil'])->name('profil');
    Route::put('/profil', [DashboardController::class, 'waliProfilUpdate'])->name('profil.update');
});
