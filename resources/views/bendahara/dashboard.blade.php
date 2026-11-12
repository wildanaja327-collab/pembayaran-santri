@extends('layouts.app')

@section('title', 'Dashboard Bendahara Keuangan')
@section('page_title', 'Dashboard Ringkasan Keuangan Pesantren')

@section('content')
<!-- Header Banner & Quick Actions -->
<div class="row align-items-center mb-4 g-3">
    <div class="col-md-6">
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 rounded-pill fw-semibold">
                <i class="bi bi-calendar2-check me-1"></i> Tahun Ajaran Aktif: 
                {{ $tahunAjaranAktif ? $tahunAjaranAktif->nama_tahun_ajaran . ' (' . $tahunAjaranAktif->semester . ')' : 'Belum Ditentukan' }}
            </span>
            <span class="badge bg-light text-muted border px-2 py-2 rounded-pill">
                <i class="bi bi-clock me-1"></i> {{ date('H:i') }} WIB
            </span>
        </div>
    </div>
    <div class="col-md-6 text-md-end">
        <div class="d-flex flex-wrap gap-2 justify-content-md-end">
            <a href="{{ route('bendahara.pembayaran.kasir.create') }}" class="btn btn-success fw-semibold shadow-sm">
                <i class="bi bi-cash-stack me-1"></i> Kasir Pembayaran Tunai
            </a>
            <a href="{{ route('bendahara.tagihan.generate.show') }}" class="btn btn-warning text-dark fw-semibold shadow-sm">
                <i class="bi bi-lightning-charge me-1"></i> Tagihan Massal
            </a>
            <a href="{{ route('bendahara.tagihan.create') }}" class="btn btn-outline-primary fw-semibold bg-white shadow-sm">
                <i class="bi bi-plus-circle me-1"></i> Tagihan Baru
            </a>
            <a href="{{ route('bendahara.pembayaran.rekonsiliasi') }}" class="btn btn-outline-secondary fw-semibold bg-white shadow-sm">
                <i class="bi bi-arrow-repeat me-1"></i> Rekonsiliasi Gateway
            </a>
        </div>
    </div>
</div>

<!-- 4 Key Stat Cards -->
<div class="row g-3 mb-4">
    <!-- Stat 1: Pemasukan Bulan Ini -->
    <div class="col-xl-3 col-md-6">
        <div class="card-stat border-start border-4 border-success">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-muted small fw-bold text-uppercase">Pemasukan Bulan Ini</span>
                <span class="badge bg-success-subtle text-success p-2 rounded-circle">
                    <i class="bi bi-cash-coin fs-5"></i>
                </span>
            </div>
            <h3 class="fw-bold mb-1 text-success">Rp {{ number_format($pemasukanBulanIni, 0, ',', '.') }}</h3>
            <div class="small text-muted d-flex justify-content-between">
                <span>Tahun {{ date('Y') }}:</span>
                <strong class="text-dark">Rp {{ number_format($pemasukanTahunIni, 0, ',', '.') }}</strong>
            </div>
        </div>
    </div>

    <!-- Stat 2: Total Tunggakan Piutang -->
    <div class="col-xl-3 col-md-6">
        <div class="card-stat border-start border-4 border-danger">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-muted small fw-bold text-uppercase">Total Tunggakan Santri</span>
                <span class="badge bg-danger-subtle text-danger p-2 rounded-circle">
                    <i class="bi bi-exclamation-triangle fs-5"></i>
                </span>
            </div>
            <h3 class="fw-bold mb-1 text-danger">Rp {{ number_format($totalTunggakan, 0, ',', '.') }}</h3>
            <div class="small text-muted d-flex justify-content-between">
                <span>Belum Lunas:</span>
                <span class="badge bg-danger-subtle text-danger fw-bold">{{ $tagihanBelumLunas }} Tagihan</span>
            </div>
        </div>
    </div>

    <!-- Stat 3: Midtrans Gateway Pending -->
    <div class="col-xl-3 col-md-6">
        <div class="card-stat border-start border-4 border-warning">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-muted small fw-bold text-uppercase">Gateway Midtrans Pending</span>
                <span class="badge bg-warning-subtle text-warning p-2 rounded-circle">
                    <i class="bi bi-hourglass-split fs-5"></i>
                </span>
            </div>
            <h3 class="fw-bold mb-1 text-warning">{{ $transaksiPending }} <small class="fs-6 text-muted fw-normal">transaksi</small></h3>
            <div class="small text-muted d-flex justify-content-between">
                <span>Pending >24 Jam:</span>
                <span class="badge {{ $transaksiPendingOverdue > 0 ? 'bg-danger' : 'bg-secondary' }}">{{ $transaksiPendingOverdue }} Menunggu</span>
            </div>
        </div>
    </div>

    <!-- Stat 4: Total Santri Aktif -->
    <div class="col-xl-3 col-md-6">
        <div class="card-stat border-start border-4 border-primary">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-muted small fw-bold text-uppercase">Santri Terdaftar</span>
                <span class="badge bg-primary-subtle text-primary p-2 rounded-circle">
                    <i class="bi bi-people fs-5"></i>
                </span>
            </div>
            <h3 class="fw-bold mb-1 text-primary">{{ number_format($totalSantriAktif) }} <small class="fs-6 text-muted fw-normal">santri aktif</small></h3>
            <div class="small text-muted d-flex justify-content-between">
                <span class="badge badge-smp">SMP: {{ $santriSmp }}</span>
                <span class="badge badge-sma">SMA: {{ $santriSma }}</span>
            </div>
        </div>
    </div>
