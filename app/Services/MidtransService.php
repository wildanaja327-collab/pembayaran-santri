<?php

namespace App\Services;

use App\Models\Kwitansi;
use App\Models\Pembayaran;
use App\Models\Tagihan;
use Exception;
use Illuminate\Support\Facades\Log;
use Midtrans\Config;
use Midtrans\Notification;
use Midtrans\Snap;
use Midtrans\Transaction;

class MidtransService
{
    public function __construct()
    {
        Config::$serverKey = config('services.midtrans.server_key', env('MIDTRANS_SERVER_KEY', 'SB-Mid-server-YOUR_SERVER_KEY'));
        Config::$isProduction = (bool) config('services.midtrans.is_production', env('MIDTRANS_IS_PRODUCTION', false));
        Config::$isSanitized = (bool) config('services.midtrans.is_sanitized', env('MIDTRANS_IS_SANITIZED', true));
        Config::$is3ds = (bool) config('services.midtrans.is_3ds', env('MIDTRANS_IS_3DS', true));
    }

    /**
     * Membuat Snap Token untuk pembayaran tagihan
     */
    public function createSnapTransaction(Tagihan $tagihan, float $nominalBayar, float $biayaAdmin = 0)
    {
        $santri = $tagihan->santri;
        $orderId = 'DMTQ-' . $tagihan->id_tagihan . '-' . time();
        $totalBayar = $nominalBayar + $biayaAdmin;

        $params = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => (int) round($totalBayar),
            ],
            'item_details' => [
                [
                    'id' => 'TAG-' . $tagihan->id_tagihan,
                    'price' => (int) round($nominalBayar),
                    'quantity' => 1,
                    'name' => substr($tagihan->jenisTagihan->nama_tagihan . ' (' . ($tagihan->bulan ? $tagihan->nama_bulan . ' ' : '') . $tagihan->tahun_ajaran . ')', 0, 50),
                ],
            ],
            'customer_details' => [
                'first_name' => $santri->nama_santri,
                'email' => 'santri_' . $santri->nis . '@darulmuttaqin.sch.id',
                'phone' => $santri->wali?->no_telp ?? '08123456789',
            ],
        ];

        if ($biayaAdmin > 0) {
            $params['item_details'][] = [
                'id' => 'ADMIN-FEE',
                'price' => (int) round($biayaAdmin),
                'quantity' => 1,
                'name' => 'Biaya Layanan Payment Gateway',
            ];
        }

        try {
            $snapToken = Snap::getSnapToken($params);
        } catch (Exception $e) {
            Log::error('Midtrans Snap Error: ' . $e->getMessage());
            // Fallback token untuk demo lokal jika key belum diganti dengan key asli
            $snapToken = 'DEMO-TOKEN-' . md5($orderId);
        }

        // Catat entri pembayaran baru berstatus pending
        $pembayaran = Pembayaran::create([
            'order_id' => $orderId,
            'id_tagihan' => $tagihan->id_tagihan,
            'tgl_bayar' => now(),
            'jumlah_bayar' => $nominalBayar,
            'biaya_admin' => $biayaAdmin,
            'total_bayar' => $totalBayar,
            'metode_bayar' => 'gateway',
            'snap_token' => $snapToken,
            'status_transaksi' => 'pending',
        ]);

        return [
            'snap_token' => $snapToken,
            'order_id' => $orderId,
            'pembayaran' => $pembayaran,
        ];
    }

    /**
     * Memproses notifikasi webhook dari Midtrans
     */
    public function handleNotification(): array
    {
        try {
            $notif = new Notification();
        } catch (Exception $e) {
            Log::error('Midtrans Webhook Error: ' . $e->getMessage());
            return ['status' => 'error', 'message' => $e->getMessage()];
        }

        $transactionStatus = $notif->transaction_status;
        $orderId = $notif->order_id;
        $paymentType = $notif->payment_type ?? null;
        $fraudStatus = $notif->fraud_status ?? null;

        $vaNumber = null;
        if (isset($notif->va_numbers) && is_array($notif->va_numbers) && count($notif->va_numbers) > 0) {
            $vaNumber = $notif->va_numbers[0]->va_number ?? null;
        }

        $pembayaran = Pembayaran::where('order_id', $orderId)->first();
        if (!$pembayaran) {
            return ['status' => 'error', 'message' => 'Transaksi tidak ditemukan'];
        }

        return $this->updatePaymentStatus($pembayaran, $transactionStatus, $paymentType, $vaNumber, json_encode($notif->getResponse()));
    }

    /**
     * Sinkronisasi status on-demand via API Midtrans (Untuk Demo Localhost)
     */
    public function syncTransactionStatus(Pembayaran $pembayaran): array
    {
        try {
            $statusObj = Transaction::status($pembayaran->order_id);
            $txStatus = $statusObj->transaction_status ?? 'pending';
            $paymentType = $statusObj->payment_type ?? $pembayaran->channel_bayar;
            $vaNumber = $statusObj->va_numbers[0]->va_number ?? $pembayaran->va_number;

            return $this->updatePaymentStatus($pembayaran, $txStatus, $paymentType, $vaNumber, json_encode($statusObj));
        } catch (Exception $e) {
            Log::warning('Midtrans Sync Error: ' . $e->getMessage());
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    /**
     * Memperbarui status pembayaran, tagihan, dan menerbitkan kwitansi jika settlement
     */
    public function updatePaymentStatus(Pembayaran $pembayaran, string $txStatus, ?string $channelBayar = null, ?string $vaNumber = null, ?string $rawResponse = null): array
    {
        $tagihan = $pembayaran->tagihan;

        $pembayaran->channel_bayar = $channelBayar ?? $pembayaran->channel_bayar;
        $pembayaran->va_number = $vaNumber ?? $pembayaran->va_number;
        $pembayaran->gateway_response = $rawResponse ?? $pembayaran->gateway_response;

        if ($txStatus == 'capture' || $txStatus == 'settlement') {
            $pembayaran->status_transaksi = 'settlement';
            $pembayaran->settlement_time = now();
            $pembayaran->save();

            // Update Tagihan
            $newTotalTerbayar = $tagihan->total_terbayar + $pembayaran->jumlah_bayar;
            $newSisa = max(0, $tagihan->nominal_tagihan - $newTotalTerbayar);

            $tagihan->total_terbayar = $newTotalTerbayar;
            $tagihan->sisa_tagihan = $newSisa;
            $tagihan->status_tagihan = ($newSisa <= 0) ? 'lunas' : 'dicicil';
            $tagihan->save();

            // Terbitkan Kwitansi Resmi secara otomatis
            if (!$pembayaran->kwitansi) {
                $noKwitansi = 'KWT/' . date('Ym') . '/' . str_pad($pembayaran->id_pembayaran, 4, '0', STR_PAD_LEFT);
                Kwitansi::create([
                    'no_kwitansi' => $noKwitansi,
                    'id_pembayaran' => $pembayaran->id_pembayaran,
                    'tgl_terbit' => now(),
                ]);
            }

            return ['status' => 'success', 'message' => 'Pembayaran berhasil diselesaikan (Settlement)'];
        } elseif ($txStatus == 'pending') {
            $pembayaran->status_transaksi = 'pending';
            $pembayaran->save();
            return ['status' => 'pending', 'message' => 'Menunggu penyelesaian pembayaran'];
        } elseif (in_array($txStatus, ['deny', 'expire', 'cancel'])) {
            $pembayaran->status_transaksi = $txStatus;
            $pembayaran->save();

            // Kembalikan status tagihan jika belum pernah dicicil
            if ($tagihan->total_terbayar <= 0) {
                $tagihan->status_tagihan = 'belum_bayar';
                $tagihan->save();
            }

            return ['status' => 'cancelled', 'message' => 'Transaksi dibatalkan atau kadaluwarsa'];
        }

        return ['status' => 'unknown', 'message' => 'Status tidak dikenali: ' . $txStatus];
    }
}

