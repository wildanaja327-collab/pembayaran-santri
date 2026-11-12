<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Sistem Informasi Pembayaran Santri') - PP Darul Muttaqin</title>
    <link rel="icon" type="image/jpeg" href="{{ asset('img/logo.jpg') }}">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 CSS & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <style>
        :root {
            --primary: #15803d;
            --primary-hover: #166534;
            --primary-light: #f0fdf4;
            --secondary: #0f172a;
            --sidebar-width: 260px;
        }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f8fafc;
            color: #1e293b;
        }
        .sidebar {
            width: var(--sidebar-width);
            min-height: 100vh;
            background: #ffffff;
            border-right: 1px solid #e2e8f0;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .sidebar-brand {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        .sidebar-nav {
            padding: 1rem 0.75rem;
            overflow-y: auto;
            max-height: calc(100vh - 145px);
        }
        .sidebar-nav .nav-link {
            color: #64748b;
            font-weight: 500;
            padding: 0.75rem 1rem;
            border-radius: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 0.25rem;
            transition: all 0.2s;
        }
        .sidebar-nav .nav-link:hover {
            color: var(--primary);
            background: var(--primary-light);
        }
        .sidebar-nav .nav-link.active {
            color: #ffffff;
            background: var(--primary);
            font-weight: 600;
        }
        .card-santri-hover {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            border-left-width: 4px !important;
        }
        .card-santri-hover:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 24px rgba(0,0,0,0.1) !important;
        }
        .sidebar-heading {
            font-size: 0.75rem;
            text-transform: uppercase;
            font-weight: 700;
            color: #94a3b8;
            padding: 0.75rem 1rem 0.25rem;
            letter-spacing: 0.05em;
        }
        .main-content {
            margin-left: var(--sidebar-width);
            padding: 1.5rem 2rem;
            min-height: 100vh;
            transition: margin-left 0.3s ease;
            min-width: 0;
            overflow-x: hidden;
        }
        .topbar {
            background: #ffffff;
            border-radius: 0.75rem;
            padding: 0.75rem 1.25rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.5rem;
            gap: 0.75rem;
            flex-wrap: wrap;
        }

        /* ========================================================
           RESPONSIVE BREAKPOINTS (LAPTOP, TABLET, HP)
           ======================================================== */
        @media (max-width: 991.98px) {
            .sidebar {
                transform: translateX(-100%);
                z-index: 1060;
                box-shadow: 0 0 35px rgba(0, 0, 0, 0.2);
            }
            .sidebar.show {
                transform: translateX(0);
            }
            .main-content {
                margin-left: 0 !important;
                padding: 1rem 0.85rem !important;
            }
            #sidebar-backdrop {
                position: fixed;
                inset: 0;
                background: rgba(15, 23, 42, 0.55);
                backdrop-filter: blur(2px);
                z-index: 1055;
                opacity: 0;
                visibility: hidden;
                transition: opacity 0.3s ease, visibility 0.3s ease;
            }
            #sidebar-backdrop.show {
                opacity: 1;
                visibility: visible;
            }
        }

        @media (max-width: 767.98px) {
            .topbar {
                flex-direction: column;
                align-items: stretch !important;
                gap: 0.6rem;
                padding: 0.75rem 1rem;
            }
            .topbar-right-box {
                justify-content: space-between;
                border-top: 1px dashed #e2e8f0;
                padding-top: 0.5rem;
            }
            .card-stat {
                padding: 1rem !important;
            }
        }

        @media (max-width: 575.98px) {
            .main-content {
                padding: 0.6rem 0.5rem !important;
            }
            .topbar {
                border-radius: 0.5rem;
                margin-bottom: 0.85rem;
            }
            #topbar-page-title {
                font-size: 1.05rem;
            }
            .card {
                border-radius: 0.65rem !important;
            }
            .card-body {
                padding: 0.9rem !important;
            }
            .card-header {
                padding: 0.75rem 0.9rem !important;
            }
            .table td, .table th {
                padding: 0.6rem 0.45rem !important;
                font-size: 0.82rem;
            }
            .btn-sm {
                padding: 0.3rem 0.5rem !important;
                font-size: 0.78rem !important;
            }
            .badge {
                font-size: 0.72rem !important;
            }
        }

        /* Responsive Table Scroll Styling */
        .table-responsive {
            -webkit-overflow-scrolling: touch;
            scrollbar-width: thin;
        }
        .table-responsive::-webkit-scrollbar {
            height: 5px;
        }
        .table-responsive::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }
        .card-stat {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 0.75rem;
            padding: 1.25rem;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .card-stat:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.08);
        }
        .btn-primary {
            background-color: var(--primary);
            border-color: var(--primary);
        }
        .btn-primary:hover {
            background-color: var(--primary-hover);
            border-color: var(--primary-hover);
        }
        .table-responsive {
            border: 1px solid #e2e8f0;
            border-radius: 0.75rem;
            /* PENTING: overflow-x harus auto agar tabel lebar bisa digeser di HP/tablet (bukan terpotong) */
            overflow-x: auto;
            overflow-y: hidden;
            max-width: 100%;
            /* Bayangan tepi otomatis sebagai petunjuk "masih bisa digeser" */
            background:
                linear-gradient(to right, #fff 30%, rgba(255,255,255,0)) left center,
                linear-gradient(to left, #fff 30%, rgba(255,255,255,0)) right center,
                radial-gradient(farthest-side at 0 50%, rgba(15,23,42,.14), rgba(15,23,42,0)) left center,
                radial-gradient(farthest-side at 100% 50%, rgba(15,23,42,.14), rgba(15,23,42,0)) right center;
            background-color: #ffffff;
            background-repeat: no-repeat;
            background-size: 36px 100%, 36px 100%, 12px 100%, 12px 100%;
            background-attachment: local, local, scroll, scroll;
        }
        .table > :not(caption) > * > * {
            padding: 0.85rem 1rem;
        }
        .badge-smp {
            background-color: #0284c7;
            color: white;
        }
        .badge-sma {
            background-color: #6366f1;
            color: white;
        }

        /* ========================================================
           SEAMLESS SMOOTH PAGE TRANSITIONS (TRUE SPA SWAPPING)
           ======================================================== */
        /* Top Loading Bar */
        #app-loading-bar {
            position: fixed;
            top: 0;
            left: 0;
            width: 0%;
            height: 3px;
            background: linear-gradient(90deg, #15803d 0%, #22c55e 50%, #4ade80 100%);
            box-shadow: 0 0 10px rgba(34, 197, 94, 0.6);
            z-index: 99999;
            pointer-events: none;
            opacity: 0;
            transition: width 0.3s ease, opacity 0.2s ease;
        }

        #app-loading-bar.loading {
            opacity: 1;
            width: 75%;
            transition: width 0.4s cubic-bezier(0.1, 0.5, 0.1, 1), opacity 0.1s ease;
        }

        #app-loading-bar.finishing {
            opacity: 1;
            width: 100%;
            transition: width 0.15s ease, opacity 0.25s ease 0.15s;
        }

        /* Transisi Konten Halaman: Halus, Mulus, & Responsif */
        #app-page-wrapper {
            transition: opacity 0.18s cubic-bezier(0.16, 1, 0.3, 1), transform 0.18s cubic-bezier(0.16, 1, 0.3, 1);
            will-change: opacity, transform;
        }

        #app-page-wrapper.is-leaving {
            opacity: 0;
            transform: translateY(-6px);
            transition: opacity 0.12s ease, transform 0.12s ease;
        }

        #app-page-wrapper.is-entering {
            animation: smoothContentFadeIn 0.24s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        @keyframes smoothContentFadeIn {
            0% {
                opacity: 0;
                transform: translateY(8px);
            }
            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Micro-interactions: Sidebar Nav links & Cards */
        .sidebar-nav .nav-link {
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
        }
        .sidebar-nav .nav-link:active {
            transform: scale(0.98);
        }
        .sidebar-nav .nav-link.active {
            box-shadow: 0 4px 12px rgba(21, 128, 61, 0.22);
        }
        .card {
            transition: transform 0.22s ease, box-shadow 0.22s ease;
        }

        /* Pastikan Bootstrap Modals selalu berada di atas backdrop */
        .modal {
            z-index: 1065 !important;
        }
        .modal-backdrop {
            z-index: 1055 !important;
        }

        /* ========================================================
           LAPISAN RESPONSIF GLOBAL (HP, TABLET, LAPTOP)
           Berlaku untuk seluruh halaman Admin, Bendahara & Wali
           ======================================================== */
        html { -webkit-text-size-adjust: 100%; }
        .min-w-0 { min-width: 0 !important; }
        .text-truncate-2 { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
        img, svg, video, canvas, iframe { max-width: 100%; }
        img { height: auto; }
        .main-content { overflow-wrap: anywhere; }
        .main-content pre, .main-content code { white-space: pre-wrap; word-break: break-word; }

        /* Tinggi sidebar mengikuti layar HP (address bar browser tidak memotong menu bawah) */
        .sidebar {
            height: 100vh;
            height: 100dvh;
            min-height: 0;
            overflow: hidden;
        }
        .sidebar-nav {
            flex: 1 1 auto;
            min-height: 0;
            max-height: none;
            overflow-y: auto;
            overscroll-behavior: contain;
        }

        /* Menu dropdown tidak melebihi lebar layar */
        .dropdown-menu { max-width: calc(100vw - 1.5rem); }

        /* Baris "label ... nilai": nilai panjang tidak mendorong keluar layar */
        .d-flex.justify-content-between > * { min-width: 0; }
        .list-group-item.d-flex { gap: 0.25rem 0.5rem; }

        /* Sticky kolom pertama? Tidak. Tapi judul tabel jangan patah di layar kecil */
        @media (max-width: 991.98px) {
            .table th { white-space: nowrap; }
            .table > :not(caption) > * > * { padding: 0.7rem 0.75rem; }
            /* Tombol aksi di header kartu turun ke bawah rapi */
            .card-header.d-flex { flex-wrap: wrap; row-gap: 0.5rem; }
            .btn-group.shadow-sm { flex-wrap: nowrap; }
            /* Form filter: kolom otomatis penuh bila sempit */
            .row.g-2 > [class*="col-md-"], .row.g-3 > [class*="col-md-"] { min-width: 0; }
        }

        @media (max-width: 767.98px) {
            /* Grup tombol di header (Export/Cetak, dll) memenuhi lebar agar mudah ditekan */
            .card-header .d-flex.flex-wrap.gap-2 > .btn,
            .card-header .d-flex.flex-wrap.gap-2 > .btn-group,
            .card-header .d-flex.gap-2 > .btn,
            .card-header .d-flex.gap-2 > a.btn { flex: 1 1 auto; text-align: center; }
            h1, .h1 { font-size: 1.6rem; }
            h2, .h2 { font-size: 1.4rem; }
            h3, .h3 { font-size: 1.25rem; }
            .display-5, .display-6 { font-size: 1.8rem; }
            /* Modal: hampir layar penuh & isi bisa di-scroll sehingga tombol Simpan selalu terjangkau */
            .modal-dialog { margin: 0.5rem; max-width: calc(100vw - 1rem); }
            .modal-dialog:not(.modal-fullscreen) .modal-content { max-height: calc(100dvh - 1rem); }
            .modal-body { overflow-y: auto; -webkit-overflow-scrolling: touch; }
            .modal-footer { flex-wrap: wrap; gap: 0.4rem; }
            .modal-footer > .btn { flex: 1 1 auto; margin: 0; }
            .modal-header { padding: 0.75rem 1rem; }
        }

        @media (max-width: 575.98px) {
            /* Hindari zoom otomatis iOS saat mengetik & perbesar area sentuh */
            .form-control, .form-select, .input-group-text { font-size: 16px; }
            .form-control-sm, .form-select-sm { font-size: 16px; min-height: 38px; }
            .btn { min-height: 38px; }
            .btn-sm { min-height: 34px; }
            .pagination { flex-wrap: wrap; justify-content: center; gap: 2px; }
            .pagination .page-link { padding: 0.35rem 0.6rem; }
            .alert { font-size: 0.88rem; }
            .toast-container, .position-fixed.bottom-0.end-0 { max-width: calc(100vw - 1rem); right: 0.5rem !important; margin: 0.5rem !important; }
            .nav-tabs, .nav-pills { flex-wrap: nowrap; overflow-x: auto; overflow-y: hidden; scrollbar-width: none; }
            .nav-tabs::-webkit-scrollbar, .nav-pills::-webkit-scrollbar { display: none; }
            .nav-tabs .nav-link, .nav-pills .nav-link { white-space: nowrap; }
            .dropdown-menu { font-size: 0.9rem; }
        }

        /* HP layar sangat kecil (<= 360px) */
        @media (max-width: 374.98px) {
            .topbar { padding: 0.6rem 0.7rem; }
            #topbar-page-title { font-size: 0.98rem; }
            .main-content { padding: 0.5rem 0.4rem !important; }
        }

        /* Layar sentuh: matikan efek hover-melayang agar tidak "lengket" */
        @media (hover: none) {
            .card-santri-hover:hover, .card-stat:hover { transform: none; }
        }

        /* Landscape HP: sidebar tidak makan tempat vertikal */
        @media (max-height: 480px) and (orientation: landscape) {
            .sidebar-brand { padding: 0.6rem 1rem; }
            .sidebar-nav .nav-link { padding: 0.5rem 0.9rem; }
        }
    </style>
    
    @php
        $dynamicMidtransMode = \App\Models\Pengaturan::get('midtrans_mode', config('services.midtrans.is_production') ? 'production' : 'sandbox');
        $dynamicClientKey = \App\Models\Pengaturan::get('midtrans_client_key') ?: config('services.midtrans.client_key');
        $snapScriptUrl = ($dynamicMidtransMode === 'production')
            ? 'https://app.midtrans.com/snap/snap.js'
            : 'https://app.sandbox.midtrans.com/snap/snap.js';
    @endphp
    <!-- Midtrans Snap Script -->
    <script 
        src="{{ $snapScriptUrl }}" 
        data-client-key="{{ $dynamicClientKey }}">
    </script>
    @stack('styles')
</head>
<body>
    <!-- Top Loading Indicator -->
    <div id="app-loading-bar"></div>

    <!-- Mobile Sidebar Backdrop Overlay -->
    <div id="sidebar-backdrop"></div>

    <!-- Sidebar -->
    <div class="sidebar d-flex flex-column" id="app-sidebar">
        <div class="sidebar-brand justify-content-between">
            <div class="d-flex align-items-center gap-2">
                <div class="d-flex align-items-center justify-content-center rounded-circle bg-white shadow-sm border" style="width: 40px; height: 40px; padding: 2px; flex-shrink: 0;">
                    <img src="{{ asset('img/logo.jpg') }}" alt="Logo PP Darul Muttaqin" class="rounded-circle" style="width: 100%; height: 100%; object-fit: contain;">
                </div>
                <div>
                    <div class="fw-bold lh-1 text-dark" style="font-size: 0.95rem;">PP Darul Muttaqin</div>
                    <small class="text-muted" style="font-size: 0.75rem;">SIM Keuangan Santri</small>
                </div>
            </div>
            <button type="button" class="btn btn-sm btn-light border-0 d-lg-none text-muted py-1 px-2" id="btn-sidebar-close" aria-label="Tutup Menu">
                <i class="bi bi-x-lg fs-6"></i>
            </button>
        </div>

        <div class="sidebar-nav flex-grow-1">
            @if(Auth::user()->isAdmin())
                <div class="sidebar-heading">Menu Administrator</div>
                <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <i class="bi bi-grid-1x2"></i> Dashboard Admin
                </a>
                <a href="{{ route('admin.activity-logs') }}" class="nav-link {{ request()->routeIs('admin.activity-logs') ? 'active' : '' }}">
                    <i class="bi bi-shield-check"></i> Log Aktivitas (Audit)
                </a>
                <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                    <i class="bi bi-person-gear"></i> Kelola Pengguna (Users)
                </a>
                <a href="{{ route('admin.tahun-ajaran.index') }}" class="nav-link {{ request()->routeIs('admin.tahun-ajaran.*') ? 'active' : '' }}">
                    <i class="bi bi-calendar-range"></i> Tahun Ajaran
                </a>
                <a href="{{ route('admin.santri.index') }}" class="nav-link {{ request()->routeIs('admin.santri.*') ? 'active' : '' }}">
                    <i class="bi bi-people"></i> Master Data Santri
                </a>
                <a href="{{ route('admin.program.index') }}" class="nav-link {{ request()->routeIs('admin.program.*') ? 'active' : '' }}">
                    <i class="bi bi-mortarboard"></i> Program Unggulan
                </a>
                <a href="{{ route('admin.midtrans.index') }}" class="nav-link {{ request()->routeIs('admin.midtrans.*') ? 'active' : '' }}">
                    <i class="bi bi-credit-card-2-front"></i> Gateway Midtrans
                </a>
                <a href="{{ route('admin.whatsapp.index') }}" class="nav-link {{ request()->routeIs('admin.whatsapp.*') ? 'active' : '' }}">
                    <i class="bi bi-whatsapp"></i> WhatsApp Gateway
                </a>
                <a href="{{ route('admin.pengaturan') }}" class="nav-link {{ request()->routeIs('admin.pengaturan*') ? 'active' : '' }}">
                    <i class="bi bi-sliders"></i> Konfigurasi Sistem
                </a>

            @elseif(Auth::user()->isBendahara())
                @php
                    $pendingCount = \App\Models\Pembayaran::where('status_transaksi', 'pending')->count();
                    $tunggakanCount = \App\Models\Tagihan::whereIn('status_tagihan', ['belum_bayar', 'dicicil'])->count();
                    $santriCount = \App\Models\Santri::where('status_aktif', 'aktif')->count();
                @endphp
                <div class="sidebar-heading">Menu Utama</div>
                <a href="{{ route('bendahara.dashboard') }}" class="nav-link {{ request()->routeIs('bendahara.dashboard') ? 'active' : '' }}">
                    <i class="bi bi-grid-1x2"></i> Dashboard Keuangan
                </a>
                <a href="{{ route('bendahara.santri.index') }}" class="nav-link {{ request()->routeIs('bendahara.santri.*') ? 'active' : '' }}">
                    <i class="bi bi-people"></i> Direktori Santri
                    <span class="badge bg-secondary ms-auto" style="font-size: 0.7rem;">{{ $santriCount }}</span>
                </a>
                
                <div class="sidebar-heading">Tagihan & Keuangan</div>
                <a href="{{ route('bendahara.jenis-tagihan.index') }}" class="nav-link {{ request()->routeIs('bendahara.jenis-tagihan.*') ? 'active' : '' }}">
                    <i class="bi bi-tags"></i> Master Jenis Tagihan
                </a>
                <a href="{{ route('bendahara.tagihan.index') }}" class="nav-link {{ request()->routeIs('bendahara.tagihan.index') || request()->routeIs('bendahara.tagihan.show') || request()->routeIs('bendahara.tagihan.create') ? 'active' : '' }}">
                    <i class="bi bi-receipt"></i> Data Tagihan Santri
                </a>
                <a href="{{ route('bendahara.tagihan.generate.show') }}" class="nav-link {{ request()->routeIs('bendahara.tagihan.generate.*') ? 'active' : '' }}">
                    <i class="bi bi-lightning-charge"></i> Generator Massal
                </a>
                <a href="{{ route('bendahara.pembayaran.index') }}" class="nav-link {{ request()->routeIs('bendahara.pembayaran.index') ? 'active' : '' }}">
                    <i class="bi bi-wallet2"></i> Transaksi Pembayaran
                    @if($pendingCount > 0)
                        <span class="badge bg-warning text-dark ms-auto" style="font-size: 0.7rem;">{{ $pendingCount }}</span>
                    @endif
                </a>
                <a href="{{ route('bendahara.pembayaran.rekonsiliasi') }}" class="nav-link {{ request()->routeIs('bendahara.pembayaran.rekonsiliasi') ? 'active' : '' }}">
                    <i class="bi bi-arrow-repeat"></i> Rekonsiliasi Gateway
                </a>
                <a href="{{ route('bendahara.pembayaran.kasir.create') }}" class="nav-link {{ request()->routeIs('bendahara.pembayaran.kasir.*') ? 'active' : '' }}">
                    <i class="bi bi-cash-stack"></i> Kasir Tunai Pesantren
                </a>

                <div class="sidebar-heading">Laporan & Rekapitulasi</div>
                <a href="{{ route('bendahara.laporan.pemasukan') }}" class="nav-link {{ request()->routeIs('bendahara.laporan.pemasukan*') ? 'active' : '' }}">
                    <i class="bi bi-file-earmark-bar-graph"></i> Laporan Pemasukan
                </a>
                <a href="{{ route('bendahara.laporan.tunggakan') }}" class="nav-link {{ request()->routeIs('bendahara.laporan.tunggakan*') ? 'active' : '' }}">
                    <i class="bi bi-exclamation-triangle"></i> Rekapan Tunggakan
                    @if($tunggakanCount > 0)
                        <span class="badge bg-danger ms-auto" style="font-size: 0.7rem;">{{ $tunggakanCount }}</span>
                    @endif
                </a>
            @else
                <div class="sidebar-heading">Portal Wali Santri</div>
                <a href="{{ route('wali.dashboard') }}" class="nav-link {{ request()->routeIs('wali.dashboard') ? 'active' : '' }}">
                    <i class="bi bi-grid-1x2"></i> Dashboard Tagihan
                </a>
                <a href="{{ route('wali.riwayat') }}" class="nav-link {{ request()->routeIs('wali.riwayat') ? 'active' : '' }}">
                    <i class="bi bi-clock-history"></i> Riwayat Pembayaran
                </a>
                <a href="{{ route('wali.panduan') }}" class="nav-link {{ request()->routeIs('wali.panduan') ? 'active' : '' }}">
                    <i class="bi bi-book-half"></i> Panduan Pembayaran
                </a>

                <div class="sidebar-heading">Akun Saya</div>
                <a href="{{ route('wali.profil') }}" class="nav-link {{ request()->routeIs('wali.profil*') ? 'active' : '' }}">
                    <i class="bi bi-person-circle"></i> Profil & Keamanan
                </a>
            @endif
        </div>

        <div class="p-3 border-top bg-light">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="fw-bold small text-truncate" style="max-width: 140px;">{{ Auth::user()->nama_lengkap }}</div>
                    <small class="badge bg-secondary" style="font-size: 0.7rem;">{{ ucfirst(str_replace('_', ' ', Auth::user()->role)) }}</small>
                </div>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger btn-sm p-1 px-2" title="Keluar">
                        <i class="bi bi-box-arrow-right"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Main Content Area -->
    <div class="main-content">
        <!-- Topbar -->
        <div class="topbar">
            <div class="d-flex align-items-center gap-2">
                <button type="button" class="btn btn-outline-success btn-sm d-lg-none shadow-sm px-2 py-1" id="btn-sidebar-toggle" aria-label="Buka Navigasi">
                    <i class="bi bi-list fs-5"></i>
                </button>
                <div>
                    <h5 class="mb-0 fw-bold" id="topbar-page-title">@yield('page_title', 'Dashboard')</h5>
                    <small class="text-muted d-none d-sm-inline">{{ \App\Models\Pengaturan::get('pesantren_nama', 'Pondok Pesantren Darul Muttaqin Al Islami') }}</small>
                </div>
            </div>
            <div class="topbar-right-box d-flex align-items-center gap-2 flex-wrap">
                @if(Auth::user()?->isAdmin())
                    <a href="{{ route('admin.midtrans.index') }}" class="text-decoration-none" title="Buka Konfigurasi Payment Gateway Midtrans">
                        @if(($dynamicMidtransMode ?? 'sandbox') === 'production')
                            <span class="badge bg-danger text-white px-2 px-sm-3 py-2 shadow-sm">
                                <i class="bi bi-shield-fill-check me-1"></i> <span class="d-none d-sm-inline">Midtrans </span>LIVE
                            </span>
                        @else
                            <span class="badge bg-warning text-dark px-2 px-sm-3 py-2 shadow-sm">
                                <i class="bi bi-shield-check me-1"></i> Sandbox<span class="d-none d-sm-inline"> Mode</span>
                            </span>
                        @endif
                    </a>
                @else
                    @if(($dynamicMidtransMode ?? 'sandbox') === 'production')
                        <span class="badge bg-success-subtle text-success border border-success-subtle px-2 px-sm-3 py-2">
                            <i class="bi bi-shield-check me-1"></i> <span class="d-none d-sm-inline">Gateway </span>Ready
                        </span>
                    @else
                        <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-2 px-sm-3 py-2">
                            <i class="bi bi-shield-check me-1"></i> Sandbox
                        </span>
                    @endif
                @endif
                <span class="text-muted small d-none d-sm-inline">|</span>
                <div class="d-flex align-items-center gap-2 small">
                    <span class="text-secondary d-none d-md-inline"><i class="bi bi-calendar3 text-success me-1"></i>{{ now()->translatedFormat('d M Y') }}</span>
                    <span class="badge bg-light text-dark border px-2 py-1 font-monospace" title="Waktu Nyata Server Pesantren (WIB)">
                        <i class="bi bi-clock text-primary me-1"></i><span id="topbar-live-clock">{{ now()->format('H:i:s') }}</span> WIB
                    </span>
                </div>
            </div>
        </div>

        <!-- Page Dynamic Body with Horizontal Slide -->
        <main id="app-page-wrapper" class="app-page-slide">
            <!-- Flash Messages -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show d-flex align-items-center" role="alert">
                    <i class="bi bi-check-circle-fill fs-5 me-2"></i>
                    <div>{{ session('success') }}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center" role="alert">
                    <i class="bi bi-exclamation-triangle-fill fs-5 me-2"></i>
                    <div>{{ session('error') }}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('info'))
                <div class="alert alert-info alert-dismissible fade show d-flex align-items-center" role="alert">
                    <i class="bi bi-info-circle-fill fs-5 me-2"></i>
                    <div>{{ session('info') }}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Dynamic Scripts Container for SPA Swapping -->
    <div id="app-dynamic-scripts">
        @stack('scripts')
    </div>

    <!-- True SPA Seamless Swapping with Smooth Content Fade & Rise -->
    <script>
    (function() {
        const loadingBar = document.getElementById('app-loading-bar');
        const pageWrapper = document.getElementById('app-page-wrapper');
        const topbarTitle = document.getElementById('topbar-page-title');
        const scriptsContainer = document.getElementById('app-dynamic-scripts');

        let isNavigating = false;

        function navigateSPA(targetUrl, updateHistory = true) {
            if (isNavigating) return;
            isNavigating = true;

            // Tutup sidebar otomatis di perangkat mobile saat navigasi dimulai
            if (window.innerWidth < 992) {
                closeSidebar();
            }

            // 1. Munculkan garis progres hijau di atas & pudar lembut konten saat ini
            if (loadingBar) {
                loadingBar.className = 'loading';
            }
            if (pageWrapper) {
                pageWrapper.classList.add('is-leaving');
                pageWrapper.classList.remove('is-entering');
            }

            // 2. Ambil HTML halaman baru di balik layar (Instant Background Fetch)
            fetch(targetUrl, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                if (!response.ok) throw new Error('HTTP ' + response.status);
                return response.text();
            })
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');

                // A. Update Document Title
                if (doc.title) {
                    document.title = doc.title;
                }

                // B. Update Topbar Title
                const newTitle = doc.getElementById('topbar-page-title');
                if (newTitle && topbarTitle) {
                    topbarTitle.innerHTML = newTitle.innerHTML;
                }

                // C. Tukar Konten Halaman & Picu Animasi Masuk Halus
                const newPageWrapper = doc.getElementById('app-page-wrapper');
                if (newPageWrapper && pageWrapper) {
                    pageWrapper.innerHTML = newPageWrapper.innerHTML;
                    pageWrapper.classList.remove('is-leaving');
                    void pageWrapper.offsetWidth; // Force reflow
                    pageWrapper.classList.add('is-entering');
                }

                // D. Update Status Navigasi Aktif di Sidebar
                const targetPath = new URL(targetUrl, window.location.origin).pathname;
                document.querySelectorAll('.sidebar-nav .nav-link').forEach(link => {
                    const linkPath = new URL(link.href, window.location.origin).pathname;
                    if (linkPath === targetPath) {
                        link.classList.add('active');
                    } else {
                        link.classList.remove('active');
                    }
                });

                // E. Eksekusi Skrip Dinamis Halaman Baru
                const newScriptsContainer = doc.getElementById('app-dynamic-scripts');
                if (newScriptsContainer && scriptsContainer) {
                    scriptsContainer.innerHTML = '';
                    Array.from(newScriptsContainer.querySelectorAll('script')).forEach(s => {
                        const script = document.createElement('script');
                        Array.from(s.attributes).forEach(attr => script.setAttribute(attr.name, attr.value));
                        script.text = s.text;
                        scriptsContainer.appendChild(script);
                    });
                }

                // Eksekusi skrip di dalam wrapper konten jika ada
                if (pageWrapper) {
                    pageWrapper.querySelectorAll('script').forEach(s => {
                        const script = document.createElement('script');
                        Array.from(s.attributes).forEach(attr => script.setAttribute(attr.name, attr.value));
                        script.text = s.text;
                        s.parentNode.replaceChild(script, s);
                    });
                }

                // F. Picu event DOMContentLoaded untuk inisialisasi script halaman baru
                document.dispatchEvent(new Event('DOMContentLoaded'));

                // G. Reset posisi scroll ke paling atas
                window.scrollTo({ top: 0, behavior: 'instant' });

                // H. Update URL di browser bar
                if (updateHistory) {
                    window.history.pushState({ url: targetUrl }, '', targetUrl);
                }

                // 3. Selesaikan garis progres di atas
                if (loadingBar) {
                    loadingBar.className = 'finishing';
                    setTimeout(() => {
                        loadingBar.className = '';
                        isNavigating = false;
                    }, 220);
                } else {
                    isNavigating = false;
                }
            })
            .catch(err => {
                console.warn('SPA Navigation fallback to hard reload:', err);
                window.location.href = targetUrl;
            });
        }

        // Intercept klik pada link navigasi internal
        document.addEventListener('click', function(e) {
            const link = e.target.closest('a');
            if (!link) return;

            // Lewati jika tombol keyboard ditekan (Ctrl/Cmd/Shift/Alt klik = buka tab baru)
            if (e.ctrlKey || e.metaKey || e.shiftKey || e.altKey) return;

            // Lewati jika aksi klik sudah dicegah oleh script lain
            if (e.defaultPrevented) return;

            const href = link.getAttribute('href');
            if (!href) return;

            // Lewati link non-navigasi
            if (href.startsWith('#') || href.startsWith('javascript:') || href.startsWith('mailto:') || href.startsWith('tel:')) return;

            // Lewati jika target buka jendela baru
            const target = link.getAttribute('target');
            if (target && target !== '_self') return;

            // Lewati komponen interaktif Bootstrap (Modal, Dropdown, Collapse, Tab)
            if (link.hasAttribute('data-bs-toggle') || link.hasAttribute('data-bs-target')) return;
            if (link.closest('[data-bs-toggle="dropdown"]') || link.closest('.dropdown-menu')) return;

            // Lewati download file CSV, cetak dokumen resmi, atau link WhatsApp
            if (link.hasAttribute('download') || href.includes('.csv') || href.includes('/cetak') || href.includes('wa.me')) return;

            // Cek apakah URL berada dalam domain yang sama
            let url;
            try {
                url = new URL(link.href, window.location.origin);
            } catch (_) {
                return;
            }

            if (url.origin !== window.location.origin) return;

            // Jika mengeklik menu yang sedang aktif dibuka, tidak perlu reload
            if (url.href === window.location.href) return;

            // Cegah reload kasar browser
            e.preventDefault();
            navigateSPA(url.href, true);
        });

        // Tangani navigasi tombol Back / Forward di browser
        window.addEventListener('popstate', function(e) {
            navigateSPA(window.location.href, false);
        });

        // Mobile Sidebar Controls
        const appSidebar = document.getElementById('app-sidebar');
        const sidebarToggle = document.getElementById('btn-sidebar-toggle');
        const sidebarClose = document.getElementById('btn-sidebar-close');
        const sidebarBackdrop = document.getElementById('sidebar-backdrop');

        function openSidebar() {
            if (appSidebar) appSidebar.classList.add('show');
            if (sidebarBackdrop) sidebarBackdrop.classList.add('show');
            document.body.style.overflow = 'hidden';
        }

        function closeSidebar() {
            if (appSidebar) appSidebar.classList.remove('show');
            if (sidebarBackdrop) sidebarBackdrop.classList.remove('show');
            document.body.style.overflow = '';
        }

        if (sidebarToggle) sidebarToggle.addEventListener('click', openSidebar);
        if (sidebarClose) sidebarClose.addEventListener('click', closeSidebar);
        if (sidebarBackdrop) sidebarBackdrop.addEventListener('click', closeSidebar);

        document.querySelectorAll('.sidebar-nav .nav-link').forEach(link => {
            link.addEventListener('click', () => {
                if (window.innerWidth < 992) closeSidebar();
            });
        });

        // Jam digital real-time sinkron WIB
        function updateLiveClock() {
            const clockEl = document.getElementById('topbar-live-clock');
            if (!clockEl) return;
            const now = new Date();
            const h = String(now.getHours()).padStart(2, '0');
            const m = String(now.getMinutes()).padStart(2, '0');
            const s = String(now.getSeconds()).padStart(2, '0');
            clockEl.textContent = `${h}:${m}:${s}`;
        }
        setInterval(updateLiveClock, 1000);

        // Pastikan modal Bootstrap selalu diposisikan di root document.body agar tidak tertutup backdrop
        document.addEventListener('show.bs.modal', function (event) {
            const modal = event.target;
            if (modal && modal.parentElement !== document.body) {
                document.body.appendChild(modal);
            }
        });
    })();
    </script>
    {{-- Khusus Akun Wali Santri: Asisten Virtual Interaktif Kang Santri --}}
    @if(Auth::check() && Auth::user()->role === 'wali_santri')
        @include('wali.partials.assistant-buddy')
    @endif
</body>
</html>