</div>

<!-- Charts Section: Tren 6 Bulan & Komposisi -->
<div class="row g-4 mb-4">
    <!-- Bar Chart: Tren 6 Bulan -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-3 h-100">
            <div class="card-header bg-white py-3 border-0 d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="mb-0 fw-bold"><i class="bi bi-bar-chart-line text-success me-2"></i>Tren Pemasukan Keuangan (6 Bulan Terakhir)</h6>
                    <small class="text-muted">Arus kas masuk pembayaran yang terverifikasi sah (Settlement)</small>
                </div>
                <div>
                    <span class="badge bg-light text-dark border">
                        Total Akumulasi: Rp {{ number_format($totalPemasukan, 0, ',', '.') }}
                    </span>
                </div>
            </div>
            <div class="card-body">
                <div style="height: 290px;">
                    <canvas id="monthlyIncomeChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Doughnut Chart & SPP Progress -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm rounded-3 h-100">
            <div class="card-header bg-white py-3 border-0">
                <h6 class="mb-0 fw-bold"><i class="bi bi-pie-chart text-primary me-2"></i>Komposisi Jenis Penerimaan</h6>
                <small class="text-muted">Distribusi pendapatan per jenis tagihan</small>
            </div>
            <div class="card-body d-flex flex-column justify-content-between">
                <div style="height: 180px; position: relative;">
                    <canvas id="compositionChart"></canvas>
                </div>

                <!-- Progress Kolektabilitas SPP Bulan Ini -->
                <div class="p-3 bg-light rounded-3 mt-3 border">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="small fw-semibold text-dark">Kolektabilitas SPP Bulan Ini</span>
                        <span class="small fw-bold text-success">{{ $persenKolektabilitasSpp }}%</span>
                    </div>
                    <div class="progress mb-2" style="height: 8px;">
                        <div class="progress-bar bg-success progress-bar-striped progress-bar-animated" role="progressbar" style="width: {{ $persenKolektabilitasSpp }}%"></div>
                    </div>
                    <div class="d-flex justify-content-between small text-muted">
                        <span>Lunas: <strong>{{ $sppBulanIniLunas }}</strong> santri</span>
                        <span>Total Tagihan: <strong>{{ $sppBulanIniTagihan }}</strong> santri</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Middle Section: Recent Transactions & Due Date Alerts -->
