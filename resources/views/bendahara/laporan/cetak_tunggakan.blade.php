<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekapitulasi Tunggakan Santri - {{ \App\Models\Pengaturan::get('pesantren_nama', 'PP Darul Muttaqin') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            color: #000;
            background: #fff;
        }
        .kop-surat {
            border-bottom: 3px double #000;
            padding-bottom: 12px;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 20px;
        }
        .kop-logo {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            background: #14532d;
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .kop-text {
            flex-grow: 1;
            text-align: center;
        }
        .kop-text h4 {
            font-size: 1.3rem;
            font-weight: 800;
            margin-bottom: 2px;
            color: #14532d;
        }
        .kop-text p {
            margin-bottom: 2px;
            font-size: 0.85rem;
        }
        @media screen and (max-width: 767.98px) {
            body { padding: 0.75rem !important; }
            .kop-surat { flex-direction: column; text-align: center; gap: 8px; }
            .kop-text h4 { font-size: 1.05rem; }
            .kop-logo { width: 64px; height: 64px; }
            .no-print { flex-wrap: wrap; gap: 0.5rem; }
            .no-print .btn { flex: 1 1 auto; }
            .print-scroll { overflow-x: auto; -webkit-overflow-scrolling: touch; }
            .print-scroll > table { min-width: 980px; }
            .ttd-row .col-6 { flex: 0 0 100%; max-width: 100%; margin-bottom: 1.5rem; }
        }
        @media print {
            .no-print { display: none !important; }
            body { padding: 0 !important; }
            .print-scroll { overflow: visible !important; }
            .print-scroll > table { min-width: 0 !important; }
        }
    </style>
</head>
<body class="p-4">

    <div class="no-print mb-4 d-flex justify-content-between">
        <a href="javascript:history.back()" class="btn btn-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
        <button onclick="window.print()" class="btn btn-danger btn-sm fw-semibold">
            <i class="bi bi-printer me-1"></i> Cetak Dokumen Tunggakan (Print / PDF)
        </button>
    </div>

    <!-- Kop Surat Lembaga -->
    <div class="kop-surat">
        <div class="kop-logo bg-white p-1 shadow-sm border">
            <img src="{{ asset('img/logo.jpg') }}" alt="Logo PP Darul Muttaqin" class="rounded-circle" style="width: 100%; height: 100%; object-fit: contain;">
        </div>
        <div class="kop-text">
            <h4 class="text-uppercase">{{ \App\Models\Pengaturan::get('pesantren_nama', 'PONDOK PESANTREN DARUL MUTTAQIN AL ISLAMI') }}</h4>
            <p>{{ \App\Models\Pengaturan::get('pesantren_alamat', 'Jl. Pesantren No. 1, Mulyojati 16 C, Metro Barat, Kota Metro, Lampung') }}</p>
            <p class="small text-muted mb-0">Telp/WA: {{ \App\Models\Pengaturan::get('pesantren_telepon', '0812-3456-7890') }} &bull; Email: {{ \App\Models\Pengaturan::get('pesantren_email', 'info@darulmuttaqin.ac.id') }}</p>
        </div>
    </div>

    <!-- Judul Dokumen Laporan -->
    <div class="text-center mb-4">
        <h5 class="fw-bold text-uppercase mb-1" style="letter-spacing: 0.05em; text-decoration: underline;">REKAPITULASI PIUTANG & TUNGGAKAN SANTRI</h5>
        <div class="small text-muted">Nomor Registrasi: <strong>{{ $noDokumen }}</strong></div>
        <div class="small fw-semibold mt-1">Tanggal Terbit Rekap: {{ date('d F Y') }}</div>
    </div>

    <div class="print-scroll">
    <table class="table table-bordered table-sm align-middle mb-4" style="font-size: 0.85rem;">
        <thead class="table-light text-center">
            <tr>
                <th style="width: 4%;">No</th>
                <th>NIS</th>
                <th>Nama Santri</th>
                <th>Tingkat / Kelas</th>
                <th>Program</th>
                <th>Wali Santri</th>
                <th>No Telepon</th>
                <th>Komponen Tagihan</th>
                <th>Jatuh Tempo</th>
                <th class="text-end">Sisa Piutang</th>
                <th>Keterangan / Alasan Penundaan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($tunggakan as $index => $t)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="text-center font-monospace">{{ $t->nis }}</td>
                    <td><strong>{{ $t->santri->nama_santri ?? '-' }}</strong></td>
                    <td class="text-center">{{ $t->santri->tingkat ?? '-' }} {{ $t->santri->kelas ?? '' }}</td>
                    <td>{{ $t->santri->program->nama_program ?? '-' }}</td>
                    <td>{{ $t->santri->wali->nama_lengkap ?? '-' }}</td>
                    <td class="small">{{ $t->santri->wali->no_telp ?? '-' }}</td>
                    <td>
                        {{ $t->jenisTagihan->nama_tagihan ?? '-' }}
                        <small class="text-muted d-block">{{ $t->bulan ? 'Bln ' . $t->nama_bulan : '' }} {{ $t->tahun_ajaran }}</small>
                    </td>
                    <td class="text-center small">
                        {{ $t->jatuh_tempo ? date('d/m/Y', strtotime($t->jatuh_tempo)) : '-' }}
                    </td>
                    <td class="text-end fw-bold text-danger">Rp {{ number_format($t->sisa_tagihan, 0, ',', '.') }}</td>
                    <td class="small" style="max-width: 200px;">
                        @if($t->kategori_info)
                            <strong class="d-block text-dark">{{ $t->kategori_info['label'] }}</strong>
                        @endif
                        @if($t->alasan_tunggakan)
                            <div>{{ $t->alasan_tunggakan }}</div>
                        @endif
                        @if($t->tgl_janji_bayar)
                            <div class="fst-italic text-secondary" style="font-size: 0.78rem;">Janji Bayar: {{ date('d/m/Y', strtotime($t->tgl_janji_bayar)) }}</div>
                        @endif
                        @if(!$t->kategori_info && !$t->alasan_tunggakan && !$t->tgl_janji_bayar)
                            <span class="text-muted fst-italic">Belum ada keterangan</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
        <tfoot class="fw-bold">
            <tr class="table-light">
                <td colspan="9" class="text-end text-uppercase">Total Seluruh Tunggakan Piutang:</td>
                <td class="text-end fs-6 text-danger">Rp {{ number_format($totalTunggakan, 0, ',', '.') }}</td>
                <td></td>
            </tr>
            <tr>
                <td colspan="11" class="small text-muted fst-italic">
                    Terbilang: # {{ \App\Helpers\Terbilang::rupiah($totalTunggakan) }} #
                </td>
            </tr>
        </tfoot>
    </table>
    </div>

    <!-- Tanda Tangan Lembaga -->
    <div class="row mt-5 ttd-row" style="page-break-inside: avoid;">
        <div class="col-6 text-center">
            <p class="mb-5 small text-muted">
                Mengetahui,<br>
                <strong>Pimpinan Pondok Pesantren</strong>
            </p>
            <p class="fw-bold text-decoration-underline mb-0">{{ \App\Models\Pengaturan::get('pimpinan_nama', 'KH. Muhammad Ridwan, Lc., M.Pd.I') }}</p>
            <small class="text-muted">Pengasuh Pesantren</small>
        </div>
        <div class="col-6 text-center">
            <p class="mb-5 small text-muted">
                Metro, {{ date('d F Y') }}<br>
                <strong>Bendahara Pesantren</strong>
            </p>
            <p class="fw-bold text-decoration-underline mb-0">{{ Auth::user()->nama_lengkap ?? \App\Models\Pengaturan::get('bendahara_nama', 'Ahmad Fauzi, S.E.') }}</p>
            <small class="text-muted">Bagian Administrasi Keuangan</small>
        </div>
    </div>

    <div class="text-muted text-center mt-4 small" style="font-size: 0.72rem;">
        <em>Dokumen ini dicetak secara otomatis melalui Sistem Informasi Manajemen Keuangan Pembayaran Santri pada {{ date('d/m/Y H:i:s') }} WIB</em>
    </div>

</body>
</html>
