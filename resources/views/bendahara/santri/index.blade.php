@extends('layouts.app')

@php
    $prefix = Auth::user()->isAdmin() ? 'admin.santri' : 'bendahara.santri';
@endphp

@section('title', 'Data Santri')
@section('page_title', 'Data Santri Pondok Pesantren')

@section('content')
<div class="card border-0 shadow-sm rounded-3">
    <div class="card-header bg-white py-3 border-0 d-flex flex-wrap align-items-center justify-content-between gap-2">
        <div>
            <h6 class="mb-0 fw-bold">Master Santri (SMP & SMA)</h6>
            <small class="text-muted">Total terdata: {{ $santri->total() }} santri</small>
        </div>
        <a href="{{ route($prefix . '.create') }}" class="btn btn-success btn-sm fw-semibold">
            <i class="bi bi-person-plus me-1"></i> Tambah Santri
        </a>
    </div>

    <!-- Filter Bar (AJAX Live Search Enabled) -->
    <div class="p-3 border-top border-bottom bg-light">
        <form action="{{ route($prefix . '.index') }}" method="GET" id="filter_form_santri" class="row g-2">
            <div class="col-md-3">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" id="input_santri_search" class="form-control border-start-0 ps-0" placeholder="Ketik nama, NIS, atau NISN..." value="{{ request('search') }}" autocomplete="off">
                </div>
            </div>
            <div class="col-md-2">
                <select name="tingkat" class="form-select form-select-sm filter-santri-select">
                    <option value="">-- Semua Tingkat --</option>
                    <option value="SMP" {{ request('tingkat') == 'SMP' ? 'selected' : '' }}>SMP (243)</option>
                    <option value="SMA" {{ request('tingkat') == 'SMA' ? 'selected' : '' }}>SMA (113)</option>
                </select>
            </div>
            <div class="col-md-2">
                <input type="text" name="kelas" id="input_santri_kelas" class="form-control form-control-sm" placeholder="Kelas (misal: VII A)..." value="{{ request('kelas') }}" autocomplete="off">
            </div>
            <div class="col-md-3">
                <select name="id_program" class="form-select form-select-sm filter-santri-select">
                    <option value="">-- Semua Program Unggulan --</option>
                    @foreach($programs as $prg)
                        <option value="{{ $prg->id_program }}" {{ request('id_program') == $prg->id_program ? 'selected' : '' }}>
                            {{ $prg->nama_program }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 d-flex gap-1">
                <button type="button" id="btn_reset_santri_filter" class="btn btn-outline-secondary btn-sm w-100" title="Reset Filter">
                    <i class="bi bi-arrow-counterclockwise me-1"></i>Reset
                </button>
            </div>
        </form>
    </div>

    <!-- Container Table & Pagination (AJAX Target) -->
    <div id="santri_table_wrapper" class="position-relative">
        <!-- Loading Overlay -->
        <div id="santri_loading_overlay" class="position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center bg-white bg-opacity-75" style="display: none !important; z-index: 10;">
            <div class="text-center p-3">
                <div class="spinner-border spinner-border-sm text-primary mb-1" role="status"></div>
                <div class="small fw-semibold text-muted">Memuat data santri...</div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr class="small text-muted text-uppercase">
                        <th>NIS / NISN</th>
                        <th>Nama Santri</th>
                        <th>Tingkat & Kelas</th>
                        <th>Program Unggulan</th>
                        <th>Wali Santri & Kontak</th>
                        <th>Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($santri as $s)
                        <tr>
                            <td>
                                <span class="fw-bold text-dark">{{ $s->nis }}</span>
                                <div class="small text-muted">NISN: {{ $s->nisn ?? '-' }}</div>
                            </td>
                            <td>
                                <div class="fw-semibold">{{ $s->nama_santri }}</div>
                                <small class="text-muted"><i class="bi bi-gender-{{ $s->jenis_kelamin == 'L' ? 'male text-primary' : 'female text-danger' }} me-1"></i>{{ $s->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</small>
                            </td>
                            <td>
                                <span class="badge {{ $s->tingkat == 'SMP' ? 'badge-smp' : 'badge-sma' }}">{{ $s->tingkat }}</span>
                                <div class="small text-muted mt-1">{{ $s->kelas }}</div>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border">{{ $s->program->nama_program ?? '-' }}</span>
                            </td>
                            <td>
                                <div class="fw-semibold">{{ $s->wali->nama_lengkap ?? '-' }}</div>
                                <small class="text-muted"><i class="bi bi-whatsapp me-1 text-success"></i>{{ $s->wali->no_telp ?? '-' }}</small>
                            </td>
                            <td>
                                @if($s->status_aktif == 'aktif')
                                    <span class="badge bg-success-subtle text-success border border-success-subtle">Aktif</span>
                                @else
                                    <span class="badge bg-secondary">{{ ucfirst($s->status_aktif) }}</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-1">
                                    <a href="{{ route($prefix . '.edit', $s->nis) }}" class="btn btn-sm btn-outline-secondary" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route($prefix . '.destroy', $s->nis) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus santri ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">Tidak ada data santri ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($santri->hasPages())
            <div class="p-3 border-top d-flex justify-content-end" id="santri_pagination_wrapper">
                {{ $santri->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
    (function() {
        const form = document.getElementById('filter_form_santri');
        const searchInput = document.getElementById('input_santri_search');
        const kelasInput = document.getElementById('input_santri_kelas');
        const filterSelects = form.querySelectorAll('.filter-santri-select');
        const tableWrapper = document.getElementById('santri_table_wrapper');
        const loadingOverlay = document.getElementById('santri_loading_overlay');
        const btnReset = document.getElementById('btn_reset_santri_filter');

        let debounceTimer = null;
        let abortController = null;

        function fetchSantriAjax(url, updateHistory = true) {
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

                const newTable = doc.getElementById('santri_table_wrapper');
                if (newTable && tableWrapper) {
                    tableWrapper.innerHTML = newTable.innerHTML;
                    bindPaginationLinks();
                }

                if (updateHistory) {
                    window.history.pushState(null, '', url);
                }
            })
            .catch(err => {
                if (err.name !== 'AbortError') console.error('AJAX Error:', err);
            })
            .finally(() => {
                const overlay = document.getElementById('santri_loading_overlay');
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
            fetchSantriAjax(targetUrl);
        }

        searchInput.addEventListener('input', function() {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(triggerFilter, 300);
        });

        if (kelasInput) {
            kelasInput.addEventListener('input', function() {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(triggerFilter, 300);
            });
        }

        filterSelects.forEach(sel => sel.addEventListener('change', triggerFilter));

        form.addEventListener('submit', function(e) {
            e.preventDefault();
            triggerFilter();
        });

        btnReset.addEventListener('click', function() {
            searchInput.value = '';
            if (kelasInput) kelasInput.value = '';
            filterSelects.forEach(sel => sel.value = '');
            fetchSantriAjax(form.action);
        });

        function bindPaginationLinks() {
            document.querySelectorAll('#santri_pagination_wrapper a.page-link').forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    if (this.href && !this.href.includes('#')) {
                        fetchSantriAjax(this.href);
                    }
                });
            });
        }

        bindPaginationLinks();

        window.addEventListener('popstate', function() {
            fetchSantriAjax(window.location.href, false);
        });
    })();
</script>
@endpush
</div>
@endsection

