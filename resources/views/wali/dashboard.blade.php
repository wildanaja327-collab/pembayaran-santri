@extends('layouts.app')

@section('title', 'Portal Wali Santri')
@section('page_title', 'Portal Tagihan & Pembayaran Santri')

@section('content')
<!-- Profil Santri Yang Diampu -->
<div class="row g-3 mb-4">
    @forelse($santriList as $s)
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-3 p-3 bg-white">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-success text-white rounded-circle p-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                        <i class="bi bi-person-fill fs-4"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div class="d-flex align-items-center justify-content-between">
                            <h6 class="fw-bold mb-1 text-dark">{{ $s->nama_santri }}</h6>
                            <span class="badge {{ $s->tingkat == 'SMP' ? 'badge-smp' : 'badge-sma' }}">{{ $s->tingkat }}</span>
                        </div>
                        <div class="small text-muted">
                            NIS: <strong>{{ $s->nis }}</strong> | Kelas: {{ $s->kelas }}
                        </div>
                        <div class="small text-muted">
                            Program: <span class="badge bg-light text-dark border">{{ $s->program->nama_program ?? '-' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="alert alert-warning">Belum ada data santri yang ditautkan ke akun Anda. Silakan hubungi bendahara pesantren.</div>
        </div>
    @endforelse
</div>

<!-- Informasi Total Tunggakan -->
<div class="alert {{ $totalTunggakan > 0 ? 'alert-warning border-warning' : 'alert-success border-success' }} py-3 mb-4 shadow-sm">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
        <div class="d-flex align-items-center gap-3">
            <i class="bi {{ $totalTunggakan > 0 ? 'bi-exclamation-circle-fill text-warning' : 'bi-check-circle-fill text-success' }} fs-2"></i>
            <div>
                <h6 class="fw-bold mb-0">Total Kewajiban Tagihan Aktif</h6>
                <small class="text-muted">Total biaya yang belum diselesaikan untuk seluruh ananda santri.</small>
            </div>
        </div>
        <div>
            <h4 class="fw-bold mb-0 {{ $totalTunggakan > 0 ? 'text-danger' : 'text-success' }}">
                Rp {{ number_format($totalTunggakan, 0, ',', '.') }}
            </h4>
        </div>
    </div>
</div>

<!-- Daftar Tagihan Aktif & Tombol Bayar Midtrans -->
<div class="card border-0 shadow-sm rounded-3 mb-4">
    <div class="card-header bg-white py-3 border-0">
        <h6 class="mb-0 fw-bold"><i class="bi bi-receipt me-2 text-danger"></i>Tagihan Belum Lunas (Bayar via Payment Gateway)</h6>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr class="small text-muted text-uppercase">
                    <th>Santri</th>
                    <th>Komponen Tagihan</th>
                    <th>Periode</th>
                    <th>Total Biaya</th>
                    <th>Sudah Dibayar</th>
                    <th>Sisa Tagihan</th>
                    <th>Status</th>
                    <th class="text-center">Aksi Bayar</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tagihanBelumLunas as $t)
                    <tr>
                        <td class="fw-bold">{{ $t->santri->nama_santri }}</td>
                        <td>{{ $t->jenisTagihan->nama_tagihan }}</td>
                        <td>{{ $t->bulan ? $t->nama_bulan . ' ' : '' }}{{ $t->tahun_ajaran }}</td>
                        <td>Rp {{ number_format($t->nominal_tagihan, 0, ',', '.') }}</td>
                        <td class="text-success">Rp {{ number_format($t->total_terbayar, 0, ',', '.') }}</td>
                        <td class="fw-bold text-danger">Rp {{ number_format($t->sisa_tagihan, 0, ',', '.') }}</td>
                        <td>{!! $t->status_badge !!}</td>
                        <td class="text-center">
                            <button type="button" class="btn btn-success btn-sm fw-bold px-3 shadow-sm btn-pay-midtrans" 
                                    data-id="{{ $t->id_tagihan }}"
                                    data-title="{{ $t->jenisTagihan->nama_tagihan }}"
                                    data-sisa="{{ (int)$t->sisa_tagihan }}"
                                    data-santri="{{ $t->santri->nama_santri }}">
                                <i class="bi bi-credit-card me-1"></i> Bayar Sekarang
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="bi bi-check-circle fs-3 text-success d-block mb-2"></i>
                            Alhamdulillah! Seluruh tagihan pembayaran ananda sudah lunas.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Riwayat Pembayaran & Kwitansi -->
<div class="card border-0 shadow-sm rounded-3">
    <div class="card-header bg-white py-3 border-0">
        <h6 class="mb-0 fw-bold"><i class="bi bi-clock-history me-2 text-primary"></i>Riwayat Pembayaran & Kwitansi Resmi</h6>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr class="small text-muted text-uppercase">
                    <th>No Kwitansi</th>
                    <th>Tanggal Bayar</th>
                    <th>Santri</th>
                    <th>Komponen Biaya</th>
                    <th>Channel / Metode</th>
                    <th>Jumlah Dibayar</th>
                    <th>Status</th>
                    <th class="text-center">Kwitansi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($riwayatPembayaran as $p)
                    <tr>
                        <td>
                            @if($p->kwitansi)
                                <span class="badge bg-light text-dark border font-monospace">{{ $p->kwitansi->no_kwitansi }}</span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td class="small">{{ date('d/m/Y H:i', strtotime($p->tgl_bayar)) }}</td>
                        <td class="fw-semibold">{{ $p->tagihan->santri->nama_santri }}</td>
                        <td>{{ $p->tagihan->jenisTagihan->nama_tagihan }}</td>
                        <td><span class="badge bg-light text-dark border">{{ $p->channel_bayar ?? ucfirst($p->metode_bayar) }}</span></td>
                        <td class="fw-bold text-success">Rp {{ number_format($p->jumlah_bayar, 0, ',', '.') }}</td>
                        <td>
                            @if($p->status_transaksi == 'settlement')
                                <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Berhasil</span>
                            @elseif($p->status_transaksi == 'pending')
                                <span class="badge bg-warning text-dark"><i class="bi bi-hourglass me-1"></i>Pending</span>
                            @else
                                <span class="badge bg-secondary">{{ ucfirst($p->status_transaksi) }}</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($p->kwitansi)
                                <a href="{{ route('kwitansi.show', $p->id_pembayaran) }}" class="btn btn-sm btn-outline-success">
                                    <i class="bi bi-printer me-1"></i> Lihat Kwitansi
                                </a>
                            @else
                                <span class="small text-muted">-</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-4 text-muted">Belum ada riwayat transaksi.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Konfirmasi Pembayaran Midtrans -->
<div class="modal fade" id="modalPaymentConfirm" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h6 class="modal-title fw-bold"><i class="bi bi-shield-check text-success me-2"></i>Pembayaran Online (Payment Gateway)</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="p-3 bg-light rounded-3 mb-3">
                    <small class="text-muted d-block">Santri: <strong id="modal_santri_name" class="text-dark"></strong></small>
                    <small class="text-muted d-block">Tagihan: <strong id="modal_tagihan_name" class="text-dark"></strong></small>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-semibold">Jumlah Yang Ingin Dibayar (Rp)</label>
                    <input type="number" id="modal_input_bayar" class="form-control form-control-lg fw-bold text-success" min="10000">
                    <small class="text-muted">Dapat dibayar lunas atau sebagian (cicilan).</small>
                </div>

                <div class="d-flex justify-content-between small text-muted mb-2">
                    <span>Biaya Layanan Gateway:</span>
                    <span>Rp 2.500</span>
                </div>
                <div class="alert alert-info py-2 small mb-0">
                    <i class="bi bi-info-circle me-1"></i> Pembayaran mendukung <strong>Virtual Account (BCA, BNI, BRI, Mandiri)</strong> dan <strong>QRIS</strong>. Status lunas langsung ter-verifikasi otomatis dalam beberapa detik!
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                <button type="button" id="btn_confirm_snap" class="btn btn-success btn-sm fw-bold px-3">
                    <i class="bi bi-wallet2 me-1"></i> Lanjut Ke Pembayaran
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    let activeTagihanId = null;
    const modalPaymentConfirm = new bootstrap.Modal(document.getElementById('modalPaymentConfirm'));

    document.querySelectorAll('.btn-pay-midtrans').forEach(btn => {
        btn.addEventListener('click', function() {
            activeTagihanId = this.getAttribute('data-id');
            const title = this.getAttribute('data-title');
            const sisa = this.getAttribute('data-sisa');
            const santri = this.getAttribute('data-santri');

            document.getElementById('modal_santri_name').innerText = santri;
            document.getElementById('modal_tagihan_name').innerText = title;
            document.getElementById('modal_input_bayar').value = sisa;
            document.getElementById('modal_input_bayar').max = sisa;

            modalPaymentConfirm.show();
        });
    });

    document.getElementById('btn_confirm_snap').addEventListener('click', function() {
        if (!activeTagihanId) return;

        const nominalBayar = document.getElementById('modal_input_bayar').value;
        const btn = this;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Menghubungkan Gateway...';

        fetch(`/wali/tagihan/${activeTagihanId}/pay`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ nominal_bayar: nominalBayar })
        })
        .then(res => res.json())
        .then(data => {
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-wallet2 me-1"></i> Lanjut Ke Pembayaran';
            modalPaymentConfirm.hide();

            if (data.snap_token) {
                // Panggil Popup Snap Midtrans
                window.snap.pay(data.snap_token, {
                    onSuccess: function(result) {
                        alert('Alhamdulillah! Pembayaran Anda berhasil diterima.');
                        window.location.reload();
                    },
                    onPending: function(result) {
                        alert('Menunggu pembayaran diselesaikan. Silakan transfer sesuai nomor VA atau QRIS yang tertera.');
                        window.location.reload();
                    },
                    onError: function(result) {
                        alert('Pembayaran gagal atau dibatalkan.');
                        window.location.reload();
                    },
                    onClose: function() {
                        window.location.reload();
                    }
                });
            } else {
                alert('Gagal memproses payment gateway: ' + (data.error || 'Terjadi kesalahan'));
            }
        })
        .catch(err => {
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-wallet2 me-1"></i> Lanjut Ke Pembayaran';
            alert('Koneksi terputus ke server gateway: ' + err);
        });
    });
</script>
@endpush
@endsection

