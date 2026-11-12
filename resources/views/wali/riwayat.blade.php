@extends('layouts.app')

@section('title', 'Riwayat Pembayaran Lengkap')
@section('page_title', 'Riwayat Pembayaran Lengkap')

@section('content')

{{-- ============================================================ --}}
{{-- HEADER & FILTER --}}
{{-- ============================================================ --}}
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-4">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
            <div>
                <h5 class="fw-bold mb-0"><i class="bi bi-clock-history text-primary me-2"></i>Riwayat Semua Transaksi</h5>
                <small class="text-muted">Seluruh transaksi pembayaran untuk ananda santri Anda.</small>
            </div>
            <a href="{{ route('wali.dashboard') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i> Kembali Dashboard
            </a>
        </div>

        {{-- Filter (Live AJAX Search Enabled) --}}
        <form method="GET" action="{{ route('wali.riwayat') }}" id="filter_form_riwayat">
            <div class="row g-2 align-items-end">
                <div class="col-md-4">
                    <label class="form-label small fw-semibold">Cari Transaksi</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" name="search" id="input_riwayat_search" class="form-control border-start-0 ps-0" placeholder="Ketik jenis tagihan / order ID..." value="{{ request('search') }}" autocomplete="off">
                    </div>
                </div>
                @if($santriList->count() > 1)
                <div class="col-md-3">
                    <label class="form-label small fw-semibold">Filter per Santri</label>
                    <select name="nis" class="form-select form-select-sm filter-select">
                        <option value="">— Semua Santri —</option>
                        @foreach($santriList as $s)
                            <option value="{{ $s->nis }}" {{ request('nis') == $s->nis ? 'selected' : '' }}>
                                {{ $s->nama_santri }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @endif
                <div class="col-md-3">
                    <label class="form-label small fw-semibold">Filter Status</label>
                    <select name="status" class="form-select form-select-sm filter-select">
                        <option value="">— Semua Status —</option>
                        <option value="settlement" {{ request('status') == 'settlement' ? 'selected' : '' }}>✅ Berhasil</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>⏳ Pending</option>
                        <option value="expire" {{ request('status') == 'expire' ? 'selected' : '' }}>❌ Expire/Gagal</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex gap-1">
                    <button type="button" id="btn_reset_riwayat" class="btn btn-outline-secondary btn-sm w-100">
                        <i class="bi bi-arrow-counterclockwise me-1"></i> Reset
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- ============================================================ --}}
{{-- TABEL RIWAYAT (LIVE AJAX TARGET) --}}
{{-- ============================================================ --}}
<div class="card border-0 shadow-sm rounded-4 position-relative" id="riwayat_table_wrapper">
    <!-- Loading Overlay -->
    <div id="riwayat_loading_overlay" class="position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center bg-white bg-opacity-75 rounded-4" style="display: none !important; z-index: 10;">
        <div class="text-center p-3">
            <div class="spinner-border spinner-border-sm text-primary mb-1" role="status"></div>
            <div class="small fw-semibold text-muted">Menyaring riwayat transaksi...</div>
        </div>
    </div>

    <div class="card-header bg-white py-3 px-4 border-0 d-flex align-items-center justify-content-between">
        <h6 class="fw-bold mb-0" id="riwayat_total_label">Daftar Transaksi ({{ $riwayat->total() }} total)</h6>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr class="small text-muted text-uppercase">
                    <th class="ps-4">#</th>
                    <th>No Kwitansi</th>
                    <th>Tanggal Bayar</th>
                    <th>Santri</th>
                    <th>Komponen Biaya</th>
                    <th>Metode</th>
                    <th>Jumlah</th>
                    <th>Status</th>
                    <th class="text-center pe-4">Kwitansi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($riwayat as $i => $p)
                    <tr>
                        <td class="ps-4 text-muted small">{{ $riwayat->firstItem() + $i }}</td>
                        <td>
                            @if($p->kwitansi)
                                <span class="badge bg-light text-dark border font-monospace">{{ $p->kwitansi->no_kwitansi }}</span>
                            @else
                                <span class="text-muted small">-</span>
                            @endif
                        </td>
                        <td class="small">{{ date('d/m/Y H:i', strtotime($p->tgl_bayar)) }}</td>
                        <td class="fw-semibold">{{ $p->tagihan->santri->nama_santri }}</td>
                        <td>{{ $p->tagihan->jenisTagihan->nama_tagihan }}</td>
                        <td>
                            <span class="badge bg-light text-dark border">
                                {{ $p->channel_bayar ?? ucfirst($p->metode_bayar) }}
                            </span>
                        </td>
                        <td class="fw-bold text-success">Rp {{ number_format($p->jumlah_bayar, 0, ',', '.') }}</td>
                        <td>
                            @if($p->status_transaksi == 'settlement')
                                <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Berhasil</span>
                            @elseif($p->status_transaksi == 'pending')
                                <span class="badge bg-warning text-dark"><i class="bi bi-hourglass me-1"></i>Pending</span>
                            @elseif(in_array($p->status_transaksi, ['expire','cancel','failure']))
                                <span class="badge bg-danger">{{ ucfirst($p->status_transaksi) }}</span>
                            @else
                                <span class="badge bg-secondary">{{ ucfirst($p->status_transaksi) }}</span>
                            @endif
                        </td>
                        <td class="text-center pe-4">
                            @if($p->kwitansi)
                                <a href="{{ route('kwitansi.show', $p->id_pembayaran) }}" class="btn btn-sm btn-outline-success" target="_blank">
                                    <i class="bi bi-printer me-1"></i> Kwitansi
                                </a>
                            @else
                                <span class="text-muted small">-</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center py-5 text-muted">
                            <i class="bi bi-inbox fs-2 d-block mb-2 text-secondary"></i>
                            Tidak ada riwayat transaksi yang sesuai kriteria pencarian.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($riwayat->hasPages())
        <div class="card-footer bg-white border-0 d-flex justify-content-center py-3">
            {{ $riwayat->links() }}
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    let searchTimer = null;
    const form = document.getElementById('filter_form_riwayat');
    const searchInput = document.getElementById('input_riwayat_search');
    const wrapper = document.getElementById('riwayat_table_wrapper');
    const overlay = document.getElementById('riwayat_loading_overlay');
    const resetBtn = document.getElementById('btn_reset_riwayat');

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

    function fetchRiwayat(url) {
        if (overlay) overlay.style.setProperty('display', 'flex', 'important');

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
            const newWrapper = doc.getElementById('riwayat_table_wrapper');

            if (newWrapper && wrapper) {
                wrapper.innerHTML = newWrapper.innerHTML;
            }

            window.history.pushState({}, '', url);
            bindPagination();
        })
        .catch(err => {
            console.error('AJAX Load riwayat error:', err);
        })
        .finally(() => {
            const activeOverlay = document.getElementById('riwayat_loading_overlay');
            if (activeOverlay) activeOverlay.style.setProperty('display', 'none', 'important');
        });
    }

    function bindPagination() {
        if (!wrapper) return;
        const paginationLinks = wrapper.querySelectorAll('.pagination a');
        paginationLinks.forEach(link => {
            link.addEventListener('click', function (e) {
                e.preventDefault();
                fetchRiwayat(this.getAttribute('href'));
            });
        });
    }

    if (searchInput) {
        searchInput.addEventListener('input', function () {
            clearTimeout(searchTimer);
            searchTimer = setTimeout(() => {
                fetchRiwayat(buildFilterUrl());
            }, 300);
        });
    }

    const filterSelects = form.querySelectorAll('.filter-select');
    filterSelects.forEach(sel => {
        sel.addEventListener('change', function () {
            fetchRiwayat(buildFilterUrl());
        });
    });

    if (resetBtn) {
        resetBtn.addEventListener('click', function () {
            form.reset();
            if (searchInput) searchInput.value = '';
            fetchRiwayat(form.action);
        });
    }

    bindPagination();

    window.addEventListener('popstate', function () {
        fetchRiwayat(window.location.href);
    });
});
</script>
@endpush
