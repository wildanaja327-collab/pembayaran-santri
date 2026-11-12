@extends('layouts.app')

@section('title', 'Manajemen WhatsApp Gateway')
@section('page_title', 'WhatsApp Gateway & Notifikasi Otomatis')

@section('content')
<!-- Header Banner WhatsApp Gateway -->
<div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
    <div class="card-body p-4" style="background: linear-gradient(135deg, #064e3b 0%, #065f46 50%, #047857 100%);">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div class="text-white">
                <div class="d-flex align-items-center gap-2 mb-2">
                    @if(($deviceInfo['status'] ?? '') == 'connect' || ($deviceInfo['status'] ?? '') == 'connected')
                        <span class="badge bg-success-subtle text-white border border-success px-3 py-1 fw-bold">
                            <i class="bi bi-whatsapp me-1"></i> STATUS: TERHUBUNG (CONNECTED)
                        </span>
                    @else
                        <span class="badge bg-warning text-dark px-3 py-1 fw-bold">
                            <i class="bi bi-exclamation-triangle me-1"></i> STATUS: DISCONNECTED
                        </span>
                    @endif
                    <span class="badge bg-white text-dark px-3 py-1 font-monospace">
                        Pengirim: {{ $deviceInfo['device'] ?? $senderNumber ?? '-' }}
                    </span>
                    <span class="badge bg-dark px-3 py-1">
                        Sisa Kuota: {{ number_format($deviceInfo['quota'] ?? 0) }} Pesan
                    </span>
                    <span class="badge bg-light text-dark px-3 py-1">
                        <i class="bi bi-globe me-1 text-primary"></i> Domain Publik: <strong class="text-primary">{{ \App\Services\WhatsAppService::getPublicBaseUrl() }}</strong>
                    </span>
                </div>
                <h4 class="fw-bold mb-1 text-white">Integrasi WhatsApp Gateway Pesantren (Fonnte API)</h4>
                <p class="text-white-50 small mb-0">
                    Sistem otomatis mengirimkan bukti kuitansi pembayaran resmi (PDF link & rincian biaya) ke nomor WhatsApp orang tua/wali santri begitu kasir tunai disimpan atau transaksi online Midtrans lunas.
                </p>
            </div>
            <div class="d-flex align-items-center gap-2">
                <button type="button" class="btn btn-light btn-sm fw-semibold shadow-sm" id="btn_refresh_device">
                    <i class="bi bi-arrow-repeat me-1"></i> Perbarui Status Device
                </button>
                <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-light btn-sm">
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
                <span class="text-muted small fw-semibold">TOTAL NOTIFIKASI</span>
                <span class="badge bg-primary-subtle text-primary p-2 rounded-circle">
                    <i class="bi bi-chat-dots fs-5"></i>
                </span>
            </div>
            <h3 class="fw-bold mb-1">{{ number_format($stats['total_notif']) }}</h3>
            <div class="small text-muted">Seluruh rekaman pesan tercatat</div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card-stat">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-muted small fw-semibold">BERHASIL TERKIRIM</span>
                <span class="badge bg-success-subtle text-success p-2 rounded-circle">
                    <i class="bi bi-check2-all fs-5"></i>
                </span>
            </div>
            <h3 class="fw-bold mb-1 text-success">{{ number_format($stats['terkirim']) }}</h3>
            <div class="small text-success fw-semibold">100% sampai di HP Wali</div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card-stat">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-muted small fw-semibold">SISA KUOTA FONNTE</span>
                <span class="badge bg-info-subtle text-info p-2 rounded-circle">
                    <i class="bi bi-speedometer2 fs-5"></i>
                </span>
            </div>
            <h3 class="fw-bold mb-1 text-info">{{ number_format($deviceInfo['quota'] ?? 1000) }}</h3>
            <div class="small text-muted">Paket: {{ $deviceInfo['package'] ?? 'Free' }} (Exp: {{ $deviceInfo['expired'] ?? '-' }})</div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card-stat">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-muted small fw-semibold">AUTO-NOTIFICATION</span>
                <span class="badge bg-success-subtle text-success p-2 rounded-circle">
                    <i class="bi bi-lightning-charge fs-5"></i>
                </span>
            </div>
            <h4 class="fw-bold mb-1 text-success">{{ $waEnabled ? 'AKTIF' : 'NONAKTIF' }}</h4>
            <div class="small text-muted">Kasir Tunai & Midtrans Hook</div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- Form Konfigurasi Token Fonnte -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-white py-3 px-4 border-0 d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="mb-0 fw-bold"><i class="bi bi-key-fill text-success me-2"></i>Kredensial API Token Fonnte</h6>
                    <small class="text-muted">Token autentikasi perangkat pengirim WhatsApp</small>
                </div>
                <span class="badge bg-success font-monospace">Fonnte API v1</span>
            </div>
            <div class="card-body px-4 pb-4 pt-1">
                <form action="{{ route('admin.whatsapp.update') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-uppercase text-muted">
                            Token API Fonnte <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-shield-lock-fill"></i></span>
                            <input type="password" name="fonnte_token" id="input_wa_token"
                                   class="form-control font-monospace"
                                   value="{{ old('fonnte_token', $token) }}"
                                   placeholder="Masukkan Token dari Fonnte" required>
                            <button class="btn btn-outline-secondary" type="button" onclick="toggleVisibility('input_wa_token', 'eye_wa_token')">
                                <i class="bi bi-eye" id="eye_wa_token"></i>
                            </button>
                        </div>
                        <small class="text-muted">Diperoleh dari dashboard Fonnte menu Device &gt; Copy Token.</small>
                    </div>

                    <div class="p-3 bg-light rounded-3 mb-3 border">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <div class="fw-semibold small text-dark">Nomor Perangkat Terhubung:</div>
                                <div class="font-monospace fw-bold text-success fs-6">{{ $deviceInfo['device'] ?? $senderNumber ?? '-' }}</div>
                            </div>
                            <div>
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2">
                                    <i class="bi bi-check-circle-fill me-1"></i> Device Online
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="form-check form-switch mb-4">
                        <input class="form-check-input" type="checkbox" role="switch" name="wa_notif_enabled" id="switch_wa_enabled" value="1" {{ $waEnabled ? 'checked' : '' }}>
                        <label class="form-check-label fw-semibold small text-dark" for="switch_wa_enabled">
                            Kirim Notifikasi Kwitansi Otomatis ke WhatsApp Wali
                        </label>
                        <div class="small text-muted" style="font-size: 0.8rem;">
                            Setiap ada transaksi lunas (kasir tunai bendahara atau transfer online Midtrans), sistem langsung menembakkan bukti kuitansi resmi ke nomor wali santri.
                        </div>
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-success btn-sm fw-bold px-4 shadow-sm">
                            <i class="bi bi-save me-1"></i> Simpan Konfigurasi Token
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Form Uji Coba Kirim Pesan Cepat (Live Test Message) -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-white py-3 px-4 border-0 d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="mb-0 fw-bold"><i class="bi bi-send-check text-primary me-2"></i>Uji Coba Kirim Pesan WhatsApp Live</h6>
                    <small class="text-muted">Kirimkan pesan uji coba langsung ke nomor HP Anda untuk memvalidasi koneksi</small>
                </div>
                <span class="badge bg-info-subtle text-info border font-monospace">Live Handshake</span>
            </div>
            <div class="card-body px-4 pb-4 pt-1">
                <form id="form_test_send">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-uppercase text-muted">
                            Nomor WhatsApp Tujuan <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-telephone-fill"></i></span>
                            <input type="text" id="test_target_phone" class="form-control font-monospace"
                                   placeholder="Contoh: 081234567890 atau 6281234567890"
                                   value="{{ Auth::user()->no_telp ?? '' }}" required>
                        </div>
                        <small class="text-muted">Gunakan nomor WhatsApp Anda sendiri untuk melihat pesan masuk.</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-uppercase text-muted">
                            Isi Pesan Uji Coba <span class="text-danger">*</span>
                        </label>
                        <textarea id="test_message_content" class="form-control" rows="3" required>*Pondok Pesantren Darul Muttaqin Al Islami*
