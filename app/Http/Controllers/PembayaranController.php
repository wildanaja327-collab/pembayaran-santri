<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
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

        // Count pending
        $pendingCount = Pembayaran::where('status_transaksi', 'pending')->count();
        $settlementCount = Pembayaran::where('status_transaksi', 'settlement')->count();

        return view('bendahara.pembayaran.index', compact('pembayaran', 'pendingCount', 'settlementCount'));
    }

    /**
     * Halaman Rekonsiliasi Gateway Midtrans (Khusus Transaksi Tertunda/Pending)
     */
    public function rekonsiliasi(Request $request)
    {
        $totalGateway = Pembayaran::where('metode_bayar', 'gateway')->count();
        $totalSettlement = Pembayaran::where('metode_bayar', 'gateway')->where('status_transaksi', 'settlement')->count();
        $totalPending = Pembayaran::where('status_transaksi', 'pending')->count();
        $pendingOverdue = Pembayaran::where('status_transaksi', 'pending')
            ->where('tgl_bayar', '<', now()->subHours(24))
            ->count();

        $query = Pembayaran::with(['tagihan.santri.wali', 'tagihan.jenisTagihan'])
            ->where('status_transaksi', 'pending');

        if ($request->filled('kategori') && $request->kategori == 'overdue') {
            $query->where('tgl_bayar', '<', now()->subHours(24));
        }

        $pendingTransactions = $query->latest('tgl_bayar')->paginate(15)->withQueryString();

        return view('bendahara.pembayaran.rekonsiliasi', compact(
            'totalGateway',
            'totalSettlement',
            'totalPending',
            'pendingOverdue',
            'pendingTransactions'
        ));
    }

    /**
     * Pembatalan Transaksi Pending (Void / Cancel) oleh Bendahara
     */
    public function cancelTransaction($id)
    {
        $pembayaran = Pembayaran::with('tagihan')->findOrFail($id);

        if ($pembayaran->status_transaksi !== 'pending') {
            return back()->with('error', 'Hanya transaksi berstatus pending yang dapat dibatalkan.');
        }

        $pembayaran->status_transaksi = 'cancel';
        $pembayaran->save();

        ActivityLog::record(
            'PEMBAYARAN_CANCEL',
            "Membatalkan pesanan pending Order ID: {$pembayaran->order_id} secara manual."
        );

        return back()->with('success', "Order ID {$pembayaran->order_id} berhasil dibatalkan (Cancel).");
    }

    /**
     * Input Pembayaran Kasir Tunai di Kantor Pesantren (Role Bendahara)
     */
    public function createKasirTunai(Request $request)
    {
        $tagihanList = Tagihan::whereIn('status_tagihan', ['belum_bayar', 'dicicil'])
            ->with(['santri', 'jenisTagihan'])
            ->orderBy('nis')
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

            ActivityLog::record('PEMBAYARAN_KASIR', "Menerima kasir tunai senilai Rp " . number_format($pembayaran->jumlah_bayar, 0, ',', '.') . " untuk santri: {$tagihan->santri->nama_santri} (Tagihan: {$tagihan->jenisTagihan->nama_tagihan})");

            // Otomatis kirim notifikasi kwitansi resmi via WhatsApp
            $waMsg = '';
            try {
                $waService = app(\App\Services\WhatsAppService::class);
                $waRes = $waService->sendPaymentReceipt($pembayaran);
                if ($waRes['success']) {
                    $waMsg = ' Notifikasi kuitansi otomatis berhasil dikirimkan ke nomor WhatsApp wali santri.';
                }
            } catch (\Throwable $waErr) {
                \Illuminate\Support\Facades\Log::warning("Gagal kirim WA Kasir: " . $waErr->getMessage());
            }

            return redirect()->route('kwitansi.show', $pembayaran->id_pembayaran)
                ->with('success', 'Pembayaran tunai berhasil dicatat dan kwitansi resmi telah diterbitkan.' . $waMsg);
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
        try {
            $tagihan = Tagihan::with(['santri', 'jenisTagihan'])->findOrFail($id_tagihan);

            // Validasi kepemilikan tagihan untuk wali santri
            if (Auth::user()->isWali() && $tagihan->santri->id_wali !== Auth::id()) {
                return response()->json(['error' => 'Akses tidak sah'], 403);
            }

            if ($tagihan->sisa_tagihan <= 0) {
                return response()->json(['error' => 'Tagihan ini telah berstatus lunas sepenuhnya.'], 422);
            }

            $nominalBayar = (float) $request->input('nominal_bayar', $tagihan->sisa_tagihan);
            if ($nominalBayar < 10000) {
                return response()->json(['error' => 'Nominal pembayaran minimal adalah Rp 10.000 sesuai regulasi payment gateway.'], 422);
            }
            if ($nominalBayar > $tagihan->sisa_tagihan) {
                $nominalBayar = $tagihan->sisa_tagihan;
            }

            // Biaya admin pembayaran online gateway
            $biayaAdmin = (float) \App\Models\Pengaturan::get('midtrans_biaya_admin', 2500);

            $result = $this->midtransService->createSnapTransaction($tagihan, $nominalBayar, $biayaAdmin);

            // Deteksi apakah ini DEMO token (Midtrans key belum valid / sandbox 401)
            $isDemoToken = str_starts_with($result['snap_token'], 'DEMO-TOKEN-');

            return response()->json([
                'success' => true,
                'snap_token' => $result['snap_token'],
                'order_id' => $result['order_id'],
                'id_pembayaran' => $result['pembayaran']->id_pembayaran,
                'nominal_bayar' => $nominalBayar,
                'biaya_admin' => $biayaAdmin,
                'total_bayar' => $nominalBayar + $biayaAdmin,
                'is_demo' => $isDemoToken,
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => 'Tagihan tidak ditemukan.'], 404);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('getSnapToken Error: ' . $e->getMessage());
            return response()->json(['error' => 'Gagal memproses permintaan: ' . $e->getMessage()], 500);
        }
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
     * Simulator Instant Settlement (Khusus Mode Sandbox / Uji Coba)
     */
    public function simulateSettlement($id_pembayaran)
    {
        $isProduction = (\App\Models\Pengaturan::get('midtrans_mode', config('services.midtrans.is_production') ? 'production' : 'sandbox') === 'production');
        if ($isProduction) {
            return back()->with('error', 'Fitur simulasi pembayaran dinonaktifkan pada mode Production.');
        }

        $pembayaran = Pembayaran::with('tagihan')->findOrFail($id_pembayaran);

        $this->midtransService->updatePaymentStatus(
            $pembayaran,
            'settlement',
            'BCA Virtual Account (Demo)',
            '8808' . rand(10000000, 99999999),
            json_encode(['simulation' => true, 'timestamp' => now()->toIso8601String()])
        );

        ActivityLog::record('SIMULASI_SETTLEMENT', "Simulasi pelunasan virtual account untuk Order ID: {$pembayaran->order_id}");

        return back()->with('success', 'Simulasi pembayaran berhasil! Transaksi dinyatakan SETTLEMENT dan kwitansi resmi telah diterbitkan.');
    }

    /**
     * Sinkronisasi Status Transaksi dari Midtrans untuk Wali Santri
     */
    public function syncWaliStatus(Request $request, $id_pembayaran)
    {
        $pembayaran = Pembayaran::with('tagihan.santri')->findOrFail($id_pembayaran);

        if ($pembayaran->tagihan->santri->id_wali !== Auth::id()) {
            return response()->json(['error' => 'Akses tidak sah'], 403);
        }

        // Jika transaksi ini sudah lunas, langsung return respons sukses
        if (in_array($pembayaran->status_transaksi, ['capture', 'settlement'])) {
            return response()->json([
                'success' => true,
                'status' => 'settlement',
                'message' => 'Pembayaran sudah lunas diverifikasi.',
                'is_settlement' => true,
            ]);
        }

        // 1. Utamakan verifikasi langsung ke API Server Midtrans menggunakan Server Key resmi
        $res = $this->midtransService->syncTransactionStatus($pembayaran);
        if (($res['status'] ?? '') !== 'error') {
            $fresh = $pembayaran->fresh();
            return response()->json([
                'success' => true,
                'status' => $fresh->status_transaksi,
                'message' => $res['message'] ?? 'Status berhasil diperbarui dari server Midtrans.',
                'is_settlement' => in_array($fresh->status_transaksi, ['capture', 'settlement']),
            ]);
        }

        // 2. Fallback payload Snap hanya diizinkan pada Mode Sandbox (tidak pernah pada Mode Production)
        $isProduction = (\App\Models\Pengaturan::get('midtrans_mode', config('services.midtrans.is_production') ? 'production' : 'sandbox') === 'production');
        $snapStatus = $request->input('transaction_status');
        $snapOrderId = $request->input('order_id');
        $snapPaymentType = $request->input('payment_type');
        $snapVa = $request->input('va_numbers.0.va_number');

        if (!$isProduction && $snapStatus && $snapOrderId === $pembayaran->order_id) {
            $this->midtransService->updatePaymentStatus(
                $pembayaran,
                $snapStatus,
                $snapPaymentType,
                $snapVa,
                json_encode($request->all())
            );

            return response()->json([
                'success' => true,
                'status' => $snapStatus,
                'message' => 'Status pembayaran berhasil disinkronkan dari Midtrans Sandbox.',
                'is_settlement' => in_array($pembayaran->fresh()->status_transaksi, ['capture', 'settlement']),
            ]);
        }

        return response()->json([
            'success' => true,
            'status' => $res['status'] ?? 'unknown',
            'message' => $res['message'] ?? 'Status berhasil diperbarui',
            'is_settlement' => in_array($pembayaran->fresh()->status_transaksi, ['capture', 'settlement']),
        ]);
    }

    /**
     * Simulator Instant Settlement untuk Wali Santri (Khusus Mode Sandbox)
     */
    public function simulateSettlementWali(Request $request, $id_pembayaran)
    {
        $isProduction = (\App\Models\Pengaturan::get('midtrans_mode', config('services.midtrans.is_production') ? 'production' : 'sandbox') === 'production');
        if ($isProduction) {
            return response()->json(['error' => 'Fitur simulasi pembayaran tidak tersedia pada mode Production.'], 403);
        }

        $pembayaran = Pembayaran::with('tagihan.santri')->findOrFail($id_pembayaran);

        if ($pembayaran->tagihan->santri->id_wali !== Auth::id()) {
            return response()->json(['error' => 'Akses tidak sah'], 403);
        }

        $this->midtransService->updatePaymentStatus(
            $pembayaran,
            'settlement',
            'BCA Virtual Account (Demo)',
            '8808' . rand(10000000, 99999999),
            json_encode(['simulation' => true, 'timestamp' => now()->toIso8601String()])
        );

        ActivityLog::record('SIMULASI_SETTLEMENT_WALI', "Simulasi pelunasan virtual account oleh wali santri untuk Order ID: {$pembayaran->order_id}");

        return response()->json([
            'success' => true,
            'message' => 'Simulasi pembayaran berhasil! Transaksi dinyatakan SETTLEMENT dan kwitansi resmi telah diterbitkan.',
        ]);
    }
}
