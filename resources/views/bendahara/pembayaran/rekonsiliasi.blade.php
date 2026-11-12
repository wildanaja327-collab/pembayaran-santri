@extends('layouts.app')

@section('title', 'Rekonsiliasi Transaksi Gateway')
@section('page_title', 'Rekonsiliasi Pembayaran Digital Midtrans')

@section('content')
<!-- Header Summary Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card-stat border-start border-4 border-primary">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-muted small fw-bold text-uppercase">Total Transaksi Gateway</span>
                <span class="badge bg-primary-subtle text-primary p-2 rounded-circle">
                    <i class="bi bi-wallet2 fs-5"></i>
                </span>
            </div>
            <h3 class="fw-bold mb-1">{{ $totalGateway }}</h3>
            <small class="text-muted">Virtual Account & QRIS</small>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card-stat border-start border-4 border-success">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-muted small fw-bold text-uppercase">Settlement Sukses</span>
                <span class="badge bg-success-subtle text-success p-2 rounded-circle">
                    <i class="bi bi-check-circle fs-5"></i>
                </span>
            </div>
            <h3 class="fw-bold mb-1 text-success">{{ $totalSettlement }}</h3>
            <small class="text-muted">Kas masuk terverifikasi sah</small>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card-stat border-start border-4 border-warning">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-muted small fw-bold text-uppercase">Pending Menunggu</span>
                <span class="badge bg-warning-subtle text-warning p-2 rounded-circle">
                    <i class="bi bi-hourglass-split fs-5"></i>
                </span>
            </div>
            <h3 class="fw-bold mb-1 text-warning">{{ $totalPending }}</h3>
            <small class="text-muted">Menunggu pembayaran wali</small>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card-stat border-start border-4 border-danger">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-muted small fw-bold text-uppercase">Pending Kritis (>24 Jam)</span>
                <span class="badge bg-danger-subtle text-danger p-2 rounded-circle">
                    <i class="bi bi-exclamation-triangle fs-5"></i>
                </span>
            </div>
            <h3 class="fw-bold mb-1 text-danger">{{ $pendingOverdue }}</h3>
            <small class="text-muted">Perlu sinkronisasi atau pembatalan</small>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-3">
    <div class="card-header bg-white py-3 border-0 d-flex flex-wrap align-items-center justify-content-between gap-2">
        <div>
            <h6 class="mb-0 fw-bold"><i class="bi bi-arrow-repeat text-primary me-2"></i>Daftar Transaksi Midtrans Tertunda (Pending)</h6>
            <small class="text-muted">Monitoring transaksi Virtual Account / QRIS yang belum selesai diselesaikan oleh wali santri</small>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('bendahara.pembayaran.rekonsiliasi') }}" class="btn btn-sm {{ !request('kategori') ? 'btn-primary' : 'btn-outline-primary' }}">
                Semua Pending ({{ $totalPending }})
            </a>
            <a href="{{ route('bendahara.pembayaran.rekonsiliasi', ['kategori' => 'overdue']) }}" class="btn btn-sm {{ request('kategori') == 'overdue' ? 'btn-danger' : 'btn-outline-danger' }}">
                <i class="bi bi-clock-history me-1"></i> Kritis >24 Jam ({{ $pendingOverdue }})
            </a>
            <a href="{{ route('bendahara.pembayaran.index') }}" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-list-ul me-1"></i> Semua Transaksi
            </a>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr class="small text-muted text-uppercase">
                    <th>Order ID</th>
                    <th>Waktu Dibuat</th>
                    <th>Santri & Wali</th>
                    <th>Komponen Tagihan</th>
                    <th>VA / Channel</th>
                    <th class="text-end">Nominal Pokok</th>
                    <th>Status Umur Order</th>
                    <th class="text-center">Aksi Rekonsiliasi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pendingTransactions as $p)
                    @php
                        $hoursOld = \Carbon\Carbon::parse($p->tgl_bayar)->diffInHours(now());
                        $isOverdue = $hoursOld >= 24;
                    @endphp
                    <tr>
                        <td>
                            <code class="fw-bold text-dark">{{ $p->order_id }}</code>
                            @if($p->snap_token)
                                <div class="text-muted small" style="font-size: 0.7rem;">Token Snap Ready</div>
                            @endif
                        </td>
                        <td>
                            <div>{{ date('d/m/Y H:i', strtotime($p->tgl_bayar)) }} WIB</div>
                            <small class="text-muted">{{ \Carbon\Carbon::parse($p->tgl_bayar)->diffForHumans() }}</small>
                        </td>
                        <td>
                            <div class="fw-semibold">{{ $p->tagihan->santri->nama_santri ?? '-' }}</div>
                            <small class="text-muted">
                                NIS: {{ $p->tagihan->santri->nis ?? '-' }} &bull;
                                Wali: {{ $p->tagihan->santri->wali->nama_lengkap ?? '-' }}
                            </small>
                        </td>
                        <td>
                            <div class="fw-semibold">{{ $p->tagihan->jenisTagihan->nama_tagihan ?? '-' }}</div>
                            <small class="text-muted">{{ $p->tagihan->bulan ? $p->tagihan->nama_bulan . ' ' : '' }}{{ $p->tagihan->tahun_ajaran }}</small>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border">
                                {{ $p->channel_bayar ?? 'Payment Gateway' }}
                            </span>
                            @if($p->va_number)
                                <div class="small font-monospace text-primary mt-1">VA: {{ $p->va_number }}</div>
                            @endif
                        </td>
                        <td class="text-end fw-bold text-dark">Rp {{ number_format($p->jumlah_bayar, 0, ',', '.') }}</td>
                        <td>
                            @if($isOverdue)
                                <span class="badge bg-danger">
                                    <i class="bi bi-exclamation-triangle-fill me-1"></i> {{ $hoursOld }} Jam (Lewat)
                                </span>
                            @else
                                <span class="badge bg-warning text-dark">
                                    <i class="bi bi-hourglass-split me-1"></i> {{ $hoursOld }} Jam (Aktif)
                                </span>
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-1">
                                <!-- Sync via Midtrans API -->
                                <form action="{{ route('bendahara.pembayaran.sync', $p->id_pembayaran) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-info" title="Sinkronisasi Status dari Midtrans">
                                        <i class="bi bi-arrow-repeat"></i> Sync
                                    </button>
                                </form>

                                <!-- Demo Settlement Simulation -->
                                <form action="{{ route('bendahara.pembayaran.simulate', $p->id_pembayaran) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-success" title="Simulasi Settlement (Keperluan Ujian Sidang Skripsi)">
                                        <i class="bi bi-play-circle me-1"></i> Settle
                                    </button>
                                </form>

                                <!-- Void / Cancel -->
                                <form action="{{ route('bendahara.pembayaran.cancel', $p->id_pembayaran) }}" method="POST" class="d-inline" onsubmit="return confirm('Batalkan pesanan transaksi pending ini?')">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Batalkan Pesanan (Void)">
                                        <i class="bi bi-x-circle"></i> Batal
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="bi bi-check2-circle fs-2 text-success d-block mb-2"></i>
                            Alhamdulillah! Tidak ada transaksi pending yang perlu direkonsiliasi.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($pendingTransactions->hasPages())
        <div class="p-3 border-top d-flex justify-content-end">
            {{ $pendingTransactions->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>
@endsection

