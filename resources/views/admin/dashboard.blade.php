@extends('layouts.app')

@section('title', 'Dashboard Administrator')
@section('page_title', 'Dashboard Administrator Sistem')

@section('content')
<!-- Quick Action Buttons (Disesuaikan khusus wewenang Administrator Sistem) -->
<div class="row g-3 mb-4">
    <div class="col-12 d-flex flex-wrap gap-2 justify-content-end">
        <a href="{{ route('admin.whatsapp.index') }}" class="btn btn-success fw-semibold shadow-sm">
            <i class="bi bi-whatsapp me-1"></i> WhatsApp Gateway
        </a>
        <a href="{{ route('admin.midtrans.index') }}" class="btn btn-warning text-dark fw-semibold shadow-sm">
            <i class="bi bi-credit-card-2-front me-1"></i> Gateway Midtrans
        </a>
        <a href="{{ route('admin.activity-logs') }}" class="btn btn-primary fw-semibold shadow-sm">
            <i class="bi bi-shield-check me-1"></i> Log Aktivitas / Audit Trail
        </a>
        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-dark bg-white fw-semibold shadow-sm">
            <i class="bi bi-person-gear me-1"></i> Kelola Pengguna (Users)
        </a>
        <a href="{{ route('admin.tahun-ajaran.index') }}" class="btn btn-outline-secondary bg-white fw-semibold shadow-sm">
            <i class="bi bi-calendar-range me-1"></i> Periode Tahun Ajaran
        </a>
        <a href="{{ route('admin.pengaturan') }}" class="btn btn-outline-success bg-white fw-semibold shadow-sm">
            <i class="bi bi-sliders me-1"></i> Konfigurasi Sistem & Kwitansi
        </a>
    </div>
</div>

