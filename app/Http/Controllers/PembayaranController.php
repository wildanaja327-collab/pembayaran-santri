<?php

namespace App\Http\Controllers;

use App\Models\Kwitansi;
use App\Models\Pembayaran;
use App\Models\Tagihan;
use App\Services\MidtransService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PembayaranController extends Controller
{
    protected MidtransService $midtransService;

    public function __construct(MidtransService $midtransService)
    {
        $this->midtransService = $midtransService;
    }

    /**
     * Halaman Monitoring Seluruh Transaksi Pembayaran (Role Bendahara)
     */
    public function index(Request $request)
    {
        $query = Pembayaran::with(['tagihan.santri', 'tagihan.jenisTagihan', 'kwitansi', 'verifier']);

        if ($request->filled('status_transaksi')) {
            $query->where('status_transaksi', $request->status_transaksi);
        }

        if ($request->filled('metode_bayar')) {
            $query->where('metode_bayar', $request->metode_bayar);
        }

        if ($request->filled('tgl_mulai') && $request->filled('tgl_selesai')) {
            $query->whereBetween('tgl_bayar', [$request->tgl_mulai . ' 00:00:00', $request->tgl_selesai . ' 23:59:59']);
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

        $pembayaran = $query->latest('id_pembayaran')->paginate(15)->withQueryString();

        return view('bendahara.pembayaran.index', compact('pembayaran'));
    }

    /**
     * Input Pembayaran Kasir Tunai di Kantor Pesantren (Role Bendahara)
     */
    public function createKasirTunai(Request $request)
    {
        $tagihanList = Tagihan::whereIn('status_tagihan', ['belum_bayar', 'dicicil'])
            ->with(['santri', 'jenisTagihan'])
            ->get();

        $selectedTagihanId = $request->query('id_tagihan');

        return view('bendahara.pembayaran.kasir', compact('tagihanList', 'selectedTagihanId'));
    }

    /**
     * Simpan Pembayaran Kasir Tunai (Otomatis Settlement & Terbit Kwitansi)
     */
    public function storeKasirTunai(Request $request)
    {
        $request->validate([
            'id_tagihan' => 'required|exists:tagihan,id_tagihan',
            'jumlah_bayar' => 'required|numeric|min:1',
            'catatan' => 'nullable|string|max:255',
        ]);

        $tagihan = Tagihan::findOrFail($request->id_tagihan);

        if ($request->jumlah_bayar > $tagihan->sisa_tagihan) {
            return back()->withInput()->with('error', 'Jumlah bayar melebihi sisa tagihan (Rp ' . number_format($tagihan->sisa_tagihan, 0, ',', '.') . ').');
        }

        DB::beginTransaction();
        try {
            $orderId = 'TUNAI-' . $tagihan->id_tagihan . '-' . time();

            $pembayaran = Pembayaran::create([
                'order_id' => $orderId,
                'id_tagihan' => $tagihan->id_tagihan,
                'tgl_bayar' => now(),
                'jumlah_bayar' => $request->jumlah_bayar,
                'biaya_admin' => 0.00,
                'total_bayar' => $request->jumlah_bayar,
                'metode_bayar' => 'tunai',
                'channel_bayar' => 'Tunai Kasir Bendahara',
                'status_transaksi' => 'settlement',
                'settlement_time' => now(),
                'gateway_response' => $request->catatan,
                'verified_by' => Auth::id(),
            ]);

            // Update Tagihan
            $newTotalTerbayar = $tagihan->total_terbayar + $request->jumlah_bayar;
            $newSisa = max(0, $tagihan->nominal_tagihan - $newTotalTerbayar);

            $tagihan->total_terbayar = $newTotalTerbayar;
            $tagihan->sisa_tagihan = $newSisa;
            $tagihan->status_tagihan = ($newSisa <= 0) ? 'lunas' : 'dicicil';
            $tagihan->save();

            // Terbitkan Kwitansi Resmi
            $noKwitansi = 'KWT/' . date('Ym') . '/' . str_pad($pembayaran->id_pembayaran, 4, '0', STR_PAD_LEFT);
            Kwitansi::create([
                'no_kwitansi' => $noKwitansi,
                'id_pembayaran' => $pembayaran->id_pembayaran,
                'tgl_terbit' => now(),
            ]);

            DB::commit();

            return redirect()->route('kwitansi.show', $pembayaran->id_pembayaran)
                ->with('success', 'Pembayaran tunai berhasil dicatat dan kwitansi resmi telah diterbitkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal mencatat pembayaran tunai: ' . $e->getMessage());
        }
    }

    /**
     * Meminta Snap Token Midtrans untuk Wali Santri
     */
    public function getSnapToken(Request $request, $id_tagihan)
    {
        $tagihan = Tagihan::with(['santri', 'jenisTagihan'])->findOrFail($id_tagihan);

        // Validasi kepemilikan tagihan untuk wali santri
        if (Auth::user()->isWali() && $tagihan->santri->id_wali !== Auth::id()) {
            return response()->json(['error' => 'Akses tidak sah'], 403);
        }

        $nominalBayar = $request->input('nominal_bayar', $tagihan->sisa_tagihan);
        if ($nominalBayar <= 0 || $nominalBayar > $tagihan->sisa_tagihan) {
            $nominalBayar = $tagihan->sisa_tagihan;
        }

        // Biaya admin simulasi / standar payment gateway (misal Rp 2.500)
        $biayaAdmin = 2500;

        $result = $this->midtransService->createSnapTransaction($tagihan, $nominalBayar, $biayaAdmin);

        return response()->json([
            'success' => true,
            'snap_token' => $result['snap_token'],
            'order_id' => $result['order_id'],
            'id_pembayaran' => $result['pembayaran']->id_pembayaran,
            'nominal_bayar' => $nominalBayar,
            'biaya_admin' => $biayaAdmin,
            'total_bayar' => $nominalBayar + $biayaAdmin,
        ]);
    }

    /**
     * Sinkronisasi Status Transaksi dari Midtrans (Fitur Fallback Localhost Demo)
     */
    public function syncStatus($id_pembayaran)
    {
        $pembayaran = Pembayaran::with('tagihan')->findOrFail($id_pembayaran);
        $res = $this->midtransService->syncTransactionStatus($pembayaran);

        return back()->with('info', 'Status transaksi diperbarui: ' . $res['message']);
    }

    /**
     * Simulator Instant Settlement (Untuk Keperluan Demo Sidang Skripsi di Localhost)
     */
    public function simulateSettlement($id_pembayaran)
    {
        $pembayaran = Pembayaran::with('tagihan')->findOrFail($id_pembayaran);
        
        $this->midtransService->updatePaymentStatus(
            $pembayaran,
            'settlement',
            'BCA Virtual Account (Demo)',
            '8808' . rand(10000000, 99999999),
            json_encode(['simulation' => true, 'timestamp' => now()->toIso8601String()])
        );

        return back()->with('success', 'Simulasi pembayaran berhasil! Transaksi dinyatakan SETTLEMENT dan kwitansi resmi telah diterbitkan.');
    }
}

