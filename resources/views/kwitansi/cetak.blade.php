<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kwitansi Resmi {{ $pembayaran->kwitansi->no_kwitansi }} - {{ \App\Models\Pengaturan::get('pesantren_nama', 'PP Darul Muttaqin') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            color: #1a1a1a;
            background: #f8fafc;
        }
        .kwitansi-wrapper {
            max-width: 860px;
            margin: 20px auto;
            background: #ffffff;
            border: 3px double #14532d;
            outline: 1px solid #b45309;
            outline-offset: -7px;
            padding: 35px 40px;
            position: relative;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
            overflow: hidden;
        }
        /* Watermark Lunas */
        .watermark-lunas {
            position: absolute;
            top: 48%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-25deg);
            font-size: 7.5rem;
            font-weight: 900;
            color: rgba(22, 101, 52, 0.07);
            letter-spacing: 0.35em;
            text-transform: uppercase;
            user-select: none;
            pointer-events: none;
            z-index: 1;
        }
        .kop-container {
            border-bottom: 3px double #14532d;
            padding-bottom: 15px;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 20px;
            position: relative;
            z-index: 2;
        }
        .kop-logo {
            width: 75px;
            height: 75px;
            border-radius: 50%;
            background: #ffffff;
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            border: 2px solid #14532d;
            overflow: hidden;
            padding: 2px;
        }
        .kop-text {
            flex-grow: 1;
            text-align: center;
        }
        .kop-text h3 {
            font-size: 1.35rem;
            font-weight: 800;
            margin-bottom: 3px;
            color: #14532d;
            letter-spacing: 0.03em;
        }
        .kop-text p {
            margin-bottom: 2px;
            font-size: 0.88rem;
            color: #333333;
        }
        .judul-dokumen {
            text-align: center;
            margin-bottom: 25px;
            position: relative;
            z-index: 2;
        }
        .judul-dokumen h4 {
            font-size: 1.25rem;
            font-weight: 800;
            letter-spacing: 0.08em;
            text-decoration: underline;
            margin-bottom: 4px;
            color: #0f172a;
        }
        .tabel-rincian td {
            padding: 6px 4px;
            font-size: 0.95rem;
            vertical-align: top;
        }
        .box-nominal {
            border: 2px solid #14532d;
            background: #f0fdf4;
            padding: 12px 20px;
            border-radius: 6px;
            position: relative;
            z-index: 2;
        }
        .box-terbilang {
            font-style: italic;
            background: #f8fafc;
            border-left: 3px solid #14532d;
            padding: 8px 15px;
            font-size: 0.92rem;
            color: #334155;
            margin-top: 8px;
        }
        .security-badge {
            border: 1px dashed #94a3b8;
            padding: 10px;
            background: #f8fafc;
            border-radius: 6px;
            font-size: 0.75rem;
            font-family: monospace;
            position: relative;
            z-index: 2;
        }

        /* ========================================================
           RESPONSIVE MOBILE STYLES (SMARTPHONE & TABLET)
           ======================================================== */
        @media (max-width: 767.98px) {
            body {
                padding: 10px 8px !important;
            }
            .kwitansi-wrapper {
                margin: 5px auto !important;
                padding: 20px 14px !important;
                outline-offset: -4px !important;
            }
            .kop-container {
                flex-direction: column !important;
                gap: 8px !important;
                text-align: center !important;
                margin-bottom: 15px !important;
                padding-bottom: 10px !important;
            }
            .kop-logo {
                width: 55px !important;
                height: 55px !important;
            }
            .kop-text h3 {
                font-size: 1.05rem !important;
            }
            .kop-text p {
                font-size: 0.78rem !important;
            }
            .judul-dokumen h4 {
                font-size: 1rem !important;
            }
            .watermark-lunas {
                font-size: 4rem !important;
            }
            .tabel-rincian td {
                font-size: 0.85rem !important;
                padding: 4px 2px !important;
            }
            .box-nominal h3 {
                font-size: 1.35rem !important;
            }
            .box-nominal {
                padding: 10px 14px !important;
            }
            .box-terbilang {
                font-size: 0.82rem !important;
                padding: 6px 10px !important;
            }
        }

        @media print {
            body {
                background: #ffffff !important;
                padding: 0 !important;
            }
            .no-print {
                display: none !important;
            }
            .kwitansi-wrapper {
                margin: 0 !important;
                border: 2px solid #000000 !important;
                box-shadow: none !important;
                width: 100% !important;
                max-width: 100% !important;
                padding: 20px 25px !important;
            }
        }
    </style>
