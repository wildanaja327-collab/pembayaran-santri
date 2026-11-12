<?php

namespace App\Http\Controllers;

use App\Models\JenisTagihan;
use App\Models\Pembayaran;
use App\Models\Program;
use App\Models\Santri;
use App\Models\Tagihan;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LaporanController extends Controller
{
    /**
     * Laporan Rekapitulasi Pemasukan Keuangan (Web Table View)
     */
    public function pemasukan(Request $request)
    {
        $query = Pembayaran::with(['tagihan.santri.program', 'tagihan.jenisTagihan', 'kwitansi'])
            ->where('status_transaksi', 'settlement');

        if ($request->filled('tgl_mulai') && $request->filled('tgl_selesai')) {
            $query->whereBetween('tgl_bayar', [$request->tgl_mulai . ' 00:00:00', $request->tgl_selesai . ' 23:59:59']);
        } elseif (!$request->has('semua_periode')) {
            // Default: 3 bulan terakhir atau bulan berjalan jika tidak ada filter
            if ($request->filled('bulan') && $request->filled('tahun')) {
                $query->whereMonth('tgl_bayar', $request->bulan)->whereYear('tgl_bayar', $request->tahun);
            }
        }

        if ($request->filled('id_jenis_tagihan')) {
            $query->whereHas('tagihan', function ($q) use ($request) {
                $q->where('id_jenis_tagihan', $request->id_jenis_tagihan);
            });
        }

        if ($request->filled('tahun_ajaran')) {
            $query->whereHas('tagihan', function ($q) use ($request) {
                $q->where('tahun_ajaran', $request->tahun_ajaran);
            });
        }

        if ($request->filled('tingkat')) {
            $query->whereHas('tagihan.santri', function ($q) use ($request) {
                $q->where('tingkat', $request->tingkat);
            });
        }

        if ($request->filled('metode_bayar')) {
            $query->where('metode_bayar', $request->metode_bayar);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('order_id', 'like', "%{$search}%")
                  ->orWhereHas('tagihan.santri', function ($sq) use ($search) {
                      $sq->where('nama_santri', 'like', "%{$search}%")
                         ->orWhere('nis', 'like', "%{$search}%");
                  });
            });
        }

        $pemasukan = $query->latest('tgl_bayar')->get();
        $totalNominal = $pemasukan->sum('jumlah_bayar');
        $totalAdmin = $pemasukan->sum('biaya_admin');

        $jenisTagihanList = JenisTagihan::all();
        $tahunAjaranList = TahunAjaran::orderBy('id_tahun_ajaran', 'desc')->get();
        $tahunAjaranAktif = TahunAjaran::getAktif();

        return view('bendahara.laporan.pemasukan', compact(
            'pemasukan',
            'totalNominal',
            'totalAdmin',
            'jenisTagihanList',
            'tahunAjaranList',
            'tahunAjaranAktif'
        ));
    }

    /**
     * Export Laporan Pemasukan ke Format File Microsoft Excel (.xls Native Spreadsheet)
     */
    public function exportPemasukanExcel(Request $request)
    {
        $query = Pembayaran::with(['tagihan.santri.program', 'tagihan.jenisTagihan', 'kwitansi'])
            ->where('status_transaksi', 'settlement');

        if ($request->filled('tgl_mulai') && $request->filled('tgl_selesai')) {
            $query->whereBetween('tgl_bayar', [$request->tgl_mulai . ' 00:00:00', $request->tgl_selesai . ' 23:59:59']);
        }

        if ($request->filled('id_jenis_tagihan')) {
            $query->whereHas('tagihan', function ($q) use ($request) {
                $q->where('id_jenis_tagihan', $request->id_jenis_tagihan);
            });
        }

        if ($request->filled('tahun_ajaran')) {
            $query->whereHas('tagihan', function ($q) use ($request) {
                $q->where('tahun_ajaran', $request->tahun_ajaran);
            });
        }

        if ($request->filled('tingkat')) {
            $query->whereHas('tagihan.santri', function ($q) use ($request) {
                $q->where('tingkat', $request->tingkat);
            });
        }

        if ($request->filled('metode_bayar')) {
            $query->where('metode_bayar', $request->metode_bayar);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('order_id', 'like', "%{$search}%")
                  ->orWhereHas('tagihan.santri', function ($sq) use ($search) {
                      $sq->where('nama_santri', 'like', "%{$search}%")
                         ->orWhere('nis', 'like', "%{$search}%");
                  });
            });
        }

        $data = $query->latest('tgl_bayar')->get();
        $totalNominal = $data->sum('jumlah_bayar');
        $filename = 'Laporan_Pemasukan_Pesantren_' . date('Ymd_His') . '.xls';

        return response()->streamDownload(function () use ($data, $totalNominal) {
            echo view('bendahara.laporan.export_pemasukan_excel', compact('data', 'totalNominal'))->render();
        }, $filename, [
            'Content-Type' => 'application/vnd.ms-excel; charset=utf-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ]);
    }

    /**
     * Export Laporan Pemasukan ke Format File CSV (Native Excel Compatible)
     */
    public function exportPemasukanCsv(Request $request)
    {
        $query = Pembayaran::with(['tagihan.santri.program', 'tagihan.jenisTagihan', 'kwitansi'])
            ->where('status_transaksi', 'settlement');

        if ($request->filled('tgl_mulai') && $request->filled('tgl_selesai')) {
            $query->whereBetween('tgl_bayar', [$request->tgl_mulai . ' 00:00:00', $request->tgl_selesai . ' 23:59:59']);
        }

        if ($request->filled('id_jenis_tagihan')) {
            $query->whereHas('tagihan', function ($q) use ($request) {
                $q->where('id_jenis_tagihan', $request->id_jenis_tagihan);
            });
        }

        if ($request->filled('tahun_ajaran')) {
            $query->whereHas('tagihan', function ($q) use ($request) {
                $q->where('tahun_ajaran', $request->tahun_ajaran);
            });
        }

        if ($request->filled('tingkat')) {
            $query->whereHas('tagihan.santri', function ($q) use ($request) {
                $q->where('tingkat', $request->tingkat);
            });
        }

        if ($request->filled('metode_bayar')) {
            $query->where('metode_bayar', $request->metode_bayar);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('order_id', 'like', "%{$search}%")
                  ->orWhereHas('tagihan.santri', function ($sq) use ($search) {
                      $sq->where('nama_santri', 'like', "%{$search}%")
                         ->orWhere('nis', 'like', "%{$search}%");
                  });
            });
        }

        $data = $query->latest('tgl_bayar')->get();
        $filename = 'Laporan_Pemasukan_Pesantren_' . date('Ymd_His') . '.csv';

        return response()->streamDownload(function () use ($data) {
            $file = fopen('php://output', 'w');
            // Write UTF-8 BOM agar rapi di Microsoft Excel Indonesia
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fwrite($file, "sep=,\r\n");

            // Header Kolom CSV
            fputcsv($file, [
                'No',
                'No Kwitansi',
                'Order ID',
                'Tanggal Pembayaran',
                'NIS Santri',
                'Nama Lengkap Santri',
                'Tingkat',
                'Kelas',
                'Program Unggulan',
                'Komponen Tagihan',
                'Bulan',
                'Tahun Ajaran',
                'Metode Bayar',
                'Channel Pembayaran',
                'Jumlah Bersih (Rp)',
                'Biaya Admin (Rp)',
                'Total Ditransfer (Rp)',
                'Status Transaksi',
            ]);

            $no = 1;
            foreach ($data as $p) {
                fputcsv($file, [
                    $no++,
                    $p->kwitansi->no_kwitansi ?? '-',
                    $p->order_id,
                    date('d/m/Y H:i:s', strtotime($p->tgl_bayar)),
                    $p->tagihan->santri->nis ?? '-',
                    $p->tagihan->santri->nama_santri ?? '-',
                    $p->tagihan->santri->tingkat ?? '-',
                    $p->tagihan->santri->kelas ?? '-',
                    $p->tagihan->santri->program->nama_program ?? '-',
                    $p->tagihan->jenisTagihan->nama_tagihan ?? '-',
                    $p->tagihan->bulan ? $p->tagihan->nama_bulan : '-',
                    $p->tagihan->tahun_ajaran ?? '-',
                    ucfirst($p->metode_bayar),
                    $p->channel_bayar ?? ucfirst($p->metode_bayar),
                    $p->jumlah_bayar,
                    $p->biaya_admin,
                    $p->total_bayar,
                    $p->status_transaksi,
                ]);
            }

            fclose($file);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ]);
    }

    /**
     * Laporan Rekapan Tunggakan Santri (Web Table View)
     */
    public function tunggakan(Request $request)
    {
        $query = Tagihan::with(['santri.program', 'santri.wali', 'jenisTagihan'])
            ->whereIn('status_tagihan', ['belum_bayar', 'dicicil']);

        if ($request->filled('tingkat')) {
            $query->whereHas('santri', function ($q) use ($request) {
                $q->where('tingkat', $request->tingkat);
            });
        }

        if ($request->filled('kelas')) {
            $query->whereHas('santri', function ($q) use ($request) {
                $q->where('kelas', 'like', '%' . $request->kelas . '%');
            });
        }

        if ($request->filled('id_program')) {
            $query->whereHas('santri', function ($q) use ($request) {
                $q->where('id_program', $request->id_program);
            });
        }

        if ($request->filled('id_jenis_tagihan')) {
            $query->where('id_jenis_tagihan', $request->id_jenis_tagihan);
        }

        if ($request->filled('tahun_ajaran')) {
            $query->where('tahun_ajaran', $request->tahun_ajaran);
        }

        if ($request->filled('bulan')) {
            $query->where('bulan', $request->bulan);
        }

        if ($request->filled('kategori_tunggakan')) {
            if ($request->kategori_tunggakan === 'belum_ada') {
                $query->whereNull('kategori_tunggakan');
            } else {
                $query->where('kategori_tunggakan', $request->kategori_tunggakan);
            }
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('santri', function ($sq) use ($search) {
                    $sq->where('nama_santri', 'like', "%{$search}%")
                        ->orWhere('nis', 'like', "%{$search}%");
                })->orWhereHas('jenisTagihan', function ($jsq) use ($search) {
                    $jsq->where('nama_tagihan', 'like', "%{$search}%");
                });
            });
        }

        $tunggakan = $query->orderBy('jatuh_tempo', 'asc')->orderBy('nis')->get();
        $totalTunggakan = $tunggakan->sum('sisa_tagihan');

        $jenisTagihanList = JenisTagihan::all();
        $tahunAjaranList = TahunAjaran::orderBy('id_tahun_ajaran', 'desc')->get();
        $programList = Program::all();

        return view('bendahara.laporan.tunggakan', compact(
            'tunggakan',
            'totalTunggakan',
            'jenisTagihanList',
            'tahunAjaranList',
            'programList'
        ));
    }

    /**
     * Export Rekapan Tunggakan ke Format File Microsoft Excel (.xls Native Spreadsheet)
     */
    public function exportTunggakanExcel(Request $request)
    {
        $query = Tagihan::with(['santri.program', 'santri.wali', 'jenisTagihan'])
            ->whereIn('status_tagihan', ['belum_bayar', 'dicicil']);

        if ($request->filled('tingkat')) {
            $query->whereHas('santri', function ($q) use ($request) {
                $q->where('tingkat', $request->tingkat);
            });
        }

        if ($request->filled('kelas')) {
            $query->whereHas('santri', function ($q) use ($request) {
                $q->where('kelas', 'like', '%' . $request->kelas . '%');
            });
        }

        if ($request->filled('id_program')) {
            $query->whereHas('santri', function ($q) use ($request) {
                $q->where('id_program', $request->id_program);
            });
        }

        if ($request->filled('id_jenis_tagihan')) {
            $query->where('id_jenis_tagihan', $request->id_jenis_tagihan);
        }

        if ($request->filled('tahun_ajaran')) {
            $query->where('tahun_ajaran', $request->tahun_ajaran);
        }

        if ($request->filled('bulan')) {
            $query->where('bulan', $request->bulan);
        }

        if ($request->filled('kategori_tunggakan')) {
            if ($request->kategori_tunggakan === 'belum_ada') {
                $query->whereNull('kategori_tunggakan');
            } else {
                $query->where('kategori_tunggakan', $request->kategori_tunggakan);
            }
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('santri', function ($sq) use ($search) {
                    $sq->where('nama_santri', 'like', "%{$search}%")
                        ->orWhere('nis', 'like', "%{$search}%");
                })->orWhereHas('jenisTagihan', function ($jsq) use ($search) {
                    $jsq->where('nama_tagihan', 'like', "%{$search}%");
                });
            });
        }

        $data = $query->orderBy('jatuh_tempo', 'asc')->orderBy('nis')->get();
        $totalNominal = $data->sum('sisa_tagihan');
        $filename = 'Rekapan_Tunggakan_Pesantren_' . date('Ymd_His') . '.xls';

        return response()->streamDownload(function () use ($data, $totalNominal) {
            echo view('bendahara.laporan.export_tunggakan_excel', compact('data', 'totalNominal'))->render();
        }, $filename, [
            'Content-Type' => 'application/vnd.ms-excel; charset=utf-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ]);
    }

    /**
     * Export Rekapan Tunggakan ke Format File CSV (Native Excel Compatible)
     */
    public function exportTunggakanCsv(Request $request)
    {
        $query = Tagihan::with(['santri.program', 'santri.wali', 'jenisTagihan'])
            ->whereIn('status_tagihan', ['belum_bayar', 'dicicil']);

        if ($request->filled('tingkat')) {
            $query->whereHas('santri', function ($q) use ($request) {
                $q->where('tingkat', $request->tingkat);
            });
        }

        if ($request->filled('kelas')) {
            $query->whereHas('santri', function ($q) use ($request) {
                $q->where('kelas', 'like', '%' . $request->kelas . '%');
            });
        }

        if ($request->filled('id_program')) {
            $query->whereHas('santri', function ($q) use ($request) {
                $q->where('id_program', $request->id_program);
            });
        }

        if ($request->filled('id_jenis_tagihan')) {
            $query->where('id_jenis_tagihan', $request->id_jenis_tagihan);
        }

        if ($request->filled('tahun_ajaran')) {
            $query->where('tahun_ajaran', $request->tahun_ajaran);
        }

        if ($request->filled('bulan')) {
            $query->where('bulan', $request->bulan);
        }

        if ($request->filled('kategori_tunggakan')) {
            if ($request->kategori_tunggakan === 'belum_ada') {
                $query->whereNull('kategori_tunggakan');
            } else {
                $query->where('kategori_tunggakan', $request->kategori_tunggakan);
            }
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('santri', function ($sq) use ($search) {
                    $sq->where('nama_santri', 'like', "%{$search}%")
                        ->orWhere('nis', 'like', "%{$search}%");
                })->orWhereHas('jenisTagihan', function ($jsq) use ($search) {
                    $jsq->where('nama_tagihan', 'like', "%{$search}%");
                });
            });
        }

        $data = $query->orderBy('jatuh_tempo', 'asc')->orderBy('nis')->get();
        $filename = 'Rekapan_Tunggakan_Pesantren_' . date('Ymd_His') . '.csv';

        return response()->streamDownload(function () use ($data) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fwrite($file, "sep=,\r\n");

            fputcsv($file, [
                'No',
                'NIS Santri',
                'Nama Lengkap Santri',
                'Tingkat',
                'Kelas',
                'Program Unggulan',
                'Nama Wali Santri',
                'No Telepon / WhatsApp Wali',
                'Komponen Tagihan',
                'Bulan / Periode',
                'Tahun Ajaran',
                'Tanggal Jatuh Tempo',
                'Total Nominal Tagihan (Rp)',
                'Total Terbayar (Rp)',
                'Sisa Tunggakan (Rp)',
                'Status Tagihan',
                'Kategori Alasan',
                'Keterangan / Alasan Tunggakan',
                'Tanggal Janji Bayar',
                'Catatan Internal Bendahara',
                'Waktu Catatan Terakhir',
            ]);

            $no = 1;
            foreach ($data as $t) {
                fputcsv($file, [
                    $no++,
                    $t->nis,
                    $t->santri->nama_santri ?? '-',
                    $t->santri->tingkat ?? '-',
                    $t->santri->kelas ?? '-',
                    $t->santri->program->nama_program ?? '-',
                    $t->santri->wali->nama_lengkap ?? '-',
                    $t->santri->wali->no_telp ?? '-',
                    $t->jenisTagihan->nama_tagihan ?? '-',
                    $t->bulan ? $t->nama_bulan : 'Non-Bulanan',
                    $t->tahun_ajaran,
                    $t->jatuh_tempo ? date('d/m/Y', strtotime($t->jatuh_tempo)) : '-',
                    $t->nominal_tagihan,
                    $t->total_terbayar,
                    $t->sisa_tagihan,
                    ucfirst(str_replace('_', ' ', $t->status_tagihan)),
                    $t->kategori_info['label'] ?? ($t->kategori_tunggakan ?: 'Belum Ada'),
                    $t->alasan_tunggakan ?? '-',
                    $t->tgl_janji_bayar ? date('d/m/Y', strtotime($t->tgl_janji_bayar)) : '-',
                    $t->catatan_bendahara ?? '-',
                    $t->updated_alasan_at ? date('d/m/Y H:i', strtotime($t->updated_alasan_at)) : '-',
                ]);
            }

            fclose($file);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ]);
    }

    /**
     * Cetak Laporan Pemasukan (Format Print / PDF Mewah & Resmi)
     */
    public function cetakPemasukan(Request $request)
    {
        $query = Pembayaran::with(['tagihan.santri.program', 'tagihan.jenisTagihan', 'kwitansi'])
            ->where('status_transaksi', 'settlement');

        if ($request->filled('tgl_mulai') && $request->filled('tgl_selesai')) {
            $query->whereBetween('tgl_bayar', [$request->tgl_mulai . ' 00:00:00', $request->tgl_selesai . ' 23:59:59']);
            $periode = date('d F Y', strtotime($request->tgl_mulai)) . ' s/d ' . date('d F Y', strtotime($request->tgl_selesai));
        } else {
            $periode = 'Seluruh Periode Berjalan (' . date('F Y') . ')';
        }

        if ($request->filled('id_jenis_tagihan')) {
            $query->whereHas('tagihan', function ($q) use ($request) {
                $q->where('id_jenis_tagihan', $request->id_jenis_tagihan);
            });
        }

        if ($request->filled('tahun_ajaran')) {
            $query->whereHas('tagihan', function ($q) use ($request) {
                $q->where('tahun_ajaran', $request->tahun_ajaran);
            });
        }

        if ($request->filled('tingkat')) {
            $query->whereHas('tagihan.santri', function ($q) use ($request) {
                $q->where('tingkat', $request->tingkat);
            });
        }

        if ($request->filled('metode_bayar')) {
            $query->where('metode_bayar', $request->metode_bayar);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('order_id', 'like', "%{$search}%")
                  ->orWhereHas('tagihan.santri', function ($sq) use ($search) {
                      $sq->where('nama_santri', 'like', "%{$search}%")
                         ->orWhere('nis', 'like', "%{$search}%");
                  });
            });
        }

        $pemasukan = $query->latest('tgl_bayar')->get();
        $totalNominal = $pemasukan->sum('jumlah_bayar');
        $noDokumen = 'LAP/KEU-DMTQ/' . date('Y/m') . '/' . sprintf('%03d', rand(10, 99));

        return view('bendahara.laporan.cetak_pemasukan', compact('pemasukan', 'totalNominal', 'periode', 'noDokumen'));
    }

    /**
     * Cetak Laporan Tunggakan Santri (Format Print / PDF Mewah & Resmi)
     */
    public function cetakTunggakan(Request $request)
    {
        $query = Tagihan::with(['santri.program', 'santri.wali', 'jenisTagihan'])
            ->whereIn('status_tagihan', ['belum_bayar', 'dicicil']);

        if ($request->filled('tingkat')) {
            $query->whereHas('santri', function ($q) use ($request) {
                $q->where('tingkat', $request->tingkat);
            });
        }

        if ($request->filled('id_program')) {
            $query->whereHas('santri', function ($q) use ($request) {
                $q->where('id_program', $request->id_program);
            });
        }

        if ($request->filled('id_jenis_tagihan')) {
            $query->where('id_jenis_tagihan', $request->id_jenis_tagihan);
        }

        if ($request->filled('tahun_ajaran')) {
            $query->where('tahun_ajaran', $request->tahun_ajaran);
        }

        if ($request->filled('bulan')) {
            $query->where('bulan', $request->bulan);
        }

        if ($request->filled('kategori_tunggakan')) {
            if ($request->kategori_tunggakan === 'belum_ada') {
                $query->whereNull('kategori_tunggakan');
            } else {
                $query->where('kategori_tunggakan', $request->kategori_tunggakan);
            }
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('santri', function ($sq) use ($search) {
                    $sq->where('nama_santri', 'like', "%{$search}%")
                        ->orWhere('nis', 'like', "%{$search}%");
                })->orWhereHas('jenisTagihan', function ($jsq) use ($search) {
                    $jsq->where('nama_tagihan', 'like', "%{$search}%");
                });
            });
        }

        $tunggakan = $query->orderBy('jatuh_tempo', 'asc')->orderBy('nis')->get();
        $totalTunggakan = $tunggakan->sum('sisa_tagihan');
        $noDokumen = 'LAP/TUNGGAKAN-DMTQ/' . date('Y/m') . '/' . sprintf('%03d', rand(10, 99));

        return view('bendahara.laporan.cetak_tunggakan', compact('tunggakan', 'totalTunggakan', 'noDokumen'));
    }
}
