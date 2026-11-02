@extends('layouts.app')

@section('title', 'Kwitansi Resmi Pembayaran')
@section('page_title', 'Tanda Bukti Pembayaran Digital')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <a href="javascript:history.back()" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i> Kembali
            </a>
            <a href="{{ route('kwitansi.cetak', $pembayaran->id_pembayaran) }}" target="_blank" class="btn btn-success btn-sm fw-semibold shadow-sm">
                <i class="bi bi-printer me-1"></i> Cetak Kwitansi Resmi
            </a>
        </div>

        <div class="card border-0 shadow rounded-3 p-4 p-md-5 bg-white">
            <!-- Kop Surat -->
            <div class="text-center pb-3 border-bottom border-dark border-2 mb-4">
                <h4 class="fw-bold mb-1">PONDOK PESANTREN DARUL MUTTAQIN AL ISLAMI</h4>
                <p class="mb-0 small text-muted">Kelurahan Mulyojati, Kecamatan Metro Barat, Kota Metro, Lampung</p>
                <small class="text-muted">No. SK Kemenag RI | BAN-S/M Terakreditasi | Mudir: Mukhlasin, S.Pd</small>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1">
                        <i class="bi bi-check-circle me-1"></i> PEMBAYARAN SAH (LUNAS)
                    </span>
                </div>
                <div class="text-end">
                    <div class="small text-muted">Nomor Kwitansi:</div>
                    <h6 class="fw-bold text-dark mb-0 font-monospace">{{ $pembayaran->kwitansi->no_kwitansi }}</h6>
                </div>
            </div>

            <!-- Detail Data Kwitansi -->
            <div class="table-responsive border-0 mb-4">
                <table class="table table-borderless align-middle mb-0">
                    <tbody>
                        <tr>
                            <td class="text-muted" style="width: 25%;">Telah Diterima Dari</td>
                            <td class="fw-bold">: {{ $pembayaran->tagihan->santri->wali->nama_lengkap ?? 'Wali Santri' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Untuk Pembayaran Santri</td>
                            <td class="fw-bold">: {{ $pembayaran->tagihan->santri->nama_santri }} (NIS: {{ $pembayaran->tagihan->santri->nis }})</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Tingkat & Kelas</td>
                            <td>: {{ $pembayaran->tagihan->santri->tingkat }} - {{ $pembayaran->tagihan->santri->kelas }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Program Unggulan</td>
                            <td>: {{ $pembayaran->tagihan->santri->program->nama_program ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Komponen Tagihan</td>
                            <td class="fw-bold text-primary">: {{ $pembayaran->tagihan->jenisTagihan->nama_tagihan }} {{ $pembayaran->tagihan->bulan ? 'Bulan ' . $pembayaran->tagihan->nama_bulan : '' }} {{ $pembayaran->tagihan->tahun_ajaran }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Metode / Channel</td>
                            <td>: {{ $pembayaran->channel_bayar ?? ucfirst($pembayaran->metode_bayar) }} (Order ID: {{ $pembayaran->order_id }})</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Tanggal Transaksi</td>
                            <td>: {{ date('d F Y - H:i:s WIB', strtotime($pembayaran->tgl_bayar)) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Kotak Jumlah Pembayaran -->
            <div class="p-3 bg-light border rounded-3 mb-5 d-flex align-items-center justify-content-between">
                <div>
                    <span class="small text-muted text-uppercase fw-semibold d-block">Jumlah Uang Diterima:</span>
                    <h3 class="fw-bold text-success mb-0">Rp {{ number_format($pembayaran->jumlah_bayar, 0, ',', '.') }}</h3>
                </div>
                <div class="text-end">
                    <span class="badge bg-secondary mb-1">Status Tagihan Induk</span>
                    <div>{!! $pembayaran->tagihan->status_badge !!}</div>
                </div>
            </div>

            <!-- Tanda Tangan & Cap Digital -->
            <div class="row pt-4 text-center">
                <div class="col-6">
                    <div class="small text-muted mb-5">Wali Santri,</div>
                    <div class="fw-bold text-decoration-underline">{{ $pembayaran->tagihan->santri->wali->nama_lengkap ?? 'Wali Santri' }}</div>
                </div>
                <div class="col-6">
                    <div class="small text-muted mb-5">Metro, {{ date('d F Y', strtotime($pembayaran->kwitansi->tgl_terbit)) }}<br>Bendahara Pesantren,</div>
                    <div class="fw-bold text-decoration-underline">{{ $pembayaran->verifier->nama_lengkap ?? 'Bendahara Darul Muttaqin' }}</div>
                    <small class="text-muted d-block font-monospace">Validasi Digital ID: DMTQ-{{ $pembayaran->kwitansi->id_kwitansi }}</small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

