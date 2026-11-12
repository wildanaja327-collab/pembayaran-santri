@extends('layouts.app')

@section('title', 'Log Aktivitas Sistem')
@section('page_title', 'Log Aktivitas & Audit Trail Keamanan')

@section('content')
<div class="card border-0 shadow-sm rounded-3">
    <div class="card-header bg-white py-3 border-0 d-flex flex-wrap align-items-center justify-content-between gap-2">
        <div>
            <h6 class="mb-0 fw-bold"><i class="bi bi-shield-check text-primary me-2"></i>Audit Trail Log Aktivitas</h6>
            <small class="text-muted">Mencatat riwayat autentikasi, mutasi data, dan interaksi pengguna sistem untuk kepatuhan & transparansi.</small>
        </div>
        <span class="badge bg-light text-dark border">
            Total Entri: {{ $logs->total() }}
        </span>
    </div>

    <!-- Filter Bar (Live AJAX Search Enabled) -->
    <div class="p-3 border-top border-bottom bg-light">
        <form action="{{ route('admin.activity-logs') }}" method="GET" id="filter_form_logs" class="row g-2 align-items-center">
            <div class="col-md-6">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" name="search" id="input_logs_search" class="form-control border-start-0 ps-0" placeholder="Cari uraian aktivitas, username, atau IP address..." value="{{ request('search') }}" autocomplete="off">
                </div>
            </div>
            <div class="col-md-4">
                <select name="role" class="form-select form-select-sm filter-select">
                    <option value="">-- Semua Role --</option>
                    <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Administrator</option>
                    <option value="bendahara" {{ request('role') == 'bendahara' ? 'selected' : '' }}>Bendahara</option>
                    <option value="wali_santri" {{ request('role') == 'wali_santri' ? 'selected' : '' }}>Wali Santri</option>
                    <option value="system" {{ request('role') == 'system' ? 'selected' : '' }}>Sistem Otomatis</option>
                </select>
            </div>
            <div class="col-md-2 d-flex gap-1">
                <button type="button" id="btn_reset_logs" class="btn btn-outline-secondary btn-sm w-100" title="Reset Saringan">
                    <i class="bi bi-arrow-counterclockwise me-1"></i>Reset
                </button>
            </div>
        </form>
    </div>

    <!-- Wrapper Table & Live AJAX Target -->
    <div id="logs_table_wrapper" class="position-relative">
        <!-- Loading Overlay -->
        <div id="logs_loading_overlay" class="position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center bg-white bg-opacity-75" style="display: none !important; z-index: 10;">
            <div class="text-center p-3">
                <div class="spinner-border spinner-border-sm text-primary mb-1" role="status"></div>
                <div class="small fw-semibold text-muted">Menyaring log aktivitas...</div>
            </div>
        </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr class="small text-muted text-uppercase">
                    <th>Waktu (WIB)</th>
                    <th>Pengguna</th>
                    <th>Jenis Aksi</th>
                    <th>Deskripsi Aktivitas</th>
                    <th>Alamat IP</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                    <tr>
                        <td class="small text-nowrap">
                            <div class="fw-semibold text-dark">{{ $log->created_at->format('d/m/Y H:i:s') }}</div>
                            <small class="text-muted">{{ $log->created_at->diffForHumans() }}</small>
                        </td>
                        <td>
                            <div class="fw-semibold text-dark">{{ $log->username ?? 'Tamu/Sistem' }}</div>
                            @if($log->role == 'admin')
                                <span class="badge bg-dark" style="font-size: 0.65rem;">Admin</span>
                            @elseif($log->role == 'bendahara')
                                <span class="badge bg-success" style="font-size: 0.65rem;">Bendahara</span>
                            @elseif($log->role == 'wali_santri')
                                <span class="badge bg-info text-dark" style="font-size: 0.65rem;">Wali</span>
                            @else
                                <span class="badge bg-secondary" style="font-size: 0.65rem;">System</span>
                            @endif
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
                            <span class="badge {{ $badgeClass }} font-monospace" style="font-size: 0.72rem;">
                                {{ $log->action }}
                            </span>
                        </td>
                        <td>
                            <div class="text-dark small">{{ $log->description }}</div>
                        </td>
                        <td class="small text-muted font-monospace">
                            {{ $log->ip_address ?? '-' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-4 text-muted">
                            <i class="bi bi-clock-history fs-3 d-block mb-1"></i>
                            Belum ada catatan log aktivitas yang tercatat.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

        @if($logs->hasPages())
            <div class="card-footer bg-white py-3 border-0">
                {{ $logs->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    let searchTimer = null;
    const form = document.getElementById('filter_form_logs');
    const searchInput = document.getElementById('input_logs_search');
    const wrapper = document.getElementById('logs_table_wrapper');
    const overlay = document.getElementById('logs_loading_overlay');
    const resetBtn = document.getElementById('btn_reset_logs');

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

    function fetchLogs(url) {
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
            const newWrapper = doc.getElementById('logs_table_wrapper');

            if (newWrapper && wrapper) {
                wrapper.innerHTML = newWrapper.innerHTML;
            }

            window.history.pushState({}, '', url);
            bindPagination();
        })
        .catch(err => {
            console.error('AJAX Load logs error:', err);
        })
        .finally(() => {
            const activeOverlay = document.getElementById('logs_loading_overlay');
            if (activeOverlay) activeOverlay.style.setProperty('display', 'none', 'important');
        });
    }

    function bindPagination() {
        if (!wrapper) return;
        const paginationLinks = wrapper.querySelectorAll('.pagination a');
        paginationLinks.forEach(link => {
            link.addEventListener('click', function (e) {
                e.preventDefault();
                fetchLogs(this.getAttribute('href'));
            });
        });
    }

    if (searchInput) {
        searchInput.addEventListener('input', function () {
            clearTimeout(searchTimer);
            searchTimer = setTimeout(() => {
                fetchLogs(buildFilterUrl());
            }, 300);
        });
    }

    const filterSelects = form.querySelectorAll('.filter-select');
    filterSelects.forEach(sel => {
        sel.addEventListener('change', function () {
            fetchLogs(buildFilterUrl());
        });
    });

    if (resetBtn) {
        resetBtn.addEventListener('click', function () {
            form.reset();
            if (searchInput) searchInput.value = '';
            fetchLogs(form.action);
        });
    }

    bindPagination();

    window.addEventListener('popstate', function () {
        fetchLogs(window.location.href);
    });
});
</script>
@endpush

