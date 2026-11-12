@extends('layouts.app')

@section('title', 'Laporan Pemasukan Keuangan')
@section('page_title', 'Laporan Rekapitulasi Pemasukan Kas Pesantren')

@section('content')
<div class="card border-0 shadow-sm rounded-3">
    <div class="card-header bg-white py-3 border-0 d-flex flex-wrap align-items-center justify-content-between gap-2">
        <div>
            <h6 class="mb-0 fw-bold"><i class="bi bi-file-earmark-bar-graph text-success me-2"></i>Rekapitulasi Arus Kas Masuk</h6>
            <small class="text-muted" id="pemasukan_header_info">Total Pembayaran Sah: <strong class="text-success fs-6" id="pemasukan_total_nominal">Rp {{ number_format($totalNominal, 0, ',', '.') }}</strong> (<span id="pemasukan_count_label">{{ $pemasukan->count() }}</span> transaksi settlement)</small>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <!-- Dropdown Export Excel & CSV -->
            <div class="btn-group shadow-sm">
                <a href="{{ route('bendahara.laporan.pemasukan.excel', request()->query()) }}" id="btn_export_excel" class="btn btn-success btn-sm fw-semibold">
                    <i class="bi bi-file-earmark-excel-fill me-1"></i> Export Excel (.xls)
                </a>
                <button type="button" class="btn btn-success btn-sm dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-expanded="false" title="Pilih Format Lain">
                    <span class="visually-hidden">Pilih Format Unduh</span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow border-0 small">
                    <li>
                        <a class="dropdown-item py-2 d-flex align-items-center gap-2" href="{{ route('bendahara.laporan.pemasukan.excel', request()->query()) }}" id="btn_export_excel_dropdown">
                            <i class="bi bi-file-earmark-excel text-success fs-5"></i>
                            <div>
                                <div class="fw-bold">Microsoft Excel (.xls)</div>
                                <div class="text-muted" style="font-size: 0.72rem;">Format spreadsheet rapi berkolom &amp; berwana</div>
                            </div>
                        </a>
                    </li>
                    <li><hr class="dropdown-divider my-1"></li>
                    <li>
                        <a class="dropdown-item py-2 d-flex align-items-center gap-2" href="{{ route('bendahara.laporan.pemasukan.csv', request()->query()) }}" id="btn_export_csv">
                            <i class="bi bi-filetype-csv text-primary fs-5"></i>
                            <div>
                                <div class="fw-bold">Format CSV (.csv)</div>
                                <div class="text-muted" style="font-size: 0.72rem;">Format teks koma terpisah (Universal)</div>
                            </div>
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Tombol Cetak Dokumen Resmi -->
            <a href="{{ route('bendahara.laporan.pemasukan.cetak', request()->query()) }}" id="btn_cetak_pdf" target="_blank" class="btn btn-outline-danger btn-sm fw-semibold shadow-sm">
                <i class="bi bi-printer me-1"></i> Cetak Dokumen Resmi
            </a>
        </div>
    </div>

    <!-- Filter Form Multi-Kriteria (Live AJAX Search Enabled) -->
    <div class="p-3 border-top border-bottom bg-light">
        <form action="{{ route('bendahara.laporan.pemasukan') }}" method="GET" id="filter_form_pemasukan" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label small text-muted mb-1">Cari Order ID / Santri / NIS</label>
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" name="search" id="input_pemasukan_search" class="form-control border-start-0 ps-0" placeholder="Ketik order ID, nama, NIS..." value="{{ request('search') }}" autocomplete="off">
                </div>
            </div>
            <div class="col-md-2">
                <label class="form-label small text-muted mb-1">Dari Tanggal</label>
                <input type="date" name="tgl_mulai" class="form-control form-control-sm filter-date" value="{{ request('tgl_mulai') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label small text-muted mb-1">Sampai Tanggal</label>
                <input type="date" name="tgl_selesai" class="form-control form-control-sm filter-date" value="{{ request('tgl_selesai') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label small text-muted mb-1">Tahun Ajaran</label>
                <select name="tahun_ajaran" class="form-select form-select-sm filter-select">
                    <option value="">-- Semua Periode --</option>
                    @foreach($tahunAjaranList as $ta)
                        <option value="{{ $ta->nama_tahun_ajaran }}" {{ request('tahun_ajaran') == $ta->nama_tahun_ajaran ? 'selected' : '' }}>
                            {{ $ta->nama_tahun_ajaran }} ({{ $ta->semester }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-1">
                <label class="form-label small text-muted mb-1">Tingkat</label>
                <select name="tingkat" class="form-select form-select-sm filter-select">
                    <option value="">-- Semua --</option>
                    <option value="SMP" {{ request('tingkat') == 'SMP' ? 'selected' : '' }}>SMP</option>
                    <option value="SMA" {{ request('tingkat') == 'SMA' ? 'selected' : '' }}>SMA</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small text-muted mb-1">Komponen Tagihan</label>
                <select name="id_jenis_tagihan" class="form-select form-select-sm filter-select">
                    <option value="">-- Semua Komponen --</option>
                    @foreach($jenisTagihanList as $j)
                        <option value="{{ $j->id_jenis_tagihan }}" {{ request('id_jenis_tagihan') == $j->id_jenis_tagihan ? 'selected' : '' }}>
                            {{ $j->nama_tagihan }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small text-muted mb-1">Metode Bayar</label>
                <select name="metode_bayar" class="form-select form-select-sm filter-select">
                    <option value="">-- Semua Metode --</option>
                    <option value="gateway" {{ request('metode_bayar') == 'gateway' ? 'selected' : '' }}>Gateway Midtrans</option>
                    <option value="tunai" {{ request('metode_bayar') == 'tunai' ? 'selected' : '' }}>Tunai / Kasir</option>
                </select>
            </div>
            <div class="col-md-1 d-flex gap-1">
                <button type="button" id="btn_reset_pemasukan" class="btn btn-outline-secondary btn-sm w-100" title="Reset Filter">
                    <i class="bi bi-arrow-counterclockwise"></i> Reset
                </button>
            </div>
        </form>
    </div>

    <!-- Wrapper Table & Live AJAX Target -->
    <div id="pemasukan_table_wrapper" class="position-relative">
        <!-- Loading Overlay -->
        <div id="pemasukan_loading_overlay" class="position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center bg-white bg-opacity-75" style="display: none !important; z-index: 10;">
            <div class="text-center p-3">
                <div class="spinner-border spinner-border-sm text-success mb-1" role="status"></div>
                <div class="small fw-semibold text-muted">Menyaring pemasukan kas...</div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr class="small text-muted text-uppercase">
                        <th>No</th>
                        <th>No Kwitansi & Order</th>
                        <th>Tanggal Bayar</th>
                        <th>Santri & NIS</th>
                        <th>Program & Kelas</th>
                        <th>Komponen Tagihan</th>
                        <th>Channel / Metode</th>
                        <th class="text-end">Jumlah Bersih Kas</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pemasukan as $index => $p)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>
                                <span class="badge bg-light text-dark border fw-bold font-monospace">{{ $p->kwitansi->no_kwitansi ?? '-' }}</span>
                                <div class="text-muted small" style="font-size: 0.72rem;">{{ $p->order_id }}</div>
                            </td>
                            <td class="small">
                                <div>{{ date('d/m/Y', strtotime($p->tgl_bayar)) }}</div>
                                <small class="text-muted">{{ date('H:i', strtotime($p->tgl_bayar)) }} WIB</small>
                            </td>
                            <td>
                                <div class="fw-semibold">{{ $p->tagihan->santri->nama_santri ?? '-' }}</div>
                                <small class="text-muted">NIS: {{ $p->tagihan->santri->nis ?? '-' }}</small>
                            </td>
                            <td>
                                <div>
                                    <span class="badge {{ ($p->tagihan->santri->tingkat ?? '') == 'SMP' ? 'badge-smp' : 'badge-sma' }}">
                                        {{ $p->tagihan->santri->tingkat ?? '-' }}
                                    </span>
                                    {{ $p->tagihan->santri->kelas ?? '' }}
                                </div>
                                <small class="text-muted">{{ $p->tagihan->santri->program->nama_program ?? '-' }}</small>
                            </td>
                            <td>
                                <div class="fw-semibold">{{ $p->tagihan->jenisTagihan->nama_tagihan ?? '-' }}</div>
                                <small class="text-muted">{{ $p->tagihan->bulan ? $p->tagihan->nama_bulan . ' ' : '' }}{{ $p->tagihan->tahun_ajaran }}</small>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border">{{ $p->channel_bayar ?? ucfirst($p->metode_bayar) }}</span>
                            </td>
                            <td class="text-end fw-bold text-success fs-6">Rp {{ number_format($p->jumlah_bayar, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox fs-2 d-block mb-2 text-muted"></i>
                                Tidak ada data pemasukan pada periode dan kriteria pencarian yang dipilih.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                @if($pemasukan->isNotEmpty())
                    <tfoot class="table-light fw-bold">
                        <tr>
                            <td colspan="7" class="text-end text-uppercase">Total Penerimaan Kas Sah:</td>
                            <td class="text-end text-success fs-5">Rp {{ number_format($totalNominal, 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    let searchTimer = null;
    const form = document.getElementById('filter_form_pemasukan');
    const searchInput = document.getElementById('input_pemasukan_search');
    const wrapper = document.getElementById('pemasukan_table_wrapper');
    const overlay = document.getElementById('pemasukan_loading_overlay');
    const resetBtn = document.getElementById('btn_reset_pemasukan');

    function syncExportUrls() {
        if (!form) return;
        const formData = new FormData(form);
        const params = new URLSearchParams();
        for (const [key, val] of formData.entries()) {
            if (val.trim() !== '') {
                params.append(key, val.trim());
            }
        }
        const qs = params.toString() ? '?' + params.toString() : '';
        const excelBtn = document.getElementById('btn_export_excel');
        const excelDrop = document.getElementById('btn_export_excel_dropdown');
        const csvBtn = document.getElementById('btn_export_csv');
        const pdfBtn = document.getElementById('btn_cetak_pdf');

        const baseExcel = "{{ route('bendahara.laporan.pemasukan.excel') }}";
        const baseCsv = "{{ route('bendahara.laporan.pemasukan.csv') }}";
        const basePdf = "{{ route('bendahara.laporan.pemasukan.cetak') }}";

        if (excelBtn) excelBtn.setAttribute('href', baseExcel + qs);
        if (excelDrop) excelDrop.setAttribute('href', baseExcel + qs);
        if (csvBtn) csvBtn.setAttribute('href', baseCsv + qs);
        if (pdfBtn) pdfBtn.setAttribute('href', basePdf + qs);
    }

    function buildFilterUrl() {
        const formData = new FormData(form);
        const params = new URLSearchParams();
        for (const [key, val] of formData.entries()) {
            if (val.trim() !== '') {
                params.append(key, val.trim());
            }
        }
        return form.action + (params.toString() ? '?' + params.toString() : '');
    }

    function fetchPemasukan(url) {
        if (overlay) overlay.style.setProperty('display', 'flex', 'important');
        syncExportUrls();

        fetch(url, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => {
            if (!res.ok) throw new Error('HTTP ' + res.status);
            return res.text();
        })
        .then(html => {
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const newWrapper = doc.getElementById('pemasukan_table_wrapper');
            const newHeader = doc.getElementById('pemasukan_header_info');

            if (newWrapper && wrapper) {
                wrapper.innerHTML = newWrapper.innerHTML;
            }
            if (newHeader && document.getElementById('pemasukan_header_info')) {
                document.getElementById('pemasukan_header_info').innerHTML = newHeader.innerHTML;
            }

            syncExportUrls();
            window.history.pushState({}, '', url);
        })
        .catch(err => {
            console.error('AJAX Load pemasukan error:', err);
        })
        .finally(() => {
            const activeOverlay = document.getElementById('pemasukan_loading_overlay');
            if (activeOverlay) activeOverlay.style.setProperty('display', 'none', 'important');
        });
    }

    syncExportUrls();

    if (searchInput) {
        searchInput.addEventListener('input', function () {
            syncExportUrls();
            clearTimeout(searchTimer);
            searchTimer = setTimeout(() => {
                fetchPemasukan(buildFilterUrl());
            }, 300);
        });
    }

    const filterInputs = form.querySelectorAll('.filter-select, .filter-date');
    filterInputs.forEach(el => {
        el.addEventListener('change', function () {
            syncExportUrls();
            fetchPemasukan(buildFilterUrl());
        });
    });

    if (resetBtn) {
        resetBtn.addEventListener('click', function () {
            form.reset();
            if (searchInput) searchInput.value = '';
            syncExportUrls();
            fetchPemasukan(form.action);
        });
    }

    window.addEventListener('popstate', function () {
        fetchPemasukan(window.location.href);
    });
});
</script>
@endpush