</head>
<body class="p-3 p-md-4">

    <!-- Tombol Navigasi Layar (Disembunyikan saat Print) -->
    <div class="no-print d-flex justify-content-between align-items-center mb-3 mx-auto" style="max-width: 860px;">
        <button onclick="window.history.length > 1 ? window.history.back() : window.location.href='/'" class="btn btn-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </button>
        <div class="d-flex gap-2">
            <button onclick="window.print()" class="btn btn-success btn-sm fw-semibold shadow-sm">
                <i class="bi bi-printer me-1"></i> Cetak / Simpan PDF
            </button>
        </div>
    </div>

    <!-- Lembar Dokumen Kwitansi Resmi -->
    <div class="kwitansi-wrapper">
        <!-- Watermark Lunas -->
        <div class="watermark-lunas">LUNAS</div>

        <!-- Kop Surat Lembaga Pesantren -->
        <div class="kop-container">
            <div class="kop-logo">
                <img src="{{ asset('img/logo.jpg') }}" alt="Logo PP Darul Muttaqin" style="width: 100%; height: 100%; object-fit: contain;">
            </div>
            <div class="kop-text">
                <h3 class="text-uppercase">{{ \App\Models\Pengaturan::get('pesantren_nama', 'PONDOK PESANTREN DARUL MUTTAQIN AL ISLAMI') }}</h3>
                <p>{{ \App\Models\Pengaturan::get('pesantren_alamat', 'Jl. Pesantren No. 1, Mulyojati 16 C, Metro Barat, Kota Metro, Lampung') }}</p>
                <p class="small text-muted mb-0">Telp/WA: {{ \App\Models\Pengaturan::get('pesantren_telepon', '0812-3456-7890') }} &bull; Email: {{ \App\Models\Pengaturan::get('pesantren_email', 'info@darulmuttaqin.ac.id') }}</p>
            </div>
        </div>

        <!-- Header Kwitansi -->
        <div class="judul-dokumen">
            <h4 class="text-uppercase">TANDA BUKTI PEMBAYARAN (KWITANSI RESMI)</h4>
            <div class="d-flex justify-content-between align-items-center mt-2 px-1">
                <span class="badge bg-success px-3 py-1 font-monospace" style="font-size: 0.82rem;">
                    <i class="bi bi-patch-check-fill me-1"></i> STATUS: SAH / LUNAS
                </span>
                <span class="text-muted small">
                    Nomor: <strong class="font-monospace text-dark fs-6">{{ $pembayaran->kwitansi->no_kwitansi }}</strong>
                </span>
            </div>
        </div>

        <!-- Isi Data Rincian Transaksi -->
        <table class="table table-borderless tabel-rincian mb-3" style="position: relative; z-index: 2;">
            <tbody>
                <tr>
                    <td style="width: 28%;" class="text-muted">Telah Diterima Dari</td>
                    <td style="width: 2%;">:</td>
                    <td><strong>{{ $pembayaran->tagihan->santri->wali->nama_lengkap ?? 'Wali Santri' }}</strong></td>
                </tr>
                <tr>
                    <td class="text-muted">Nama Lengkap Santri</td>
                    <td>:</td>
                    <td>
                        <strong class="text-dark">{{ $pembayaran->tagihan->santri->nama_santri }}</strong> 
                        (NIS: <code>{{ $pembayaran->tagihan->santri->nis }}</code>)
                    </td>
                </tr>
                <tr>
                    <td class="text-muted">Tingkat & Kelas</td>
                    <td>:</td>
                    <td>{{ $pembayaran->tagihan->santri->tingkat }} &mdash; {{ $pembayaran->tagihan->santri->kelas }}</td>
                </tr>
                <tr>
                    <td class="text-muted">Program Unggulan</td>
                    <td>:</td>
                    <td class="fw-semibold text-success">{{ $pembayaran->tagihan->santri->program->nama_program ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="text-muted">Guna Pembayaran</td>
                    <td>:</td>
                    <td>
                        <strong class="text-primary">{{ $pembayaran->tagihan->jenisTagihan->nama_tagihan }}</strong>
                        {{ $pembayaran->tagihan->bulan ? 'Bulan ' . $pembayaran->tagihan->nama_bulan : '' }} 
                        Tahun Ajaran {{ $pembayaran->tagihan->tahun_ajaran }}
                    </td>
                </tr>
                <tr>
                    <td class="text-muted">Metode & Referensi</td>
                    <td>:</td>
                    <td>
                        {{ $pembayaran->channel_bayar ?? ucfirst($pembayaran->metode_bayar) }}
                        <span class="text-muted small">(Ref Order ID: <code>{{ $pembayaran->order_id }}</code>)</span>
                    </td>
                </tr>
                <tr>
                    <td class="text-muted">Waktu Transaksi Masuk</td>
                    <td>:</td>
                    <td>{{ \Carbon\Carbon::parse($pembayaran->tgl_bayar)->setTimezone('Asia/Jakarta')->translatedFormat('d F Y') }} Pukul {{ \Carbon\Carbon::parse($pembayaran->tgl_bayar)->setTimezone('Asia/Jakarta')->format('H:i:s') }} WIB</td>
                </tr>
            </tbody>
        </table>

        <!-- Box Nominal & Terbilang -->
        <div class="row align-items-center g-3 mb-4" style="position: relative; z-index: 2;">
            <div class="col-md-7">
                <div class="box-nominal">
                    <span class="small text-muted text-uppercase fw-bold d-block" style="font-size: 0.75rem;">Jumlah Uang Yang Diterima:</span>
                    <h3 class="fw-bold mb-0 text-success" style="font-family: Arial, sans-serif;">
                        Rp {{ number_format($pembayaran->jumlah_bayar, 0, ',', '.') }},-
                    </h3>
                </div>
                <div class="box-terbilang">
                    <span class="fw-bold text-dark">Terbilang:</span> # {{ \App\Helpers\Terbilang::rupiah($pembayaran->jumlah_bayar) }} #
                </div>
            </div>
            <div class="col-md-5">
                <div class="security-badge text-center">
                    <div class="fw-bold text-dark mb-1"><i class="bi bi-shield-check text-success me-1"></i>VERIFIED DIGITAL RECEIPT</div>
                    <div class="text-truncate text-muted small" style="font-size: 0.68rem;">
                        HASH: {{ hash('sha256', $pembayaran->order_id . $pembayaran->tgl_bayar . $pembayaran->jumlah_bayar) }}
                    </div>
                    <div class="text-muted small mt-1" style="font-size: 0.68rem;">
                        Tercatat pada Sistem Keuangan Pesantren
                    </div>
                </div>
            </div>
        </div>

        <!-- Kolom Tanda Tangan Resmi -->
        <div class="row text-center mt-4 pt-2" style="position: relative; z-index: 2; page-break-inside: avoid;">
            <div class="col-6">
                <p class="mb-5 small text-muted">Penyetor / Wali Santri,</p>
                <p class="fw-bold text-decoration-underline mb-0 text-dark">
                    {{ $pembayaran->tagihan->santri->wali->nama_lengkap ?? 'Wali Santri' }}
                </p>
                <small class="text-muted">Orang Tua / Wali Murid</small>
            </div>
            <div class="col-6">
                <p class="mb-5 small text-muted">
                    Metro, {{ \Carbon\Carbon::parse($pembayaran->kwitansi->tgl_terbit)->setTimezone('Asia/Jakarta')->translatedFormat('d F Y') }}<br>
                    Bendahara Penerima Pesantren,
                </p>
                <p class="fw-bold text-decoration-underline mb-0 text-dark">
                    {{ $pembayaran->verifier->nama_lengkap ?? \App\Models\Pengaturan::get('bendahara_nama', 'Ahmad Fauzi, S.E.') }}
                </p>
                <small class="text-muted">Bagian Administrasi Keuangan</small>
            </div>
        </div>

        <!-- Catatan Legalitas Footer -->
        <div class="border-top pt-3 mt-4 text-center text-muted small" style="position: relative; z-index: 2; font-size: 0.75rem;">
            <em>{{ \App\Models\Pengaturan::get('kwitansi_footer', 'Bukti pembayaran sah ini diterbitkan secara otomatis oleh Sistem Informasi Keuangan Pesantren dan berkekuatan hukum administrasi yang sah.') }}</em>
        </div>
    </div>

</body>
</html>
