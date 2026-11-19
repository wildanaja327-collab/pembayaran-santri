@extends('layouts.app')

@section('title', 'Panduan Pembayaran')
@section('page_title', 'Pusat Panduan & Edukasi Pembayaran Santri')

@section('content')
<style>
    /* Styling Khusus Halaman Panduan */
    .guide-banner {
        background: linear-gradient(135deg, #022c22 0%, #064e3b 45%, #065f46 75%, #047857 100%);
        border-radius: 1.25rem;
        padding: 2.25rem 2rem;
        color: #ffffff;
        position: relative;
        overflow: hidden;
        box-shadow: 0 10px 30px -5px rgba(6, 78, 59, 0.25);
    }
    .guide-banner-orb {
        position: absolute;
        width: 300px;
        height: 300px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(52, 211, 153, 0.25) 0%, rgba(6, 95, 70, 0) 70%);
        top: -100px;
        right: -50px;
        pointer-events: none;
    }
    .guide-tab-nav .nav-link {
        color: #475569;
        font-weight: 600;
        padding: 0.85rem 1.25rem;
        border: none;
        border-radius: 0.75rem;
        background: #f1f5f9;
        transition: all 0.25s ease;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.9rem;
    }
    .guide-tab-nav .nav-link:hover {
        background: #e2e8f0;
        color: #0f172a;
    }
    .guide-tab-nav .nav-link.active {
        background: #15803d;
        color: #ffffff;
        box-shadow: 0 4px 15px rgba(21, 128, 61, 0.25);
    }

    /* Step Card Visual */
    .guide-step-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 1rem;
        padding: 1.5rem;
        height: 100%;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        position: relative;
    }
    .guide-step-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.08);
        border-color: #86efac;
    }
    .step-number-badge {
        width: 38px;
        height: 38px;
        border-radius: 50%;
        background: linear-gradient(135deg, #15803d, #22c55e);
        color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 1.1rem;
        margin-bottom: 1rem;
        box-shadow: 0 4px 10px rgba(34, 197, 94, 0.3);
    }

    /* Bank Logo Pemicu */
    .bank-brand-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        padding: 0.4rem 0.85rem;
        border-radius: 999px;
        background: #f8fafc;
        border: 1px solid #cbd5e1;
        font-weight: 700;
        font-size: 0.8rem;
    }

    /* Visual Simulated Phone Mockup */
    .phone-mockup-box {
        background: #0f172a;
        border-radius: 1.5rem;
        padding: 1.25rem;
        color: #ffffff;
        max-width: 320px;
        margin: 0 auto;
        border: 4px solid #334155;
        box-shadow: 0 15px 35px -5px rgba(15, 23, 42, 0.3);
    }

    .qris-badge-tag {
        background: #e11d48;
        color: #ffffff;
        font-weight: 800;
        padding: 0.2rem 0.6rem;
        border-radius: 0.35rem;
        letter-spacing: 1px;
        font-size: 0.75rem;
    }

    /* Responsif: Tablet & HP */
    @media (max-width: 767.98px) {
        .guide-banner { padding: 1.4rem 1.15rem; border-radius: 1rem; }
        .guide-banner h3 { font-size: 1.2rem; }
        .guide-banner-orb { width: 200px; height: 200px; top: -70px; right: -70px; }
        .guide-tab-nav { gap: 0.5rem !important; }
        .guide-tab-nav .nav-item { flex: 1 1 100%; }
        .guide-tab-nav .nav-link { padding: 0.7rem 0.9rem; font-size: 0.85rem; justify-content: flex-start !important; text-align: left; }
        .guide-step-card { padding: 1.1rem; }
        .phone-mockup-box { max-width: 100%; padding: 1rem; }
    }
    @media (hover: none) {
        .guide-step-card:hover { transform: none; }
    }
</style>

