@extends('layouts.app')

@section('title', 'Kwitansi Resmi Pembayaran')
@section('page_title', 'Tanda Bukti Pembayaran Digital')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-9">
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
            <a href="javascript:history.back()" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i> Kembali
            </a>
            <div class="d-flex gap-2 flex-wrap">
                @php
                    $targetPhone = $pembayaran->tagihan->santri->wali?->no_telp ?? $pembayaran->tagihan->santri->no_telp;
                    $cleanPhone = preg_replace('/[^0-9]/', '', $targetPhone ?? '');
                    if (str_starts_with($cleanPhone, '0')) {
                        $cleanPhone = '62' . substr($cleanPhone, 1);
                    }
                    $publicKwtUrl = $pembayaran->getPublicKwitansiUrl();
                    $waText = urlencode("Assalamu'alaikum Warahmatullahi Wabarakatuh.\nBerikut kami sampaikan Bukti Kwitansi Resmi Pembayaran Santri:\n\n*Pondok Pesantren Darul Muttaqin Al Islami*\nNo. Kwitansi: " . ($pembayaran->kwitansi->no_kwitansi ?? '-') . "\nSantri: " . ($pembayaran->tagihan->santri->nama_santri ?? '-') . "\nNominal: Rp " . number_format($pembayaran->jumlah_bayar, 0, ',', '.') . "\nStatus: LUNAS\n\nUnduh Dokumen Kwitansi Sah (PDF):\n" . $publicKwtUrl);
                @endphp

                <!-- Tombol Cetak Kwitansi Utama -->
                <a href="{{ $publicKwtUrl }}" target="_blank" class="btn btn-outline-dark btn-sm fw-semibold shadow-sm">
                    <i class="bi bi-printer me-1"></i> Cetak Kwitansi Resmi
                </a>

                <!-- Dropdown Opsi Lainnya (Cadangan Resend / Chat Manual) -->
                <div class="dropdown d-inline-block">
                    <button class="btn btn-outline-secondary btn-sm dropdown-toggle fw-semibold shadow-sm" type="button" id="dropdownKwitansiAksi" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-three-dots me-1"></i> Opsi Lainnya
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow border-0 rounded-3 mt-1 py-1" aria-labelledby="dropdownKwitansiAksi" style="min-width: 240px;">
                        <li>
                            <form action="{{ route('kwitansi.kirim-wa', $pembayaran->id_pembayaran) }}" method="POST">
                                @csrf
                                <button type="submit" class="dropdown-item small py-2 d-flex align-items-center gap-2">
                                    <i class="bi bi-whatsapp text-success fs-6"></i>
                                    <div>
                                        <div class="fw-semibold">Kirim Ulang Notifikasi WA</div>
                                        <div class="text-muted" style="font-size: 0.72rem;">Kirim otomatis via Fonnte Gateway</div>
                                    </div>
                                </button>
                            </form>
                        </li>
                        @if(!empty($cleanPhone))
                            <li><hr class="dropdown-divider my-1"></li>
                            <li>
                                <a href="https://wa.me/{{ $cleanPhone }}?text={{ $waText }}" target="_blank" class="dropdown-item small py-2 d-flex align-items-center gap-2">
                                    <i class="bi bi-chat-dots text-primary fs-6"></i>
                                    <div>
                                        <div class="fw-semibold">Buka Chat WA Manual</div>
                                        <div class="text-muted" style="font-size: 0.72rem;">Buka WhatsApp Web/App (Cadangan)</div>
                                    </div>
                                </a>
                            </li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-lg rounded-3 p-4 p-md-5 bg-white position-relative overflow-hidden" style="border: 2px solid #e2e8f0 !important;">
            <!-- Subtle Watermark -->
            <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%) rotate(-25deg); font-size: 7rem; font-weight: 900; color: rgba(25, 135, 84, 0.05); user-select: none; pointer-events: none; z-index: 0; letter-spacing: 0.3em;">
                LUNAS
            </div>

            <div style="position: relative; z-index: 1;">
                <!-- Kop Surat -->
                <div class="d-flex align-items-center gap-3 pb-3 border-bottom border-dark border-2 mb-4">
                    <div class="bg-white border rounded-circle p-1 d-flex align-items-center justify-content-center flex-shrink-0 shadow-sm" style="width: 60px; height: 60px;">
                        <img src="{{ asset('img/logo.jpg') }}" alt="Logo PP Darul Muttaqin" class="rounded-circle" style="width: 100%; height: 100%; object-fit: contain;">
                    </div>
                    <div class="text-center flex-grow-1">
                        <h4 class="fw-bold mb-1 text-uppercase text-success">{{ \App\Models\Pengaturan::get('pesantren_nama', 'PONDOK PESANTREN DARUL MUTTAQIN AL ISLAMI') }}</h4>
                        <p class="mb-0 small text-muted">{{ \App\Models\Pengaturan::get('pesantren_alamat', 'Jl. Pesantren No. 1, Mulyojati 16 C, Metro Barat, Kota Metro, Lampung') }}</p>
                        <small class="text-muted">Telp/WA: {{ \App\Models\Pengaturan::get('pesantren_telepon', '0812-3456-7890') }} &bull; Email: {{ \App\Models\Pengaturan::get('pesantren_email', 'info@darulmuttaqin.ac.id') }}</small>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 fw-semibold">
                            <i class="bi bi-check-circle-fill me-1"></i> PEMBAYARAN SAH (LUNAS)
                        </span>
                    </div>
                    <div class="text-end">
                        <div class="small text-muted">Nomor Kwitansi:</div>
                        <h6 class="fw-bold text-dark mb-0 font-monospace fs-5">{{ $pembayaran->kwitansi->no_kwitansi }}</h6>
                    </div>
                </div>

                <!-- Detail Data Kwitansi -->
                <div class="table-responsive border-0 mb-4">
                    <table class="table table-borderless align-middle mb-0">
                        <tbody>
                            <tr>
                                <td class="text-muted" style="width: 28%;">Telah Diterima Dari</td>
                                <td class="fw-bold">: {{ $pembayaran->tagihan->santri->wali->nama_lengkap ?? 'Wali Santri' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Untuk Pembayaran Santri</td>
                                <td class="fw-bold text-dark">: {{ $pembayaran->tagihan->santri->nama_santri }} (NIS: {{ $pembayaran->tagihan->santri->nis }})</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Tingkat & Kelas</td>
                                <td>: {{ $pembayaran->tagihan->santri->tingkat }} - {{ $pembayaran->tagihan->santri->kelas }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Program Unggulan</td>
                                <td class="text-success fw-semibold">: {{ $pembayaran->tagihan->santri->program->nama_program ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Komponen Tagihan</td>
                                <td class="fw-bold text-primary">: {{ $pembayaran->tagihan->jenisTagihan->nama_tagihan }} {{ $pembayaran->tagihan->bulan ? 'Bulan ' . $pembayaran->tagihan->nama_bulan : '' }} {{ $pembayaran->tagihan->tahun_ajaran }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Metode / Channel</td>
                                <td>: {{ $pembayaran->channel_bayar ?? ucfirst($pembayaran->metode_bayar) }} (Order ID: <code>{{ $pembayaran->order_id }}</code>)</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Waktu Transaksi Masuk</td>
                                <td>: {{ date('d F Y - H:i:s WIB', strtotime($pembayaran->tgl_bayar)) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Kotak Jumlah Pembayaran & Terbilang -->
                <div class="p-3 bg-light border border-success-subtle rounded-3 mb-4">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <div>
                            <span class="small text-muted text-uppercase fw-semibold d-block">Jumlah Uang Diterima:</span>
                            <h3 class="fw-bold text-success mb-0">Rp {{ number_format($pembayaran->jumlah_bayar, 0, ',', '.') }}</h3>
                        </div>
                        <div class="text-end">
                            <span class="badge bg-secondary mb-1">Status Induk Tagihan</span>
                            <div>{!! $pembayaran->tagihan->status_badge !!}</div>
                        </div>
                    </div>
                    <div class="pt-2 border-top small text-muted">
                        <strong>Terbilang:</strong> <em># {{ \App\Helpers\Terbilang::rupiah($pembayaran->jumlah_bayar) }} #</em>
                    </div>
                </div>

                <!-- Keaslian Digital -->
                <div class="p-2 px-3 bg-white border rounded text-muted small font-monospace mb-4 d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-shield-check text-success me-1"></i>Security Hash: {{ hash('sha256', $pembayaran->order_id . $pembayaran->tgl_bayar) }}</span>
                    <span class="badge bg-light text-dark border">Verified Digital Receipt</span>
                </div>

                <!-- Tanda Tangan & Cap Digital -->
                <div class="row pt-2 text-center">
                    <div class="col-6">
                        <div class="small text-muted mb-5">Penyetor / Wali Santri,</div>
                        <div class="fw-bold text-decoration-underline">{{ $pembayaran->tagihan->santri->wali->nama_lengkap ?? 'Wali Santri' }}</div>
                        <small class="text-muted">Orang Tua / Wali Santri</small>
                    </div>
                    <div class="col-6">
                        <div class="small text-muted mb-5">
                            Metro, {{ date('d F Y', strtotime($pembayaran->kwitansi->tgl_terbit)) }}<br>
                            Bendahara Penerima Pesantren,
                        </div>
                        <div class="fw-bold text-decoration-underline">{{ $pembayaran->verifier->nama_lengkap ?? \App\Models\Pengaturan::get('bendahara_nama', 'Ahmad Fauzi, S.E.') }}</div>
                        <small class="text-muted d-block font-monospace">Validasi Digital ID: DMTQ-{{ $pembayaran->kwitansi->id_kwitansi }}</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
