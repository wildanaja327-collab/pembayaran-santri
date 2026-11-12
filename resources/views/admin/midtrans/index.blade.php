@extends('layouts.app')

@section('title', 'Konfigurasi Payment Gateway Midtrans')
@section('page_title', 'Konfigurasi Payment Gateway Midtrans')

@section('content')
<!-- Header Banner Status Gateway -->
<div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
    <div class="card-body p-4" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div class="text-white">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <span class="badge {{ $midtransSettings['mode'] == 'production' ? 'bg-danger text-white' : 'bg-warning text-dark' }} px-3 py-1 font-monospace fw-bold">
                        <i class="bi {{ $midtransSettings['mode'] == 'production' ? 'bi-shield-fill-check' : 'bi-shield-check' }} me-1"></i>
                        MODE: {{ strtoupper($midtransSettings['mode']) }}
                    </span>
                    <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1">
                        <i class="bi bi-broadcast me-1"></i> Webhook {{ $webhookInfo['status'] }}
                    </span>
                    <span class="badge bg-secondary px-3 py-1 font-monospace">
                        Biaya Admin: Rp {{ number_format($midtransSettings['biaya_admin'], 0, ',', '.') }}
                    </span>
                </div>
                <h4 class="fw-bold mb-1 text-white">Integrasi Payment Gateway Midtrans (Snap & Webhook)</h4>
                <p class="text-white-50 small mb-0">
                    Kelola lingkungan transaksi, Server Key, Client Key, dan pantau status URL notifikasi webhook secara terpusat tanpa perlu mengedit file <code>.env</code> manual.
                </p>
            </div>
            <div class="d-flex align-items-center gap-2">
                <button type="button" class="btn btn-outline-light btn-sm fw-semibold" id="btn_ping_api">
                    <i class="bi bi-hdd-network me-1"></i> Uji Koneksi API Midtrans
                </button>
                <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary btn-sm">
                    <i class="bi bi-arrow-left me-1"></i> Kembali
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Stat Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card-stat">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-muted small fw-semibold">TOTAL TRANSAKSI GATEWAY</span>
                <span class="badge bg-primary-subtle text-primary p-2 rounded-circle">
                    <i class="bi bi-credit-card-2-front fs-5"></i>
                </span>
            </div>
            <h3 class="fw-bold mb-1">{{ number_format($stats['total_gateway']) }}</h3>
            <div class="small text-muted">Seluruh transaksi Midtrans</div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card-stat">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-muted small fw-semibold">SETTLEMENT (SUKSES)</span>
                <span class="badge bg-success-subtle text-success p-2 rounded-circle">
                    <i class="bi bi-check2-circle fs-5"></i>
                </span>
            </div>
            <h3 class="fw-bold mb-1 text-success">{{ number_format($stats['settlement_count']) }}</h3>
            <div class="small text-success fw-semibold">Rp {{ number_format($stats['total_nominal'], 0, ',', '.') }}</div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card-stat">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-muted small fw-semibold">PENDING (MENUNGGU)</span>
                <span class="badge bg-warning-subtle text-warning p-2 rounded-circle">
                    <i class="bi bi-hourglass-split fs-5"></i>
                </span>
            </div>
            <h3 class="fw-bold mb-1 text-warning">{{ number_format($stats['pending_count']) }}</h3>
            <div class="small text-muted">Menunggu pembayaran wali</div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card-stat">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-muted small fw-semibold">STATUS WEBHOOK</span>
                <span class="badge bg-info-subtle text-info p-2 rounded-circle">
                    <i class="bi bi-activity fs-5"></i>
                </span>
            </div>
            <h4 class="fw-bold mb-1 text-info">READY</h4>
            <div class="small text-muted">Bypass CSRF Verified</div>
        </div>
    </div>
</div>

<!-- Alert Uji Koneksi Live Hasil AJAX -->
<div id="ping_result_container" class="mb-4" style="display: none;"></div>

