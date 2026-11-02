<?php

namespace App\Http\Controllers;

use App\Models\JenisTagihan;
use App\Models\Santri;
use App\Models\Tagihan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TagihanController extends Controller
{
    public function index(Request $request)
    {
        $query = Tagihan::with(['santri.program', 'jenisTagihan']);

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

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('santri', function ($q) use ($search) {
                $q->where('nama_santri', 'like', "%{$search}%")
                    ->orWhere('nis', 'like', "%{$search}%");
            });
        }

        $tagihan = $query->latest('id_tagihan')->paginate(15)->withQueryString();
        $jenisTagihanList = JenisTagihan::all();

        return view('bendahara.tagihan.index', compact('tagihan', 'jenisTagihanList'));
    }

    public function create()
    {
        $santriList = Santri::where('status_aktif', 'aktif')->orderBy('nama_santri')->get();
        $jenisTagihanList = JenisTagihan::all();

        return view('bendahara.tagihan.create', compact('santriList', 'jenisTagihanList'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nis' => 'required|exists:santri,nis',
            'id_jenis_tagihan' => 'required|exists:jenis_tagihan,id_jenis_tagihan',
            'tahun_ajaran' => 'required|string|max:10',
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
            return back()->withInput()->with('error', 'Tagihan untuk santri dan periode tersebut sudah ada.');
        }

        Tagihan::create([
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

        return redirect()->route('bendahara.tagihan.index')->with('success', 'Tagihan baru berhasil dibuat.');
    }

    /**
     * Tampilan form generator tagihan massal untuk 356 santri
     */
    public function showGenerateMassal()
    {
        $jenisTagihanList = JenisTagihan::all();
        $totalSantriAktif = Santri::where('status_aktif', 'aktif')->count();
        $smpCount = Santri::where('status_aktif', 'aktif')->where('tingkat', 'SMP')->count();
        $smaCount = Santri::where('status_aktif', 'aktif')->where('tingkat', 'SMA')->count();

        return view('bendahara.tagihan.generate', compact(
            'jenisTagihanList',
            'totalSantriAktif',
            'smpCount',
            'smaCount'
        ));
    }

    /**
     * Eksekusi generator tagihan massal
     */
    public function generateMassal(Request $request)
    {
        $request->validate([
            'id_jenis_tagihan' => 'required|exists:jenis_tagihan,id_jenis_tagihan',
            'tahun_ajaran' => 'required|string|max:10',
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

            return redirect()->route('bendahara.tagihan.index')->with('success', "Berhasil men-generate {$createdCount} tagihan santri secara massal! ({$skippedCount} santri dilewati karena sudah ada).");
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
        return redirect()->route('bendahara.tagihan.index')->with('success', 'Tagihan berhasil dihapus.');
    }
}

