<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check()) {
            if (Auth::user()->isAdmin()) {
                return redirect()->route('admin.dashboard');
            } elseif (Auth::user()->isBendahara()) {
                return redirect()->route('bendahara.dashboard');
            }
            return redirect()->route('wali.dashboard');
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $user = Auth::user();

            // Cek status keaktifan akun
            if (!$user->isActive()) {
                ActivityLog::record('LOGIN_BLOCKED', "Percobaan login ditolak: akun {$user->username} berstatus nonaktif.", $user);
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Akun Anda dinonaktifkan sementara oleh Administrator Pesantren.'
                    ], 403);
                }

                return back()->withErrors([
                    'username' => 'Akun Anda dinonaktifkan sementara oleh Administrator Pesantren.',
                ])->onlyInput('username');
            }

            $request->session()->regenerate();

            // Update waktu login terakhir
            $user->update(['last_login_at' => now()]);

            // Catat log audit
            ActivityLog::record('LOGIN', "Pengguna {$user->nama_lengkap} ({$user->role}) berhasil masuk ke sistem.", $user);

            $targetUrl = match ($user->role) {
                'admin' => route('admin.dashboard'),
                'bendahara' => route('bendahara.dashboard'),
                default => route('wali.dashboard'),
            };

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'redirect' => $targetUrl,
                    'name' => $user->nama_lengkap,
                    'role' => ucfirst($user->role),
                    'message' => 'Autentikasi berhasil! Membuka portal...'
                ]);
            }

            if ($user->isAdmin()) {
                return redirect()->intended(route('admin.dashboard'))->with('success', 'Selamat datang Administrator, ' . $user->nama_lengkap);
            } elseif ($user->isBendahara()) {
                return redirect()->intended(route('bendahara.dashboard'))->with('success', 'Selamat datang Bendahara, ' . $user->nama_lengkap);
            }

            return redirect()->intended(route('wali.dashboard'))->with('success', 'Selamat datang, ' . $user->nama_lengkap);
        }

        // Catat kegagalan login
        ActivityLog::record('LOGIN_FAILED', "Gagal login: Kredensial tidak valid untuk username '{$request->username}'.");

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => false,
                'message' => 'Username atau password yang dimasukkan salah.'
            ], 422);
        }

        return back()->withErrors([
            'username' => 'Username atau password yang dimasukkan salah.',
        ])->onlyInput('username');
    }

    public function logout(Request $request)
    {
        $user = Auth::user();
        if ($user) {
            ActivityLog::record('LOGOUT', "Pengguna {$user->nama_lengkap} ({$user->role}) keluar dari sesi sistem.", $user);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('info', 'Anda telah berhasil keluar.');
    }
}