Halo! Ini adalah pesan uji coba integrasi WhatsApp Gateway resmi pesantren. Layanan notifikasi kuitansi dan tagihan santri berjalan dengan normal.</textarea>
                    </div>

                    <div id="test_send_alert" class="mb-3" style="display: none;"></div>

                    <div class="d-flex justify-content-end">
                        <button type="button" class="btn btn-primary btn-sm fw-bold px-4 shadow-sm" id="btn_submit_test_send">
                            <i class="bi bi-send-fill me-1"></i> Kirim Pesan Uji Coba Sekarang
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Riwayat Notifikasi WhatsApp Terkini -->
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-header bg-white py-3 px-4 border-0 d-flex align-items-center justify-content-between flex-wrap gap-2">
        <div>
            <h6 class="mb-0 fw-bold"><i class="bi bi-clock-history text-success me-2"></i>Log Riwayat Pesan WhatsApp Terkirim (`notifikasi_wa`)</h6>
            <small class="text-muted">Catatan riwayat seluruh pesan yang dikirimkan oleh sistem ke wali murid.</small>
        </div>
        <span class="badge bg-light text-dark border font-monospace">Tercatat di Database</span>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr class="small text-muted text-uppercase">
                    <th class="ps-4">Waktu Kirim</th>
                    <th>Santri</th>
                    <th>No. Tujuan</th>
                    <th>Jenis Pesan</th>
                    <th>Status</th>
                    <th>Cuplikan Isi Pesan</th>
                    <th class="text-center pe-4">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentLogs as $log)
                    <tr>
                        <td class="ps-4 small text-muted" style="white-space: nowrap;">
                            {{ date('d/m/Y H:i', strtotime($log->tgl_kirim)) }}
                        </td>
                        <td class="small fw-semibold">
                            {{ $log->santri->nama_santri ?? ($log->nis ? 'NIS: ' . $log->nis : 'Uji Coba Sistem') }}
                        </td>
                        <td>
                            <a href="https://wa.me/{{ $log->no_tujuan }}" target="_blank" class="text-decoration-none fw-semibold font-monospace small text-success">
                                <i class="bi bi-whatsapp me-1"></i>{{ $log->no_tujuan }}
                            </a>
                        </td>
                        <td>
                            @if($log->jenis_pesan == 'pembayaran_sukses')
                                <span class="badge bg-success-subtle text-success border border-success-subtle">
                                    <i class="bi bi-receipt me-1"></i> Kwitansi Lunas
                                </span>
                            @elseif($log->jenis_pesan == 'pengingat_tempo')
                                <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle">
                                    <i class="bi bi-alarm me-1"></i> Pengingat Tempo
                                </span>
                            @else
                                <span class="badge bg-info-subtle text-info border border-info-subtle">
                                    {{ ucfirst(str_replace('_', ' ', $log->jenis_pesan)) }}
                                </span>
                            @endif
                        </td>
                        <td>
                            @if($log->status_kirim == 'terkirim')
                                <span class="badge bg-success"><i class="bi bi-check2-all me-1"></i>Terkirim</span>
                            @elseif($log->status_kirim == 'pending')
                                <span class="badge bg-warning text-dark"><i class="bi bi-hourglass me-1"></i>Pending</span>
                            @else
                                <span class="badge bg-danger"><i class="bi bi-x-circle me-1"></i>Gagal</span>
                            @endif
                        </td>
                        <td>
                            <div class="small text-truncate text-muted" style="max-width: 320px;" title="{{ $log->pesan }}">
                                {{ Str::limit($log->pesan, 70) }}
                            </div>
                        </td>
                        <td class="text-center pe-4">
                            <button type="button" class="btn btn-outline-secondary btn-sm p-1 px-2 btn-view-wa-detail"
                                    data-pesan="{{ e($log->pesan) }}"
                                    data-target="{{ $log->no_tujuan }}"
                                    data-waktu="{{ date('d/m/Y H:i:s', strtotime($log->tgl_kirim)) }}"
                                    data-status="{{ $log->status_kirim }}"
                                    title="Lihat Pesan Lengkap">
                                <i class="bi bi-eye"></i>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="bi bi-chat-left-text fs-2 d-block mb-2 text-secondary"></i>
                            Belum ada riwayat pengiriman pesan WhatsApp.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Rincian Pesan WhatsApp -->
