<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Sistem Informasi Pembayaran Santri') - PP Darul Muttaqin</title>
    
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
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 0.75rem;
            overflow: hidden;
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
    </style>
    
    <!-- Midtrans Snap Script -->
    <script 
        src="{{ config('services.midtrans.is_production') ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js' }}" 
        data-client-key="{{ config('services.midtrans.client_key') }}">
    </script>
    @stack('styles')
</head>
<body>

    <!-- Sidebar -->
    <div class="sidebar d-flex flex-column">
        <div class="sidebar-brand">
            <div class="bg-success text-white rounded p-2 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                <i class="bi bi-bank2 fs-5"></i>
            </div>
            <div>
                <div class="fw-bold lh-1 text-dark" style="font-size: 0.95rem;">PP Darul Muttaqin</div>
                <small class="text-muted" style="font-size: 0.75rem;">SIM Keuangan Santri</small>
            </div>
        </div>

        <div class="sidebar-nav flex-grow-1">
            @if(Auth::user()->isBendahara())
                <div class="sidebar-heading">Menu Utama</div>
                <a href="{{ route('bendahara.dashboard') }}" class="nav-link {{ request()->routeIs('bendahara.dashboard') ? 'active' : '' }}">
                    <i class="bi bi-grid-1x2"></i> Dashboard
                </a>
                <a href="{{ route('bendahara.santri.index') }}" class="nav-link {{ request()->routeIs('bendahara.santri.*') ? 'active' : '' }}">
                    <i class="bi bi-people"></i> Data Santri (356)
                </a>
                
                <div class="sidebar-heading">Tagihan & Keuangan</div>
                <a href="{{ route('bendahara.jenis-tagihan.index') }}" class="nav-link {{ request()->routeIs('bendahara.jenis-tagihan.*') ? 'active' : '' }}">
                    <i class="bi bi-tags"></i> Master Jenis Tagihan
                </a>
                <a href="{{ route('bendahara.tagihan.index') }}" class="nav-link {{ request()->routeIs('bendahara.tagihan.index') || request()->routeIs('bendahara.tagihan.create') ? 'active' : '' }}">
                    <i class="bi bi-receipt"></i> Data Tagihan Santri
                </a>
                <a href="{{ route('bendahara.tagihan.generate.show') }}" class="nav-link {{ request()->routeIs('bendahara.tagihan.generate.*') ? 'active' : '' }}">
                    <i class="bi bi-lightning-charge"></i> Generator Massal
                </a>
                <a href="{{ route('bendahara.pembayaran.index') }}" class="nav-link {{ request()->routeIs('bendahara.pembayaran.index') ? 'active' : '' }}">
                    <i class="bi bi-wallet2"></i> Transaksi Pembayaran
                </a>
                <a href="{{ route('bendahara.pembayaran.kasir.create') }}" class="nav-link {{ request()->routeIs('bendahara.pembayaran.kasir.*') ? 'active' : '' }}">
                    <i class="bi bi-cash-stack"></i> Kasir Tunai Pesantren
                </a>

                <div class="sidebar-heading">Laporan & Rekapitulasi</div>
                <a href="{{ route('bendahara.laporan.pemasukan') }}" class="nav-link {{ request()->routeIs('bendahara.laporan.pemasukan') ? 'active' : '' }}">
                    <i class="bi bi-file-earmark-bar-graph"></i> Laporan Pemasukan
                </a>
                <a href="{{ route('bendahara.laporan.tunggakan') }}" class="nav-link {{ request()->routeIs('bendahara.laporan.tunggakan') ? 'active' : '' }}">
                    <i class="bi bi-exclamation-triangle"></i> Rekapan Tunggakan
                </a>
            @else
                <div class="sidebar-heading">Portal Wali Santri</div>
                <a href="{{ route('wali.dashboard') }}" class="nav-link {{ request()->routeIs('wali.dashboard') ? 'active' : '' }}">
                    <i class="bi bi-grid-1x2"></i> Dashboard Tagihan
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
            <div>
                <h5 class="mb-0 fw-bold">@yield('page_title', 'Dashboard')</h5>
                <small class="text-muted">Pondok Pesantren Darul Muttaqin Al Islami, Mulyojati Metro Barat</small>
            </div>
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2">
                    <i class="bi bi-shield-check me-1"></i> Midtrans Gateway Ready
                </span>
                <span class="text-muted small">|</span>
                <span class="small text-muted"><i class="bi bi-calendar3 me-1"></i> {{ date('d M Y') }}</span>
            </div>
        </div>

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
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>

