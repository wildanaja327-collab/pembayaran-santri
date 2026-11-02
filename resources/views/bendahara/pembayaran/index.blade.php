@extends('layouts.app')

@section('title', 'Monitoring Pembayaran')
@section('page_title', 'Monitoring Transaksi Pembayaran')

@section('content')
<div class="card border-0 shadow-sm rounded-3">
    <div class="card-header bg-white py-3 border-0 d-flex flex-wrap align-items-center justify-content-between gap-2">
        <div>
            <h6 class="mb-0 fw-bold">Audit & Log Transaksi Pembayaran Masuk</h6>
            <small class="text-muted">Total: {{ $pembayaran->total() }} transaksi tercatat</small>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('bendahara.pembayaran.kasir.create') }}" class="btn btn-success btn-sm fw-semibold shadow-sm">
                <i class="bi bi-cash-stack me-1"></i> Bayar Tunai (Kasir Pesantren)
            </a>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="p-3 border-top border-bottom bg-light">
        <form action="{{ route('bendahara.pembayaran.index') }}" method="GET" class="row g-2">
            <div class="col-md-3">
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Order ID, nama santri, NIS..." value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <select name="status_transaksi" class="form-select form-select-sm">
                    <option value="">-- Status Transaksi --</option>
                    <option value="settlement" {{ request('status_transaksi') == 'settlement' ? 'selected' : '' }}>Settlement / Berhasil</option>
                    <option value="pending" {{ request('status_transaksi') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="expire" {{ request('status_transaksi') == 'expire' ? 'selected' : '' }}>Expired / Kadaluwarsa</option>
                    <option value="cancel" {{ request('status_transaksi') == 'cancel' ? 'selected' : '' }}>Dibatalkan</option>
                </select>
            </div>
            <div class="col-md-2">
                <select name="metode_bayar" class="form-select form-select-sm">
                    <option value="">-- Metode Bayar --</option>
                    <option value="gateway" {{ request('metode_bayar') == 'gateway' ? 'selected' : '' }}>Payment Gateway</option>
                    <option value="tunai" {{ request('metode_bayar') == 'tunai' ? 'selected' : '' }}>Tunai Kasir</option>
                </select>
            </div>
            <div class="col-md-2">
                <input type="date" name="tgl_mulai" class="form-control form-control-sm" value="{{ request('tgl_mulai') }}" title="Dari Tanggal">
            </div>
            <div class="col-md-2">
                <input type="date" name="tgl_selesai" class="form-control form-control-sm" value="{{ request('tgl_selesai') }}" title="Sampai Tanggal">
            </div>
            <div class="col-md-1 d-flex gap-1">
                <button type="submit" class="btn btn-primary btn-sm w-100"><i class="bi bi-search"></i></button>
                <a href="{{ route('bendahara.pembayaran.index') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-counterclockwise"></i></a>
            </div>
        </form>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr class="small text-muted text-uppercase">
                    <th>Order ID & Waktu</th>
                    <th>Santri</th>
                    <th>Komponen Tagihan</th>
                    <th>Metode & Channel</th>
                    <th>Nominal Pokok</th>
                    <th>Biaya Admin</th>
                    <th>Total Ditransfer</th>
                    <th>Status</th>
                    <th class="text-center">Aksi / Demo</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pembayaran as $p)
                    <tr>
                        <td>
                            <code class="fw-bold">{{ $p->order_id }}</code>
                            <div class="small text-muted">{{ date('d/m/Y H:i', strtotime($p->tgl_bayar)) }}</div>
                        </td>
                        <td>
                            <div class="fw-semibold">{{ $p->tagihan->santri->nama_santri ?? '-' }}</div>
                            <small class="text-muted">NIS: {{ $p->tagihan->santri->nis ?? '-' }}</small>
                        </td>
                        <td>
                            <div class="fw-semibold">{{ $p->tagihan->jenisTagihan->nama_tagihan ?? '-' }}</div>
                            <small class="text-muted">{{ $p->tagihan->bulan ? $p->tagihan->nama_bulan . ' ' : '' }}{{ $p->tagihan->tahun_ajaran }}</small>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border">
                                {{ $p->channel_bayar ?? ucfirst($p->metode_bayar) }}
                            </span>
                            @if($p->va_number)
                                <div class="small text-muted mt-1">VA: <code>{{ $p->va_number }}</code></div>
                            @endif
                        </td>
                        <td class="fw-semibold">Rp {{ number_format($p->jumlah_bayar, 0, ',', '.') }}</td>
                        <td class="small text-muted">Rp {{ number_format($p->biaya_admin, 0, ',', '.') }}</td>
                        <td class="fw-bold text-success">Rp {{ number_format($p->total_bayar, 0, ',', '.') }}</td>
                        <td>
                            @if($p->status_transaksi == 'settlement')
                                <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Settlement</span>
                                @if($p->kwitansi)
                                    <div class="small text-muted mt-1"><i class="bi bi-receipt me-1"></i>{{ $p->kwitansi->no_kwitansi }}</div>
                                @endif
                            @elseif($p->status_transaksi == 'pending')
                                <span class="badge bg-warning text-dark"><i class="bi bi-hourglass me-1"></i>Pending</span>
                            @else
                                <span class="badge bg-secondary">{{ ucfirst($p->status_transaksi) }}</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-1">
                                @if($p->status_transaksi == 'settlement')
                                    <a href="{{ route('kwitansi.show', $p->id_pembayaran) }}" class="btn btn-sm btn-outline-primary" title="Lihat Kwitansi Resmi">
                                        <i class="bi bi-receipt"></i>
                                    </a>
                                @elseif($p->status_transaksi == 'pending')
                                    <!-- Sync Button via API Midtrans -->
                                    <form action="{{ route('bendahara.pembayaran.sync', $p->id_pembayaran) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-info" title="Sinkronisasi Status dari Midtrans">
                                            <i class="bi bi-arrow-repeat"></i> Sync
                                        </button>
                                    </form>

                                    <!-- Instant Simulator for Sidang Demo -->
                                    <form action="{{ route('bendahara.pembayaran.simulate', $p->id_pembayaran) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success" title="Simulasi Settlement (Keperluan Demo Localhost)">
                                            <i class="bi bi-play-circle me-1"></i> Settle
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center py-5 text-muted">Belum ada transaksi pembayaran.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($pembayaran->hasPages())
        <div class="p-3 border-top d-flex justify-content-end">
            {{ $pembayaran->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>
@endsection

