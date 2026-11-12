<?php

namespace App\Providers;

use App\Models\Pengaturan;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Konfigurasi Zona Waktu Indonesia Barat (WIB) & Bahasa Carbon
        date_default_timezone_set(config('app.timezone', 'Asia/Jakarta'));
        \Illuminate\Support\Carbon::setLocale('id');

        // Otomatis paksa skema HTTPS jika request berasal dari Ngrok atau proxy HTTPS
        if (request()->header('x-forwarded-proto') === 'https' || (request()->hasHeader('host') && str_contains(request()->getHost(), 'ngrok'))) {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }

        // Sinkronisasi Konfigurasi Gateway Midtrans Dinamis dari Database
        try {
            if (Schema::hasTable('pengaturan')) {
                $mode = Pengaturan::get('midtrans_mode');
                $serverKey = Pengaturan::get('midtrans_server_key');
                $clientKey = Pengaturan::get('midtrans_client_key');
                $merchantId = Pengaturan::get('midtrans_merchant_id');

                if ($mode !== null) {
                    config(['services.midtrans.is_production' => ($mode === 'production')]);
                }
                if (!empty($serverKey)) {
                    config(['services.midtrans.server_key' => $serverKey]);
                }
                if (!empty($clientKey)) {
                    config(['services.midtrans.client_key' => $clientKey]);
                }
                if (!empty($merchantId)) {
                    config(['services.midtrans.merchant_id' => $merchantId]);
                }
            }
        } catch (\Throwable $e) {
            // Abaikan jika DB belum siap atau sedang migration
        }
    }
}
