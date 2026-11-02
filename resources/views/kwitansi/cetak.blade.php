<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kwitansi {{ $pembayaran->kwitansi->no_kwitansi }} - PP Darul Muttaqin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-family: 'Times New Roman', serif; color: #000; background: #fff; }
        .kop-surat { border-bottom: 2px solid #000; padding-bottom: 8px; margin-bottom: 20px; text-align: center; }
        .box-nominal { border: 2px solid #000; padding: 10px 15px; display: inline-block; }
        @media print {
            .no-print { display: none !important; }
            body { padding: 0 !important; }
        }
    </style>
</head>
<body class="p-4" onload="window.print()">

    <div class="no-print mb-4 d-flex justify-content-between">
        <a href="javascript:history.back()" class="btn btn-secondary btn-sm">Kembali</a>
        <button onclick="window.print()" class="btn btn-primary btn-sm">Cetak Ulang</button>
    </div>

    <div class="container" style="max-width: 800px; border: 1px dashed #999; padding: 25px;">
        <div class="kop-surat">
            <h4 class="fw-bold mb-1">PONDOK PESANTREN DARUL MUTTAQIN AL ISLAMI</h4>
            <p class="mb-0 small">Kelurahan Mulyojati, Kecamatan Metro Barat, Kota Metro, Lampung</p>
            <small class="text-muted">No. SK Kemenag RI | BAN-S/M Terakreditasi | Mudir: Mukhlasin, S.Pd</small>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="fw-bold text-uppercase mb-0 text-decoration-underline">KWITANSI PEMBAYARAN RESMI</h5>
            <div class="text-end">
                <small class="d-block text-muted">Nomor Kwitansi:</small>
                <strong>{{ $pembayaran->kwitansi->no_kwitansi }}</strong>
            </div>
        </div>

        <table class="table table-borderless table-sm mb-4" style="font-size: 0.95rem;">
            <tbody>
                <tr>
                    <td style="width: 28%;">Telah Terima Dari</td>
                    <td>: <strong>{{ $pembayaran->tagihan->santri->wali->nama_lengkap ?? 'Wali Santri' }}</strong></td>
                </tr>
                <tr>
                    <td>Nama Santri</td>
                    <td>: <strong>{{ $pembayaran->tagihan->santri->nama_santri }}</strong> (NIS: {{ $pembayaran->tagihan->santri->nis }})</td>
                </tr>
                <tr>
                    <td>Tingkat & Kelas</td>
                    <td>: {{ $pembayaran->tagihan->santri->tingkat }} - {{ $pembayaran->tagihan->santri->kelas }}</td>
                </tr>
                <tr>
                    <td>Program Unggulan</td>
                    <td>: {{ $pembayaran->tagihan->santri->program->nama_program ?? '-' }}</td>
                </tr>
                <tr>
                    <td>Untuk Pembayaran</td>
                    <td>: {{ $pembayaran->tagihan->jenisTagihan->nama_tagihan }} {{ $pembayaran->tagihan->bulan ? 'Bulan ' . $pembayaran->tagihan->nama_bulan : '' }} {{ $pembayaran->tagihan->tahun_ajaran }}</td>
                </tr>
                <tr>
                    <td>Metode Transaksi</td>
                    <td>: {{ $pembayaran->channel_bayar ?? ucfirst($pembayaran->metode_bayar) }} (Ref: {{ $pembayaran->order_id }})</td>
                </tr>
                <tr>
                    <td>Tanggal Transaksi</td>
                    <td>: {{ date('d F Y - H:i', strtotime($pembayaran->tgl_bayar)) }} WIB</td>
                </tr>
            </tbody>
        </table>

        <div class="d-flex justify-content-between align-items-center mb-5">
            <div class="box-nominal">
                <span class="small d-block text-uppercase">Terbilang Jumlah:</span>
                <h4 class="fw-bold mb-0">Rp {{ number_format($pembayaran->jumlah_bayar, 0, ',', '.') }}</h4>
            </div>
            <div class="text-muted small text-end">
                Status: <strong>LUNAS</strong><br>
                Bukti sah administrasi keuangan pesantren.
            </div>
        </div>

        <div class="row text-center mt-4">
            <div class="col-6">
                <p class="mb-5">Wali Santri,</p>
                <p class="fw-bold text-decoration-underline mb-0">{{ $pembayaran->tagihan->santri->wali->nama_lengkap ?? 'Wali Santri' }}</p>
            </div>
            <div class="col-6">
                <p class="mb-5">Metro, {{ date('d F Y', strtotime($pembayaran->kwitansi->tgl_terbit)) }}<br>Bendahara Pesantren,</p>
                <p class="fw-bold text-decoration-underline mb-0">{{ $pembayaran->verifier->nama_lengkap ?? 'Bendahara Darul Muttaqin' }}</p>
            </div>
        </div>
    </div>

</body>
</html>