<!-- Stat Cards -->
<div class="row g-3 mb-4">
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card-stat">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-muted small fw-semibold">TOTAL SANTRI AKTIF</span>
                <span class="badge bg-primary-subtle text-primary p-2 rounded-circle">
                    <i class="bi bi-mortarboard fs-5"></i>
                </span>
            </div>
            <h3 class="fw-bold mb-1">{{ number_format($totalSantri) }}</h3>
            <div class="small">
                <span class="badge badge-smp me-1">SMP: {{ $santriSmp }}</span>
                <span class="badge badge-sma me-1">SMA: {{ $santriSma }}</span>
                <a href="{{ route('admin.santri.index') }}" class="text-decoration-none small text-muted">Lihat &rarr;</a>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card-stat">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-muted small fw-semibold">TOTAL PENGGUNA SISTEM</span>
                <span class="badge bg-success-subtle text-success p-2 rounded-circle">
                    <i class="bi bi-people fs-5"></i>
                </span>
            </div>
            <h3 class="fw-bold mb-1 text-success">{{ number_format($totalUsers) }}</h3>
            <div class="small text-muted">
                <span class="badge bg-dark me-1">Adm: {{ $adminCount }}</span>
                <span class="badge bg-success me-1">Bdh: {{ $bendaharaCount }}</span>
                <span class="badge bg-info text-dark">Wali: {{ $waliCount }}</span>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card-stat">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-muted small fw-semibold">TAHUN AJARAN AKTIF</span>
                <span class="badge bg-warning-subtle text-warning p-2 rounded-circle">
                    <i class="bi bi-calendar-check fs-5"></i>
                </span>
            </div>
            <h4 class="fw-bold mb-1 text-dark text-truncate">
                {{ $tahunAjaranAktif ? $tahunAjaranAktif->nama_tahun_ajaran : 'Belum Disetel' }}
            </h4>
            <div class="small">
                <span class="badge bg-success">
                    {{ $tahunAjaranAktif ? 'Semester ' . $tahunAjaranAktif->semester : 'Nonaktif' }}
                </span>
                <a href="{{ route('admin.tahun-ajaran.index') }}" class="text-decoration-none small text-muted ms-1">Ubah</a>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card-stat">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-muted small fw-semibold">MONITORING KEUANGAN</span>
                <span class="badge bg-info-subtle text-info p-2 rounded-circle">
                    <i class="bi bi-cash-stack fs-5"></i>
                </span>
            </div>
            <h4 class="fw-bold mb-1 text-dark text-truncate">Rp {{ number_format($totalPemasukan, 0, ',', '.') }}</h4>
            <div class="small text-muted">
                <span class="text-danger fw-semibold">{{ $jumlahTagihanBelumLunas }}</span> tagihan belum lunas
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Log Aktivitas Terkini (Audit Trail Real-Time) -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-header bg-white py-3 border-0 d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="mb-0 fw-bold"><i class="bi bi-shield-check me-2 text-primary"></i>Aktivitas Sistem & Audit Trail Terkini</h6>
                    <small class="text-muted">Pemantauan riwayat login, mutasi pengguna, dan aktivitas operasional pesantren.</small>
                </div>
                <a href="{{ route('admin.activity-logs') }}" class="btn btn-sm btn-link text-decoration-none">Semua Log &rarr;</a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr class="small text-muted text-uppercase">
                            <th>Waktu</th>
                            <th>Pengguna</th>
                            <th>Aksi</th>
                            <th>Keterangan Aktivitas</th>
                            <th>IP Address</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentLogs as $log)
                            <tr>
                                <td class="small text-nowrap">
                                    <div class="fw-semibold text-dark">{{ $log->created_at->format('H:i') }} WIB</div>
                                    <small class="text-muted">{{ $log->created_at->diffForHumans() }}</small>
                                </td>
                                <td>
                                    <div class="fw-semibold small text-dark">{{ $log->username }}</div>
                                    <span class="badge bg-secondary" style="font-size: 0.65rem;">{{ $log->role }}</span>
                                </td>
                                <td>
                                    @php
                                        $badgeClass = 'bg-secondary';
                                        if (str_contains($log->action, 'LOGIN') && !str_contains($log->action, 'FAILED')) {
                                            $badgeClass = 'bg-info text-dark';
                                        } elseif (str_contains($log->action, 'FAILED') || str_contains($log->action, 'DELETE') || str_contains($log->action, 'BLOCKED')) {
                                            $badgeClass = 'bg-danger';
                                        } elseif (str_contains($log->action, 'CREATE') || str_contains($log->action, 'AKTIF')) {
                                            $badgeClass = 'bg-success';
                                        } elseif (str_contains($log->action, 'UPDATE') || str_contains($log->action, 'STATUS')) {
                                            $badgeClass = 'bg-primary';
                                        } elseif (str_contains($log->action, 'RESET')) {
                                            $badgeClass = 'bg-warning text-dark';
                                        }
                                    @endphp
                                    <span class="badge {{ $badgeClass }} font-monospace" style="font-size: 0.68rem;">{{ $log->action }}</span>
                                </td>
                                <td>
                                    <small class="text-dark">{{ $log->description }}</small>
                                </td>
                                <td class="small text-muted font-monospace">{{ $log->ip_address }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">
                                    Belum ada log aktivitas yang tercatat.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Status Sistem & Gateway -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm rounded-3 mb-4">
            <div class="card-header bg-white py-3 border-0">
                <h6 class="mb-0 fw-bold"><i class="bi bi-gear-wide-connected me-2 text-success"></i>Status Gateway & Infrastruktur</h6>
            </div>
            <div class="card-body">
                <ul class="list-group list-group-flush small">
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <span>Payment Engine:</span>
                        <span class="badge bg-success">Midtrans Snap SDK</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <span>Mode Gateway:</span>
                        <span class="badge {{ $systemInfo['midtrans_mode'] == 'production' ? 'bg-danger' : 'bg-warning text-dark' }}">
                            {{ strtoupper($systemInfo['midtrans_mode']) }} TESTING
                        </span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <span>Framework:</span>
                        <span class="badge bg-primary">Laravel {{ $systemInfo['laravel_version'] }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <span>Runtime PHP:</span>
                        <span class="badge bg-secondary">PHP {{ $systemInfo['php_version'] }} ({{ $systemInfo['server_os'] }})</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <span>Database Engine:</span>
                        <span class="badge bg-dark">MySQL (3NF Normalized)</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <span>Hak Akses Terpisah:</span>
                        <span class="badge bg-info text-dark">3 Role (Admin, Bendahara, Wali)</span>
                    </li>
                </ul>

                <div class="d-grid gap-2 mt-3">
                    <a href="{{ route('admin.midtrans.index') }}" class="btn btn-warning text-dark btn-sm fw-semibold">
                        <i class="bi bi-credit-card-2-front me-1"></i> Konfigurasi Gateway Midtrans
                    </a>
                    <a href="{{ route('admin.pengaturan') }}" class="btn btn-outline-secondary btn-sm fw-semibold">
                        <i class="bi bi-sliders me-1"></i> Pengaturan Identitas & Kwitansi
                    </a>
                </div>

                <div class="alert alert-light border small mt-3 mb-0">
                    <i class="bi bi-shield-check text-success me-1"></i>
                    <strong>Separation of Duties (SoD):</strong> Administrator Sistem bertanggung jawab atas audit, akun pengguna, konfigurasi gateway, dan stabilitas server; pencatatan transaksi kasir dan rekonsiliasi keuangan dipegang oleh Bendahara.
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
