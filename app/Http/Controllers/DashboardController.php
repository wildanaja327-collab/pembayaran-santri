<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\JenisTagihan;
use App\Models\Pembayaran;
use App\Models\Program;
use App\Models\Santri;
use App\Models\Tagihan;
use App\Models\TahunAjaran;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Dashboard Eksekutif Bendahara Keuangan Pesantren
     */
    public function bendahara()
    {
        // 1. Metrik Pokok Santri
        $totalSantri = Santri::count();
        $totalSantriAktif = Santri::where('status_aktif', 'aktif')->count();
        $santriSmp = Santri::where('tingkat', 'SMP')->count();
        $santriSma = Santri::where('tingkat', 'SMA')->count();

        // 2. Metrik Keuangan Akumulasi & Periode
        $totalPemasukan = Pembayaran::where('status_transaksi', 'settlement')->sum('jumlah_bayar');
        $pemasukanTahunIni = Pembayaran::where('status_transaksi', 'settlement')
            ->whereYear('tgl_bayar', now()->year)
            ->sum('jumlah_bayar');
        $pemasukanBulanIni = Pembayaran::where('status_transaksi', 'settlement')
            ->whereMonth('tgl_bayar', now()->month)
            ->whereYear('tgl_bayar', now()->year)
            ->sum('jumlah_bayar');

        $totalTunggakan = Tagihan::whereIn('status_tagihan', ['belum_bayar', 'dicicil'])->sum('sisa_tagihan');
        $tagihanLunas = Tagihan::where('status_tagihan', 'lunas')->count();
        $tagihanBelumLunas = Tagihan::whereIn('status_tagihan', ['belum_bayar', 'dicicil'])->count();
        $totalTagihanCount = $tagihanLunas + $tagihanBelumLunas;
        $rasioKepatuhan = $totalTagihanCount > 0 ? round(($tagihanLunas / $totalTagihanCount) * 100) : 0;

        // 3. Status Gateway Midtrans & Rekonsiliasi
        $transaksiPending = Pembayaran::where('status_transaksi', 'pending')->count();
        $transaksiPendingOverdue = Pembayaran::where('status_transaksi', 'pending')
            ->where('tgl_bayar', '<', now()->subHours(24))
            ->count();

        // 4. Tahun Ajaran Aktif
        $tahunAjaranAktif = TahunAjaran::getAktif();

        // 5. Data Tren Pemasukan 6 Bulan Terakhir untuk Chart.js (Grafik Batang & Garis)
        $chartLabels = [];
        $chartValues = [];
        $namaBulanIndo = [
            1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr',
            5 => 'Mei', 6 => 'Jun', 7 => 'Jul', 8 => 'Agt',
            9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des'
        ];

        for ($i = 5; $i >= 0; $i--) {
            $dt = now()->copy()->subMonths($i);
            $monthNum = (int)$dt->format('n');
            $yearNum = (int)$dt->format('Y');

            $label = $namaBulanIndo[$monthNum] . ' ' . $yearNum;
            $totalBln = Pembayaran::where('status_transaksi', 'settlement')
                ->whereMonth('tgl_bayar', $monthNum)
                ->whereYear('tgl_bayar', $yearNum)
                ->sum('jumlah_bayar');

            $chartLabels[] = $label;
            $chartValues[] = (float)$totalBln;
        }

        // 6. Komposisi Pemasukan per Jenis Tagihan untuk Doughnut Chart
        $komposisiJenis = DB::table('pembayaran')
            ->join('tagihan', 'pembayaran.id_tagihan', '=', 'tagihan.id_tagihan')
            ->join('jenis_tagihan', 'tagihan.id_jenis_tagihan', '=', 'jenis_tagihan.id_jenis_tagihan')
            ->where('pembayaran.status_transaksi', 'settlement')
            ->select('jenis_tagihan.nama_tagihan', DB::raw('SUM(pembayaran.jumlah_bayar) as total'))
            ->groupBy('jenis_tagihan.id_jenis_tagihan', 'jenis_tagihan.nama_tagihan')
            ->get();

        $donutLabels = [];
        $donutValues = [];
        foreach ($komposisiJenis as $kj) {
            $donutLabels[] = $kj->nama_tagihan;
            $donutValues[] = (float)$kj->total;
        }

        // 7. Kolektabilitas SPP Bulan Berjalan (Syahriah)
        $sppJenis = JenisTagihan::where('nama_tagihan', 'like', '%SPP%')
            ->orWhere('tipe', 'bulanan')
            ->first();

        $sppBulanIniTagihan = 0;
        $sppBulanIniLunas = 0;
        if ($sppJenis) {
            $sppBulanIniTagihan = Tagihan::where('id_jenis_tagihan', $sppJenis->id_jenis_tagihan)
                ->where('bulan', now()->month)
                ->count();
            $sppBulanIniLunas = Tagihan::where('id_jenis_tagihan', $sppJenis->id_jenis_tagihan)
                ->where('bulan', now()->month)
                ->where('status_tagihan', 'lunas')
                ->count();
        }
        $persenKolektabilitasSpp = $sppBulanIniTagihan > 0
            ? round(($sppBulanIniLunas / $sppBulanIniTagihan) * 100)
            : 0;

        // 8. Alert Tagihan Jatuh Tempo (Mendesak & Segera)
        $todayStr = now()->toDateString();
        $threeDaysStr = now()->addDays(3)->toDateString();
        $sevenDaysStr = now()->addDays(7)->toDateString();

        // Mendesak: Lewat jatuh tempo ATAU jatuh tempo dlm 3 hari ke depan
        $tagihanMendesak = Tagihan::with(['santri.wali', 'jenisTagihan'])
            ->whereIn('status_tagihan', ['belum_bayar', 'dicicil'])
            ->whereNotNull('jatuh_tempo')
            ->where('jatuh_tempo', '<=', $threeDaysStr)
            ->orderBy('jatuh_tempo', 'asc')
            ->limit(5)
            ->get();

        // Segera: Jatuh tempo 4 s/d 7 hari ke depan
        $tagihanSegera = Tagihan::with(['santri.wali', 'jenisTagihan'])
            ->whereIn('status_tagihan', ['belum_bayar', 'dicicil'])
            ->whereNotNull('jatuh_tempo')
            ->where('jatuh_tempo', '>', $threeDaysStr)
            ->where('jatuh_tempo', '<=', $sevenDaysStr)
            ->orderBy('jatuh_tempo', 'asc')
            ->limit(5)
            ->get();

        $totalJatuhTempoSegera = Tagihan::whereIn('status_tagihan', ['belum_bayar', 'dicicil'])
            ->whereNotNull('jatuh_tempo')
            ->where('jatuh_tempo', '<=', $sevenDaysStr)
            ->count();

        // 9. Kinerja Keuangan per Program Unggulan
        $programStats = Program::with(['santri' => function ($q) {
            $q->where('status_aktif', 'aktif');
        }])->get()->map(function ($prog) {
            $nisList = $prog->santri->pluck('nis');
            $jmlSantri = $nisList->count();
            $totalTagihan = Tagihan::whereIn('nis', $nisList)->sum('nominal_tagihan');
            $totalTerbayar = Tagihan::whereIn('nis', $nisList)->sum('total_terbayar');
            $totalSisa = Tagihan::whereIn('nis', $nisList)->sum('sisa_tagihan');
            $persen = $totalTagihan > 0 ? round(($totalTerbayar / $totalTagihan) * 100) : 0;

            return (object)[
                'id_program' => $prog->id_program,
                'nama_program' => $prog->nama_program,
                'jml_santri' => $jmlSantri,
                'total_tagihan' => $totalTagihan,
                'total_terbayar' => $totalTerbayar,
                'total_sisa' => $totalSisa,
                'persen_lunas' => $persen,
            ];
        });

        // 10. Transaksi Terkini Masuk
        $recentPayments = Pembayaran::with(['tagihan.santri', 'tagihan.jenisTagihan'])
            ->latest('id_pembayaran')
            ->limit(6)
            ->get();

        return view('bendahara.dashboard', compact(
            'totalSantri',
            'totalSantriAktif',
            'santriSmp',
            'santriSma',
            'totalPemasukan',
            'pemasukanTahunIni',
            'pemasukanBulanIni',
            'totalTunggakan',
            'tagihanLunas',
            'tagihanBelumLunas',
            'rasioKepatuhan',
            'transaksiPending',
            'transaksiPendingOverdue',
            'tahunAjaranAktif',
            'chartLabels',
            'chartValues',
            'donutLabels',
            'donutValues',
            'sppBulanIniTagihan',
            'sppBulanIniLunas',
            'persenKolektabilitasSpp',
            'tagihanMendesak',
            'tagihanSegera',
            'totalJatuhTempoSegera',
            'programStats',
            'recentPayments'
        ));
    }

    public function wali()
    {
        $user = Auth::user();
        $santriList = $user->santri()->with('program')->get();
        $nisList = $santriList->pluck('nis');

        $tagihanBelumLunas = Tagihan::whereIn('nis', $nisList)
            ->whereIn('status_tagihan', ['belum_bayar', 'dicicil', 'menunggu_verifikasi'])
            ->with(['jenisTagihan', 'santri'])
            ->orderBy('jatuh_tempo')
            ->get();

        // Riwayat 8 pembayaran terakhir di dashboard (full di /wali/riwayat)
        $riwayatPembayaran = Pembayaran::whereHas('tagihan', function ($q) use ($nisList) {
            $q->whereIn('nis', $nisList);
        })->with(['tagihan.santri', 'tagihan.jenisTagihan', 'kwitansi'])
            ->latest('tgl_bayar')
            ->limit(8)
            ->get();

        $totalTunggakan = $tagihanBelumLunas->sum('sisa_tagihan');

        // Statistik per santri untuk card dashboard
        $santriStats = $santriList->map(function ($s) {
            $tagihan = Tagihan::where('nis', $s->nis)->get();
            $totalTagihan = $tagihan->sum('nominal_tagihan');
            $totalTerbayar = $tagihan->sum('total_terbayar');
            $totalSisa = $tagihan->sum('sisa_tagihan');
            $lunas = $tagihan->where('status_tagihan', 'lunas')->count();
            $total = $tagihan->count();
            
            $kepatuhan = $s->getKepatuhanInfo();
            $persen = $kepatuhan['persen'];

            $tagihanDekat = Tagihan::where('nis', $s->nis)
                ->whereIn('status_tagihan', ['belum_bayar', 'dicicil'])
                ->whereNotNull('jatuh_tempo')
                ->where('jatuh_tempo', '>=', now())
                ->orderBy('jatuh_tempo')
                ->first();

            return (object)[
                'santri' => $s,
                'total_tagihan' => $totalTagihan,
                'total_terbayar' => $totalTerbayar,
                'total_sisa' => $totalSisa,
                'lunas_count' => $lunas,
                'total_count' => $total,
                'persen_lunas' => $persen,
                'kepatuhan' => $kepatuhan,
                'tagihan_terdekat' => $tagihanDekat,
            ];
        });

        // Tagihan mendekati jatuh tempo (7 hari ke depan)
        $tagihanMendesak = Tagihan::whereIn('nis', $nisList)
            ->whereIn('status_tagihan', ['belum_bayar', 'dicicil'])
            ->whereNotNull('jatuh_tempo')
            ->whereBetween('jatuh_tempo', [now(), now()->addDays(7)])
            ->with(['jenisTagihan', 'santri'])
            ->orderBy('jatuh_tempo')
            ->get();

        return view('wali.dashboard', compact(
            'santriList',
            'santriStats',
            'tagihanBelumLunas',
            'riwayatPembayaran',
            'totalTunggakan',
            'tagihanMendesak'
        ));
    }

    /**
     * Detail tagihan & riwayat per santri (satu anak)
     */
    public function waliSantriDetail($nis)
    {
        $user = Auth::user();
        $nisList = $user->santri()->pluck('nis');

        // Validasi kepemilikan: santri harus milik wali ini
        abort_unless($nisList->contains($nis), 403, 'Akses ditolak.');

        $santri = \App\Models\Santri::with('program')->where('nis', $nis)->firstOrFail();

        // Semua tagihan santri ini
        $semuaTagihan = Tagihan::where('nis', $nis)
            ->with('jenisTagihan')
            ->orderByRaw("FIELD(status_tagihan,'belum_bayar','dicicil','menunggu_verifikasi','lunas')")
            ->orderBy('jatuh_tempo')
            ->get();

        $tagihanBelumLunas = $semuaTagihan->whereIn('status_tagihan', ['belum_bayar', 'dicicil', 'menunggu_verifikasi']);
        $tagihanLunas = $semuaTagihan->where('status_tagihan', 'lunas');

        // Statistik
        $totalTagihan = $semuaTagihan->sum('nominal_tagihan');
        $totalTerbayar = $semuaTagihan->sum('total_terbayar');
        $totalSisa = $semuaTagihan->sum('sisa_tagihan');
        $kepatuhan = $santri->getKepatuhanInfo();
        $persen = $kepatuhan['persen'];

        // Riwayat pembayaran santri ini
        $riwayat = Pembayaran::whereHas('tagihan', function ($q) use ($nis) {
            $q->where('nis', $nis);
        })->with(['tagihan.jenisTagihan', 'kwitansi'])
            ->latest('tgl_bayar')
            ->paginate(15);

        return view('wali.santri.show', compact(
            'santri',
            'semuaTagihan',
            'tagihanBelumLunas',
            'tagihanLunas',
            'totalTagihan',
            'totalTerbayar',
            'totalSisa',
            'persen',
            'kepatuhan',
            'riwayat'
        ));
    }

    /**
     * Riwayat pembayaran lengkap wali (semua anak, filter + paginate)
     */
    public function waliRiwayat(Request $request)
    {
        $user = Auth::user();
        $santriList = $user->santri()->with('program')->get();
        $nisList = $santriList->pluck('nis');

        $query = Pembayaran::whereHas('tagihan', function ($q) use ($nisList) {
            $q->whereIn('nis', $nisList);
        })->with(['tagihan.santri', 'tagihan.jenisTagihan', 'kwitansi']);

        // Filter per santri
        if ($request->filled('nis')) {
            abort_unless($nisList->contains($request->nis), 403);
            $query->whereHas('tagihan', fn($q) => $q->where('nis', $request->nis));
        }

        // Filter status
        if ($request->filled('status')) {
            $query->where('status_transaksi', $request->status);
        }

        // Pencarian Order ID / Komponen Tagihan / Santri
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('order_id', 'like', "%{$search}%")
                    ->orWhereHas('tagihan.jenisTagihan', fn($jq) => $jq->where('nama_tagihan', 'like', "%{$search}%"))
                    ->orWhereHas('tagihan.santri', fn($sq) => $sq->where('nama_santri', 'like', "%{$search}%"));
            });
        }

        $riwayat = $query->latest('tgl_bayar')->paginate(20)->withQueryString();

        return view('wali.riwayat', compact('riwayat', 'santriList'));
    }

    /**
     * Profil akun wali santri
     */
    public function waliProfil()
    {
        $user = Auth::user();
        $santriList = $user->santri()->with('program')->get();
        return view('wali.profil', compact('user', 'santriList'));
    }

    /**
     * Update profil wali (ganti password)
     */
    public function waliProfilUpdate(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'password_lama' => 'required',
            'password_baru' => 'required|min:6|confirmed',
        ], [
            'password_lama.required' => 'Password lama wajib diisi.',
            'password_baru.required' => 'Password baru wajib diisi.',
            'password_baru.min' => 'Password baru minimal 6 karakter.',
            'password_baru.confirmed' => 'Konfirmasi password baru tidak cocok.',
        ]);

        if (!\Illuminate\Support\Facades\Hash::check($request->password_lama, $user->password)) {
            return back()->withErrors(['password_lama' => 'Password lama yang Anda masukkan salah.'])->withInput();
        }

        $user->password = \Illuminate\Support\Facades\Hash::make($request->password_baru);
        $user->save();

        return back()->with('success', 'Password berhasil diperbarui. Silakan login ulang dengan password baru Anda.');
    }

    /**
     * Halaman panduan pembayaran lengkap bergambar untuk wali santri
     */
    public function waliPanduan()
    {
        return view('wali.panduan');
    }

    /**
     * Pengajuan Penundaan / Konfirmasi Kendala Pembayaran oleh Wali Santri
     */
    public function ajukanPenundaanWali(Request $request, $id)
    {
        $user = Auth::user();
        $nisList = $user->santri()->pluck('nis');

        $tagihan = Tagihan::with(['santri', 'jenisTagihan'])->findOrFail($id);

        // Pastikan tagihan milik anak dari wali santri yang sedang login
        abort_unless($nisList->contains($tagihan->nis), 403, 'Akses ditolak.');

        if ($tagihan->status_tagihan === 'lunas') {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Tagihan ini sudah lunas.'], 422);
            }
            return back()->with('error', 'Tagihan ini sudah lunas.');
        }

        $request->validate([
            'kategori_tunggakan' => 'required|string|max:50',
            'alasan_tunggakan' => 'required|string|max:255',
            'tgl_janji_bayar' => 'nullable|date',
        ], [
            'kategori_tunggakan.required' => 'Silakan pilih kategori alasan penundaan.',
            'alasan_tunggakan.required' => 'Mohon tuliskan keterangan singkat alasan penundaan.',
            'alasan_tunggakan.max' => 'Keterangan alasan maksimal 255 karakter.',
        ]);

        $tagihan->update([
            'kategori_tunggakan' => $request->kategori_tunggakan,
            'alasan_tunggakan' => $request->alasan_tunggakan,
            'tgl_janji_bayar' => $request->tgl_janji_bayar ?: null,
            'updated_alasan_at' => now(),
        ]);

        $kategoriLabel = $tagihan->kategori_info['label'] ?? $request->kategori_tunggakan;
        ActivityLog::record(
            'WALI_PENUNDAAN',
            "Wali santri ({$user->nama_lengkap}) mengajukan penundaan bayar tagihan #{$id} ({$tagihan->santri->nama_santri} - {$tagihan->jenisTagihan->nama_tagihan}): {$kategoriLabel}"
        );

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Pengajuan penundaan & alasan berhasil dikirim ke Bendahara Pondok.',
                'data' => [
                    'id_tagihan' => $tagihan->id_tagihan,
                    'kategori_tunggakan' => $tagihan->kategori_tunggakan,
                    'kategori_badge' => $tagihan->kategori_badge,
                    'alasan_tunggakan' => $tagihan->alasan_tunggakan,
                    'tgl_janji_bayar' => $tagihan->tgl_janji_bayar ? date('d/m/Y', strtotime($tagihan->tgl_janji_bayar)) : null,
                ]
            ]);
        }

        return back()->with('success', 'Alhamdulillah, pengajuan penundaan & alasan kendala pembayaran telah dikirim dan tercatat di Rekapan Bendahara Pondok.');
    }
}
