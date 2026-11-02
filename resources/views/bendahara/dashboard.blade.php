@extends('layouts.app')

@section('title', 'Dashboard Bendahara')
@section('page_title', 'Dashboard Ringkasan Keuangan')

@section('content')
<!-- Quick Action Buttons -->
<div class="row g-3 mb-4">
    <div class="col-12 d-flex flex-wrap gap-2 justify-content-end">
        <a href="{{ route('bendahara.tagihan.generate.show') }}" class="btn btn-warning text-dark fw-semibold shadow-sm">
            <i class="bi bi-lightning-charge me-1"></i> Generate Tagihan Massal
        </a>
        <a href="{{ route('bendahara.pembayaran.kasir.create') }}" class="btn btn-success fw-semibold shadow-sm">
            <i class="bi bi-cash-stack me-1"></i> Kasir Pembayaran Tunai
        </a>
        <a href="{{ route('bendahara.santri.create') }}" class="btn btn-outline-primary fw-semibold bg-white shadow-sm">
            <i class="bi bi-person-plus me-1"></i> Tambah Santri Baru
        </a>
    </div>
</div>

<!-- Stat Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card-stat">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-muted small fw-semibold">TOTAL SANTRI</span>
                <span class="badge bg-primary-subtle text-primary p-2 rounded-circle">
                    <i class="bi bi-people fs-5"></i>
                </span>
            </div>
            <h3 class="fw-bold mb-1">{{ number_format($totalSantri) }}</h3>
            <div class="small">
                <span class="badge badge-smp me-1">SMP: {{ $santriSmp }}</span>
                <span class="badge badge-sma">SMA: {{ $santriSma }}</span>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card-stat">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-muted small fw-semibold">PEMASUKAN BULAN INI</span>
                <span class="badge bg-success-subtle text-success p-2 rounded-circle">
                    <i class="bi bi-cash-coin fs-5"></i>
                </span>
            </div>
            <h3 class="fw-bold mb-1 text-success">Rp {{ number_format($pemasukanBulanIni, 0, ',', '.') }}</h3>
            <div class="small text-muted">
                Total Keseluruhan: <strong>Rp {{ number_format($totalPemasukan, 0, ',', '.') }}</strong>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card-stat">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-muted small fw-semibold">TOTAL TUNGGAKAN PIUTANG</span>
                <span class="badge bg-danger-subtle text-danger p-2 rounded-circle">
                    <i class="bi bi-exclamation-triangle fs-5"></i>
                </span>
            </div>
            <h3 class="fw-bold mb-1 text-danger">Rp {{ number_format($totalTunggakan, 0, ',', '.') }}</h3>
            <div class="small text-muted">
                Belum lunas: <span class="badge bg-danger">{{ $tagihanBelumLunas }} tagihan</span>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card-stat">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-muted small fw-semibold">GATEWAY PENDING</span>
                <span class="badge bg-warning-subtle text-warning p-2 rounded-circle">
                    <i class="bi bi-hourglass-split fs-5"></i>
                </span>
            </div>
            <h3 class="fw-bold mb-1 text-warning">{{ $transaksiPending }}</h3>
            <div class="small text-muted">
                Menunggu transfer dari wali santri
            </div>
        </div>
    </div>
</div>

