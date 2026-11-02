<?php

namespace App\Http\Controllers;

use App\Models\Pembayaran;
use App\Models\Santri;
use App\Models\Tagihan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function bendahara()
    {
        $totalSantri = Santri::count();
        $santriSmp = Santri::where('tingkat', 'SMP')->count();
        $santriSma = Santri::where('tingkat', 'SMA')->count();

        $totalPemasukan = Pembayaran::where('status_transaksi', 'settlement')->sum('jumlah_bayar');
        $pemasukanBulanIni = Pembayaran::where('status_transaksi', 'settlement')
            ->whereMonth('tgl_bayar', now()->month)
            ->whereYear('tgl_bayar', now()->year)
            ->sum('jumlah_bayar');

        $totalTunggakan = Tagihan::whereIn('status_tagihan', ['belum_bayar', 'dicicil'])->sum('sisa_tagihan');
        $transaksiPending = Pembayaran::where('status_transaksi', 'pending')->count();

        // 5 Transaksi Terakhir
        $recentPayments = Pembayaran::with(['tagihan.santri', 'tagihan.jenisTagihan'])
            ->latest('tgl_bayar')
            ->limit(5)
            ->get();

        // Rekap status tagihan
        $tagihanLunas = Tagihan::where('status_tagihan', 'lunas')->count();
        $tagihanBelumLunas = Tagihan::whereIn('status_tagihan', ['belum_bayar', 'dicicil'])->count();

        return view('bendahara.dashboard', compact(
            'totalSantri',
            'santriSmp',
            'santriSma',
            'totalPemasukan',
            'pemasukanBulanIni',
            'totalTunggakan',
            'transaksiPending',
            'recentPayments',
            'tagihanLunas',
            'tagihanBelumLunas'
        ));
    }

    public function wali()
    {
        $user = Auth::user();
        $santriList = $user->santri()->with('program')->get();
        $nisList = $santriList->pluck('nis');

        $tagihanBelumLunas = Tagihan::whereIn('nis', $nisList)
            ->whereIn('status_tagihan', ['belum_bayar', 'dicicil', 'menunggu_verifikasi'])
            ->with(['jenisTagihan', 'santri'])
            ->orderBy('jatuh_tempo')
            ->get();

        $riwayatPembayaran = Pembayaran::whereHas('tagihan', function ($q) use ($nisList) {
            $q->whereIn('nis', $nisList);
        })->with(['tagihan.santri', 'tagihan.jenisTagihan', 'kwitansi'])
            ->latest('tgl_bayar')
            ->limit(10)
            ->get();

        $totalTunggakan = $tagihanBelumLunas->sum('sisa_tagihan');

        return view('wali.dashboard', compact(
            'santriList',
            'tagihanBelumLunas',
            'riwayatPembayaran',
            'totalTunggakan'
        ));
    }
}

