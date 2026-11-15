<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Pembayaran;
use App\Models\Pengaturan;
use App\Models\Program;
use App\Models\Santri;
use App\Models\Tagihan;
use App\Models\TahunAjaran;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function dashboard()
    {
        // 1. Data Santri
        $totalSantri = Santri::count();
        $santriSmp = Santri::where('tingkat', 'SMP')->count();
        $santriSma = Santri::where('tingkat', 'SMA')->count();

        // 2. Data Pengguna
        $totalUsers = User::count();
        $adminCount = User::where('role', 'admin')->count();
        $bendaharaCount = User::where('role', 'bendahara')->count();
        $waliCount = User::where('role', 'wali_santri')->count();
        $activeUserCount = User::where('is_active', 1)->count();

        // 3. Data Program
        $totalProgram = Program::count();

        // 4. Data Tahun Ajaran Aktif
        $tahunAjaranAktif = TahunAjaran::getAktif();

        // 5. Monitoring Keuangan Global
        $totalPemasukan = Pembayaran::where('status_transaksi', 'settlement')->sum('jumlah_bayar');
        $totalTunggakan = Tagihan::whereIn('status_tagihan', ['belum_bayar', 'dicicil'])->sum('sisa_tagihan');
        $jumlahTagihanBelumLunas = Tagihan::whereIn('status_tagihan', ['belum_bayar', 'dicicil'])->count();

        // 6. Log Aktivitas Terbaru (5 entri)
        $recentLogs = ActivityLog::latest('created_at')->limit(6)->get();

        // 7. Info Arsitektur & Lingkungan
        $systemInfo = [
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'server_os' => PHP_OS_FAMILY,
            'midtrans_mode' => Pengaturan::get('midtrans_mode', config('services.midtrans.is_production') ? 'production' : 'sandbox'),
            'db_name' => config('database.connections.mysql.database'),
        ];

        return view('admin.dashboard', compact(
            'totalSantri',
            'santriSmp',
            'santriSma',
            'totalUsers',
            'adminCount',
            'bendaharaCount',
            'waliCount',
            'activeUserCount',
            'totalProgram',
            'tahunAjaranAktif',
            'totalPemasukan',
            'totalTunggakan',
            'jumlahTagihanBelumLunas',
            'recentLogs',
            'systemInfo'
        ));
    }

    public function activityLogs(Request $request)
    {
        $query = ActivityLog::query();

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('action')) {
            $query->where('action', 'like', "%{$request->action}%");
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                    ->orWhere('username', 'like', "%{$search}%")
                    ->orWhere('ip_address', 'like', "%{$search}%");
            });
        }

        $logs = $query->orderBy('created_at', 'desc')->paginate(20)->withQueryString();

        return view('admin.activity_logs.index', compact('logs'));
    }

    public function pengaturan()
    {
        $settings = Pengaturan::all()->pluck('setting_value', 'setting_key');
        return view('admin.pengaturan.index', compact('settings'));
    }

    public function updatePengaturan(Request $request)
    {
        $request->validate([
            'pesantren_nama' => 'required|string|max:150',
            'pesantren_alamat' => 'required|string|max:255',
            'pesantren_telepon' => 'required|string|max:50',
            'pesantren_email' => 'required|email|max:100',
            'pimpinan_nama' => 'required|string|max:100',
            'bendahara_nama' => 'required|string|max:100',
            'kwitansi_footer' => 'nullable|string|max:255',
        ]);

        $fields = [
            'pesantren_nama' => 'Nama Pesantren',
            'pesantren_alamat' => 'Alamat Lengkap Pesantren',
            'pesantren_telepon' => 'Kontak / WhatsApp',
            'pesantren_email' => 'Email Resmi Pesantren',
            'pimpinan_nama' => 'Pimpinan / Pengasuh',
            'bendahara_nama' => 'Bendahara Resmi',
            'kwitansi_footer' => 'Catatan Kaki Kwitansi',
        ];

        foreach ($fields as $key => $label) {
            Pengaturan::set($key, $request->input($key), $label);
        }

        ActivityLog::record('SETTING_UPDATE', "Administrator memperbarui konfigurasi data identitas pesantren & kuitansi.");

        return redirect()->route('admin.pengaturan')->with('success', 'Konfigurasi identitas sistem berhasil diperbarui.');
    }

    /**
     * Halaman Khusus Manajemen Payment Gateway Midtrans (Administrator Only)
     */
    public function midtransConfig()
    {
        $midtransSettings = [
            'mode' => Pengaturan::get('midtrans_mode', config('services.midtrans.is_production') ? 'production' : 'sandbox'),
            'server_key' => Pengaturan::get('midtrans_server_key', config('services.midtrans.server_key', '')),
            'client_key' => Pengaturan::get('midtrans_client_key', config('services.midtrans.client_key', '')),
            'merchant_id' => Pengaturan::get('midtrans_merchant_id', config('services.midtrans.merchant_id', '')),
            'biaya_admin' => (int) Pengaturan::get('midtrans_biaya_admin', 2500),
            'is_sanitized' => (bool) Pengaturan::get('midtrans_is_sanitized', config('services.midtrans.is_sanitized', true)),
            'is_3ds' => (bool) Pengaturan::get('midtrans_is_3ds', config('services.midtrans.is_3ds', true)),
        ];

        $webhookInfo = [
            'url' => url('/midtrans/webhook'),
            'alt_url' => url('/api/midtrans/webhook'),
            'is_https' => request()->isSecure(),
            'status' => 'Aktif (Bypass CSRF)',
        ];

        $stats = [
            'total_gateway' => Pembayaran::where('metode_bayar', 'gateway')->count(),
            'settlement_count' => Pembayaran::where('metode_bayar', 'gateway')->where('status_transaksi', 'settlement')->count(),
            'pending_count' => Pembayaran::where('metode_bayar', 'gateway')->where('status_transaksi', 'pending')->count(),
            'total_nominal' => Pembayaran::where('metode_bayar', 'gateway')->where('status_transaksi', 'settlement')->sum('jumlah_bayar'),
        ];

        $recentGatewayTransactions = Pembayaran::where('metode_bayar', 'gateway')
            ->with(['tagihan.santri', 'tagihan.jenisTagihan'])
            ->latest('id_pembayaran')
            ->limit(10)
            ->get();

        $recentWebhookLogs = ActivityLog::where('action', 'like', '%MIDTRANS%')
            ->latest('created_at')
            ->limit(10)
            ->get();

        return view('admin.midtrans.index', compact(
            'midtransSettings',
            'webhookInfo',
            'stats',
            'recentGatewayTransactions',
            'recentWebhookLogs'
        ));
    }

    /**
     * Memperbarui Konfigurasi Kredensial & Lingkungan Midtrans
     */
    public function updateMidtransConfig(Request $request)
    {
        $request->validate([
            'midtrans_mode' => 'required|in:sandbox,production',
            'midtrans_server_key' => 'required|string|max:120',
            'midtrans_client_key' => 'required|string|max:120',
            'midtrans_merchant_id' => 'nullable|string|max:50',
            'midtrans_biaya_admin' => 'required|numeric|min:0',
        ], [
            'midtrans_mode.required' => 'Mode environment Midtrans wajib dipilih.',
            'midtrans_server_key.required' => 'Server Key wajib diisi.',
            'midtrans_client_key.required' => 'Client Key wajib diisi.',
            'midtrans_biaya_admin.required' => 'Biaya layanan gateway wajib diisi.',
        ]);

        $mode = $request->input('midtrans_mode');
        $serverKey = trim($request->input('midtrans_server_key'));
        $clientKey = trim($request->input('midtrans_client_key'));
        $merchantId = trim($request->input('midtrans_merchant_id', ''));
        $biayaAdmin = (int) $request->input('midtrans_biaya_admin', 2500);
        $isSanitized = $request->has('midtrans_is_sanitized') ? '1' : '0';
        $is3ds = $request->has('midtrans_is_3ds') ? '1' : '0';

        // 1. Simpan ke database (Single Source of Truth)
        Pengaturan::set('midtrans_mode', $mode, 'Mode Gateway Midtrans (sandbox/production)', 'gateway');
        Pengaturan::set('midtrans_server_key', $serverKey, 'Midtrans Server Key', 'gateway');
        Pengaturan::set('midtrans_client_key', $clientKey, 'Midtrans Client Key', 'gateway');
        Pengaturan::set('midtrans_merchant_id', $merchantId, 'Midtrans Merchant ID', 'gateway');
        Pengaturan::set('midtrans_biaya_admin', (string) $biayaAdmin, 'Biaya Layanan Payment Gateway', 'gateway');
        Pengaturan::set('midtrans_is_sanitized', $isSanitized, 'Midtrans Sanitized Filter', 'gateway');
        Pengaturan::set('midtrans_is_3ds', $is3ds, 'Midtrans 3D Secure Protection', 'gateway');

        // 2. Sinkronkan ke file .env agar tidak perlu bongkar kodingan manual
        $this->updateEnvironmentFile([
            'MIDTRANS_SERVER_KEY' => $serverKey,
            'MIDTRANS_CLIENT_KEY' => $clientKey,
            'MIDTRANS_IS_PRODUCTION' => ($mode === 'production'),
            'MIDTRANS_MERCHANT_ID' => $merchantId,
            'MIDTRANS_IS_SANITIZED' => ($isSanitized === '1'),
            'MIDTRANS_IS_3DS' => ($is3ds === '1'),
        ]);

        // 3. Update runtime configuration Laravel
        config([
            'services.midtrans.server_key' => $serverKey,
            'services.midtrans.client_key' => $clientKey,
            'services.midtrans.is_production' => ($mode === 'production'),
            'services.midtrans.is_sanitized' => ($isSanitized === '1'),
            'services.midtrans.is_3ds' => ($is3ds === '1'),
        ]);

        // 4. Catat ke Audit Trail
        $maskedServerKey = strlen($serverKey) > 12 
            ? substr($serverKey, 0, 7) . '...' . substr($serverKey, -4) 
            : '***';
        ActivityLog::record(
            'MIDTRANS_CONFIG_UPDATE',
            "Administrator memperbarui konfigurasi Midtrans: Mode " . strtoupper($mode) . ", Server Key: {$maskedServerKey}, Biaya Admin: Rp " . number_format($biayaAdmin, 0, ',', '.')
        );

        return redirect()->route('admin.midtrans.index')
            ->with('success', 'Konfigurasi Payment Gateway Midtrans berhasil diperbarui dan disinkronkan!');
    }

    /**
     * Uji Koneksi API Midtrans (Test Ping Kredensial Server Key)
     */
    public function testMidtransConnection(Request $request)
    {
        $mode = $request->input('mode', Pengaturan::get('midtrans_mode', 'sandbox'));
        $serverKey = trim($request->input('server_key', Pengaturan::get('midtrans_server_key', config('services.midtrans.server_key'))));

        if (empty($serverKey)) {
            return response()->json([
                'success' => false,
                'status' => 'error',
                'message' => 'Server Key belum diisi. Masukkan Server Key terlebih dahulu.',
            ], 422);
        }

        $baseUrl = ($mode === 'production')
            ? 'https://api.midtrans.com/v2/'
            : 'https://api.sandbox.midtrans.com/v2/';

        $startTime = microtime(true);

        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $baseUrl . 'dummy-ping-order-check/status');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_USERPWD, $serverKey . ':');
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Accept: application/json',
                'Content-Type: application/json',
            ]);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            curl_close($ch);

            $latency = round((microtime(true) - $startTime) * 1000);

            if ($curlError) {
                return response()->json([
                    'success' => false,
                    'status' => 'network_error',
                    'latency_ms' => $latency,
                    'message' => 'Gagal terhubung ke host Midtrans (' . $curlError . '). Periksa koneksi internet Anda.',
                ]);
            }

            $json = json_decode($response, true);

            // Midtrans memberikan 401 jika Server Key salah atau tidak cocok dengan environment
            if ($httpCode === 401 || (isset($json['error']) && strtolower($json['error']) === 'unauthorized')) {
                return response()->json([
                    'success' => false,
                    'status' => 'unauthorized',
                    'http_code' => $httpCode,
                    'latency_ms' => $latency,
                    'mode' => $mode,
                    'message' => "Kredensial Server Key tidak valid untuk environment " . strtoupper($mode) . " (HTTP 401 Unauthorized). Pastikan Server Key cocok dengan akun Midtrans Anda.",
                ]);
            }

            // Jika status 404 (transaksi tidak ada tapi auth lolos) atau 200/201, auth server key VALID!
            if ($httpCode === 404 || $httpCode === 200 || (isset($json['status_code']) && in_array($json['status_code'], ['404', '200', '201']))) {
                return response()->json([
                    'success' => true,
                    'status' => 'connected',
                    'http_code' => $httpCode,
                    'latency_ms' => $latency,
                    'mode' => $mode,
                    'message' => "Koneksi ke Midtrans API (" . strtoupper($mode) . ") BERHASIL! Server Key valid dan terautentikasi (Latency: {$latency} ms).",
                ]);
            }

            return response()->json([
                'success' => true,
                'status' => 'connected',
                'http_code' => $httpCode,
                'latency_ms' => $latency,
                'mode' => $mode,
                'message' => "Terhubung ke Midtrans API (" . strtoupper($mode) . ") dengan respon HTTP {$httpCode} ({$latency} ms).",
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'status' => 'error',
                'message' => 'Kesalahan internal: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Simulasi Ping Lokal Endpoint Webhook
     */
    public function simulateWebhook(Request $request)
    {
        $startTime = microtime(true);

        try {
            $payload = [
                'order_id' => 'TEST-PING-' . time(),
                'transaction_status' => 'pending',
                'payment_type' => 'bank_transfer',
                'status_code' => '201',
                'gross_amount' => '50000.00',
            ];

            // Gunakan internal request dispatcher agar tidak deadlock pada PHP built-in web server (single-threaded)
            $subRequest = Request::create(
                '/midtrans/webhook',
                'POST',
                [],
                [],
                [],
                ['CONTENT_TYPE' => 'application/json', 'HTTP_ACCEPT' => 'application/json'],
                json_encode($payload)
            );

            $controller = app(\App\Http\Controllers\WebhookController::class);
            $response = $controller->handle($subRequest);

            $latency = round((microtime(true) - $startTime) * 1000);
            $statusCode = $response->getStatusCode();

            return response()->json([
                'success' => ($statusCode >= 200 && $statusCode < 400),
                'http_code' => $statusCode,
                'latency_ms' => $latency,
                'url' => url('/midtrans/webhook'),
                'message' => "Endpoint Webhook lokal merespon HTTP {$statusCode} dalam {$latency} ms. Handler webhook aktif dan siap menerima payload notifikasi Midtrans!",
                'response' => $response->getData(true),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error eksekusi handler webhook: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Halaman Manajemen WhatsApp Gateway Fonnte (Administrator Only)
     */
    public function whatsappConfig(\App\Services\WhatsAppService $waService)
    {
        $deviceInfo = $waService->checkDeviceStatus();

        $token = Pengaturan::get('fonnte_token', config('services.fonnte.token', ''));
        $waEnabled = (bool) Pengaturan::get('wa_notif_enabled', config('services.fonnte.enabled', true));
        $senderNumber = Pengaturan::get('wa_sender_number', config('services.fonnte.sender', ''));

        $stats = [
            'total_notif' => \App\Models\NotifikasiWa::count(),
            'terkirim' => \App\Models\NotifikasiWa::where('status_kirim', 'terkirim')->count(),
            'gagal' => \App\Models\NotifikasiWa::where('status_kirim', 'gagal')->count(),
            'pending' => \App\Models\NotifikasiWa::where('status_kirim', 'pending')->count(),
        ];

        $recentLogs = \App\Models\NotifikasiWa::with('santri')
            ->orderBy('id_notifikasi', 'desc')
            ->limit(15)
            ->get();

        return view('admin.whatsapp.index', compact(
            'deviceInfo',
            'token',
            'waEnabled',
            'senderNumber',
            'stats',
            'recentLogs'
        ));
    }

    /**
     * Memperbarui Token & Pengaturan WhatsApp Gateway
     */
    public function updateWhatsappConfig(Request $request)
    {
        $request->validate([
            'fonnte_token' => 'required|string|max:100',
        ], [
            'fonnte_token.required' => 'Token API Fonnte wajib diisi.',
        ]);

        $token = trim($request->input('fonnte_token'));
        $enabled = $request->has('wa_notif_enabled') ? '1' : '0';

        Pengaturan::set('fonnte_token', $token, 'Token API Fonnte WhatsApp Gateway', 'whatsapp');
        Pengaturan::set('wa_notif_enabled', $enabled, 'Status Notifikasi Otomatis WhatsApp', 'whatsapp');

        // Sinkronkan ke .env
        $this->updateEnvironmentFile([
            'FONNTE_TOKEN' => $token,
            'WA_NOTIF_ENABLED' => ($enabled === '1'),
        ]);

        ActivityLog::record(
            'WA_CONFIG_UPDATE',
            "Administrator memperbarui konfigurasi WhatsApp Gateway (Status: " . ($enabled === '1' ? 'AKTIF' : 'NONAKTIF') . ")"
        );

        return redirect()->route('admin.whatsapp.index')
            ->with('success', 'Konfigurasi WhatsApp Gateway Fonnte berhasil diperbarui.');
    }

    /**
     * Uji Kirim Pesan WhatsApp Live dari Form Admin
     */
    public function testWhatsappSend(Request $request, \App\Services\WhatsAppService $waService)
    {
        $request->validate([
            'target_phone' => 'required|string|min:9|max:20',
            'test_message' => 'required|string|max:1000',
        ], [
            'target_phone.required' => 'Nomor HP tujuan wajib diisi.',
            'test_message.required' => 'Isi pesan uji coba wajib diisi.',
        ]);

        $target = trim($request->input('target_phone'));
        $pesan = trim($request->input('test_message'));

        $res = $waService->sendMessage($target, $pesan, null, 'pengingat_tempo');

        return response()->json($res);
    }

    /**
     * Cek Status Perangkat Fonnte (AJAX Refresh)
     */
    public function checkWhatsappDevice(\App\Services\WhatsAppService $waService)
    {
        $status = $waService->checkDeviceStatus();
        return response()->json($status);
    }

    /**
     * Helper untuk memperbarui file .env secara aman
     */
    protected function updateEnvironmentFile(array $data)
    {
        $envPath = base_path('.env');
        if (!file_exists($envPath) || !is_writable($envPath)) {
            return;
        }

        $envContent = file_get_contents($envPath);

        foreach ($data as $key => $value) {
            if (is_bool($value)) {
                $value = $value ? 'true' : 'false';
            }

            if (is_string($value) && str_contains($value, ' ')) {
                $value = '"' . $value . '"';
            }

            $pattern = "/^{$key}=.*/m";
            if (preg_match($pattern, $envContent)) {
                $envContent = preg_replace($pattern, "{$key}={$value}", $envContent);
            } else {
                $envContent .= "\n{$key}={$value}";
            }
        }

        file_put_contents($envPath, $envContent);
    }
}