{{-- ============================================================ --}}
{{-- 1. HERO BANNER PANDUAN                                       --}}
{{-- ============================================================ --}}
<div class="guide-banner mb-4">
    <div class="guide-banner-orb"></div>
    <div class="row align-items-center">
        <div class="col-lg-8">
            <div class="badge bg-white text-success fw-bold px-3 py-2 rounded-pill mb-3 shadow-sm">
                <i class="bi bi-shield-check me-1"></i> Sistem Pembayaran Terverifikasi Otomatis
            </div>
            <h3 class="fw-bold mb-2">Panduan Lengkap Pembayaran Santri</h3>
            <p class="mb-3 text-white-50" style="font-size: 0.95rem; line-height: 1.6;">
                Pelajari kemudahan melunasi tagihan pendidikan ananda secara online menggunakan <strong>QRIS</strong> dan <strong>Virtual Account Bank</strong>, maupun pembayaran tunai langsung di kantor bendahara pondok.
            </p>
            <div class="d-flex flex-wrap gap-2">
                <span class="badge bg-white shadow-sm px-3 py-2 rounded-pill fw-semibold d-inline-flex align-items-center gap-1" style="color: #064e3b !important; font-size: 0.82rem;">
                    <i class="bi bi-lightning-charge-fill text-warning fs-6"></i>
                    <span>Langsung Lunas (1-3 Detik)</span>
                </span>
                <span class="badge bg-white shadow-sm px-3 py-2 rounded-pill fw-semibold d-inline-flex align-items-center gap-1" style="color: #064e3b !important; font-size: 0.82rem;">
                    <i class="bi bi-check-circle-fill text-success fs-6"></i>
                    <span>Tanpa Kirim Bukti Transfer</span>
                </span>
                <span class="badge bg-white shadow-sm px-3 py-2 rounded-pill fw-semibold d-inline-flex align-items-center gap-1" style="color: #064e3b !important; font-size: 0.82rem;">
                    <i class="bi bi-whatsapp text-success fs-6"></i>
                    <span>Kwitansi Otomatis via WhatsApp</span>
                </span>
            </div>
        </div>
        <div class="col-lg-4 text-center mt-3 mt-lg-0 d-none d-lg-block">
            <div class="p-3 bg-white bg-opacity-10 rounded-4 border border-white border-opacity-15 d-inline-block text-center">
                <div class="fs-1 text-warning mb-1">🧕</div>
                <div class="fw-bold text-white small">Kang Santri Siap Memandu</div>
                <div class="text-white-50 small" style="font-size: 0.75rem;">Klik tombol asisten di pojok kanan bawah</div>
            </div>
        </div>
    </div>
</div>

{{-- ============================================================ --}}
{{-- 2. TAB NAVIGASI METODE                                       --}}
{{-- ============================================================ --}}
<ul class="nav guide-tab-nav gap-2 mb-4 flex-wrap" id="guideTabs" role="tablist">
    <li class="nav-item flex-grow-1" role="presentation">
        <button class="nav-link w-100 justify-content-center active" id="tab-qris-btn" data-bs-toggle="tab" data-bs-target="#tab-qris" type="button" role="tab">
            <i class="bi bi-qr-code-scan fs-5 text-warning"></i>
            <span>1. Pembayaran QRIS (Semua E-Wallet)</span>
        </button>
    </li>
    <li class="nav-item flex-grow-1" role="presentation">
        <button class="nav-link w-100 justify-content-center" id="tab-va-btn" data-bs-toggle="tab" data-bs-target="#tab-va" type="button" role="tab">
            <i class="bi bi-bank fs-5 text-primary"></i>
            <span>2. Transfer Virtual Account Bank</span>
        </button>
    </li>
    <li class="nav-item flex-grow-1" role="presentation">
        <button class="nav-link w-100 justify-content-center" id="tab-tunai-btn" data-bs-toggle="tab" data-bs-target="#tab-tunai" type="button" role="tab">
            <i class="bi bi-cash-stack fs-5 text-success"></i>
            <span>3. Pembayaran Tunai (Kantor Kasir)</span>
        </button>
    </li>
    <li class="nav-item flex-grow-1" role="presentation">
        <button class="nav-link w-100 justify-content-center" id="tab-faq-btn" data-bs-toggle="tab" data-bs-target="#tab-faq" type="button" role="tab">
            <i class="bi bi-question-circle fs-5 text-info"></i>
            <span>4. Aturan Cicilan &amp; FAQ</span>
        </button>
    </li>
