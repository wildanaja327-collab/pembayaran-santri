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

    public function cetak(Request $request, $id_pembayaran)
    {
        $pembayaran = Pembayaran::with(['tagihan.santri.wali', 'tagihan.santri.program', 'tagihan.jenisTagihan', 'kwitansi', 'verifier'])
            ->findOrFail($id_pembayaran);

        if (!$pembayaran->kwitansi) {
            return back()->with('error', 'Kwitansi belum tersedia untuk transaksi ini.');
        }

        // 1. Cek Token Akses Publik Tanpa Login (misal diklik langsung dari notifikasi WhatsApp)
        $token = $request->query('token');
        $validToken = $pembayaran->getKwitansiToken();

        if (!empty($token) && hash_equals($validToken, (string) $token)) {
            // Token valid: izinkan akses langsung cetak & unduh kwitansi tanpa perlu login
            return view('kwitansi.cetak', compact('pembayaran'));
        }

        // 2. Jika tanpa token publik, pengguna wajib login terlebih dahulu
        if (!Auth::check()) {
            return redirect()->route('login')->with('warning', 'Silakan login terlebih dahulu untuk mengakses dokumen kwitansi ini, atau gunakan tautan resmi yang dikirimkan via WhatsApp.');
        }

        // 3. Jika login sebagai wali santri, pastikan hanya berhak melihat santri binaannya
        if (Auth::user()->isWali() && $pembayaran->tagihan->santri->id_wali !== Auth::id()) {
            abort(403, 'Akses tidak diizinkan');
        }

        return view('kwitansi.cetak', compact('pembayaran'));
    }

    public function cetakPublik(Request $request, $id_pembayaran)
    {
        return $this->cetak($request, $id_pembayaran);
    }

    /**
     * Kirim Ulang Bukti Kwitansi via WhatsApp Gateway Fonnte
     */
    public function kirimWa($id_pembayaran, \App\Services\WhatsAppService $waService)
    {
        $pembayaran = Pembayaran::with(['tagihan.santri.wali', 'tagihan.jenisTagihan', 'kwitansi'])
            ->findOrFail($id_pembayaran);

        if (Auth::user()->isWali() && $pembayaran->tagihan->santri->id_wali !== Auth::id()) {
            abort(403, 'Akses tidak diizinkan');
        }

        $res = $waService->sendPaymentReceipt($pembayaran);

        if ($res['success']) {
            return back()->with('success', 'Bukti kwitansi resmi berhasil dikirimkan ke nomor WhatsApp wali santri.');
        } else {
            return back()->with('error', 'Gagal mengirim WhatsApp: ' . ($res['message'] ?? 'Kesalahan koneksi gateway'));
        }
    }
}

