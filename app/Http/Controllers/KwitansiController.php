<?php

namespace App\Http\Controllers;

use App\Models\Pembayaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KwitansiController extends Controller
{
    public function show($id_pembayaran)
    {
        $pembayaran = Pembayaran::with(['tagihan.santri.program', 'tagihan.jenisTagihan', 'kwitansi', 'verifier'])
            ->findOrFail($id_pembayaran);

        // Validasi akses untuk wali santri
        if (Auth::user()->isWali() && $pembayaran->tagihan->santri->id_wali !== Auth::id()) {
            abort(403, 'Akses tidak diizinkan');
        }

        if (!$pembayaran->kwitansi) {
            return back()->with('error', 'Kwitansi belum tersedia untuk transaksi ini karena status belum settlement.');
        }

        return view('kwitansi.show', compact('pembayaran'));
    }

    public function cetak($id_pembayaran)
    {
        $pembayaran = Pembayaran::with(['tagihan.santri.program', 'tagihan.jenisTagihan', 'kwitansi', 'verifier'])
            ->findOrFail($id_pembayaran);

        if (Auth::user()->isWali() && $pembayaran->tagihan->santri->id_wali !== Auth::id()) {
            abort(403, 'Akses tidak diizinkan');
        }

        if (!$pembayaran->kwitansi) {
            return back()->with('error', 'Kwitansi belum tersedia.');
        }

        return view('kwitansi.cetak', compact('pembayaran'));
    }
}

