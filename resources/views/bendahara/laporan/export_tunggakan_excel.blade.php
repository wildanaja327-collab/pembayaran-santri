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
        .date-format { mso-number-format: "dd\/mm\/yyyy"; text-align: center; }
        .bg-light-yellow { background-color: #fef9c3; }
        .bg-total { background-color: #f1f5f9; font-weight: bold; }
    </style>
</head>
<body>
    <table>
        <tr>
            <th colspan="21" style="font-size: 14pt; font-weight: bold; text-align: center; color: #14532d;">
                {{ \App\Models\Pengaturan::get('pesantren_nama', 'PONDOK PESANTREN DARUL MUTTAQIN AL ISLAMI') }}
            </th>
        </tr>
        <tr>
            <th colspan="21" style="font-size: 11pt; font-weight: bold; text-align: center;">
                REKAPITULASI PIUTANG &amp; TUNGGAKAN SANTRI
            </th>
        </tr>
        <tr>
            <td colspan="21" style="text-align: center; font-size: 9pt; color: #64748b;">
                Tanggal Terbit: {{ date('d F Y H:i:s') }} WIB &bull; Total Tunggakan: {{ $data->count() }} catatan &bull; Dicetak oleh Sistem Keuangan Santri
            </td>
        </tr>
        <tr><td colspan="21"></td></tr>
    </table>

    <table class="table-excel">
        <thead>
            <tr>
                <th class="th-header" style="width: 35px;">No</th>
                <th class="th-header" style="width: 90px;">NIS Santri</th>
                <th class="th-header" style="width: 180px;">Nama Santri</th>
                <th class="th-header" style="width: 70px;">Tingkat</th>
                <th class="th-header" style="width: 70px;">Kelas</th>
                <th class="th-header" style="width: 140px;">Program Unggulan</th>
                <th class="th-header" style="width: 160px;">Nama Wali Santri</th>
                <th class="th-header" style="width: 130px;">Kontak Wali / WA</th>
                <th class="th-header" style="width: 170px;">Komponen Tagihan</th>
                <th class="th-header" style="width: 90px;">Bulan</th>
                <th class="th-header" style="width: 100px;">Tahun Ajaran</th>
                <th class="th-header" style="width: 100px;">Jatuh Tempo</th>
                <th class="th-header" style="width: 120px;">Nominal Pokok (Rp)</th>
                <th class="th-header" style="width: 120px;">Total Terbayar (Rp)</th>
                <th class="th-header" style="width: 130px;">Sisa Tunggakan (Rp)</th>
                <th class="th-header" style="width: 110px;">Status Tagihan</th>
                <th class="th-header" style="width: 150px;">Kategori Alasan</th>
                <th class="th-header" style="width: 220px;">Keterangan Alasan Tunggakan</th>
                <th class="th-header" style="width: 110px;">Janji Bayar</th>
                <th class="th-header" style="width: 200px;">Catatan Internal Bendahara</th>
                <th class="th-header" style="width: 120px;">Waktu Dicatat</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $index => $t)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="text-format">{{ $t->nis }}</td>
                    <td class="fw-bold">{{ $t->santri->nama_santri ?? '-' }}</td>
                    <td class="text-center">{{ $t->santri->tingkat ?? '-' }}</td>
                    <td class="text-center">{{ $t->santri->kelas ?? '-' }}</td>
                    <td>{{ $t->santri->program->nama_program ?? '-' }}</td>
                    <td>{{ $t->santri->wali->nama_lengkap ?? '-' }}</td>
                    <td class="text-format">{{ $t->santri->wali->no_telp ?? '-' }}</td>
                    <td>{{ $t->jenisTagihan->nama_tagihan ?? '-' }}</td>
                    <td class="text-center">{{ $t->bulan ? $t->nama_bulan : 'Non-Bulanan' }}</td>
                    <td class="text-center">{{ $t->tahun_ajaran }}</td>
                    <td class="date-format">{{ $t->jatuh_tempo ? date('d/m/Y', strtotime($t->jatuh_tempo)) : '-' }}</td>
                    <td class="num-format">{{ $t->nominal_tagihan }}</td>
                    <td class="num-format">{{ $t->total_terbayar }}</td>
                    <td class="num-format fw-bold bg-light-yellow" style="color: #b91c1c;">{{ $t->sisa_tagihan }}</td>
                    <td class="text-center">{{ ucfirst(str_replace('_', ' ', $t->status_tagihan)) }}</td>
                    <td>{{ $t->kategori_info['label'] ?? ($t->kategori_tunggakan ?: 'Belum Ada') }}</td>
                    <td>{{ $t->alasan_tunggakan ?: '-' }}</td>
                    <td class="date-format">{{ $t->tgl_janji_bayar ? date('d/m/Y', strtotime($t->tgl_janji_bayar)) : '-' }}</td>
                    <td>{{ $t->catatan_bendahara ?: '-' }}</td>
                    <td class="text-center" style="font-size: 8.5pt;">{{ $t->updated_alasan_at ? date('d/m/Y H:i', strtotime($t->updated_alasan_at)) : '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="21" class="text-center" style="padding: 20px; color: #64748b;">
                        Tidak ada catatan tunggakan santri ditemukan.
                    </td>
                </tr>
            @endforelse
        </tbody>
        @if($data->isNotEmpty())
            <tfoot>
                <tr class="bg-total">
                    <td colspan="14" class="text-end fw-bold" style="text-transform: uppercase;">
                        TOTAL KESELURUHAN PIUTANG / TUNGGAKAN:
                    </td>
                    <td class="num-format fw-bold" style="color: #b91c1c; font-size: 11pt;">
                        {{ $totalNominal }}
                    </td>
                    <td colspan="6"></td>
                </tr>
                <tr>
                    <td colspan="21" style="font-size: 9pt; font-style: italic; color: #475569; padding: 8px;">
                        Terbilang: # {{ \App\Helpers\Terbilang::rupiah($totalNominal) }} #
                    </td>
                </tr>
            </tfoot>
        @endif
    </table>
</body>
</html>
