@extends('layouts.app')

@section('title', 'Manajemen Pengguna')
@section('page_title', 'Manajemen Pengguna Sistem (Users)')

@section('content')
<div class="card border-0 shadow-sm rounded-3">
    <div class="card-header bg-white py-3 border-0 d-flex flex-wrap align-items-center justify-content-between gap-2">
        <div>
            <h6 class="mb-0 fw-bold">Daftar Akun Pengguna Sistem</h6>
            <small class="text-muted">Kelola kredensial, hak akses (RBAC), relasi wali-santri, dan status keaktifan akun.</small>
        </div>
        <button type="button" class="btn btn-primary btn-sm fw-semibold" data-bs-toggle="modal" data-bs-target="#modalTambahUser">
            <i class="bi bi-person-plus me-1"></i> Tambah Pengguna Baru
        </button>
    </div>

    <!-- Filter Bar (Live AJAX Search Enabled) -->
    <div class="p-3 border-top border-bottom bg-light">
        <form action="{{ route('admin.users.index') }}" method="GET" id="filter_form_users" class="row g-2 align-items-center">
            <div class="col-md-5">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" name="search" id="input_users_search" class="form-control border-start-0 ps-0" placeholder="Cari username, nama lengkap, kontak..." value="{{ request('search') }}" autocomplete="off">
                </div>
            </div>
            <div class="col-md-3">
                <select name="role" class="form-select form-select-sm filter-select">
                    <option value="">-- Semua Role Akses --</option>
                    <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Administrator Sistem</option>
                    <option value="bendahara" {{ request('role') == 'bendahara' ? 'selected' : '' }}>Bendahara Keuangan</option>
                    <option value="wali_santri" {{ request('role') == 'wali_santri' ? 'selected' : '' }}>Wali Santri</option>
                </select>
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select form-select-sm filter-select">
                    <option value="">-- Semua Status --</option>
                    <option value="aktif" {{ request('status') == 'aktif' ? 'selected' : '' }}>Akun Aktif</option>
                    <option value="nonaktif" {{ request('status') == 'nonaktif' ? 'selected' : '' }}>Akun Nonaktif</option>
                </select>
            </div>
            <div class="col-md-2 d-flex gap-1">
                <button type="button" id="btn_reset_users" class="btn btn-outline-secondary btn-sm w-100" title="Reset Filter">
                    <i class="bi bi-arrow-counterclockwise me-1"></i>Reset
                </button>
            </div>
        </form>
    </div>

    <!-- Wrapper Table & Live AJAX Target -->
    <div id="users_table_wrapper" class="position-relative">
        <!-- Loading Overlay -->
        <div id="users_loading_overlay" class="position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center bg-white bg-opacity-75" style="display: none !important; z-index: 10;">
            <div class="text-center p-3">
                <div class="spinner-border spinner-border-sm text-primary mb-1" role="status"></div>
                <div class="small fw-semibold text-muted">Menyaring daftar pengguna...</div>
            </div>
        </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr class="small text-muted text-uppercase">
                    <th>Username</th>
                    <th>Nama Lengkap & Kontak</th>
                    <th>Role / Otoritas</th>
                    <th>Status Akun</th>
                    <th>Santri Terkait (Klik Detail)</th>
                    <th>Terakhir Login</th>
                    <th class="text-center">Aksi & Kontrol</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $u)
                    <tr class="{{ !$u->isActive() ? 'table-light opacity-75' : '' }}">
                        <td>
                            <code class="fw-bold">{{ $u->username }}</code>
                        </td>
                        <td>
                            <div class="fw-semibold text-dark">{{ $u->nama_lengkap }}</div>
                            <small class="text-muted"><i class="bi bi-whatsapp text-success me-1"></i>{{ $u->no_telp }}</small>
                        </td>
                        <td>
                            @if($u->role == 'admin')
                                <span class="badge bg-dark"><i class="bi bi-shield-lock me-1"></i>Administrator</span>
                            @elseif($u->role == 'bendahara')
                                <span class="badge bg-success"><i class="bi bi-cash-stack me-1"></i>Bendahara</span>
                            @else
                                <span class="badge bg-info text-dark"><i class="bi bi-person me-1"></i>Wali Santri</span>
                            @endif
                        </td>
                        <td>
                            @if($u->isActive())
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">
                                    <i class="bi bi-check-circle me-1"></i>Aktif
                                </span>
                            @else
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1">
                                    <i class="bi bi-slash-circle me-1"></i>Nonaktif
                                </span>
                            @endif
                        </td>
                        <td>
                            @if($u->role == 'wali_santri')
                                @if($u->santri->count() > 0)
                                    <div>
                                        <button type="button" class="btn btn-outline-primary btn-sm py-1 px-2 rounded-pill fw-semibold shadow-sm" data-bs-toggle="modal" data-bs-target="#modalSantriWali{{ $u->id_user }}" title="Klik untuk melihat daftar santri binaan">
                                            <i class="bi bi-people-fill me-1"></i> {{ $u->santri->count() }} Santri Terhubung
                                        </button>
                                        <div class="small text-muted text-truncate mt-1" style="max-width: 200px;">
                                            {{ $u->santri->pluck('nama_santri')->join(', ') }}
                                        </div>
                                    </div>
                                @else
                                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle py-1 px-2">
                                        <i class="bi bi-exclamation-triangle me-1"></i>Belum Terkait
                                    </span>
                                @endif
                            @else
                                <span class="text-muted small">- (Internal)</span>
                            @endif
                        </td>
                        <td>
                            @if($u->last_login_at)
                                <span class="small text-muted" title="{{ $u->last_login_at->format('d/m/Y H:i:s') }}">
                                    <i class="bi bi-clock-history me-1"></i>{{ $u->last_login_at->diffForHumans() }}
                                </span>
                            @else
                                <span class="small text-muted fst-italic">Belum pernah</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-1">
                                <!-- Tombol Toggle Status Aktif / Nonaktif -->
                                @if($u->id_user !== auth()->id())
                                    <form action="{{ route('admin.users.toggle-status', $u->id_user) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm {{ $u->isActive() ? 'btn-outline-success' : 'btn-outline-secondary' }}" title="{{ $u->isActive() ? 'Klik untuk Nonaktifkan Akun' : 'Klik untuk Aktifkan Akun' }}" onclick="return confirm('Apakah Anda yakin ingin {{ $u->isActive() ? 'menonaktifkan' : 'mengaktifkan' }} akun {{ $u->username }}?')">
                                            <i class="bi {{ $u->isActive() ? 'bi-toggle-on fs-6' : 'bi-toggle-off fs-6' }}"></i>
                                        </button>
                                    </form>
                                @endif

                                <!-- Tombol Edit -->
                                <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#modalEditUser{{ $u->id_user }}" title="Edit Data Pengguna">
                                    <i class="bi bi-pencil"></i>
                                </button>

                                <!-- Tombol Reset Password -->
                                <button type="button" class="btn btn-sm btn-outline-warning text-dark" data-bs-toggle="modal" data-bs-target="#modalResetPass{{ $u->id_user }}" title="Reset Password">
                                    <i class="bi bi-key"></i>
                                </button>

                                <!-- Tombol Hapus -->
                                @if($u->id_user !== auth()->id())
                                    <form action="{{ route('admin.users.destroy', $u->id_user) }}" method="POST" onsubmit="return confirm('Hapus permanen akun pengguna {{ $u->username }}? Tindakan ini tidak dapat dibatalkan.')" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus Akun">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">
                            <i class="bi bi-inbox fs-3 d-block mb-1"></i>
                            Tidak ada data pengguna yang sesuai dengan filter pencarian.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

        @if($users->hasPages())
            <div class="card-footer bg-white py-3 border-0">
                {{ $users->links() }}
            </div>
        @endif
    </div>
