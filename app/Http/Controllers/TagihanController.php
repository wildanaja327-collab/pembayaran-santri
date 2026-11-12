<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\JenisTagihan;
use App\Models\Program;
use App\Models\Santri;
use App\Models\Tagihan;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TagihanController extends Controller
{
    public function index(Request $request)
    {
        $query = Tagihan::with(['santri.program', 'jenisTagihan', 'pembayaran.kwitansi']);

        if ($request->filled('status_tagihan')) {
            $query->where('status_tagihan', $request->status_tagihan);
        }

        if ($request->filled('id_jenis_tagihan')) {
            $query->where('id_jenis_tagihan', $request->id_jenis_tagihan);
        }

        if ($request->filled('tahun_ajaran')) {
            $query->where('tahun_ajaran', $request->tahun_ajaran);
        }

        if ($request->filled('bulan')) {
            $query->where('bulan', $request->bulan);
        }

        if ($request->filled('tingkat')) {
            $query->whereHas('santri', function ($q) use ($request) {
                $q->where('tingkat', $request->tingkat);
            });
        }

        if ($request->filled('id_program')) {
            $query->whereHas('santri', function ($q) use ($request) {
                $q->where('id_program', $request->id_program);
            });
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('santri', function ($q) use ($search) {
                $q->where('nama_santri', 'like', "%{$search}%")
                    ->orWhere('nis', 'like', "%{$search}%");
            });
        }

        $tagihan = $query->latest('id_tagihan')->paginate(15)->withQueryString();
        $jenisTagihanList = JenisTagihan::all();
        $tahunAjaranList = TahunAjaran::orderBy('id_tahun_ajaran', 'desc')->get();
        $programList = Program::all();

        return view('bendahara.tagihan.index', compact('tagihan', 'jenisTagihanList', 'tahunAjaranList', 'programList'));
    }

    /**
     * Detail Tagihan & Riwayat Pembayaran Bertahap / Cicilan
     */
    public function show($id)
    {
        $tagihan = Tagihan::with([
            'santri.program',
            'santri.wali',
            'jenisTagihan',
            'pembayaran' => function ($q) {
                $q->latest('tgl_bayar');
            },
            'pembayaran.kwitansi',
            'pembayaran.verifier'
        ])->findOrFail($id);

        return view('bendahara.tagihan.show', compact('tagihan'));
    }

    public function create()
    {
        $santriList = Santri::where('status_aktif', 'aktif')->orderBy('nama_santri')->get();
        $jenisTagihanList = JenisTagihan::all();
        $tahunAjaranList = TahunAjaran::orderBy('id_tahun_ajaran', 'desc')->get();
        $tahunAjaranAktif = TahunAjaran::getAktif();

        return view('bendahara.tagihan.create', compact('santriList', 'jenisTagihanList', 'tahunAjaranList', 'tahunAjaranAktif'));
    }

    /**
     * AJAX Live Search Endpoint untuk Pencarian Tagihan (Kasir Tunai / Dropdown)
     */
    public function searchAjax(Request $request)
    {
        $q = trim($request->input('q', ''));
        $query = Tagihan::with(['santri', 'jenisTagihan'])
            ->whereIn('status_tagihan', ['belum_bayar', 'dicicil']);

        if ($q !== '') {
            $query->where(function ($sq) use ($q) {
                $sq->whereHas('santri', function ($ssq) use ($q) {
                    $ssq->where('nama_santri', 'like', "%{$q}%")
                        ->orWhere('nis', 'like', "%{$q}%");
                })->orWhereHas('jenisTagihan', function ($jsq) use ($q) {
                    $jsq->where('nama_tagihan', 'like', "%{$q}%");
                });
            });
        }

        $results = $query->orderBy('jatuh_tempo')->take(25)->get()->map(function ($t) {
            return [
                'id_tagihan' => $t->id_tagihan,
                'nis' => $t->nis,
                'nama_santri' => $t->santri->nama_santri ?? '-',
                'jenis_tagihan' => $t->jenisTagihan->nama_tagihan ?? '-',
                'bulan' => $t->bulan ? 'Bulan ' . $t->nama_bulan : '',
                'tahun_ajaran' => $t->tahun_ajaran,
                'nominal_tagihan' => (int) $t->nominal_tagihan,
                'sisa_tagihan' => (int) $t->sisa_tagihan,
                'total_terbayar' => (int) $t->total_terbayar,
                'sisa_format' => 'Rp ' . number_format($t->sisa_tagihan, 0, ',', '.'),
                'nominal_format' => 'Rp ' . number_format($t->nominal_tagihan, 0, ',', '.'),
            ];
        });

        return response()->json($results);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nis' => 'required|exists:santri,nis',
            'id_jenis_tagihan' => 'required|exists:jenis_tagihan,id_jenis_tagihan',
            'tahun_ajaran' => 'required|string|max:20',
            'bulan' => 'nullable|integer|between:1,12',
            'nominal_tagihan' => 'required|numeric|min:1',
            'jatuh_tempo' => 'nullable|date',
        ]);

        $jenis = JenisTagihan::findOrFail($request->id_jenis_tagihan);
        $bulan = ($jenis->tipe === 'bulanan') ? $request->bulan : null;

        // Cek duplikasi
        $exists = Tagihan::where('nis', $request->nis)
            ->where('id_jenis_tagihan', $request->id_jenis_tagihan)
            ->where('tahun_ajaran', $request->tahun_ajaran)
            ->where('bulan', $bulan)
            ->exists();

        if ($exists) {
            return back()->withInput()->with('error', 'Tagihan untuk santri dan periode tersebut sudah ada sebelumnya.');
        }

        $tagihan = Tagihan::create([
            'nis' => $request->nis,
            'id_jenis_tagihan' => $request->id_jenis_tagihan,
            'tahun_ajaran' => $request->tahun_ajaran,
            'bulan' => $bulan,
            'nominal_tagihan' => $request->nominal_tagihan,
            'total_terbayar' => 0.00,
            'sisa_tagihan' => $request->nominal_tagihan,
            'jatuh_tempo' => $request->jatuh_tempo,
            'status_tagihan' => 'belum_bayar',
        ]);

        ActivityLog::record('TAGIHAN_CREATE', "Membuat tagihan {$jenis->nama_tagihan} untuk santri NIS {$request->nis} nominal Rp " . number_format($request->nominal_tagihan, 0, ',', '.'));

        return redirect()->route('bendahara.tagihan.index')->with('success', 'Tagihan santri baru berhasil diterbitkan.');
    }

    /**
     * Tampilan form generator tagihan massal
     */
    public function showGenerateMassal()
    {
        $jenisTagihanList = JenisTagihan::all();
        $totalSantriAktif = Santri::where('status_aktif', 'aktif')->count();
        $smpCount = Santri::where('status_aktif', 'aktif')->where('tingkat', 'SMP')->count();
        $smaCount = Santri::where('status_aktif', 'aktif')->where('tingkat', 'SMA')->count();
        $tahunAjaranList = TahunAjaran::orderBy('id_tahun_ajaran', 'desc')->get();
        $tahunAjaranAktif = TahunAjaran::getAktif();

        return view('bendahara.tagihan.generate', compact(
            'jenisTagihanList',
            'totalSantriAktif',
            'smpCount',
            'smaCount',
            'tahunAjaranList',
            'tahunAjaranAktif'
        ));
    }

    /**
     * Eksekusi generator tagihan massal
     */
    public function generateMassal(Request $request)
    {
        $request->validate([
            'id_jenis_tagihan' => 'required|exists:jenis_tagihan,id_jenis_tagihan',
            'tahun_ajaran' => 'required|string|max:20',
            'bulan' => 'nullable|integer|between:1,12',
            'tingkat' => 'required|in:semua,SMP,SMA',
            'nominal_tagihan' => 'required|numeric|min:1',
            'jatuh_tempo' => 'nullable|date',
        ]);

        $jenis = JenisTagihan::findOrFail($request->id_jenis_tagihan);
        $bulan = ($jenis->tipe === 'bulanan') ? $request->bulan : null;

        $santriQuery = Santri::where('status_aktif', 'aktif');
        if ($request->tingkat !== 'semua') {
            $santriQuery->where('tingkat', $request->tingkat);
        }

        $santriList = $santriQuery->get();
        if ($santriList->isEmpty()) {
            return back()->with('error', 'Tidak ditemukan santri aktif untuk kriteria yang dipilih.');
        }

        $createdCount = 0;
        $skippedCount = 0;

        DB::beginTransaction();
        try {
            foreach ($santriList as $santri) {
                // Cek apakah santri ini sudah ditagih untuk periode ini
                $exists = Tagihan::where('nis', $santri->nis)
                    ->where('id_jenis_tagihan', $request->id_jenis_tagihan)
                    ->where('tahun_ajaran', $request->tahun_ajaran)
                    ->where('bulan', $bulan)
                    ->exists();

                if ($exists) {
                    $skippedCount++;
                    continue;
                }

                Tagihan::create([
                    'nis' => $santri->nis,
                    'id_jenis_tagihan' => $request->id_jenis_tagihan,
                    'tahun_ajaran' => $request->tahun_ajaran,
                    'bulan' => $bulan,
                    'nominal_tagihan' => $request->nominal_tagihan,
                    'total_terbayar' => 0.00,
                    'sisa_tagihan' => $request->nominal_tagihan,
                    'jatuh_tempo' => $request->jatuh_tempo,
                    'status_tagihan' => 'belum_bayar',
                ]);

                $createdCount++;
            }

            DB::commit();

            ActivityLog::record('TAGIHAN_GENERATE', "Menerbitkan {$createdCount} tagihan massal untuk jenis '{$jenis->nama_tagihan}' periode {$request->tahun_ajaran}");

            return redirect()->route('bendahara.tagihan.index')->with('success', "Berhasil men-generate {$createdCount} tagihan santri secara massal! ({$skippedCount} santri dilewati karena sudah memiliki tagihan ini).");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal generate tagihan: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $tagihan = Tagihan::with('pembayaran')->findOrFail($id);

        if ($tagihan->pembayaran()->where('status_transaksi', 'settlement')->exists()) {
            return back()->with('error', 'Tagihan tidak dapat dihapus karena sudah memiliki pembayaran yang sah.');
        }

        $tagihan->delete();
        ActivityLog::record('TAGIHAN_DELETE', "Menghapus catatan tagihan ID #{$id}");

        return redirect()->route('bendahara.tagihan.index')->with('success', 'Tagihan berhasil dihapus.');
    }

    /**
     * Kirim notifikasi pengingat pembayaran tagihan via WhatsApp Gateway
     */
    public function kirimReminderWa($id, \App\Services\WhatsAppService $waService)
    {
        $tagihan = Tagihan::with(['santri.wali', 'santri.program', 'jenisTagihan'])->findOrFail($id);

        if ($tagihan->status_tagihan === 'lunas') {
            return back()->with('info', 'Tagihan ini telah lunas, tidak memerlukan pengingat pembayaran.');
        }

        $res = $waService->sendPaymentReminder($tagihan);

        if ($res['success']) {
            return back()->with('success', $res['message']);
        } else {
            return back()->with('error', $res['message']);
        }
    }

    /**
     * Catat / Update Alasan Belum Bayar & Dispensasi Tunggakan Santri
     */
    public function updateAlasan(Request $request, $id)
    {
        $request->validate([
            'kategori_tunggakan' => 'nullable|string|max:50',
            'alasan_tunggakan' => 'nullable|string|max:255',
            'tgl_janji_bayar' => 'nullable|date',
            'catatan_bendahara' => 'nullable|string|max:1000',
        ]);

        $tagihan = Tagihan::with(['santri', 'jenisTagihan'])->findOrFail($id);

        $tagihan->update([
            'kategori_tunggakan' => $request->kategori_tunggakan ?: null,
            'alasan_tunggakan' => $request->alasan_tunggakan ?: null,
            'tgl_janji_bayar' => $request->tgl_janji_bayar ?: null,
            'catatan_bendahara' => $request->catatan_bendahara ?: null,
            'updated_alasan_at' => now(),
        ]);

        $kategoriLabel = $tagihan->kategori_info['label'] ?? ($request->kategori_tunggakan ?: 'Catatan Umum');
        ActivityLog::record(
            'TAGIHAN_NOTE',
            "Memperbarui catatan alasan tunggakan tagihan ID #{$id} ({$tagihan->santri->nama_santri}): {$kategoriLabel}"
        );

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Catatan alasan tunggakan berhasil disimpan!',
                'data' => [
                    'id_tagihan' => $tagihan->id_tagihan,
                    'kategori_tunggakan' => $tagihan->kategori_tunggakan,
                    'kategori_badge' => $tagihan->kategori_badge,
                    'kategori_label' => $tagihan->kategori_info['label'] ?? null,
                    'alasan_tunggakan' => $tagihan->alasan_tunggakan,
                    'tgl_janji_bayar' => $tagihan->tgl_janji_bayar ? date('d/m/Y', strtotime($tagihan->tgl_janji_bayar)) : null,
                    'tgl_janji_bayar_raw' => $tagihan->tgl_janji_bayar,
                    'catatan_bendahara' => $tagihan->catatan_bendahara,
                    'alasan_lengkap' => $tagihan->alasan_lengkap,
                    'updated_alasan_at' => $tagihan->updated_alasan_at ? date('d/m/Y H:i', strtotime($tagihan->updated_alasan_at)) : null,
                ]
            ]);
        }

        return back()->with('success', 'Catatan alasan tunggakan berhasil disimpan.');
    }
}
