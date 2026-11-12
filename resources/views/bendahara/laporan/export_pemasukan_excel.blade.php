<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <style>
        body { font-family: Arial, sans-serif; }
        .table-excel { border-collapse: collapse; width: 100%; }
        .table-excel th, .table-excel td { border: 0.5pt solid #94a3b8; padding: 6px 8px; font-size: 10pt; }
        .th-header { background-color: #14532d; color: #ffffff; font-weight: bold; text-align: center; vertical-align: middle; }
        .text-center { text-align: center; }
        .text-end { text-align: right; }
        .fw-bold { font-weight: bold; }
        .num-format { mso-number-format: "\#\,\#\#0"; text-align: right; }
        .text-format { mso-number-format: "\@"; text-align: center; }
        .date-format { mso-number-format: "dd\/mm\/yyyy\ hh:mm"; text-align: center; }
        .bg-light-green { background-color: #dcfce7; }
        .bg-total { background-color: #f1f5f9; font-weight: bold; }
    </style>
</head>
<body>
    <table>
        <tr>
            <th colspan="17" style="font-size: 14pt; font-weight: bold; text-align: center; color: #14532d;">
                {{ \App\Models\Pengaturan::get('pesantren_nama', 'PONDOK PESANTREN DARUL MUTTAQIN AL ISLAMI') }}
            </th>
        </tr>
        <tr>
            <th colspan="17" style="font-size: 11pt; font-weight: bold; text-align: center;">
                REKAPITULASI PENERIMAAN PEMBAYARAN &amp; KAS MASUK
            </th>
        </tr>
        <tr>
            <td colspan="17" style="text-align: center; font-size: 9pt; color: #64748b;">
                Tanggal Terbit: {{ date('d F Y H:i:s') }} WIB &bull; Total Transaksi: {{ $data->count() }} transaksi
            </td>
        </tr>
        <tr><td colspan="17"></td></tr>
    </table>

    <table class="table-excel">
        <thead>
            <tr>
                <th class="th-header" style="width: 35px;">No</th>
                <th class="th-header" style="width: 140px;">No Kwitansi</th>
                <th class="th-header" style="width: 140px;">Order ID</th>
                <th class="th-header" style="width: 130px;">Waktu Pembayaran</th>
                <th class="th-header" style="width: 90px;">NIS Santri</th>
                <th class="th-header" style="width: 180px;">Nama Santri</th>
                <th class="th-header" style="width: 70px;">Tingkat</th>
                <th class="th-header" style="width: 70px;">Kelas</th>
                <th class="th-header" style="width: 140px;">Program Unggulan</th>
                <th class="th-header" style="width: 170px;">Komponen Tagihan</th>
                <th class="th-header" style="width: 90px;">Bulan</th>
                <th class="th-header" style="width: 100px;">Tahun Ajaran</th>
                <th class="th-header" style="width: 100px;">Metode Bayar</th>
                <th class="th-header" style="width: 110px;">Channel</th>
                <th class="th-header" style="width: 130px;">Jumlah Bersih (Rp)</th>
                <th class="th-header" style="width: 110px;">Biaya Admin (Rp)</th>
                <th class="th-header" style="width: 130px;">Total Masuk (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $index => $p)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="text-format fw-bold">{{ $p->kwitansi->no_kwitansi ?? '-' }}</td>
                    <td class="text-format">{{ $p->order_id }}</td>
                    <td class="date-format">{{ date('d/m/Y H:i', strtotime($p->tgl_bayar)) }}</td>
                    <td class="text-format">{{ $p->tagihan->santri->nis ?? '-' }}</td>
                    <td class="fw-bold">{{ $p->tagihan->santri->nama_santri ?? '-' }}</td>
                    <td class="text-center">{{ $p->tagihan->santri->tingkat ?? '-' }}</td>
                    <td class="text-center">{{ $p->tagihan->santri->kelas ?? '-' }}</td>
                    <td>{{ $p->tagihan->santri->program->nama_program ?? '-' }}</td>
                    <td>{{ $p->tagihan->jenisTagihan->nama_tagihan ?? '-' }}</td>
                    <td class="text-center">{{ $p->tagihan->bulan ? $p->tagihan->nama_bulan : '-' }}</td>
                    <td class="text-center">{{ $p->tagihan->tahun_ajaran ?? '-' }}</td>
                    <td class="text-center">{{ ucfirst($p->metode_bayar) }}</td>
                    <td class="text-center">{{ $p->channel_bayar ?? ucfirst($p->metode_bayar) }}</td>
                    <td class="num-format fw-bold bg-light-green" style="color: #15803d;">{{ $p->jumlah_bayar }}</td>
                    <td class="num-format">{{ $p->biaya_admin }}</td>
                    <td class="num-format fw-bold">{{ $p->total_bayar }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="17" class="text-center" style="padding: 20px; color: #64748b;">
                        Tidak ada transaksi pembayaran yang ditemukan.
                    </td>
                </tr>
            @endforelse
        </tbody>
        @if($data->isNotEmpty())
            <tfoot>
                <tr class="bg-total">
                    <td colspan="14" class="text-end fw-bold" style="text-transform: uppercase;">
                        TOTAL PEMASUKAN DITERIMA:
                    </td>
                    <td class="num-format fw-bold" style="color: #15803d; font-size: 11pt;">
                        {{ $totalNominal }}
                    </td>
                    <td class="num-format fw-bold">{{ $data->sum('biaya_admin') }}</td>
                    <td class="num-format fw-bold">{{ $data->sum('total_bayar') }}</td>
                </tr>
                <tr>
                    <td colspan="17" style="font-size: 9pt; font-style: italic; color: #475569; padding: 8px;">
                        Terbilang: # {{ \App\Helpers\Terbilang::rupiah($totalNominal) }} #
                    </td>
                </tr>
            </tfoot>
        @endif
    </table>
</body>
</html>