</ul>

{{-- ============================================================ --}}
{{-- 3. KONTEN TAB                                                --}}
{{-- ============================================================ --}}
<div class="tab-content" id="guideTabsContent">

    {{-- ======================================================== --}}
    {{-- TAB 1: PANDUAN QRIS                                       --}}
    {{-- ======================================================== --}}
    <div class="tab-pane fade show active" id="tab-qris" role="tabpanel">
        
        <div class="alert alert-success border-0 shadow-sm rounded-4 mb-4 d-flex align-items-center gap-3 p-3">
            <div class="fs-2 text-success"><i class="bi bi-info-circle-fill"></i></div>
            <div>
                <h6 class="fw-bold mb-1">Mengapa Memilih QRIS?</h6>
                <div class="small text-muted">
                    QRIS adalah metode pembayaran <strong>paling praktis dan instan</strong>. Cukup buka aplikasi perbankan atau e-wallet apa saja yang ada di HP Anda, pindai (*scan*) kode QR, dan tagihan lunas detik itu juga!
                </div>
            </div>
        </div>

        {{-- Logo Aplikasi Yang Didukung --}}
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-3 p-md-4">
                <div class="small fw-bold text-muted text-uppercase mb-3 text-center">
                    Dapat Dibayar Menggunakan Seluruh Aplikasi Perbankan &amp; Dompet Digital Nasional:
                </div>
                <div class="d-flex flex-wrap justify-content-center align-items-center gap-2">
                    <span class="bank-brand-badge text-primary"><i class="bi bi-phone"></i> BCA Mobile</span>
                    <span class="bank-brand-badge text-warning-emphasis"><i class="bi bi-phone"></i> Livin' Mandiri</span>
                    <span class="bank-brand-badge text-info"><i class="bi bi-phone"></i> BRImo (BRI)</span>
                    <span class="bank-brand-badge text-danger"><i class="bi bi-phone"></i> BNI Mobile</span>
                    <span class="bank-brand-badge text-primary"><i class="bi bi-wallet2"></i> Dana</span>
                    <span class="bank-brand-badge text-success"><i class="bi bi-wallet2"></i> GoPay</span>
                    <span class="bank-brand-badge text-danger"><i class="bi bi-wallet2"></i> ShopeePay</span>
                    <span class="bank-brand-badge text-primary"><i class="bi bi-wallet2"></i> OVO</span>
                    <span class="bank-brand-badge text-danger"><i class="bi bi-wallet2"></i> LinkAja</span>
                </div>
            </div>
        </div>

        {{-- 4 Langkah Bergambar Visual --}}
        <h5 class="fw-bold mb-3 text-dark"><i class="bi bi-123 me-2 text-success"></i>Langkah-Langkah Membayar via QRIS:</h5>
        <div class="row g-3 mb-4">
            
            <div class="col-md-6 col-lg-3">
                <div class="guide-step-card">
                    <div class="step-number-badge">1</div>
                    <h6 class="fw-bold text-dark mb-2">Klik Bayar Sekarang</h6>
                    <p class="small text-muted mb-0">
                        Buka menu <strong>Dashboard Tagihan</strong> atau klik nama ananda, lalu klik tombol hijau <strong>"Bayar Sekarang"</strong> pada tagihan yang ingin dibayar.
                    </p>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="guide-step-card">
                    <div class="step-number-badge">2</div>
                    <h6 class="fw-bold text-dark mb-2">Pilih Metode QRIS</h6>
                    <p class="small text-muted mb-0">
                        Tentukan nominal yang ingin Anda bayar (Lunas / Cicilan), lalu pilih opsi <strong>QRIS (Gopay / QRIS Lainnya)</strong> pada jendela pop-up Midtrans.
                    </p>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="guide-step-card">
                    <div class="step-number-badge">3</div>
                    <h6 class="fw-bold text-dark mb-2">Pindai (Scan) Kode QR</h6>
                    <p class="small text-muted mb-0">
                        Buka aplikasi bank/e-wallet di HP Anda (BCA, Mandiri, Dana, dll), pilih menu <strong>Scan QR</strong>, lalu arahkan kamera ke kode QR yang muncul.
                    </p>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="guide-step-card">
                    <div class="step-number-badge">4</div>
                    <h6 class="fw-bold text-dark mb-2">Lunas Otomatis!</h6>
                    <p class="small text-muted mb-0">
                        Masukkan PIN transaksi Anda. Pembayaran seketika berhasil! Kwitansi resmi sah langsung terbit dan dikirimkan via WhatsApp.
                    </p>
                </div>
            </div>

        </div>

        {{-- Tips Bayar Pakai 1 HP --}}
        <div class="card border-0 bg-light rounded-4 p-4 mb-3">
            <div class="d-flex align-items-start gap-3">
                <div class="fs-1 text-warning"><i class="bi bi-lightbulb-fill"></i></div>
                <div>
                    <h6 class="fw-bold text-dark mb-1">Tips Jika Anda Mengakses Web dari HP yang Sama:</h6>
                    <p class="small text-muted mb-2">
                        Jika Anda membuka web portal ini langsung dari HP (bukan laptop), Anda tidak perlu menggunakan kamera HP lain! Cukup lakukan 2 langkah mudah ini:
                    </p>
                    <ol class="small text-muted ps-3 mb-0">
                        <li>Ambil tangkapan layar (*screenshot*) kode QR yang tampil di layar HP Anda.</li>
                        <li>Buka aplikasi m-banking / e-wallet Anda, pilih menu Scan QR, lalu klik ikon <strong>"Pilih dari Galeri / Foto"</strong> untuk mengunggah gambar screenshot tadi. Selesai!</li>
                    </ol>
                </div>
            </div>
        </div>

    </div>

    {{-- ======================================================== --}}
    {{-- TAB 2: PANDUAN VIRTUAL ACCOUNT BANK                      --}}
    {{-- ======================================================== --}}
    <div class="tab-pane fade" id="tab-va" role="tabpanel">
        
        <div class="alert alert-primary border-0 shadow-sm rounded-4 mb-4 d-flex align-items-center gap-3 p-3">
            <div class="fs-2 text-primary"><i class="bi bi-bank2"></i></div>
            <div>
                <h6 class="fw-bold mb-1">Pembayaran via Virtual Account (VA)</h6>
                <div class="small text-muted">
                    Setiap transaksi akan menghasilkan <strong>Nomor Virtual Account Unik</strong>. Anda cukup melakukan transfer ke nomor tersebut, dan sistem bank akan memvalidasi secara otomatis tanpa perlu melampirkan bukti transfer.
                </div>
            </div>
        </div>

        <h5 class="fw-bold mb-3 text-dark"><i class="bi bi-journal-bookmark me-2 text-primary"></i>Panduan Transfer per Bank Pilihan:</h5>

        <div class="accordion mb-4" id="accordionBank">
            
            {{-- Bank BCA --}}
            <div class="accordion-item border rounded-3 mb-2 overflow-hidden shadow-sm">
                <h2 class="accordion-header" id="headingBCA">
                    <button class="accordion-button collapsed fw-bold text-dark" type="button" data-bs-toggle="collapse" data-bs-target="#collapseBCA">
                        <span class="badge bg-primary text-white me-2">BCA</span> Bank Central Asia (BCA Virtual Account)
                    </button>
                </h2>
                <div id="collapseBCA" class="accordion-collapse collapse" data-bs-parent="#accordionBank">
                    <div class="accordion-body bg-light">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <h6 class="fw-bold small text-dark"><i class="bi bi-phone me-1"></i>Via BCA Mobile (m-BCA):</h6>
                                <ol class="small text-muted ps-3 mb-0">
                                    <li>Buka aplikasi m-BCA dan masukkan Kode Akses.</li>
                                    <li>Pilih menu <strong>m-Transfer</strong> &gt; <strong>BCA Virtual Account</strong>.</li>
                                    <li>Masukkan Nomor Virtual Account yang tertera di sistem.</li>
                                    <li>Periksa rincian nama santri dan nominal, lalu klik <strong>Send</strong>.</li>
                                    <li>Masukkan PIN m-BCA Anda. Transaksi selesai.</li>
                                </ol>
                            </div>
                            <div class="col-md-6">
                                <h6 class="fw-bold small text-dark"><i class="bi bi-credit-card me-1"></i>Via Mesin ATM BCA:</h6>
                                <ol class="small text-muted ps-3 mb-0">
                                    <li>Masukkan Kartu ATM BCA dan PIN Anda.</li>
                                    <li>Pilih <strong>Transaksi Lainnya</strong> &gt; <strong>Transfer</strong> &gt; <strong>ke Rek BCA Virtual Account</strong>.</li>
                                    <li>Masukkan Nomor BCA Virtual Account, lalu pilih <strong>Benar</strong>.</li>
                                    <li>Konfirmasi rincian pembayaran, lalu pilih <strong>Ya</strong>.</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Bank Mandiri --}}
            <div class="accordion-item border rounded-3 mb-2 overflow-hidden shadow-sm">
                <h2 class="accordion-header" id="headingMandiri">
                    <button class="accordion-button collapsed fw-bold text-dark" type="button" data-bs-toggle="collapse" data-bs-target="#collapseMandiri">
                        <span class="badge bg-warning text-dark me-2">Mandiri</span> Bank Mandiri (Mandiri Bill Payment)
                    </button>
                </h2>
                <div id="collapseMandiri" class="accordion-collapse collapse" data-bs-parent="#accordionBank">
                    <div class="accordion-body bg-light">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <h6 class="fw-bold small text-dark"><i class="bi bi-phone me-1"></i>Via Livin' by Mandiri (Kuning):</h6>
                                <ol class="small text-muted ps-3 mb-0">
                                    <li>Login ke aplikasi Livin' by Mandiri.</li>
                                    <li>Pilih menu <strong>Bayar</strong> &gt; cari <strong>Midtrans</strong>.</li>
                                    <li>Masukkan Nomor Pembayaran / Virtual Account.</li>
                                    <li>Konfirmasi jumlah tagihan, lalu masukkan MPIN Livin Anda.</li>
                                </ol>
                            </div>
                            <div class="col-md-6">
                                <h6 class="fw-bold small text-dark"><i class="bi bi-credit-card me-1"></i>Via Mesin ATM Mandiri:</h6>
                                <ol class="small text-muted ps-3 mb-0">
                                    <li>Masukkan Kartu ATM dan PIN Anda.</li>
                                    <li>Pilih menu <strong>Bayar/Beli</strong> &gt; <strong>Lainnya</strong> &gt; <strong>Multipayment</strong>.</li>
                                    <li>Masukkan Kode Perusahaan Midtrans (<strong>70012</strong>).</li>
                                    <li>Masukkan Nomor Virtual Account Anda dan konfirmasi bayar.</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Bank BRI --}}
            <div class="accordion-item border rounded-3 mb-2 overflow-hidden shadow-sm">
                <h2 class="accordion-header" id="headingBRI">
                    <button class="accordion-button collapsed fw-bold text-dark" type="button" data-bs-toggle="collapse" data-bs-target="#collapseBRI">
                        <span class="badge bg-info text-white me-2">BRI</span> Bank Rakyat Indonesia (BRIVA)
                    </button>
                </h2>
                <div id="collapseBRI" class="accordion-collapse collapse" data-bs-parent="#accordionBank">
                    <div class="accordion-body bg-light">
                        <h6 class="fw-bold small text-dark"><i class="bi bi-phone me-1"></i>Via BRImo:</h6>
                        <ol class="small text-muted ps-3 mb-0">
                            <li>Buka aplikasi BRImo dan Login.</li>
                            <li>Pilih menu <strong>Tagihan</strong> &gt; <strong>BRIVA</strong>.</li>
                            <li>Masukkan Nomor Virtual Account BRI yang didapatkan.</li>
                            <li>Periksa rincian santri dan nominal, masukkan PIN BRImo. Transaksi selesai.</li>
                        </ol>
                    </div>
                </div>
            </div>

            {{-- Bank BNI --}}
            <div class="accordion-item border rounded-3 mb-2 overflow-hidden shadow-sm">
                <h2 class="accordion-header" id="headingBNI">
                    <button class="accordion-button collapsed fw-bold text-dark" type="button" data-bs-toggle="collapse" data-bs-target="#collapseBNI">
                        <span class="badge bg-danger text-white me-2">BNI</span> Bank Negara Indonesia (BNI Virtual Account)
                    </button>
                </h2>
                <div id="collapseBNI" class="accordion-collapse collapse" data-bs-parent="#accordionBank">
                    <div class="accordion-body bg-light">
                        <h6 class="fw-bold small text-dark"><i class="bi bi-phone me-1"></i>Via BNI Mobile Banking:</h6>
                        <ol class="small text-muted ps-3 mb-0">
                            <li>Buka BNI Mobile Banking dan Login.</li>
                            <li>Pilih menu <strong>Pembayaran</strong> &gt; <strong>Virtual Account Billing</strong>.</li>
                            <li>Masukkan Nomor Virtual Account BNI Anda.</li>
                            <li>Konfirmasi nominal dan masukkan Password Transaksi Anda.</li>
                        </ol>
                    </div>
                </div>
            </div>

            {{-- Bank Lainnya --}}
            <div class="accordion-item border rounded-3 mb-2 overflow-hidden shadow-sm">
                <h2 class="accordion-header" id="headingLain">
                    <button class="accordion-button collapsed fw-bold text-dark" type="button" data-bs-toggle="collapse" data-bs-target="#collapseLain">
                        <span class="badge bg-secondary text-white me-2">Lainnya</span> Bank Syariah Indonesia (BSI), Bank Muamalat, Bank Daerah, dll.
                    </button>
                </h2>
                <div id="collapseLain" class="accordion-collapse collapse" data-bs-parent="#accordionBank">
                    <div class="accordion-body bg-light">
                        <p class="small text-muted mb-2">
                            Jika bank Anda belum terdaftar di atas, pilih opsi <strong>Permata Bank / Bank Lainnya</strong> pada jendela Midtrans.
                        </p>
                        <ol class="small text-muted ps-3 mb-0">
                            <li>Gunakan menu <strong>Transfer Antar Bank</strong> di m-banking Anda.</li>
                            <li>Pilih bank tujuan (sesuai yang tertera di layar).</li>
                            <li>Masukkan nomor rekening tujuan dengan Nomor Virtual Account tersebut.</li>
                            <li>Masukkan nominal persis sesuai tagihan, lalu selesaikan transaksi.</li>
                        </ol>
                    </div>
                </div>
            </div>

        </div>

    </div>

    {{-- ======================================================== --}}
    {{-- TAB 3: PEMBAYARAN TUNAI DI KANTOR KASIR                  --}}
    {{-- ======================================================== --}}
    <div class="tab-pane fade" id="tab-tunai" role="tabpanel">
        
        <div class="card border-0 shadow-sm rounded-4 p-4 mb-4" style="border-left: 5px solid #16a34a !important;">
            <div class="d-flex align-items-center gap-3 mb-3">
                <div class="bg-success text-white rounded-circle p-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                    <i class="bi bi-shop fs-4"></i>
                </div>
                <div>
                    <h5 class="fw-bold mb-1">Loket Kasir Keuangan Pesantren</h5>
                    <div class="small text-muted">Kantor Administrasi &amp; Keuangan Pondok Pesantren Darul Muttaqin Al Islami</div>
                </div>
            </div>
            <p class="text-muted small mb-0" style="line-height: 1.6;">
                Bagi Ayah/Bunda yang sedang berkunjung ke pondok pesantren atau lebih nyaman membayar secara tunai, Anda tetap dapat melakukan pembayaran langsung di loket kasir bendahara.
            </p>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-4 p-3 h-100 bg-white">
                    <div class="fs-2 text-primary mb-2"><i class="bi bi-clock-history"></i></div>
                    <h6 class="fw-bold text-dark">Jadwal Operasional Kasir</h6>
                    <div class="small text-muted">
                        Senin s/d Sabtu: <strong>08.00 – 15.00 WIB</strong><br>
                        Hari Ahad / Libur Nasional: <strong>Tutup</strong>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-4 p-3 h-100 bg-white">
                    <div class="fs-2 text-success mb-2"><i class="bi bi-person-vcard"></i></div>
                    <h6 class="fw-bold text-dark">Syarat Pembayaran</h6>
                    <div class="small text-muted">
                        Cukup sebutkan <strong>NIS (Nomor Induk Santri)</strong> atau nama lengkap ananda beserta tingkatan kelasnya kepada petugas kasir.
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-4 p-3 h-100 bg-white">
                    <div class="fs-2 text-warning mb-2"><i class="bi bi-receipt"></i></div>
                    <h6 class="fw-bold text-dark">Bukti Kwitansi Ganda</h6>
                    <div class="small text-muted">
                        Anda akan menerima <strong>Kwitansi Fisik Berstempel</strong> langsung di tempat, sekaligus notifikasi kwitansi sah PDF yang terkirim ke WhatsApp Anda.
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- ======================================================== --}}
    {{-- TAB 4: ATURAN CICILAN & FAQ                              --}}
    {{-- ======================================================== --}}
    <div class="tab-pane fade" id="tab-faq" role="tabpanel">
        
        <div class="row g-3 mb-4">
            
            <div class="col-md-6">
                <div class="card border-0 shadow-sm rounded-4 p-4 h-100 bg-white">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <i class="bi bi-pie-chart-fill text-success fs-4"></i>
                        <h6 class="fw-bold mb-0 text-dark">Kebijakan Cicilan Tagihan</h6>
                    </div>
                    <ul class="small text-muted ps-3 mb-0" style="line-height: 1.7;">
                        <li>Pesantren memberikan kemudahan mencicil tagihan pendidikan sesuai kesiapan dana wali murid.</li>
                        <li>Untuk mencicil, cukup masukkan nominal uang yang ingin dibayarkan pada kotak isian pembayaran saat checkout.</li>
                        <li>Sistem otomatis menghitung sisa tagihan yang belum terbayar tanpa denda atau bunga apa pun.</li>
                        <li>Kwitansi resmi terbit setiap kali Anda melakukan pembayaran cicilan.</li>
                    </ul>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card border-0 shadow-sm rounded-4 p-4 h-100 bg-white">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <i class="bi bi-calendar-check-fill text-primary fs-4"></i>
                        <h6 class="fw-bold mb-0 text-dark">Jatuh Tempo &amp; Status Kepatuhan</h6>
                    </div>
                    <ul class="small text-muted ps-3 mb-0" style="line-height: 1.7;">
                        <li>Tagihan Syahriah (SPP Bulanan) jatuh tempo pada tanggal yang tertera pada kartu tagihan ananda.</li>
                        <li>Status santri akan tercatat <strong>"Tertib"</strong> apabila seluruh tagihan telah lunas atau belum melewati batas jatuh tempo.</li>
                        <li>Status <strong>"Menunggak"</strong> hanya akan muncul jika terdapat tagihan yang telah melewati tanggal jatuh tempo dan belum dilunasi.</li>
                    </ul>
                </div>
            </div>

        </div>

        {{-- Kartu Kontak Bantuan Darurat --}}
        <div class="card border-0 shadow-sm rounded-4 p-4 bg-white text-center">
            <h6 class="fw-bold text-dark mb-1">Masih Mengalami Kendala atau Butuh Bantuan?</h6>
            <p class="small text-muted mb-3">Tim Layanan Administrasi &amp; Keuangan Pesantren siap membantu Ayah/Bunda dengan senang hati.</p>
            <div>
                <a href="https://wa.me/62881024264010?text={{ urlencode('Assalamu\'alaikum Bendahara Pesantren, saya wali santri ingin berkonsultasi mengenai pembayaran tagihan.') }}" target="_blank" class="btn btn-success fw-semibold px-4 py-2 shadow-sm rounded-pill">
                    <i class="bi bi-whatsapp me-2"></i>Hubungi WhatsApp Layanan Keuangan
                </a>
            </div>
        </div>

    </div>

</div>

@endsection