<div class="row g-4">
    <!-- Kolom Kiri: Form Konfigurasi Kredensial -->
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header bg-white py-3 px-4 border-0 d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="mb-0 fw-bold"><i class="bi bi-sliders2 text-primary me-2"></i>Kredensial & Lingkungan Midtrans</h6>
                    <small class="text-muted">Perubahan otomatis disimpan ke database & disinkronkan ke <code>.env</code></small>
                </div>
                <span class="badge bg-light text-dark border font-monospace">Single Source of Truth</span>
            </div>

            <div class="card-body px-4 pb-4 pt-2">
                <form action="{{ route('admin.midtrans.update') }}" method="POST" id="form_midtrans">
                    @csrf
                    @method('PUT')

                    <!-- 1. Pemilihan Mode Environment -->
                    <div class="mb-4">
                        <label class="form-label small fw-bold text-uppercase text-muted">
                            1. Mode Lingkungan Transaksi (Environment) <span class="text-danger">*</span>
                        </label>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="card p-3 border rounded-3 h-100 cursor-pointer shadow-sm position-relative {{ $midtransSettings['mode'] == 'sandbox' ? 'border-warning bg-warning-subtle' : '' }}" style="cursor: pointer;">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="midtrans_mode" id="mode_sandbox" value="sandbox" {{ $midtransSettings['mode'] == 'sandbox' ? 'checked' : '' }}>
                                        <span class="form-check-label fw-bold text-dark">
                                            <i class="bi bi-tools text-warning me-1"></i> Sandbox (Testing)
                                        </span>
                                    </div>
                                    <p class="small text-muted mb-0 mt-2" style="font-size: 0.8rem;">
                                        Mode simulasi tanpa transaksi riil. Menggunakan nomor Virtual Account dan QRIS simulator resmi Midtrans.
                                    </p>
                                </label>
                            </div>

                            <div class="col-md-6">
                                <label class="card p-3 border rounded-3 h-100 cursor-pointer shadow-sm position-relative {{ $midtransSettings['mode'] == 'production' ? 'border-danger bg-danger-subtle' : '' }}" style="cursor: pointer;">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="midtrans_mode" id="mode_production" value="production" {{ $midtransSettings['mode'] == 'production' ? 'checked' : '' }}>
                                        <span class="form-check-label fw-bold text-danger">
                                            <i class="bi bi-shield-fill-check me-1"></i> Production (LIVE)
                                        </span>
                                    </div>
                                    <p class="small text-muted mb-0 mt-2" style="font-size: 0.8rem;">
                                        Mode operasional nyata. Terkoneksi langsung ke Bank & E-Wallet dengan pemotongan saldo riil.
                                    </p>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- 2. Server Key -->
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-uppercase text-muted">
                            2. Server Key <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-key-fill"></i></span>
                            <input type="password" name="midtrans_server_key" id="input_server_key"
                                   class="form-control font-monospace"
                                   value="{{ old('midtrans_server_key', $midtransSettings['server_key']) }}"
                                   placeholder="SB-Mid-server-xxxxxxxx / Mid-server-xxxxxxxx" required>
                            <button class="btn btn-outline-secondary" type="button" onclick="toggleVisibility('input_server_key', 'eye_server_key')">
                                <i class="bi bi-eye" id="eye_server_key"></i>
                            </button>
                        </div>
                        <small class="text-muted">Kunci rahasia untuk otorisasi server-to-server. Dilarang dibagikan ke pihak umum.</small>
                    </div>

                    <!-- 3. Client Key -->
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-uppercase text-muted">
                            3. Client Key <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-unlock-fill"></i></span>
                            <input type="text" name="midtrans_client_key" id="input_client_key"
                                   class="form-control font-monospace"
                                   value="{{ old('midtrans_client_key', $midtransSettings['client_key']) }}"
                                   placeholder="SB-Mid-client-xxxxxxxx / Mid-client-xxxxxxxx" required>
                            <button class="btn btn-outline-secondary" type="button" onclick="copyToClipboard('input_client_key', 'btn_copy_client')">
                                <i class="bi bi-clipboard" id="btn_copy_client"></i>
                            </button>
                        </div>
                        <small class="text-muted">Kunci publik yang disematkan pada JavaScript frontend (Snap.js) saat wali santri membayar.</small>
                    </div>

                    <!-- 4. Merchant ID & Biaya Layanan -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-uppercase text-muted">
                                4. Merchant ID (Opsional)
                            </label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-building"></i></span>
                                <input type="text" name="midtrans_merchant_id" class="form-control font-monospace"
                                       value="{{ old('midtrans_merchant_id', $midtransSettings['merchant_id']) }}"
                                       placeholder="G123456789">
                            </div>
                            <small class="text-muted">ID unik merchant dari portal Midtrans.</small>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-uppercase text-muted">
                                5. Biaya Layanan Gateway (Rp) <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" name="midtrans_biaya_admin" class="form-control fw-bold"
                                       value="{{ old('midtrans_biaya_admin', $midtransSettings['biaya_admin']) }}"
                                       min="0" step="500" required>
                            </div>
                            <small class="text-muted">Ditambahkan ke nominal tagihan snap santri.</small>
                        </div>
                    </div>

                    <!-- 5. Sanitized & 3D Secure Toggles -->
                    <div class="p-3 bg-light rounded-3 mb-4 border">
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" role="switch" name="midtrans_is_sanitized" id="switch_sanitized" value="1" {{ $midtransSettings['is_sanitized'] ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold small text-dark" for="switch_sanitized">
                                Aktifkan Sanitized Filter (Rekomendasi Midtrans)
                            </label>
                            <div class="small text-muted" style="font-size: 0.78rem;">Membersihkan karakter non-standar pada nama santri dan item tagihan sebelum dikirim ke API.</div>
                        </div>

                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" role="switch" name="midtrans_is_3ds" id="switch_3ds" value="1" {{ $midtransSettings['is_3ds'] ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold small text-dark" for="switch_3ds">
                                Wajibkan 3D Secure Protection (Kartu Kredit / Debit)
                            </label>
                            <div class="small text-muted" style="font-size: 0.78rem;">Memerlukan autentikasi OTP SMS dari bank penerbit untuk transaksi kartu.</div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                        <button type="button" class="btn btn-outline-primary btn-sm fw-semibold" id="btn_test_ping_form">
                            <i class="bi bi-hdd-network me-1"></i> Uji Kredensial Ini
                        </button>
                        <button type="submit" class="btn btn-success btn-sm fw-bold px-4 shadow-sm">
                            <i class="bi bi-save me-1"></i> Simpan & Terapkan Konfigurasi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Kolom Kanan: Webhook Status, Panduan MAP, & Quick Copy -->
    <div class="col-lg-5">
        <!-- Card 1: Webhook URL Endpoint -->
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header bg-white py-3 px-4 border-0">
                <h6 class="mb-0 fw-bold text-dark"><i class="bi bi-link-45deg text-success me-2"></i>URL Webhook Notifikasi Midtrans</h6>
                <small class="text-muted">Salin dan masukkan URL ini di portal Midtrans Anda.</small>
            </div>
            <div class="card-body px-4 pb-4 pt-1">
                <div class="mb-3">
                    <label class="form-label small fw-semibold text-muted">Payment Notification URL (Utama):</label>
                    <div class="input-group">
                        <input type="text" id="webhook_url_main" class="form-control form-control-sm font-monospace bg-light"
                               value="{{ $webhookInfo['url'] }}" readonly>
                        <button class="btn btn-outline-success btn-sm" type="button" onclick="copyToClipboard('webhook_url_main', 'btn_copy_wh1')" title="Salin URL">
                            <i class="bi bi-clipboard" id="btn_copy_wh1"></i> Salin
                        </button>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-semibold text-muted">Alternatif Endpoint (API Route):</label>
                    <div class="input-group">
                        <input type="text" id="webhook_url_alt" class="form-control form-control-sm font-monospace bg-light"
                               value="{{ $webhookInfo['alt_url'] }}" readonly>
                        <button class="btn btn-outline-secondary btn-sm" type="button" onclick="copyToClipboard('webhook_url_alt', 'btn_copy_wh2')" title="Salin URL">
                            <i class="bi bi-clipboard" id="btn_copy_wh2"></i> Salin
                        </button>
                    </div>
                </div>

                <div class="d-flex align-items-center justify-content-between p-2 rounded-3 bg-light border mb-3">
                    <div class="small">
                        <span class="text-muted">Metode HTTP:</span> <span class="badge bg-primary">POST</span>
                        <span class="ms-2 text-muted">CSRF:</span> <span class="badge bg-success">Bypass OK</span>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-info" id="btn_simulate_webhook">
                        <i class="bi bi-play-circle me-1"></i> Simulasi Ping Lokal
                    </button>
                </div>

                <div id="wh_sim_result" style="display:none;" class="mb-0"></div>
            </div>
        </div>

        <!-- Card 2: Panduan Setting di Dashboard Midtrans -->
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header bg-white py-3 px-4 border-0">
                <h6 class="mb-0 fw-bold"><i class="bi bi-info-circle text-primary me-2"></i>Panduan Setting di Portal Midtrans (MAP)</h6>
            </div>
            <div class="card-body px-4 pb-4 pt-1 small text-muted">
                <ol class="ps-3 mb-3">
                    <li class="mb-2">Login ke dashboard resmi <a href="https://dashboard.midtrans.com/" target="_blank" class="fw-semibold text-decoration-none">dashboard.midtrans.com</a>.</li>
                    <li class="mb-2">Pastikan memilih environment yang sesuai di pojok kiri atas (<strong>Sandbox</strong> atau <strong>Production</strong>).</li>
                    <li class="mb-2">Buka menu <strong>Settings</strong> &rarr; <strong>Configuration</strong>.</li>
                    <li class="mb-2">Pada kolom <strong>Payment Notification URL</strong>, tempelkan (paste) URL webhook di atas.</li>
                    <li class="mb-2">Pada kolom <strong>Finish / Unfinish / Error Redirect URL</strong>, kosongkan atau arahkan ke alamat web pesantren.</li>
                    <li>Klik <strong>Save Changes</strong> di bagian bawah. Status pembayaran akan otomatis terupdate saat wali membayar.</li>
                </ol>

                <div class="alert alert-light border small mb-0 rounded-3">
                    <i class="bi bi-shield-check text-success me-1"></i>
                    <strong>Catatan Skripsi:</strong> Pada saat demo lokal tanpa public IP (misal via localhost), sistem tetap dapat disimulasikan menggunakan tombol <em>Sinkronisasi Status Transaksi</em> di menu Bendahara.
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Riwayat Webhook & Transaksi Gateway Terkini -->
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-header bg-white py-3 px-4 border-0 d-flex align-items-center justify-content-between flex-wrap gap-2">
        <div>
            <h6 class="mb-0 fw-bold"><i class="bi bi-clock-history text-primary me-2"></i>Log Aktivitas Webhook & Transaksi Gateway Terkini</h6>
            <small class="text-muted">Catatan notifikasi real-time yang diterima dari server Midtrans.</small>
        </div>
        <a href="{{ route('admin.activity-logs') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-list-ul me-1"></i> Buka Seluruh Audit Trail
        </a>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr class="small text-muted text-uppercase">
                    <th class="ps-4">Waktu</th>
                    <th>Aksi / Kategori</th>
                    <th>Detail Notifikasi</th>
                    <th>IP / Pengguna</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentWebhookLogs as $log)
                    <tr>
                        <td class="ps-4 small text-muted" style="white-space: nowrap;">
                            {{ date('d/m/Y H:i:s', strtotime($log->created_at)) }}
                        </td>
                        <td>
                            @if(str_contains($log->action, 'FAIL'))
                                <span class="badge bg-danger font-monospace">{{ $log->action }}</span>
                            @elseif(str_contains($log->action, 'CONFIG'))
                                <span class="badge bg-warning text-dark font-monospace">{{ $log->action }}</span>
                            @else
                                <span class="badge bg-success font-monospace">{{ $log->action }}</span>
                            @endif
                        </td>
                        <td class="small">{{ $log->description }}</td>
                        <td class="small text-muted font-monospace">{{ $log->ip_address ?? 'Gateway' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center py-4 text-muted">
                            <i class="bi bi-inbox me-1"></i> Belum ada aktivitas notifikasi webhook yang tercatat.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Panduan Uji Koneksi -->
<div class="modal fade" id="modalPingResult" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow-lg">
            <div class="modal-header border-0 pb-0 px-4 pt-4">
                <h5 class="modal-title fw-bold" id="modal_ping_title">
                    <i class="bi bi-hdd-network text-primary me-2"></i>Hasil Pengujian Koneksi Midtrans
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body px-4 py-3" id="modal_ping_body">
                <!-- Diisi via AJAX -->
            </div>
            <div class="modal-footer border-0 pt-0 px-4 pb-4">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Toggle Show/Hide Password
    function toggleVisibility(inputId, iconId) {
        const input = document.getElementById(inputId);
        const icon = document.getElementById(iconId);
        if (input.type === 'password') {
            input.type = 'text';
            icon.className = 'bi bi-eye-slash';
        } else {
            input.type = 'password';
            icon.className = 'bi bi-eye';
        }
    }

    // 1-Click Copy to Clipboard
    function copyToClipboard(inputId, btnId) {
        const input = document.getElementById(inputId);
        input.select();
        input.setSelectionRange(0, 99999);
        navigator.clipboard.writeText(input.value).then(() => {
            const btn = document.getElementById(btnId);
            const originalClass = btn.className;
            btn.className = 'bi bi-check-lg text-success';
            setTimeout(() => {
                btn.className = originalClass;
            }, 2000);
        });
    }

    // Radio Card Dynamic Border Highlight
    document.querySelectorAll('input[name="midtrans_mode"]').forEach(radio => {
        radio.addEventListener('change', function() {
            document.querySelectorAll('input[name="midtrans_mode"]').forEach(r => {
                const card = r.closest('.card');
                if (card) {
                    card.classList.remove('border-warning', 'bg-warning-subtle', 'border-danger', 'bg-danger-subtle');
                }
            });
            const activeCard = this.closest('.card');
            if (this.value === 'sandbox') {
                activeCard.classList.add('border-warning', 'bg-warning-subtle');
            } else {
                activeCard.classList.add('border-danger', 'bg-danger-subtle');
            }
        });
    });

    // AJAX Test Ping Koneksi API Midtrans
    function runMidtransPing(customServerKey, customMode) {
        const container = document.getElementById('ping_result_container');
        container.style.display = 'block';
        container.innerHTML = `
            <div class="alert alert-info d-flex align-items-center rounded-3 shadow-sm py-3">
                <div class="spinner-border spinner-border-sm me-3" role="status"></div>
                <div>
                    <strong>Menghubungi server Midtrans...</strong>
                    <div class="small">Mengirim handshake autentikasi ke endpoint Midtrans API.</div>
                </div>
            </div>
        `;

        const serverKey = customServerKey || document.getElementById('input_server_key').value;
        const mode = customMode || document.querySelector('input[name="midtrans_mode"]:checked').value;

        fetch('{{ route('admin.midtrans.test') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                server_key: serverKey,
                mode: mode
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                container.innerHTML = `
                    <div class="alert alert-success d-flex align-items-center justify-content-between rounded-3 shadow-sm py-3 border-success">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-check-circle-fill fs-3 text-success me-3"></i>
                            <div>
                                <h6 class="fw-bold text-success mb-1">Koneksi API Midtrans Berhasil!</h6>
                                <div class="small text-dark">${data.message}</div>
                                <div class="small text-muted mt-1">
                                    Environment: <strong>${data.mode.toUpperCase()}</strong> | HTTP Code: <strong>${data.http_code}</strong> | Latency: <strong>${data.latency_ms} ms</strong>
                                </div>
                            </div>
                        </div>
                        <button type="button" class="btn-close" onclick="this.closest('#ping_result_container').style.display='none'"></button>
                    </div>
                `;
            } else {
                container.innerHTML = `
                    <div class="alert alert-danger d-flex align-items-center justify-content-between rounded-3 shadow-sm py-3 border-danger">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-exclamation-triangle-fill fs-3 text-danger me-3"></i>
                            <div>
                                <h6 class="fw-bold text-danger mb-1">Gagal Terhubung ke Midtrans API</h6>
                                <div class="small text-dark">${data.message}</div>
                                <div class="small text-muted mt-1">
                                    Latency: <strong>${data.latency_ms || 0} ms</strong> | HTTP Code: <strong>${data.http_code || 'N/A'}</strong>
                                </div>
                            </div>
                        </div>
                        <button type="button" class="btn-close" onclick="this.closest('#ping_result_container').style.display='none'"></button>
                    </div>
                `;
            }
        })
        .catch(err => {
            container.innerHTML = `
                <div class="alert alert-danger rounded-3 py-3 shadow-sm">
                    <i class="bi bi-x-circle me-1"></i> Terjadi kesalahan jaringan lokal: ${err}
                </div>
            `;
        });
    }

    document.getElementById('btn_ping_api').addEventListener('click', () => runMidtransPing());
    document.getElementById('btn_test_ping_form').addEventListener('click', () => runMidtransPing());

    // Simulasi Ping Webhook Lokal
    document.getElementById('btn_simulate_webhook').addEventListener('click', function() {
        const btn = this;
        const resBox = document.getElementById('wh_sim_result');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Menguji...';
        resBox.style.display = 'block';
        resBox.innerHTML = '<div class="alert alert-light border small py-2 mb-0">Mengirim payload simulasi webhook ke endpoint lokal...</div>';

        fetch('{{ route('admin.midtrans.simulate-webhook') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(r => r.json())
        .then(data => {
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-play-circle me-1"></i> Simulasi Ping Lokal';
            if (data.success) {
                resBox.innerHTML = `
                    <div class="alert alert-success small py-2 mb-0 rounded-3">
                        <i class="bi bi-check-circle-fill me-1"></i> ${data.message}
                    </div>
                `;
            } else {
                resBox.innerHTML = `
                    <div class="alert alert-warning small py-2 mb-0 rounded-3">
                        <i class="bi bi-exclamation-triangle-fill me-1"></i> ${data.message}
                    </div>
                `;
            }
        })
        .catch(err => {
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-play-circle me-1"></i> Simulasi Ping Lokal';
            resBox.innerHTML = `<div class="alert alert-danger small py-2 mb-0">Gagal: ${err}</div>`;
        });
    });
</script>
@endpush
@endsection