</div>

<!-- ============================================================ -->
<!-- MODALS PENGGUNA (Ditempatkan di luar card agar stacking z-index bebas & bersih) -->
<!-- ============================================================ -->
<div id="users_dynamic_modals">
@foreach($users as $u)
    @if($u->role == 'wali_santri' && $u->santri->count() > 0)
        <!-- Modal Daftar Santri Terkait (Untuk Wali) -->
        <div class="modal fade" id="modalSantriWali{{ $u->id_user }}" tabindex="-1" aria-labelledby="modalSantriWaliLabel{{ $u->id_user }}" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                    <div class="modal-header bg-primary text-white py-3 px-4">
                        <div>
                            <h6 class="modal-title fw-bold" id="modalSantriWaliLabel{{ $u->id_user }}">
                                <i class="bi bi-people-fill me-2"></i>Daftar Santri Terhubung
                            </h6>
                            <small class="opacity-75">Wali: {{ $u->nama_lengkap }} (Username: {{ $u->username }})</small>
                        </div>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-0">
                        <div class="table-responsive border-0 rounded-0 mb-0">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr class="small text-muted text-uppercase">
                                        <th class="ps-4">NIS</th>
                                        <th>Nama Santri</th>
                                        <th>Tingkat / Kelas</th>
                                        <th>Program Unggulan</th>
                                        <th>Status</th>
                                        <th class="text-center pe-4">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($u->santri as $s)
                                        <tr>
                                            <td class="ps-4"><span class="badge bg-light text-dark border fw-bold">{{ $s->nis }}</span></td>
                                            <td class="fw-semibold text-dark">
                                                {{ $s->nama_santri }}
                                                <div><small class="text-muted">NISN: {{ $s->nisn ?? '-' }}</small></div>
                                            </td>
                                            <td>
                                                <span class="badge {{ $s->tingkat == 'SMP' ? 'badge-smp' : 'badge-sma' }} me-1">{{ $s->tingkat }}</span>
                                                <small class="text-muted">{{ $s->kelas }}</small>
                                            </td>
                                            <td>
                                                <small class="fw-semibold text-primary"><i class="bi bi-star-fill text-warning me-1"></i>{{ $s->program->nama_program ?? '-' }}</small>
                                            </td>
                                            <td>
                                                <span class="badge bg-success-subtle text-success border border-success-subtle text-uppercase" style="font-size: 0.7rem;">
                                                    {{ $s->status_aktif }}
                                                </span>
                                            </td>
                                            <td class="text-center pe-4">
                                                <a href="{{ route('admin.santri.edit', $s->nis) }}" class="btn btn-sm btn-outline-secondary py-1 px-2" title="Kelola Data Santri">
                                                    <i class="bi bi-pencil-square me-1"></i>Edit Santri
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer bg-light py-2 px-4">
                        <span class="small text-muted me-auto">
                            <i class="bi bi-info-circle me-1"></i>Relasi ini otomatis menghubungkan portal wali dengan rincian tagihan seluruh santri di atas.
                        </span>
                        <button type="button" class="btn btn-secondary btn-sm px-3" data-bs-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Modal Edit User -->
    <div class="modal fade" id="modalEditUser{{ $u->id_user }}" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                <form action="{{ route('admin.users.update', $u->id_user) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-header bg-light py-3 px-4">
                        <h6 class="modal-title fw-bold text-dark"><i class="bi bi-pencil me-1"></i>Edit Akun: {{ $u->username }}</h6>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body text-start px-4">
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Username <span class="text-danger">*</span></label>
                            <input type="text" name="username" class="form-control" value="{{ $u->username }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" name="nama_lengkap" class="form-control" value="{{ $u->nama_lengkap }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">No. WhatsApp / HP <span class="text-danger">*</span></label>
                            <input type="text" name="no_telp" class="form-control" value="{{ $u->no_telp }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Hak Akses (Role) <span class="text-danger">*</span></label>
                            <select name="role" class="form-select" required>
                                <option value="admin" {{ $u->role == 'admin' ? 'selected' : '' }}>Administrator Sistem</option>
                                <option value="bendahara" {{ $u->role == 'bendahara' ? 'selected' : '' }}>Bendahara Keuangan</option>
                                <option value="wali_santri" {{ $u->role == 'wali_santri' ? 'selected' : '' }}>Wali Santri</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Ganti Password (Opsional)</label>
                            <input type="password" name="password" class="form-control" placeholder="Biarkan kosong jika tidak diubah">
                            <small class="text-muted">Minimal 6 karakter jika ingin mengganti</small>
                        </div>
                    </div>
                    <div class="modal-footer bg-light px-4">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary btn-sm">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Reset Password -->
    <div class="modal fade" id="modalResetPass{{ $u->id_user }}" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                <form action="{{ route('admin.users.reset-password', $u->id_user) }}" method="POST">
                    @csrf
                    <div class="modal-header bg-warning-subtle py-3 px-4">
                        <h6 class="modal-title fw-bold text-dark"><i class="bi bi-key me-1"></i>Reset Password Pengguna</h6>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body text-start px-4">
                        <p class="small text-muted mb-3">
                            Anda akan menyetel ulang kata sandi akun <strong>{{ $u->username }}</strong> ({{ $u->nama_lengkap }}).
                        </p>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Kata Sandi Baru <span class="text-danger">*</span></label>
                            <input type="password" name="new_password" class="form-control" placeholder="Masukkan minimal 6 karakter" required minlength="6">
                        </div>
                    </div>
                    <div class="modal-footer bg-light px-4">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-warning btn-sm fw-semibold">Terapkan Password Baru</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endforeach
