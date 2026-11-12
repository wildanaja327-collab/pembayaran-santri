@extends('layouts.app')

@section('title', 'Konfigurasi Sistem')
@section('page_title', 'Konfigurasi Identitas Pesantren & Gateway')

@section('content')
<div class="row g-4">
    <!-- Form Pengaturan -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-header bg-white py-3 border-0">
                <h6 class="mb-0 fw-bold"><i class="bi bi-sliders text-primary me-2"></i>Parameter & Identitas Lembaga</h6>
                <small class="text-muted">Data identitas ini digunakan pada kop kwitansi, cetakan laporan, dan integrasi payment gateway.</small>
            </div>

            <div class="card-body">
                <form action="{{ route('admin.pengaturan.update') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-4">
                        <h6 class="text-uppercase small fw-bold text-success border-bottom pb-2 mb-3">
                            <i class="bi bi-bank2 me-1"></i> Identitas Utama Pesantren
                        </h6>

                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Nama Resmi Pesantren / Lembaga <span class="text-danger">*</span></label>
                            <input type="text" name="pesantren_nama" class="form-control" value="{{ old('pesantren_nama', $settings['pesantren_nama'] ?? '') }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Alamat Lengkap Pesantren <span class="text-danger">*</span></label>
                            <textarea name="pesantren_alamat" class="form-control" rows="2" required>{{ old('pesantren_alamat', $settings['pesantren_alamat'] ?? '') }}</textarea>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">No. Telepon / WhatsApp <span class="text-danger">*</span></label>
                                <input type="text" name="pesantren_telepon" class="form-control" value="{{ old('pesantren_telepon', $settings['pesantren_telepon'] ?? '') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">Email Resmi Pesantren <span class="text-danger">*</span></label>
                                <input type="email" name="pesantren_email" class="form-control" value="{{ old('pesantren_email', $settings['pesantren_email'] ?? '') }}" required>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <h6 class="text-uppercase small fw-bold text-success border-bottom pb-2 mb-3">
                            <i class="bi bi-person-badge me-1"></i> Penandatangan & Legalitas Kwitansi
                        </h6>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">Pimpinan / Pengasuh Pesantren <span class="text-danger">*</span></label>
                                <input type="text" name="pimpinan_nama" class="form-control" value="{{ old('pimpinan_nama', $settings['pimpinan_nama'] ?? '') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">Nama Bendahara Resmi Pesantren <span class="text-danger">*</span></label>
                                <input type="text" name="bendahara_nama" class="form-control" value="{{ old('bendahara_nama', $settings['bendahara_nama'] ?? '') }}" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Catatan Kaki Kwitansi (Footer Legalitas)</label>
                            <input type="text" name="kwitansi_footer" class="form-control" value="{{ old('kwitansi_footer', $settings['kwitansi_footer'] ?? '') }}">
                            <small class="text-muted">Teks catatan resmi yang tercetak di bagian bawah kwitansi pembayaran.</small>
                        </div>
                    </div>

                    <div class="mb-4">
                        <h6 class="text-uppercase small fw-bold text-success border-bottom pb-2 mb-3">
                            <i class="bi bi-credit-card me-1"></i> Integrasi Payment Gateway (Midtrans)
                        </h6>

                        <div class="p-3 rounded-3 bg-light border d-flex align-items-center justify-content-between flex-wrap gap-2">
                            <div>
                                <div class="fw-bold text-dark mb-1">
                                    <i class="bi bi-shield-check text-success me-1"></i> Menu Khusus Konfigurasi Midtrans
                                </div>
                                <div class="small text-muted">
                                    Pengaturan Server Key, Client Key, Webhook, Mode Transaksi (Sandbox/Production), dan Biaya Layanan kini dikelola secara mandiri melalui menu khusus Gateway Midtrans.
                                </div>
                            </div>
                            <a href="{{ route('admin.midtrans.index') }}" class="btn btn-warning text-dark btn-sm fw-semibold">
                                <i class="bi bi-credit-card-2-front me-1"></i> Buka Menu Gateway Midtrans &rarr;
                            </a>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 pt-2 border-top">
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary btn-sm">Kembali</a>
                        <button type="submit" class="btn btn-primary btn-sm fw-semibold">
                            <i class="bi bi-save me-1"></i> Simpan Konfigurasi Sistem
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Live Preview Header Kwitansi -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm rounded-3 mb-4">
            <div class="card-header bg-white py-3 border-0">
                <h6 class="mb-0 fw-bold"><i class="bi bi-eye text-primary me-2"></i>Pratinjau Kop Kwitansi</h6>
                <small class="text-muted">Tampilan kop bukti pembayaran otomatis:</small>
            </div>
            <div class="card-body">
                <div class="p-3 border rounded bg-white text-center shadow-sm">
                    <div class="fw-bold text-success fs-6 text-uppercase">{{ $settings['pesantren_nama'] ?? 'PP Darul Muttaqin' }}</div>
                    <div class="small text-muted mb-1">{{ $settings['pesantren_alamat'] ?? 'Metro, Lampung' }}</div>
                    <div class="small text-muted mb-2"><i class="bi bi-telephone me-1"></i>{{ $settings['pesantren_telepon'] ?? '-' }}</div>
                    <hr class="my-2">
                    <div class="fw-bold small text-dark">BUKTI PEMBAYARAN SANTRI (KWITANSI)</div>
                    <div class="small text-muted" style="font-size: 0.75rem;">No: KWT/{{ date('Ym') }}/XXXX</div>
                    <div class="mt-3 p-2 bg-light rounded text-start" style="font-size: 0.75rem;">
                        <div><strong>Bendahara:</strong> {{ $settings['bendahara_nama'] ?? 'Ahmad Fauzi' }}</div>
                        <div><strong>Pimpinan:</strong> {{ $settings['pimpinan_nama'] ?? 'KH. Ridwan' }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm rounded-3 bg-light">
            <div class="card-body small text-muted">
                <div class="fw-bold text-dark mb-1"><i class="bi bi-shield-check text-success me-1"></i>Single Source of Truth</div>
                Setiap kali data di formulir ini diperbarui, kwitansi cetak, PDF kuitansi santri, serta laporan keuangan bendahara otomatis merujuk ke data terbaru tanpa perlu mengubah kode sumber Blade maupun file PHP.
            </div>
        </div>
    </div>
</div>
@endsection

