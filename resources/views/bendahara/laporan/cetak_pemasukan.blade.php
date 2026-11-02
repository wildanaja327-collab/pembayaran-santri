<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak Laporan Pemasukan - PP Darul Muttaqin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-family: 'Times New Roman', serif; color: #000; }
        .kop-surat { border-bottom: 3px double #000; padding-bottom: 10px; margin-bottom: 20px; text-align: center; }
        @media print {
            .no-print { display: none !important; }
        }
    </style>
</head>
<body class="p-4">

    <div class="no-print mb-4 d-flex justify-content-between">
        <a href="javascript:history.back()" class="btn btn-secondary btn-sm">Kembali</a>
        <button onclick="window.print()" class="btn btn-primary btn-sm">Cetak Sekarang (Print / PDF)</button>
    </div>

    <div class="kop-surat">
        <h4 class="fw-bold mb-1">PONDOK PESANTREN DARUL MUTTAQIN AL ISLAMI</h4>
        <p class="mb-0 small">Kelurahan Mulyojati, Kecamatan Metro Barat, Kota Metro, Lampung</p>
        <p class="mb-0 small">SK Kemenag RI | BAN-S/M Terakreditasi | Pendiri: Drs. H. Amin Nurdin, M.A | Mudir: Mukhlasin, S.Pd</p>
    </div>

    <div class="text-center mb-4">
        <h5 class="fw-bold text-uppercase mb-1">LAPORAN REKAPITULASI PEMASUKAN KEUANGAN</h5>
        <div class="small">Periode: {{ $periode }}</div>
    </div>

    <table class="table table-bordered table-sm align-middle mb-4" style="font-size: 0.9rem;">
        <thead class="table-light text-center">
            <tr>
                <th>No</th>
                <th>No Kwitansi</th>
                <th>Tanggal</th>
                <th>Nama Santri</th>
                <th>NIS</th>
                <th>Komponen Tagihan</th>
                <th>Metode / Channel</th>
                <th class="text-end">Jumlah Masuk</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pemasukan as $index => $p)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $p->kwitansi->no_kwitansi ?? '-' }}</td>
                    <td>{{ date('d/m/Y H:i', strtotime($p->tgl_bayar)) }}</td>
                    <td>{{ $p->tagihan->santri->nama_santri ?? '-' }}</td>
                    <td class="text-center">{{ $p->tagihan->santri->nis ?? '-' }}</td>
                    <td>{{ $p->tagihan->jenisTagihan->nama_tagihan ?? '-' }}</td>
                    <td>{{ $p->channel_bayar ?? ucfirst($p->metode_bayar) }}</td>
                    <td class="text-end">Rp {{ number_format($p->jumlah_bayar, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot class="fw-bold">
            <tr>
                <td colspan="7" class="text-end text-uppercase">Total Penerimaan Bersih:</td>
                <td class="text-end">Rp {{ number_format($totalNominal, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

    <div class="row mt-5" style="page-break-inside: avoid;">
        <div class="col-6 text-center">
            <p class="mb-5">Mengetahui,<br><strong>Mudir Pondok Pesantren</strong></p>
            <p class="fw-bold text-decoration-underline mb-0">MUKHLASIN, S.Pd</p>
        </div>
        <div class="col-6 text-center">
            <p class="mb-5">Metro, {{ date('d F Y') }}<br><strong>Bendahara Pesantren</strong></p>
            <p class="fw-bold text-decoration-underline mb-0">{{ Auth::user()->nama_lengkap }}</p>
        </div>
    </div>

</body>
</html>

