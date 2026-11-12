<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\NotifikasiWa;
use App\Models\Pembayaran;
use App\Models\Pengaturan;
use App\Models\Tagihan;
use Exception;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    protected ?string $token;
    protected bool $enabled;

    public function __construct()
    {
        $this->token = Pengaturan::get('fonnte_token', env('FONNTE_TOKEN', ''));
        $this->enabled = (bool) Pengaturan::get('wa_notif_enabled', env('WA_NOTIF_ENABLED', true));
    }

    /**
     * Format nomor HP ke standar internasional (628xxx)
     */
    public function formatPhoneNumber(string $phone): string
    {
        // Hilangkan spasi, tanda hubung, tanda kurung, titik, dan plus
        $cleaned = preg_replace('/[^0-9]/', '', $phone);

        // Jika diawali 08, ubah jadi 628
        if (str_starts_with($cleaned, '0')) {
            $cleaned = '62' . substr($cleaned, 1);
        } elseif (str_starts_with($cleaned, '8')) {
            $cleaned = '62' . $cleaned;
        }

        return $cleaned;
    }

    /**
     * Cek status koneksi perangkat di Fonnte API
     */
    public function checkDeviceStatus(): array
    {
        if (empty($this->token)) {
            return [
                'success' => false,
                'status' => 'disconnected',
                'message' => 'Token API Fonnte belum diatur di menu konfigurasi.',
            ];
        }

        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://api.fonnte.com/device');
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Authorization: ' . $this->token,
                'Accept: application/json',
            ]);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            curl_close($ch);

            if ($curlError) {
                return [
                    'success' => false,
                    'status' => 'error',
                    'message' => 'Gagal menghubungi server Fonnte: ' . $curlError,
                ];
            }

            $json = json_decode($response, true);

            if (isset($json['status']) && $json['status'] === true) {
                // Simpan nomor pengirim terkini ke database
                if (!empty($json['device'])) {
                    Pengaturan::set('wa_sender_number', $json['device'], 'Nomor WhatsApp Pengirim Terhubung', 'whatsapp');
                }

                return [
                    'success' => true,
                    'status' => $json['device_status'] ?? 'connected',
                    'device' => $json['device'] ?? '-',
                    'name' => $json['name'] ?? 'Akun Pesantren',
                    'package' => $json['package'] ?? 'Free',
                    'quota' => $json['quota'] ?? 0,
                    'expired' => $json['expired'] ?? '-',
                    'raw' => $json,
                ];
            }

            return [
                'success' => false,
                'status' => 'disconnected',
                'message' => $json['reason'] ?? 'Token tidak valid atau perangkat belum terhubung.',
                'raw' => $json,
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'status' => 'error',
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Kirim pesan teks WhatsApp via Fonnte API
     */
    public function sendMessage(string $target, string $message, ?string $nis = null, string $jenisPesan = 'pembayaran_sukses'): array
    {
        if (!$this->enabled || empty($this->token)) {
            Log::info("WA Service skipped: not enabled or empty token. Target: {$target}");
            return [
                'success' => false,
                'message' => 'Layanan WhatsApp tidak aktif atau token belum diatur.',
            ];
        }

        $formattedTarget = $this->formatPhoneNumber($target);

        // Jika nomor terlalu pendek / tidak valid
        if (strlen($formattedTarget) < 9) {
            return [
                'success' => false,
                'message' => "Nomor tujuan '{$target}' tidak valid.",
            ];
        }

        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://api.fonnte.com/send');
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, [
                'target' => $formattedTarget,
                'message' => $message,
                'countryCode' => '62',
            ]);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 15);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Authorization: ' . $this->token,
            ]);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            curl_close($ch);

            if ($curlError) {
                $statusKirim = 'gagal';
                $respData = ['error' => $curlError];
            } else {
                $respData = json_decode($response, true) ?? ['raw' => $response];
                $statusKirim = (isset($respData['status']) && $respData['status'] === true) ? 'terkirim' : 'gagal';
            }

            // Catat ke tabel riwayat notifikasi_wa
            $notif = NotifikasiWa::create([
                'nis' => $nis,
                'no_tujuan' => $formattedTarget,
                'pesan' => $message,
                'jenis_pesan' => $jenisPesan,
                'status_kirim' => $statusKirim,
                'tgl_kirim' => now(),
                'response_api' => json_encode($respData),
            ]);

            // Catat ke Audit Trail
            ActivityLog::record(
                'WA_NOTIF_' . strtoupper($statusKirim),
                "Notifikasi WA ({$jenisPesan}) ke {$formattedTarget} status: " . strtoupper($statusKirim)
            );

            return [
                'success' => ($statusKirim === 'terkirim'),
                'id_notifikasi' => $notif->id_notifikasi,
                'status_kirim' => $statusKirim,
                'message' => ($statusKirim === 'terkirim') ? 'Pesan WhatsApp berhasil terkirim ke ' . $formattedTarget : 'Gagal mengirim pesan WhatsApp: ' . ($respData['reason'] ?? 'Unknown error'),
                'response' => $respData,
            ];
        } catch (Exception $e) {
            Log::error('Fonnte Send Error: ' . $e->getMessage());

            NotifikasiWa::create([
                'nis' => $nis,
                'no_tujuan' => $formattedTarget,
                'pesan' => $message,
                'jenis_pesan' => $jenisPesan,
                'status_kirim' => 'gagal',
                'tgl_kirim' => now(),
                'response_api' => json_encode(['exception' => $e->getMessage()]),
            ]);

            return [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Otomatis kirim bukti kwitansi resmi setelah pembayaran lunas / cicilan sukses
     */
    public function sendPaymentReceipt(Pembayaran $pembayaran): array
    {
        $pembayaran->loadMissing([
            'tagihan.santri.wali',
            'tagihan.santri.program',
            'tagihan.jenisTagihan',
            'kwitansi',
        ]);

        $tagihan = $pembayaran->tagihan;
        if (!$tagihan) {
            return ['success' => false, 'message' => 'Data tagihan tidak ditemukan.'];
        }

        $santri = $tagihan->santri;
        $wali = $santri?->wali;

        // Ambil nomor tujuan WhatsApp
        $targetPhone = $wali?->no_telp ?: ($santri?->no_telp ?? null);

        if (empty($targetPhone)) {
            Log::warning("Gagal kirim WA Kwitansi: No telp wali/santri {$santri?->nama_santri} tidak tersedia.");
            return [
                'success' => false,
                'message' => 'Nomor WhatsApp wali santri belum terdaftar.',
            ];
        }

        $pesantrenNama = Pengaturan::get('pesantren_nama', 'Pondok Pesantren Darul Muttaqin Al Islami');
        $pesantrenTelp = Pengaturan::get('pesantren_telepon', '0812-3456-7890');

        $noKwitansi = $pembayaran->kwitansi?->no_kwitansi ?? ('KWT/' . date('Ym') . '/' . str_pad($pembayaran->id_pembayaran, 4, '0', STR_PAD_LEFT));
        $namaSantri = $santri?->nama_santri ?? 'Santri';
        $nis = $santri?->nis ?? '-';
        $kelas = ($santri?->tingkat ?? '') . ' Kelas ' . ($santri?->kelas ?? '-');
        $namaProgram = $santri?->program?->nama_program ?? '-';
        $komponen = $tagihan->jenisTagihan?->nama_tagihan ?? 'Tagihan';
        $periode = ($tagihan->bulan ? $tagihan->nama_bulan . ' ' : '') . $tagihan->tahun_ajaran;

        $nominalBayar = 'Rp ' . number_format($pembayaran->jumlah_bayar, 0, ',', '.');
        $metodeBayar = strtoupper($pembayaran->channel_bayar ?? $pembayaran->metode_bayar);
        $waktuBayar = \Carbon\Carbon::parse($pembayaran->tgl_bayar)->setTimezone('Asia/Jakarta')->format('d-m-Y H:i') . ' WIB';

        $statusTagihan = ($tagihan->sisa_tagihan <= 0)
            ? '✅ *LUNAS PENUH*'
            : '⏳ *DICICIL* (Sisa Tagihan: Rp ' . number_format($tagihan->sisa_tagihan, 0, ',', '.') . ')';

        $urlKwitansi = $pembayaran->getPublicKwitansiUrl();

        // Susun draf pesan WhatsApp yang resmi, sopan, dan mewah
        $message = "*{$pesantrenNama}*\n";
        $message .= "_Sistem Informasi Manajemen Keuangan Santri_\n";
        $message .= "━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
        $message .= "🧾 *BUKTI PEMBAYARAN RESMI (KWITANSI)*\n";
        $message .= "Alhamdulillah, pembayaran tagihan ananda telah berhasil kami terima dengan rincian:\n\n";
        $message .= "📄 *No. Kwitansi* : `{$noKwitansi}`\n";
        $message .= "👤 *Nama Santri*  : *{$namaSantri}*\n";
        $message .= "🆔 *NIS / Kelas*  : {$nis} ({$kelas})\n";
        $message .= "📚 *Program*      : {$namaProgram}\n";
        $message .= "🏷️ *Komponen*     : {$komponen} ({$periode})\n\n";
        $message .= "💰 *Jumlah Bayar* : *{$nominalBayar}*\n";
        $message .= "💳 *Metode Bayar* : {$metodeBayar}\n";
        $message .= "🕒 *Waktu Bayar*  : {$waktuBayar}\n";
        $message .= "📌 *Status Biaya* : {$statusTagihan}\n\n";
        $message .= "Unduh & Cetak Dokumen Kwitansi Sah (PDF):\n";
        $message .= "🔗 {$urlKwitansi}\n\n";
        $message .= "_Jazakumullah khairan katsiran atas pembayaran tepat waktu. Semoga menjadi amal jariyah dan membawa keberkahan bagi ananda tercinta._\n";
        $message .= "━━━━━━━━━━━━━━━━━━━━━━━━\n";
        $message .= "📞 *Layanan Informasi & Keuangan: {$pesantrenTelp}*";

        return $this->sendMessage($targetPhone, $message, $nis, 'pembayaran_sukses');
    }

    /**
     * Kirim notifikasi pengingat pembayaran tagihan santri via WhatsApp Gateway
     */
    public function sendPaymentReminder(Tagihan $tagihan): array
    {
        $tagihan->loadMissing([
            'santri.wali',
            'santri.program',
            'jenisTagihan',
        ]);

        $santri = $tagihan->santri;
        $wali = $santri?->wali;

        $targetPhone = $wali?->no_telp ?: ($santri?->no_telp ?? null);

        if (empty($targetPhone)) {
            Log::warning("Gagal kirim WA Pengingat: No telp wali/santri {$santri?->nama_santri} tidak tersedia.");
            return [
                'success' => false,
                'message' => 'Nomor WhatsApp wali santri belum terdaftar di sistem.',
            ];
        }

        $pesantrenNama = Pengaturan::get('pesantren_nama', 'Pondok Pesantren Darul Muttaqin Al Islami');
        $pesantrenTelp = Pengaturan::get('pesantren_telepon', '0812-3456-7890');

        $namaSantri = $santri?->nama_santri ?? 'Santri';
        $namaWali = $wali?->nama_lengkap ?? 'Bapak/Ibu Wali Santri';
        $nis = $santri?->nis ?? '-';
        $kelas = ($santri?->tingkat ?? '') . ' Kelas ' . ($santri?->kelas ?? '-');
        $namaProgram = $santri?->program?->nama_program ?? '-';
        $komponen = $tagihan->jenisTagihan?->nama_tagihan ?? 'Tagihan';
        $periode = ($tagihan->bulan ? $tagihan->nama_bulan . ' ' : '') . $tagihan->tahun_ajaran;
        $sisaTagihan = 'Rp ' . number_format($tagihan->sisa_tagihan, 0, ',', '.');
        $tglJatuhTempo = $tagihan->jatuh_tempo ? date('d-m-Y', strtotime($tagihan->jatuh_tempo)) : '-';

        $isOverdue = $tagihan->jatuh_tempo && strtotime($tagihan->jatuh_tempo) < time();
        $statusTempo = $isOverdue ? '⚠️ *TELAH MELEWATI JATUH TEMPO*' : "⏳ *Jatuh Tempo: {$tglJatuhTempo}*";

        $portalUrl = self::getPublicBaseUrl() . '/login';

        // Susun draf pesan pengingat tagihan
        $message = "*{$pesantrenNama}*\n";
        $message .= "_Layanan Informasi & Administrasi Keuangan_\n";
        $message .= "━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
        $message .= "Assalamu'alaikum Warahmatullahi Wabarakatuh,\n\n";
        $message .= "Yth. *{$namaWali}*,\n";
        $message .= "Semoga Bapak/Ibu beserta keluarga senantiasa berada dalam lindungan dan rahmat Allah SWT.\n\n";
        $message .= "Melalui pemberitahuan resmi ini, kami menginformasikan mengenai kewajiban administrasi pendidikan ananda tercinta:\n\n";
        $message .= "👤 *Nama Santri*  : *{$namaSantri}*\n";
        $message .= "🆔 *NIS / Kelas*  : {$nis} ({$kelas})\n";
        $message .= "📚 *Program*      : {$namaProgram}\n";
        $message .= "🏷️ *Komponen*     : {$komponen} ({$periode})\n";
        $message .= "💰 *Sisa Tagihan* : *{$sisaTagihan}*\n";
        $message .= "📅 *Batas Waktu*  : {$statusTempo}\n\n";
        $message .= "Pembayaran dapat diselesaikan melalui salah satu jalur berikut:\n";
        $message .= "1️⃣ *Portal Wali Santri* (Online 24 Jam):\n";
        $message .= "   Login ke {$portalUrl} dan pilih metode Virtual Account / QRIS Midtrans.\n";
        $message .= "2️⃣ *Kantor Bendahara Pesantren*:\n";
        $message .= "   Pembayaran tunai langsung di loket kasir keuangan pondok.\n\n";
        $message .= "_Apabila Bapak/Ibu telah menyelesaikan pembayaran ini, mohon abaikan pesan ini. Atas perhatian dan kerjasamanya kami haturkan terima kasih. Jazakumullah khairan katsiran._\n";
        $message .= "━━━━━━━━━━━━━━━━━━━━━━━━\n";
        $message .= "📞 *Layanan Informasi Keuangan: {$pesantrenTelp}*";

        return $this->sendMessage($targetPhone, $message, $nis, 'pengingat_tempo');
    }

    /**
     * Dapatkan Base URL Publik yang sedang aktif
     * Prioritas:
     * 1. Request Host jika diakses dari domain publik / ngrok
     * 2. Pengaturan 'app_public_url' dari database
     * 3. Deteksi otomatis Ngrok tunnel lokal aktif di port 4040
     * 4. Fallback ke config('app.url')
     */
    public static function getPublicBaseUrl(): string
    {
        // 1. Cek jika request saat ini sudah menggunakan domain publik / ngrok
        if (request()->hasHeader('host')) {
            $host = request()->getHost();
            if ($host && !in_array($host, ['localhost', '127.0.0.1', '::1']) && !str_starts_with($host, '192.168.') && !str_starts_with($host, '10.')) {
                $scheme = (request()->isSecure() || request()->header('x-forwarded-proto') === 'https' || str_contains($host, 'ngrok')) ? 'https' : request()->getScheme();
                return $scheme . '://' . request()->getHttpHost();
            }
        }

        // 2. Cek apakah ada konfigurasi manual di database pengaturan
        try {
            $customPublicUrl = Pengaturan::get('app_public_url');
            if (!empty($customPublicUrl)) {
                return rtrim($customPublicUrl, '/');
            }
        } catch (\Throwable $e) {
            // Lewati jika model/tabel belum siap
        }

        // 3. Cek apakah tunnel Ngrok sedang aktif di port lokal 4040
        try {
            $ngrokUrl = \Illuminate\Support\Facades\Cache::remember('ngrok_tunnel_url', 10, function () {
                $ch = curl_init('http://127.0.0.1:4040/api/tunnels');
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_TIMEOUT_MS, 500);
                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT_MS, 300);
                $res = curl_exec($ch);
                curl_close($ch);

                if ($res) {
                    $data = json_decode($res, true);
                    if (!empty($data['tunnels'])) {
                        // Prioritaskan tunnel dengan protokol HTTPS
                        foreach ($data['tunnels'] as $tunnel) {
                            if (!empty($tunnel['public_url']) && str_starts_with($tunnel['public_url'], 'https://')) {
                                return $tunnel['public_url'];
                            }
                        }
                        return $data['tunnels'][0]['public_url'] ?? null;
                    }
                }
                return null;
            });

            if ($ngrokUrl) {
                return rtrim($ngrokUrl, '/');
            }
        } catch (\Throwable $e) {
            // Lewati jika service ngrok tidak aktif
        }

        // 4. Fallback ke config app.url
        return rtrim(config('app.url'), '/');
    }
}

