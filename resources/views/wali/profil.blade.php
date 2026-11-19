@extends('layouts.app')

@section('title', 'Profil Akun Wali')
@section('page_title', 'Profil & Keamanan Akun')

@section('content')

<div class="row g-4">
    {{-- ============================================================ --}}
    {{-- KIRI: INFO PROFIL & SANTRI TERKAIT --}}
    {{-- ============================================================ --}}
    <div class="col-md-5">
        {{-- Kartu profil --}}
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body text-center p-4">
                {{-- Avatar --}}
                <div class="mx-auto rounded-circle d-flex align-items-center justify-content-center fw-bold mb-3"
                     style="width:80px;height:80px;background:linear-gradient(135deg,#15803d,#16a34a);color:#fff;font-size:2rem;">
                    {{ strtoupper(substr($user->nama_lengkap, 0, 1)) }}
                </div>
                <h5 class="fw-bold mb-0">{{ $user->nama_lengkap }}</h5>
                <p class="text-muted small mb-2">{{ '@' . $user->username }}</p>
                <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2">
                    <i class="bi bi-person-badge me-1"></i> Wali Santri
                </span>
            </div>
        </div>

        {{-- Info akun --}}
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header bg-white py-3 px-4 border-0">
                <h6 class="fw-bold mb-0"><i class="bi bi-info-circle text-primary me-2"></i>Informasi Akun</h6>
            </div>
            <div class="card-body px-4 pb-4 pt-2">
                <div class="row g-3">
                    <div class="col-12">
                        <label class="small text-muted fw-semibold">Nama Lengkap</label>
                        <div class="fw-semibold">{{ $user->nama_lengkap }}</div>
                    </div>
                    <div class="col-12">
                        <label class="small text-muted fw-semibold">Username</label>
                        <div class="fw-semibold font-monospace">{{ $user->username }}</div>
                    </div>
                    <div class="col-12">
                        <label class="small text-muted fw-semibold">Role Akun</label>
                        <div><span class="badge bg-success">Wali Santri</span></div>
                    </div>
                    <div class="col-12">
                        <label class="small text-muted fw-semibold">Status Akun</label>
                        <div>
                            <span class="badge {{ $user->is_active ? 'bg-success' : 'bg-danger' }}">
                                {{ $user->is_active ? 'Aktif' : 'Tidak Aktif' }}
                            </span>
                        </div>
                    </div>
                    <div class="col-12">
                        <label class="small text-muted fw-semibold">Akun Terdaftar Sejak</label>
                        <div class="fw-semibold">{{ \Carbon\Carbon::parse($user->created_at)->translatedFormat('d F Y') }}</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Santri terkait --}}
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white py-3 px-4 border-0">
                <h6 class="fw-bold mb-0"><i class="bi bi-people text-success me-2"></i>Santri Terkait</h6>
            </div>
            <div class="card-body px-4 pb-4 pt-2">
                @forelse($santriList as $s)
                    @php
                        $colorMap = ['PRG-TFZ' => ['#d97706','#fef3c7'],'PRG-ARB' => ['#2563eb','#dbeafe'],'PRG-ING' => ['#7c3aed','#ede9fe']];
                        $pc = $colorMap[$s->id_program ?? ''] ?? ['#15803d','#dcfce7'];
                    @endphp
                    <div class="d-flex align-items-center gap-3 mb-3 p-3 rounded-3" style="background:#f8fafc;">
                        <div class="rounded-3 d-flex align-items-center justify-content-center fw-bold"
                             style="width:44px;height:44px;background:{{ $pc[1] }};color:{{ $pc[0] }};flex-shrink:0;">
                            {{ strtoupper(substr($s->nama_santri, 0, 1)) }}{{ strtoupper(substr(explode(' ', trim($s->nama_santri))[1] ?? 'X', 0, 1)) }}
                        </div>
                        <div class="flex-grow-1">
                            <div class="fw-semibold small">{{ $s->nama_santri }}</div>
                            <div class="text-muted" style="font-size:0.78rem;">
                                NIS: {{ $s->nis }} &nbsp;|&nbsp; {{ $s->tingkat }} {{ $s->kelas }}
                            </div>
                            <span class="badge border mt-1" style="font-size:0.7rem;background:{{ $pc[1] }};color:{{ $pc[0] }};border-color:{{ $pc[0] }}!important;">
                                {{ $s->program->nama_program ?? '-' }}
                            </span>
                        </div>
                        <a href="{{ route('wali.santri.show', $s->nis) }}" class="btn btn-outline-secondary btn-sm">
                            <i class="bi bi-eye"></i>
                        </a>
                    </div>
                @empty
                    <p class="text-muted small">Belum ada santri terkait.</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- KANAN: GANTI PASSWORD --}}
    {{-- ============================================================ --}}
    <div class="col-md-7">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white py-3 px-4 border-0">
                <h6 class="fw-bold mb-0"><i class="bi bi-shield-lock text-warning me-2"></i>Ubah Password Akun</h6>
                <small class="text-muted">Pastikan password baru Anda kuat dan mudah diingat.</small>
            </div>
            <div class="card-body px-4 pb-4">
                @if($errors->any())
                    <div class="alert alert-danger rounded-3 mb-4">
                        <ul class="mb-0 small">
                            @foreach($errors->all() as $e)
                                <li>{{ $e }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('wali.profil.update') }}">
                    @csrf
                    @method('PUT')

                    <div class="mb-4">
                        <label class="form-label fw-semibold">Password Saat Ini <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-lock"></i></span>
                            <input type="password" name="password_lama" class="form-control @error('password_lama') is-invalid @enderror"
                                   placeholder="Masukkan password lama Anda" required>
                        </div>
                        @error('password_lama')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">Password Baru <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-key"></i></span>
                            <input type="password" name="password_baru" id="pw_baru" class="form-control @error('password_baru') is-invalid @enderror"
                                   placeholder="Minimal 6 karakter" required minlength="6">
                            <button type="button" class="btn btn-outline-secondary" onclick="togglePw('pw_baru','icon_baru')">
                                <i class="bi bi-eye" id="icon_baru"></i>
                            </button>
                        </div>
                        @error('password_baru')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror

                        {{-- Strength indicator --}}
                        <div class="mt-2">
                            <div class="progress rounded-pill" style="height:5px;">
                                <div class="progress-bar rounded-pill" id="pw_strength_bar" style="width:0%;background:#dc2626;transition:all .3s;"></div>
                            </div>
                            <small id="pw_strength_text" class="text-muted"></small>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">Konfirmasi Password Baru <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-key-fill"></i></span>
                            <input type="password" name="password_baru_confirmation" id="pw_confirm"
                                   class="form-control" placeholder="Ulangi password baru" required minlength="6">
                            <button type="button" class="btn btn-outline-secondary" onclick="togglePw('pw_confirm','icon_confirm')">
                                <i class="bi bi-eye" id="icon_confirm"></i>
                            </button>
                        </div>
                        <div id="match_msg" class="small mt-1"></div>
                    </div>

                    <div class="alert alert-info small rounded-3 mb-4">
                        <i class="bi bi-info-circle me-1"></i>
                        Tips keamanan: Gunakan kombinasi huruf besar, huruf kecil, angka, dan simbol.
                        Jangan gunakan tanggal lahir atau nama sebagai password.
                    </div>

                    <button type="submit" class="btn btn-warning fw-bold px-4" id="btn_submit_pw" disabled>
                        <i class="bi bi-shield-check me-1"></i> Perbarui Password
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function togglePw(fieldId, iconId) {
        const f = document.getElementById(fieldId);
        const i = document.getElementById(iconId);
        if (f.type === 'password') {
            f.type = 'text';
            i.className = 'bi bi-eye-slash';
        } else {
            f.type = 'password';
            i.className = 'bi bi-eye';
        }
    }

    // Password strength
    document.getElementById('pw_baru').addEventListener('input', function() {
        const v = this.value;
        let score = 0;
        if (v.length >= 6) score++;
        if (v.length >= 10) score++;
        if (/[A-Z]/.test(v)) score++;
        if (/[0-9]/.test(v)) score++;
        if (/[^a-zA-Z0-9]/.test(v)) score++;

        const bar = document.getElementById('pw_strength_bar');
        const txt = document.getElementById('pw_strength_text');
        const labels = ['', 'Lemah', 'Cukup', 'Sedang', 'Kuat', 'Sangat Kuat'];
        const colors = ['', '#dc2626', '#f97316', '#eab308', '#16a34a', '#15803d'];
        bar.style.width = (score * 20) + '%';
        bar.style.background = colors[score];
        txt.textContent = labels[score] ? 'Kekuatan: ' + labels[score] : '';
        txt.style.color = colors[score];

        checkMatch();
        updateSubmit();
    });

    document.getElementById('pw_confirm').addEventListener('input', function() {
        checkMatch();
        updateSubmit();
    });

    function checkMatch() {
        const baru = document.getElementById('pw_baru').value;
        const konfirm = document.getElementById('pw_confirm').value;
        const msg = document.getElementById('match_msg');
        if (!konfirm) { msg.textContent = ''; return; }
        if (baru === konfirm) {
            msg.textContent = '✅ Password cocok';
            msg.style.color = '#16a34a';
        } else {
            msg.textContent = '❌ Password tidak cocok';
            msg.style.color = '#dc2626';
        }
    }

    function updateSubmit() {
        const baru = document.getElementById('pw_baru').value;
        const konfirm = document.getElementById('pw_confirm').value;
        document.getElementById('btn_submit_pw').disabled = !(baru.length >= 6 && baru === konfirm);
    }
</script>
@endpush
@endsection