<div class="modal fade" id="modalWaDetail" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow-lg">
            <div class="modal-header border-0 pb-0 px-4 pt-4">
                <h6 class="modal-title fw-bold text-success">
                    <i class="bi bi-whatsapp me-2"></i>Rincian Pesan WhatsApp Terkirim
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body px-4 py-3">
                <div class="p-2 bg-light rounded-3 mb-3 small">
                    <div>No. Tujuan: <strong id="modal_wa_target" class="font-monospace text-dark"></strong></div>
                    <div>Waktu: <span id="modal_wa_waktu" class="text-muted"></span></div>
                    <div>Status: <span id="modal_wa_status"></span></div>
                </div>
                <label class="small fw-bold text-muted text-uppercase mb-1">Isi Pesan:</label>
                <div class="p-3 rounded-3 border bg-white font-monospace small" id="modal_wa_content" style="white-space: pre-wrap; max-height: 320px; overflow-y: auto;"></div>
            </div>
            <div class="modal-footer border-0 pt-0 px-4 pb-4">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
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

    // Modal View Detail
    const modalWaDetail = new bootstrap.Modal(document.getElementById('modalWaDetail'));
    document.querySelectorAll('.btn-view-wa-detail').forEach(btn => {
        btn.addEventListener('click', function() {
            document.getElementById('modal_wa_target').textContent = this.getAttribute('data-target');
            document.getElementById('modal_wa_waktu').textContent = this.getAttribute('data-waktu');
            document.getElementById('modal_wa_content').textContent = this.getAttribute('data-pesan');
            const status = this.getAttribute('data-status');
            document.getElementById('modal_wa_status').innerHTML = status === 'terkirim'
                ? '<span class="badge bg-success">Terkirim</span>'
                : '<span class="badge bg-danger">Gagal</span>';
            modalWaDetail.show();
        });
    });

    // Refresh Device Status
    document.getElementById('btn_refresh_device').addEventListener('click', function() {
        const btn = this;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Memeriksa...';

        fetch('{{ route('admin.whatsapp.check-device') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(r => r.json())
        .then(data => {
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-arrow-repeat me-1"></i> Perbarui Status Device';
            if (data.success) {
                alert('Status Perangkat Fonnte: ' + data.status.toUpperCase() + ' (Perangkat: ' + data.device + ', Kuota: ' + data.quota + ')');
                window.location.reload();
            } else {
                alert('Status: ' + (data.message || 'Gagal terhubung'));
            }
        })
        .catch(err => {
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-arrow-repeat me-1"></i> Perbarui Status Device';
            alert('Kesalahan jaringan: ' + err);
        });
    });

    // Live Test Send Message
    document.getElementById('btn_submit_test_send').addEventListener('click', function() {
        const target = document.getElementById('test_target_phone').value.trim();
        const message = document.getElementById('test_message_content').value.trim();
        const alertBox = document.getElementById('test_send_alert');

        if (!target) {
            alert('Silakan masukkan nomor WhatsApp tujuan.');
            return;
        }

        const btn = this;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Mengirim Pesan...';
        alertBox.style.display = 'block';
        alertBox.className = 'alert alert-info py-2 small';
        alertBox.innerHTML = '<i class="bi bi-hourglass-split me-1"></i> Menghubungi WhatsApp Gateway Fonnte...';

        fetch('{{ route('admin.whatsapp.test-send') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                target_phone: target,
                test_message: message
            })
        })
        .then(r => r.json())
        .then(data => {
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-send-fill me-1"></i> Kirim Pesan Uji Coba Sekarang';

            if (data.success) {
                alertBox.className = 'alert alert-success py-2 small';
                alertBox.innerHTML = `<strong><i class="bi bi-check-circle-fill me-1"></i> Berhasil!</strong> Pesan berhasil dikirimkan ke <code>${target}</code>. Periksa aplikasi WhatsApp di HP penerima.`;
                setTimeout(() => window.location.reload(), 2500);
            } else {
                alertBox.className = 'alert alert-danger py-2 small';
                alertBox.innerHTML = `<strong><i class="bi bi-x-circle-fill me-1"></i> Gagal Kirim:</strong> ${data.message}`;
            }
        })
        .catch(err => {
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-send-fill me-1"></i> Kirim Pesan Uji Coba Sekarang';
            alertBox.className = 'alert alert-danger py-2 small';
            alertBox.innerHTML = 'Kesalahan jaringan: ' + err;
        });
    });
</script>
@endpush
@endsection

