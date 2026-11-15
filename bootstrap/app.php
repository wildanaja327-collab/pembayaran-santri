<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Percayai semua reverse proxy (Ngrok, Cloudflare, dsb) agar deteksi HTTPS dan IP akurat
        $middleware->trustProxies(at: '*');

        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
        ]);
        $middleware->validateCsrfTokens(except: [
            'api/midtrans/webhook',
            'midtrans/webhook',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->respond(function (\Symfony\Component\HttpFoundation\Response $response) {
            if ($response->getStatusCode() === 419) {
                if (request()->expectsJson() || request()->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Sesi keamanan halaman telah kedaluwarsa. Silakan muat ulang halaman dan coba kembali.',
                    ], 419);
                }
                return redirect()->route('login')->with('info', 'Sesi form Anda telah kedaluwarsa. Silakan masukkan kembali akun Anda.');
            }
            if ($response->getStatusCode() === 429) {
                if (request()->expectsJson() || request()->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Terlalu banyak percobaan dalam waktu singkat. Silakan tunggu 1 menit sebelum mencoba kembali.',
                    ], 429);
                }
                return back()->withErrors([
                    'username' => 'Terlalu banyak percobaan masuk. Silakan tunggu 1 menit sebelum mencoba kembali.',
                ]);
            }
            return $response;
        });
    })->create();