</div>

<!-- Modal Tambah Pengguna Baru -->
<div class="modal fade" id="modalTambahUser" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.users.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h6 class="modal-title fw-bold"><i class="bi bi-person-plus me-1"></i>Tambah Pengguna Baru</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-start">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Username Login <span class="text-danger">*</span></label>
                        <input type="text" name="username" class="form-control" placeholder="Contoh: bendahara2 / wali_2026010" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Kata Sandi Awal <span class="text-danger">*</span></label>
                        <input type="password" name="password" class="form-control" placeholder="Minimal 6 karakter" required minlength="6">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" name="nama_lengkap" class="form-control" placeholder="Contoh: H. Agus Salim" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">No. WhatsApp / HP <span class="text-danger">*</span></label>
                        <input type="text" name="no_telp" class="form-control" placeholder="Contoh: 081234567890" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Hak Akses (Role) <span class="text-danger">*</span></label>
                        <select name="role" class="form-select" required>
                            <option value="wali_santri" selected>Wali Santri (Akses Portal Wali)</option>
                            <option value="bendahara">Bendahara Keuangan (Akses Kasir & Laporan)</option>
                            <option value="admin">Administrator Sistem (Akses Penuh Master)</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm">Simpan Pengguna</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    let searchTimer = null;
    const form = document.getElementById('filter_form_users');
    const searchInput = document.getElementById('input_users_search');
    const wrapper = document.getElementById('users_table_wrapper');
    const overlay = document.getElementById('users_loading_overlay');
    const resetBtn = document.getElementById('btn_reset_users');

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

    function fetchUsers(url) {
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
            const newWrapper = doc.getElementById('users_table_wrapper');
            const newModals = doc.getElementById('users_dynamic_modals');
            const currentModals = document.getElementById('users_dynamic_modals');

            // Bersihkan modal lama di body agar tidak duplikat
            document.querySelectorAll('body > .modal[id^="modalSantriWali"], body > .modal[id^="modalEditUser"], body > .modal[id^="modalResetPass"]').forEach(m => m.remove());

            if (newWrapper && wrapper) {
                wrapper.innerHTML = newWrapper.innerHTML;
            }

            if (newModals && currentModals) {
                currentModals.innerHTML = newModals.innerHTML;
            }

            window.history.pushState({}, '', url);
            bindPagination();
        })
        .catch(err => {
            console.error('AJAX Load users error:', err);
        })
        .finally(() => {
            const activeOverlay = document.getElementById('users_loading_overlay');
            if (activeOverlay) activeOverlay.style.setProperty('display', 'none', 'important');
        });
    }

    function bindPagination() {
        if (!wrapper) return;
        const paginationLinks = wrapper.querySelectorAll('.pagination a');
        paginationLinks.forEach(link => {
            link.addEventListener('click', function (e) {
                e.preventDefault();
                fetchUsers(this.getAttribute('href'));
            });
        });
    }

    if (searchInput) {
        searchInput.addEventListener('input', function () {
            clearTimeout(searchTimer);
            searchTimer = setTimeout(() => {
                fetchUsers(buildFilterUrl());
            }, 300);
        });
    }

    const filterSelects = form.querySelectorAll('.filter-select');
    filterSelects.forEach(sel => {
        sel.addEventListener('change', function () {
            fetchUsers(buildFilterUrl());
        });
    });

    if (resetBtn) {
        resetBtn.addEventListener('click', function () {
            form.reset();
            if (searchInput) searchInput.value = '';
            fetchUsers(form.action);
        });
    }

    bindPagination();

    window.addEventListener('popstate', function () {
        fetchUsers(window.location.href);
    });
});
</script>
@endpush