<!-- Recent Transactions & Progress -->
<div class="row g-4">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-header bg-white py-3 border-0 d-flex align-items-center justify-content-between">
                <h6 class="mb-0 fw-bold"><i class="bi bi-clock-history me-2 text-success"></i>Transaksi Terkini Masuk</h6>
                <a href="{{ route('bendahara.pembayaran.index') }}" class="btn btn-sm btn-link text-decoration-none">Lihat Semua</a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr class="small text-muted text-uppercase">
                            <th>Order ID</th>
                            <th>Santri</th>
                            <th>Jenis Tagihan</th>
                            <th>Metode / Channel</th>
                            <th>Nominal</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentPayments as $p)
                            <tr>
                                <td><code class="small">{{ $p->order_id }}</code></td>
                                <td>
                                    <div class="fw-semibold">{{ $p->tagihan->santri->nama_santri ?? '-' }}</div>
                                    <small class="text-muted">NIS: {{ $p->tagihan->santri->nis ?? '-' }}</small>
                                </td>
                                <td>
                                    <div>{{ $p->tagihan->jenisTagihan->nama_tagihan ?? '-' }}</div>
                                    <small class="text-muted">{{ $p->tagihan->bulan ? $p->tagihan->nama_bulan . ' ' : '' }}{{ $p->tagihan->tahun_ajaran }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border">
                                        {{ $p->channel_bayar ?? ucfirst($p->metode_bayar) }}
                                    </span>
                                </td>
                                <td class="fw-bold">Rp {{ number_format($p->jumlah_bayar, 0, ',', '.') }}</td>
                                <td>
                                    @if($p->status_transaksi == 'settlement')
                                        <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Settlement</span>
                                    @elseif($p->status_transaksi == 'pending')
                                        <span class="badge bg-warning text-dark"><i class="bi bi-hourglass me-1"></i>Pending</span>
                                    @else
                                        <span class="badge bg-secondary">{{ ucfirst($p->status_transaksi) }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if($p->status_transaksi == 'settlement')
                                        <a href="{{ route('kwitansi.show', $p->id_pembayaran) }}" class="btn btn-sm btn-outline-primary" title="Lihat Kwitansi">
                                            <i class="bi bi-receipt"></i>
                                        </a>
                                    @elseif($p->status_transaksi == 'pending')
                                        <form action="{{ route('bendahara.pembayaran.simulate', $p->id_pembayaran) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success" title="Simulasi Settlement (Demo)">
                                                <i class="bi bi-play-fill"></i> Settle
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">Belum ada transaksi pembayaran masuk.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card border-0 shadow-sm rounded-3 mb-4">
            <div class="card-header bg-white py-3 border-0">
                <h6 class="mb-0 fw-bold"><i class="bi bi-pie-chart me-2 text-primary"></i>Status Rasio Tagihan</h6>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="small text-muted">Tagihan Lunas</span>
                    <span class="fw-bold text-success">{{ $tagihanLunas }}</span>
                </div>
                <div class="progress mb-3" style="height: 10px;">
                    @php
                        $totalTagihanCount = $tagihanLunas + $tagihanBelumLunas;
                        $persenLunas = $totalTagihanCount > 0 ? round(($tagihanLunas / $totalTagihanCount) * 100) : 0;
                    @endphp
                    <div class="progress-bar bg-success" role="progressbar" style="width: {{ $persenLunas }}%"></div>
                </div>

                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="small text-muted">Tagihan Belum Lunas / Tunggakan</span>
                    <span class="fw-bold text-danger">{{ $tagihanBelumLunas }}</span>
                </div>
                <div class="progress mb-4" style="height: 10px;">
                    <div class="progress-bar bg-danger" role="progressbar" style="width: {{ 100 - $persenLunas }}%"></div>
                </div>

                <div class="p-3 bg-light rounded text-center">
                    <small class="text-muted d-block">Persentase Kepatuhan Pembayaran</small>
                    <h2 class="fw-bold text-dark mb-0">{{ $persenLunas }}%</h2>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm rounded-3 bg-success text-white">
            <div class="card-body p-4">
                <h6 class="fw-bold"><i class="bi bi-shield-check me-2"></i>Midtrans Payment Gateway</h6>
                <p class="small text-white-50 mb-3">Integrasi pembayaran modern via Virtual Account Bank (BCA, Mandiri, BRI, BNI) & QRIS. Sistem memvalidasi transaksi secara otomatis.</p>
                <div class="d-flex gap-2">
                    <a href="{{ route('bendahara.tagihan.index') }}" class="btn btn-light btn-sm text-success fw-semibold">
                        Lihat Tagihan
                    </a>
                    <a href="{{ route('bendahara.laporan.tunggakan') }}" class="btn btn-outline-light btn-sm fw-semibold">
                        Rekapan Tunggakan
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

