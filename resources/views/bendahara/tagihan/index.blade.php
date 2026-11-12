@extends('layouts.app')

@section('title', 'Data Tagihan Santri')
@section('page_title', 'Manajemen Tagihan & Piutang Santri')

@section('content')
<div class="card border-0 shadow-sm rounded-3">
    <div class="card-header bg-white py-3 border-0 d-flex flex-wrap align-items-center justify-content-between gap-2">
        <div>
            <h6 class="mb-0 fw-bold"><i class="bi bi-receipt text-primary me-2"></i>Daftar Tagihan & Kewajiban Santri</h6>
            <small class="text-muted" id="tagihan_total_label">Total: {{ $tagihan->total() }} catatan kewajiban pembayaran santri</small>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('bendahara.tagihan.generate.show') }}" class="btn btn-warning text-dark btn-sm fw-semibold shadow-sm">
                <i class="bi bi-lightning-charge me-1"></i> Generator Massal
            </a>
            <a href="{{ route('bendahara.tagihan.create') }}" class="btn btn-success btn-sm fw-semibold shadow-sm">
                <i class="bi bi-plus-circle me-1"></i> Tagihan Tunggal
            </a>
        </div>
    </div>

    <!-- Filter Bar Multi-Kriteria (AJAX Live Search Enabled) -->
    <div class="p-3 border-top border-bottom bg-light">
        <form action="{{ route('bendahara.tagihan.index') }}" method="GET" id="filter_form_tagihan" class="row g-2 align-items-end">
            <div class="col-md-2">
                <label class="form-label small text-muted mb-1">Cari Santri / NIS</label>
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" name="search" id="input_tagihan_search" class="form-control border-start-0 ps-0" placeholder="Ketik nama / NIS..." value="{{ request('search') }}" autocomplete="off">
                </div>
            </div>
            <div class="col-md-2">
                <label class="form-label small text-muted mb-1">Status Pembayaran</label>
                <select name="status_tagihan" class="form-select form-select-sm filter-select">
                    <option value="">-- Semua Status --</option>
                    <option value="belum_bayar" {{ request('status_tagihan') == 'belum_bayar' ? 'selected' : '' }}>Belum Bayar</option>
                    <option value="dicicil" {{ request('status_tagihan') == 'dicicil' ? 'selected' : '' }}>Dicicil</option>
                    <option value="lunas" {{ request('status_tagihan') == 'lunas' ? 'selected' : '' }}>Lunas</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small text-muted mb-1">Komponen Tagihan</label>
                <select name="id_jenis_tagihan" class="form-select form-select-sm filter-select">
                    <option value="">-- Semua Jenis --</option>
                    @foreach($jenisTagihanList as $j)
                        <option value="{{ $j->id_jenis_tagihan }}" {{ request('id_jenis_tagihan') == $j->id_jenis_tagihan ? 'selected' : '' }}>
                            {{ $j->nama_tagihan }}
                        </option>
                    @endforeach
                </select>
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
                <label class="form-label small text-muted mb-1">Program Unggulan</label>
                <select name="id_program" class="form-select form-select-sm filter-select">
                    <option value="">-- Semua Program --</option>
                    @foreach($programList as $prg)
                        <option value="{{ $prg->id_program }}" {{ request('id_program') == $prg->id_program ? 'selected' : '' }}>
                            {{ $prg->nama_program }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-1 d-flex gap-1">
                <button type="button" id="btn_reset_tagihan_filter" class="btn btn-outline-secondary btn-sm w-100" title="Reset Semua Filter">
                    <i class="bi bi-arrow-counterclockwise me-1"></i>Reset
                </button>
            </div>
        </form>
    </div>

    <!-- Container Table & Pagination (Live AJAX Target) -->
    <div id="tagihan_table_wrapper" class="position-relative">
        <!-- Loading Overlay -->
        <div id="tagihan_loading_overlay" class="position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center bg-white bg-opacity-75" style="display: none !important; z-index: 10;">
            <div class="text-center p-3">
                <div class="spinner-border spinner-border-sm text-primary mb-1" role="status"></div>
                <div class="small fw-semibold text-muted">Memuat data...</div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr class="small text-muted text-uppercase">
                        <th>Santri & Kelas</th>
                        <th>Komponen Tagihan & Periode</th>
                        <th class="text-end">Nominal Pokok</th>
                        <th class="text-end">Terbayar</th>
                        <th class="text-end">Sisa Tagihan</th>
                        <th>Jatuh Tempo</th>
                        <th>Status</th>
                        <th class="text-center">Aksi & Rincian</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tagihan as $t)
                        <tr>
                            <td>
                                <div class="fw-bold text-dark">{{ $t->santri->nama_santri ?? '-' }}</div>
                                <small class="text-muted">
                                    NIS: {{ $t->nis }} &bull; 
                                    <span class="badge {{ ($t->santri->tingkat ?? '') == 'SMP' ? 'badge-smp' : 'badge-sma' }}">{{ $t->santri->tingkat ?? '-' }}</span> 
                                    {{ $t->santri->kelas ?? '' }}
                                </small>
                                <div class="small text-muted">{{ $t->santri->program->nama_program ?? '-' }}</div>
                            </td>
                            <td>
                                <div class="fw-semibold text-primary">{{ $t->jenisTagihan->nama_tagihan ?? '-' }}</div>
                                <small class="text-muted">
                                    {{ $t->bulan ? $t->nama_bulan . ' ' : '' }}{{ $t->tahun_ajaran }}
                                </small>
                            </td>
                            <td class="text-end fw-bold text-dark">Rp {{ number_format($t->nominal_tagihan, 0, ',', '.') }}</td>
                            <td class="text-end text-success fw-semibold">
                                Rp {{ number_format($t->total_terbayar, 0, ',', '.') }}
                                @if($t->pembayaran->count() > 0)
                                    <div class="small text-muted font-monospace" style="font-size: 0.7rem;">({{ $t->pembayaran->count() }}x bayar)</div>
                                @endif
                            </td>
                            <td class="text-end text-danger fw-bold fs-6">Rp {{ number_format($t->sisa_tagihan, 0, ',', '.') }}</td>
                            <td class="small">
                                @if($t->jatuh_tempo && strtotime($t->jatuh_tempo) < time() && $t->status_tagihan != 'lunas')
                                    <span class="badge bg-danger">Lewat: {{ date('d/m/Y', strtotime($t->jatuh_tempo)) }}</span>
                                @elseif($t->jatuh_tempo)
                                    <span class="text-muted">{{ date('d/m/Y', strtotime($t->jatuh_tempo)) }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                {!! $t->status_badge !!}
                                @if($t->kategori_info && $t->status_tagihan != 'lunas')
                                    <div class="mt-1" style="font-size: 0.72rem;">
                                        {!! $t->kategori_badge !!}
                                    </div>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-1">
                                    <!-- Tombol Rincian & Riwayat Cicilan -->
                                    <a href="{{ route('bendahara.tagihan.show', $t->id_tagihan) }}" class="btn btn-sm btn-outline-primary" title="Rincian Tagihan & Riwayat Cicilan">
                                        <i class="bi bi-journal-text"></i>
                                    </a>

                                    @if($t->status_tagihan != 'lunas')
                                        <!-- Tombol Bayar Tunai Kasir -->
                                        <a href="{{ route('bendahara.pembayaran.kasir.create', ['id_tagihan' => $t->id_tagihan]) }}" class="btn btn-sm btn-success" title="Bayar Kasir Tunai">
                                            <i class="bi bi-cash-stack"></i>
                                        </a>
                                    @endif

                                    @if($t->pembayaran->where('status_transaksi', 'settlement')->isEmpty())
                                        <form action="{{ route('bendahara.tagihan.destroy', $t->id_tagihan) }}" method="POST" onsubmit="return confirm('Hapus tagihan ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox fs-2 d-block mb-2 text-muted"></i>
                                Tidak ada data tagihan yang sesuai dengan filter pencarian.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($tagihan->hasPages())
            <div class="p-3 border-top d-flex justify-content-end" id="tagihan_pagination_wrapper">
                {{ $tagihan->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
    (function() {
        const form = document.getElementById('filter_form_tagihan');
        const searchInput = document.getElementById('input_tagihan_search');
        const filterSelects = form.querySelectorAll('.filter-select');
        const tableWrapper = document.getElementById('tagihan_table_wrapper');
        const loadingOverlay = document.getElementById('tagihan_loading_overlay');
        const btnReset = document.getElementById('btn_reset_tagihan_filter');
        const totalLabel = document.getElementById('tagihan_total_label');

        let debounceTimer = null;
        let abortController = null;

        function fetchTagihanAjax(url, updateHistory = true) {
            if (abortController) abortController.abort();
            abortController = new AbortController();

            loadingOverlay.style.setProperty('display', 'flex', 'important');

            fetch(url, {
                signal: abortController.signal,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'text/html, application/xhtml+xml'
                }
            })
            .then(res => res.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');

                const newTable = doc.getElementById('tagihan_table_wrapper');
                const newTotal = doc.getElementById('tagihan_total_label');

                if (newTable && tableWrapper) {
                    tableWrapper.innerHTML = newTable.innerHTML;
                    bindPaginationLinks();
                }

                if (newTotal && totalLabel) {
                    totalLabel.textContent = newTotal.textContent;
                }

                if (updateHistory) {
                    window.history.pushState(null, '', url);
                }
            })
            .catch(err => {
                if (err.name !== 'AbortError') {
                    console.error('AJAX Error:', err);
                }
            })
            .finally(() => {
                const overlay = document.getElementById('tagihan_loading_overlay');
                if (overlay) overlay.style.setProperty('display', 'none', 'important');
            });
        }

        function triggerFilter() {
            const formData = new FormData(form);
            const params = new URLSearchParams();

            for (const [key, value] of formData.entries()) {
                if (value && value.trim() !== '') {
                    params.append(key, value.trim());
                }
            }

            const targetUrl = `${form.action}?${params.toString()}`;
            fetchTagihanAjax(targetUrl);
        }

        // Live search on typing with 300ms debounce
        searchInput.addEventListener('input', function() {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(triggerFilter, 300);
        });

        // Instant filter on select change
        filterSelects.forEach(sel => {
            sel.addEventListener('change', triggerFilter);
        });

        // Prevent normal form submit
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            triggerFilter();
        });

        // Reset button
        btnReset.addEventListener('click', function() {
            searchInput.value = '';
            filterSelects.forEach(sel => sel.value = '');
            fetchTagihanAjax(form.action);
        });

        // Pagination links binding
        function bindPaginationLinks() {
            document.querySelectorAll('#tagihan_pagination_wrapper a.page-link').forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    if (this.href && !this.href.includes('#')) {
                        fetchTagihanAjax(this.href);
                    }
                });
            });
        }

        bindPaginationLinks();

        // Browser back/forward button support
        window.addEventListener('popstate', function() {
            fetchTagihanAjax(window.location.href, false);
        });
    })();
</script>
@endpush
</div>
@endsection
