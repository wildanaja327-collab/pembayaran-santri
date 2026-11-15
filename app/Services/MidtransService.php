<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\Kwitansi;
use App\Models\Pembayaran;
use App\Models\Pengaturan;
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
        $mode = Pengaturan::get('midtrans_mode', config('services.midtrans.is_production') ? 'production' : 'sandbox');
        $isProduction = ($mode === 'production');

        $serverKey = Pengaturan::get('midtrans_server_key') ?: config('services.midtrans.server_key', 'SB-Mid-server-YOUR_SERVER_KEY');
        $isSanitized = (bool) Pengaturan::get('midtrans_is_sanitized', config('services.midtrans.is_sanitized', true));
        $is3ds = (bool) Pengaturan::get('midtrans_is_3ds', config('services.midtrans.is_3ds', true));

        Config::$serverKey = $serverKey;
        Config::$isProduction = $isProduction;
        Config::$isSanitized = $isSanitized;
        Config::$is3ds = $is3ds;
    }

    /**
     * Membuat Snap Token untuk pembayaran tagihan
     */
    public function createSnapTransaction(Tagihan $tagihan, float $nominalBayar, float $biayaAdmin = 0)
    {
        $santri = $tagihan->santri;
        $orderId = 'DMTQ-' . $tagihan->id_tagihan . '-' . time();
        $totalBayar = $nominalBayar + $biayaAdmin;

        // Sanitasi nomor telepon agar valid sesuai spesifikasi Midtrans (9-19 digit)
        $rawPhone = $santri->wali?->no_telp ?: ($santri->no_telp ?? '');
        $cleanPhone = preg_replace('/[^0-9]/', '', $rawPhone);
        if (strlen($cleanPhone) < 9 || strlen($cleanPhone) > 19) {
            $cleanPhone = '081234567890';
        }

        // Sanitasi nama pelanggan (hindari karakter khusus yang ditolak Midtrans)
        $cleanName = preg_replace('/[^a-zA-Z0-9\s\.\,\']/', '', $santri->nama_santri ?? '');
        if (empty(trim($cleanName))) {
            $cleanName = 'Santri ' . ($santri->nis ?? 'Pesantren');
        }

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
                'first_name' => substr($cleanName, 0, 50),
                'email' => 'santri_' . ($santri->nis ?? '0000') . '@darulmuttaqin.sch.id',
                'phone' => $cleanPhone,
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
            Log::error('Midtrans Snap Error: ' . $e->getMessage(), ['params' => $params]);
            
            // Pada mode Production, jangan pernah sembunyikan error dengan demo token!
            if (Config::$isProduction) {
                throw new Exception("Gagal membuat transaksi di Midtrans Production: " . $e->getMessage());
            }

            // Fallback token hanya untuk mode Sandbox jika server key belum dikonfigurasi
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
    public function handleNotification(?\Illuminate\Http\Request $incomingRequest = null): array
    {
        $req = $incomingRequest ?: request();
        $orderId = null;
        $transactionStatus = null;
        $statusCode = null;
        $grossAmount = null;
        $signatureKey = null;
        $paymentType = null;
        $vaNumber = null;
        $raw = null;

        try {
            // Cek apakah php://input memiliki konten yang valid sebelum instansiasi Midtrans Notification
            $rawInput = file_get_contents('php://input');
            if (empty($rawInput) || !json_decode($rawInput, true)) {
                throw new \Exception("Empty or invalid php://input");
            }

            $notif = new Notification();
            $transactionStatus = $notif->transaction_status ?? null;
            $orderId = $notif->order_id ?? null;
            $statusCode = $notif->status_code ?? null;
            $grossAmount = $notif->gross_amount ?? null;
            $signatureKey = $notif->signature_key ?? null;
            $paymentType = $notif->payment_type ?? null;
            if (isset($notif->va_numbers) && is_array($notif->va_numbers) && count($notif->va_numbers) > 0) {
                $vaNumber = $notif->va_numbers[0]->va_number ?? null;
            }
            $raw = json_encode($notif->getResponse());
        } catch (\Throwable $e) {
            // Fallback: Baca langsung dari Request POST payload (berguna jika via webhook proxy / simulasi)
            $orderId = $req->input('order_id');
            $transactionStatus = $req->input('transaction_status');
            $statusCode = $req->input('status_code');
            $grossAmount = $req->input('gross_amount');
            $signatureKey = $req->input('signature_key');
            $paymentType = $req->input('payment_type');
            $vaNumber = $req->input('va_numbers.0.va_number');
            $raw = json_encode($req->all());

            if (!$orderId || !$transactionStatus) {
                Log::error('Midtrans Webhook Error: ' . $e->getMessage());
                return ['status' => 'error', 'message' => $e->getMessage()];
            }
        }

        // Tangani jika simulasi ping
        if (str_starts_with($orderId, 'TEST-PING-') || str_starts_with($orderId, 'TEST-SIMULASI-')) {
            ActivityLog::record('MIDTRANS_WEBHOOK_PING', "Simulasi ping webhook berhasil diterima (Order ID: {$orderId}). Endpoint aktif!");
            return [
                'status' => 'simulation_ok',
                'message' => 'Simulasi payload notifikasi webhook berhasil diverifikasi oleh sistem.',
                'order_id' => $orderId,
            ];
        }

        // Verifikasi Pesanan di Database
        $pembayaran = Pembayaran::where('order_id', $orderId)->first();
        if (!$pembayaran) {
            ActivityLog::record('MIDTRANS_WEBHOOK_FAIL', "Webhook ditolak: Order ID '{$orderId}' tidak ditemukan di sistem.");
            return ['status' => 'error', 'message' => 'Transaksi tidak ditemukan'];
        }

        // Verifikasi Keamanan Webhook:
        // 1. Cek Signature Key jika cocok dengan konfigurasi Server Key
        // 2. Jika signature mismatch (misal sandbox environment / perbedaan key merchant), verifikasi validitas order & nominal
        $serverKey = Config::$serverKey;
        $signatureValid = false;
        if (!empty($signatureKey) && !empty($serverKey) && !empty($statusCode) && !empty($grossAmount)) {
            $expectedSignature = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);
            if (hash_equals($expectedSignature, (string)$signatureKey)) {
                $signatureValid = true;
            }
        }

        if (!$signatureValid) {
            // Pada mode Production, wajib memiliki signature_key resmi Midtrans yang valid
            if (Config::$isProduction) {
                Log::error("Midtrans Production Webhook Rejected: Invalid or missing signature_key for Order: {$orderId}");
                ActivityLog::record('MIDTRANS_WEBHOOK_REJECTED', "Webhook Production ditolak: Signature key tidak valid untuk Order ID: {$orderId}");
                return ['status' => 'error', 'message' => 'Invalid signature key'];
            }

            if (!empty($signatureKey)) {
                $expectedAmount = (float) $pembayaran->total_bayar;
                $receivedAmount = (float) $grossAmount;
                if (abs($expectedAmount - $receivedAmount) > 1.0) {
                    Log::error("Midtrans Webhook: Signature mismatch & nominal tidak cocok! Order: {$orderId}, Expected: {$expectedAmount}, Received: {$receivedAmount}");
                    ActivityLog::record('MIDTRANS_WEBHOOK_REJECTED', "Notifikasi webhook ditolak: Nominal mismatch untuk Order ID: {$orderId}");
                    return ['status' => 'error', 'message' => 'Invalid signature or amount mismatch'];
                }
                Log::info("Midtrans Webhook: Signature verification disesuaikan untuk Order ID: {$orderId} (Valid order & amount match: Rp {$receivedAmount})");
            }
        }

        ActivityLog::record('MIDTRANS_WEBHOOK', "Webhook diterima: Order {$orderId}, Status: {$transactionStatus}, Channel: " . ($paymentType ?? '-'));

        return $this->updatePaymentStatus($pembayaran, $transactionStatus, $paymentType, $vaNumber, $raw);
    }

    /**
     * Sinkronisasi status on-demand via API Midtrans (Untuk Demo Localhost)
     */
    public function syncTransactionStatus(Pembayaran $pembayaran): array
    {
        if (in_array($pembayaran->status_transaksi, ['capture', 'settlement'])) {
            return [
                'status' => 'already_settled',
                'message' => "Order ID {$pembayaran->order_id} sudah lunas (Settlement).",
            ];
        }

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
     * Memperbarui status pembayaran, tagihan, dan menerbitkan kwitansi jika settlement (Idempotent & ACID Safe)
     */
    public function updatePaymentStatus(Pembayaran $pembayaran, string $txStatus, ?string $channelBayar = null, ?string $vaNumber = null, ?string $rawResponse = null): array
    {
        // 1. Cek Idempotensi: Jika transaksi ini sudah berstatus settlement, jangan proses ulang penambahan ke tagihan!
        if ($pembayaran->status_transaksi === 'settlement' && in_array($txStatus, ['capture', 'settlement'])) {
            return [
                'status' => 'already_settled',
                'message' => "Order ID {$pembayaran->order_id} sudah dinyatakan Lunas (Settlement) sebelumnya. Data tagihan tetap aman.",
            ];
        }

        return \Illuminate\Support\Facades\DB::transaction(function () use ($pembayaran, $txStatus, $channelBayar, $vaNumber, $rawResponse) {
            $tagihan = $pembayaran->tagihan;

            $pembayaran->channel_bayar = $channelBayar ?? $pembayaran->channel_bayar;
            $pembayaran->va_number = $vaNumber ?? $pembayaran->va_number;
            $pembayaran->gateway_response = $rawResponse ?? $pembayaran->gateway_response;

            if ($txStatus == 'capture' || $txStatus == 'settlement') {
                $pembayaran->status_transaksi = 'settlement';
                $pembayaran->settlement_time = now();
                $pembayaran->save();

                // Update Tagihan secara atomik
                $newTotalTerbayar = $tagihan->total_terbayar + $pembayaran->jumlah_bayar;
                $newSisa = max(0, $tagihan->nominal_tagihan - $newTotalTerbayar);

                $tagihan->total_terbayar = $newTotalTerbayar;
                $tagihan->sisa_tagihan = $newSisa;
                $tagihan->status_tagihan = ($newSisa <= 0) ? 'lunas' : 'dicicil';
                $tagihan->save();

                // Terbitkan Kwitansi Resmi secara otomatis (jika belum ada)
                if (!$pembayaran->kwitansi) {
                    $noKwitansi = 'KWT/' . date('Ym') . '/' . str_pad($pembayaran->id_pembayaran, 4, '0', STR_PAD_LEFT);
                    Kwitansi::create([
                        'no_kwitansi' => $noKwitansi,
                        'id_pembayaran' => $pembayaran->id_pembayaran,
                        'tgl_terbit' => now(),
                    ]);
                }

                // Otomatis kirim notifikasi kuitansi resmi via WhatsApp Gateway
                try {
                    $waService = app(\App\Services\WhatsAppService::class);
                    $waService->sendPaymentReceipt($pembayaran);
                } catch (\Throwable $waErr) {
                    Log::warning("Gagal kirim WA Settlement Midtrans: " . $waErr->getMessage());
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
        });
    }
}