<div class="row g-4 mb-4">
    <!-- Recent Payments (Col-7) -->
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm rounded-3 h-100">
            <div class="card-header bg-white py-3 border-0 d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="mb-0 fw-bold"><i class="bi bi-clock-history text-primary me-2"></i>Transaksi Pembayaran Terkini Masuk</h6>
                    <small class="text-muted">Monitoring mutasi kas tunai & gateway digital</small>
                </div>
                <a href="{{ route('bendahara.pembayaran.index') }}" class="btn btn-sm btn-outline-primary fw-semibold">
                    Semua Transaksi <i class="bi bi-arrow-right"></i>
                </a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr class="small text-muted text-uppercase">
                            <th>Order ID</th>
                            <th>Santri</th>
                            <th>Jenis & Periode</th>
                            <th>Metode</th>
                            <th class="text-end">Nominal</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentPayments as $p)
                            <tr>
                                <td>
                                    <code class="fw-bold small">{{ $p->order_id }}</code>
                                    <div class="text-muted" style="font-size: 0.72rem;">{{ date('d/m/Y H:i', strtotime($p->tgl_bayar)) }}</div>
                                </td>
                                <td>
                                    <div class="fw-semibold text-truncate" style="max-width: 140px;">{{ $p->tagihan->santri->nama_santri ?? '-' }}</div>
                                    <small class="text-muted">{{ $p->tagihan->santri->nis ?? '-' }}</small>
                                </td>
                                <td>
                                    <div class="small fw-semibold text-truncate" style="max-width: 130px;">{{ $p->tagihan->jenisTagihan->nama_tagihan ?? '-' }}</div>
                                    <small class="text-muted">{{ $p->tagihan->bulan ? $p->tagihan->nama_bulan . ' ' : '' }}{{ $p->tagihan->tahun_ajaran }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border small" style="font-size: 0.75rem;">
                                        {{ $p->channel_bayar ?? ucfirst($p->metode_bayar) }}
                                    </span>
                                </td>
                                <td class="text-end fw-bold text-success">Rp {{ number_format($p->jumlah_bayar, 0, ',', '.') }}</td>
                                <td class="text-center">
                                    @if($p->status_transaksi == 'settlement')
                                        <span class="badge bg-success" style="font-size: 0.75rem;"><i class="bi bi-check-circle me-1"></i>Settlement</span>
                                    @elseif($p->status_transaksi == 'pending')
                                        <span class="badge bg-warning text-dark" style="font-size: 0.75rem;"><i class="bi bi-hourglass me-1"></i>Pending</span>
                                    @else
                                        <span class="badge bg-secondary" style="font-size: 0.75rem;">{{ ucfirst($p->status_transaksi) }}</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($p->status_transaksi == 'settlement')
                                        <a href="{{ route('kwitansi.show', $p->id_pembayaran) }}" class="btn btn-sm btn-outline-primary p-1 px-2" title="Lihat Kwitansi Resmi">
                                            <i class="bi bi-receipt"></i>
                                        </a>
                                    @elseif($p->status_transaksi == 'pending')
                                        <form action="{{ route('bendahara.pembayaran.simulate', $p->id_pembayaran) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success p-1 px-2" title="Simulasi Settlement (Demo)">
                                                <i class="bi bi-play-fill"></i>
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

    <!-- Alert Jatuh Tempo (Col-5) -->
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm rounded-3 h-100">
            <div class="card-header bg-white py-3 border-0 d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="mb-0 fw-bold text-danger"><i class="bi bi-alarm text-danger me-2"></i>Alert Tagihan Jatuh Tempo</h6>
                    <small class="text-muted">{{ $totalJatuhTempoSegera }} kewajiban perlu perhatian bendahara</small>
                </div>
                <a href="{{ route('bendahara.laporan.tunggakan') }}" class="btn btn-sm btn-outline-danger fw-semibold">
                    Cek Semua
                </a>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    @if($tagihanMendesak->isNotEmpty())
                        <div class="p-2 px-3 bg-danger-subtle text-danger fw-bold small text-uppercase">
                            <i class="bi bi-exclamation-octagon-fill me-1"></i> Mendesak (≤ 3 Hari / Overdue)
                        </div>
                        @foreach($tagihanMendesak as $tm)
                            <div class="list-group-item d-flex justify-content-between align-items-center py-2 px-3">
                                <div>
                                    <div class="fw-semibold text-dark">{{ $tm->santri->nama_santri ?? '-' }}</div>
                                    <div class="small text-muted">
                                        {{ $tm->jenisTagihan->nama_tagihan ?? '-' }} &bull;
                                        <span class="text-danger fw-bold">Rp {{ number_format($tm->sisa_tagihan, 0, ',', '.') }}</span>
                                    </div>
                                    <small class="text-danger">
                                        <i class="bi bi-calendar-x me-1"></i>Jatuh Tempo: {{ date('d M Y', strtotime($tm->jatuh_tempo)) }}
                                    </small>
                                </div>
                                <div class="d-flex flex-column gap-1 align-items-end">
                                    <div class="d-flex gap-1">
                                        <a href="{{ route('bendahara.pembayaran.kasir.create', ['id_tagihan' => $tm->id_tagihan]) }}" class="btn btn-sm btn-success py-1 px-2" title="Terima Pembayaran">
                                            <i class="bi bi-cash-stack"></i> Kasir
                                        </a>
                                        <form action="{{ route('bendahara.tagihan.kirim-reminder-wa', $tm->id_tagihan) }}" method="POST" class="d-inline" onsubmit="return confirm('Kirim pengingat WhatsApp Gateway ke wali {{ $tm->santri->nama_santri }}?');">
                                            @csrf
                                            <button type="submit" class="btn btn-outline-danger btn-sm py-1 px-2" title="Kirim Reminder WA Gateway">
                                                <i class="bi bi-whatsapp"></i>
                                            </button>
                                        </form>
                                    </div>
                                    @if($tm->santri?->wali?->no_telp)
                                        @php
                                            $waText = urlencode("Assalamu'alaikum Wr. Wb. Yth. Wali dari ananda {$tm->santri->nama_santri}, mengingatkan bahwa tagihan {$tm->jenisTagihan->nama_tagihan} sebesar Rp " . number_format($tm->sisa_tagihan, 0, ',', '.') . " jatuh tempo pada " . date('d/m/Y', strtotime($tm->jatuh_tempo)) . ". Mohon segera diselesaikan. Syukron. - Bendahara PP Darul Muttaqin");
                                        @endphp
                                        <a href="https://wa.me/{{ preg_replace('/^0/', '62', $tm->santri->wali->no_telp) }}?text={{ $waText }}" target="_blank" class="text-success small text-decoration-none" style="font-size: 0.72rem;">
                                            <i class="bi bi-chat-dots me-1"></i>Chat Manual WA
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    @endif

                    @if($tagihanSegera->isNotEmpty())
                        <div class="p-2 px-3 bg-warning-subtle text-warning-emphasis fw-bold small text-uppercase">
                            <i class="bi bi-clock-history me-1"></i> Segera (4 - 7 Hari ke Depan)
                        </div>
                        @foreach($tagihanSegera as $ts)
                            <div class="list-group-item d-flex justify-content-between align-items-center py-2 px-3">
                                <div>
                                    <div class="fw-semibold text-dark">{{ $ts->santri->nama_santri ?? '-' }}</div>
                                    <div class="small text-muted">
                                        {{ $ts->jenisTagihan->nama_tagihan ?? '-' }} &bull;
                                        <span class="text-dark fw-bold">Rp {{ number_format($ts->sisa_tagihan, 0, ',', '.') }}</span>
                                    </div>
                                    <small class="text-muted">
                                        <i class="bi bi-calendar3 me-1"></i>Jatuh Tempo: {{ date('d M Y', strtotime($ts->jatuh_tempo)) }}
                                    </small>
                                </div>
                                <div class="d-flex align-items-center gap-1">
                                    <a href="{{ route('bendahara.pembayaran.kasir.create', ['id_tagihan' => $ts->id_tagihan]) }}" class="btn btn-sm btn-outline-secondary py-1 px-2" title="Terima Bayar">
                                        <i class="bi bi-cash-stack"></i> Kasir
                                    </a>
                                    <form action="{{ route('bendahara.tagihan.kirim-reminder-wa', $ts->id_tagihan) }}" method="POST" class="d-inline" onsubmit="return confirm('Kirim pengingat WhatsApp Gateway ke wali {{ $ts->santri->nama_santri }}?');">
                                        @csrf
                                        <button type="submit" class="btn btn-outline-success btn-sm py-1 px-2" title="Kirim Reminder WA Gateway">
                                            <i class="bi bi-whatsapp"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    @endif

                    @if($tagihanMendesak->isEmpty() && $tagihanSegera->isEmpty())
                        <div class="p-4 text-center text-muted">
                            <i class="bi bi-check-circle-fill fs-2 text-success mb-2 d-block"></i>
                            Tidak ada tagihan yang mendekati jatuh tempo dalam 7 hari ke depan. Semua terpantau aman!
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bottom Section: Kinerja Keuangan per Program Unggulan -->
<div class="row g-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-header bg-white py-3 border-0 d-flex flex-wrap align-items-center justify-content-between gap-2">
                <div>
                    <h6 class="mb-0 fw-bold"><i class="bi bi-mortarboard text-primary me-2"></i>Kinerja Keuangan per Program Unggulan Santri</h6>
                    <small class="text-muted">Analisis kolektabilitas dan rasio pembayaran berdasarkan peminatan santri</small>
                </div>
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-1">
                    {{ $programStats->count() }} Program Unggulan
                </span>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    @foreach($programStats as $prog)
                        <div class="col-md-4">
                            <div class="p-3 border rounded-3 bg-light">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <h6 class="fw-bold text-dark mb-0">{{ $prog->nama_program }}</h6>
                                        <small class="text-muted">{{ $prog->jml_santri }} Santri Terdaftar</small>
                                    </div>
                                    <span class="badge {{ $prog->persen_lunas >= 70 ? 'bg-success' : 'bg-warning text-dark' }}">
                                        {{ $prog->persen_lunas }}% Lunas
                                    </span>
                                </div>

                                <div class="progress mb-3" style="height: 6px;">
                                    <div class="progress-bar {{ $prog->persen_lunas >= 70 ? 'bg-success' : 'bg-warning' }}" role="progressbar" style="width: {{ $prog->persen_lunas }}%"></div>
                                </div>

                                <div class="small">
                                    <div class="d-flex justify-content-between mb-1">
                                        <span class="text-muted">Total Tagihan:</span>
                                        <strong>Rp {{ number_format($prog->total_tagihan, 0, ',', '.') }}</strong>
                                    </div>
                                    <div class="d-flex justify-content-between mb-1">
                                        <span class="text-muted">Terbayar:</span>
                                        <strong class="text-success">Rp {{ number_format($prog->total_terbayar, 0, ',', '.') }}</strong>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <span class="text-muted">Sisa Tunggakan:</span>
                                        <strong class="text-danger">Rp {{ number_format($prog->total_sisa, 0, ',', '.') }}</strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>

<script>
document.addEventListener("DOMContentLoaded", function () {
    // 1. Grafik Batang: Tren Pemasukan 6 Bulan Terakhir
    const monthlyCtx = document.getElementById('monthlyIncomeChart').getContext('2d');
    const monthlyLabels = @json($chartLabels);
    const monthlyValues = @json($chartValues);

    new Chart(monthlyCtx, {
        type: 'bar',
        data: {
            labels: monthlyLabels,
            datasets: [{
                label: 'Pemasukan Kas (Rp)',
                data: monthlyValues,
                backgroundColor: 'rgba(25, 135, 84, 0.85)',
                borderColor: 'rgba(25, 135, 84, 1)',
                borderWidth: 1,
                borderRadius: 6,
                hoverBackgroundColor: 'rgba(20, 108, 67, 1)',
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function (context) {
                            let val = context.raw || 0;
                            return ' Masuk: Rp ' + val.toLocaleString('id-ID');
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function (val) {
                            if (val >= 1000000) {
                                return 'Rp ' + (val / 1000000) + ' Jt';
                            }
                            return 'Rp ' + val.toLocaleString('id-ID');
                        }
                    },
                    grid: { color: '#f1f5f9' }
                },
                x: {
                    grid: { display: false }
                }
            }
        }
    });

    // 2. Donut Chart: Komposisi Komponen Tagihan
    const compCtx = document.getElementById('compositionChart').getContext('2d');
    const donutLabels = @json($donutLabels);
    const donutValues = @json($donutValues);

    new Chart(compCtx, {
        type: 'doughnut',
        data: {
            labels: donutLabels,
            datasets: [{
                data: donutValues,
                backgroundColor: [
                    '#198754', '#0d6efd', '#ffc107', '#0dcaf0', '#6f42c1', '#d63384'
                ],
                borderWidth: 2,
                borderColor: '#ffffff',
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { boxWidth: 12, font: { size: 10 } }
                },
                tooltip: {
                    callbacks: {
                        label: function (context) {
                            let val = context.raw || 0;
                            return ' ' + context.label + ': Rp ' + val.toLocaleString('id-ID');
                        }
                    }
                }
            },
            cutout: '65%'
        }
    });
});
</script>
@endpush
