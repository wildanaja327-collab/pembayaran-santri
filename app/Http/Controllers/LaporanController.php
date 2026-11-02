<?php

namespace App\Http\Controllers;

use App\Models\JenisTagihan;
use App\Models\Pembayaran;
use App\Models\Santri;
use App\Models\Tagihan;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    /**
     * Laporan Rekapitulasi Pemasukan Keuangan
     */
    public function pemasukan(Request $request)
    {
        $query = Pembayaran::with(['tagihan.santri', 'tagihan.jenisTagihan'])
            ->where('status_transaksi', 'settlement');

        if ($request->filled('tgl_mulai') && $request->filled('tgl_selesai')) {
            $query->whereBetween('tgl_bayar', [$request->tgl_mulai . ' 00:00:00', $request->tgl_selesai . ' 23:59:59']);
        } else {
            // Default bulan berjalan
            $query->whereMonth('tgl_bayar', now()->month)
                  ->whereYear('tgl_bayar', now()->year);
        }

        if ($request->filled('id_jenis_tagihan')) {
            $query->whereHas('tagihan', function ($q) use ($request) {
                $q->where('id_jenis_tagihan', $request->id_jenis_tagihan);
            });
        }

        if ($request->filled('metode_bayar')) {
            $query->where('metode_bayar', $request->metode_bayar);
        }

        $pemasukan = $query->latest('tgl_bayar')->get();
        $totalNominal = $pemasukan->sum('jumlah_bayar');
        $totalAdmin = $pemasukan->sum('biaya_admin');
        $jenisTagihanList = JenisTagihan::all();

        return view('bendahara.laporan.pemasukan', compact('pemasukan', 'totalNominal', 'totalAdmin', 'jenisTagihanList'));
    }

    /**
     * Laporan Rekapan Tunggakan Santri (Fitur Novelty Utama Bab I)
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

        if ($request->filled('id_jenis_tagihan')) {
            $query->where('id_jenis_tagihan', $request->id_jenis_tagihan);
        }

        if ($request->filled('tahun_ajaran')) {
            $query->where('tahun_ajaran', $request->tahun_ajaran);
        }

        if ($request->filled('bulan')) {
            $query->where('bulan', $request->bulan);
        }

        $tunggakan = $query->orderBy('nis')->get();
        $totalTunggakan = $tunggakan->sum('sisa_tagihan');
        $jenisTagihanList = JenisTagihan::all();

        return view('bendahara.laporan.tunggakan', compact('tunggakan', 'totalTunggakan', 'jenisTagihanList'));
    }

    /**
     * Cetak Laporan Pemasukan (Format Print / PDF)
     */
    public function cetakPemasukan(Request $request)
    {
        $query = Pembayaran::with(['tagihan.santri', 'tagihan.jenisTagihan'])
            ->where('status_transaksi', 'settlement');

        if ($request->filled('tgl_mulai') && $request->filled('tgl_selesai')) {
            $query->whereBetween('tgl_bayar', [$request->tgl_mulai . ' 00:00:00', $request->tgl_selesai . ' 23:59:59']);
            $periode = $request->tgl_mulai . ' s/d ' . $request->tgl_selesai;
        } else {
            $query->whereMonth('tgl_bayar', now()->month)
                  ->whereYear('tgl_bayar', now()->year);
            $periode = 'Bulan ' . date('F Y');
        }

        $pemasukan = $query->latest('tgl_bayar')->get();
        $totalNominal = $pemasukan->sum('jumlah_bayar');

        return view('bendahara.laporan.cetak_pemasukan', compact('pemasukan', 'totalNominal', 'periode'));
    }

    /**
     * Cetak Laporan Tunggakan Santri (Format Print / PDF)
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

        $tunggakan = $query->orderBy('nis')->get();
        $totalTunggakan = $tunggakan->sum('sisa_tagihan');

        return view('bendahara.laporan.cetak_tunggakan', compact('tunggakan', 'totalTunggakan'));
    }
}

