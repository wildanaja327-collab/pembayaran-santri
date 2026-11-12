<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Program;
use App\Models\Santri;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SantriController extends Controller
{
    public function index(Request $request)
    {
        $query = Santri::with(['program', 'wali']);

        if ($request->filled('tingkat')) {
            $query->where('tingkat', $request->tingkat);
        }

        if ($request->filled('kelas')) {
            $query->where('kelas', 'like', '%' . $request->kelas . '%');
        }

        if ($request->filled('id_program')) {
            $query->where('id_program', $request->id_program);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_santri', 'like', "%{$search}%")
                    ->orWhere('nis', 'like', "%{$search}%")
                    ->orWhere('nisn', 'like', "%{$search}%");
            });
        }

        $santri = $query->orderBy('nama_santri')->paginate(15)->withQueryString();
        $programs = Program::all();

        return view('bendahara.santri.index', compact('santri', 'programs'));
    }

    /**
     * AJAX Live Search Endpoint untuk Pencarian Santri (Autocomplete / Dropdown)
     */
    public function searchAjax(Request $request)
    {
        $q = trim($request->input('q', ''));
        $query = Santri::with('program')->where('status_aktif', 'aktif');

        if ($q !== '') {
            $query->where(function ($sq) use ($q) {
                $sq->where('nama_santri', 'like', "%{$q}%")
                    ->orWhere('nis', 'like', "%{$q}%")
                    ->orWhere('nisn', 'like', "%{$q}%");
            });
        }

        $results = $query->orderBy('nama_santri')->take(25)->get()->map(function ($s) {
            $parts = explode(' ', trim($s->nama_santri));
            $inisial = strtoupper(substr($parts[0] ?? 'S', 0, 1)) . strtoupper(substr($parts[1] ?? ($parts[0] ?? 'X'), 0, 1));
            return [
                'nis' => $s->nis,
                'nama' => $s->nama_santri,
                'tingkat' => $s->tingkat,
                'kelas' => $s->kelas,
                'program' => $s->program->nama_program ?? '-',
                'inisial' => $inisial,
            ];
        });

        return response()->json($results);
    }

    public function create()
    {
        $programs = Program::all();
        $waliList = User::where('role', 'wali_santri')->orderBy('nama_lengkap')->get();

        return view('bendahara.santri.create', compact('programs', 'waliList'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nis' => 'required|string|max:20|unique:santri,nis',
            'nisn' => 'nullable|string|max:20|unique:santri,nisn',
            'nama_santri' => 'required|string|max:100',
            'tingkat' => 'required|in:SMP,SMA',
            'kelas' => 'required|string|max:50',
            'jenis_kelamin' => 'required|in:L,P',
            'tgl_lahir' => 'nullable|date',
            'id_program' => 'required|exists:program,id_program',
            'tipe_wali' => 'required|in:baru,pilih',
        ]);

        DB::beginTransaction();
        try {
            $idWali = null;

            if ($request->tipe_wali === 'baru') {
                $request->validate([
                    'nama_wali' => 'required|string|max:100',
                    'no_telp_wali' => 'required|string|max:20',
                ]);

                // Buat akun wali santri otomatis: username = NIS, password = NIS
                $wali = User::create([
                    'username' => 'wali_' . $request->nis,
                    'password' => Hash::make($request->nis),
                    'nama_lengkap' => $request->nama_wali,
                    'no_telp' => $request->no_telp_wali,
                    'role' => 'wali_santri',
                ]);
                $idWali = $wali->id_user;
            } else {
                $idWali = $request->id_wali;
            }

            Santri::create([
                'nis' => $request->nis,
                'nisn' => $request->nisn,
                'nama_santri' => $request->nama_santri,
                'tingkat' => $request->tingkat,
                'kelas' => $request->kelas,
                'jenis_kelamin' => $request->jenis_kelamin,
                'tgl_lahir' => $request->tgl_lahir,
                'id_program' => $request->id_program,
                'id_wali' => $idWali,
                'status_aktif' => 'aktif',
            ]);

            DB::commit();

            ActivityLog::record('SANTRI_CREATE', "Menambahkan santri baru: {$request->nama_santri} (NIS: {$request->nis}, Kelas: {$request->kelas})");

            $msg = 'Data santri berhasil ditambahkan.';
            if ($request->tipe_wali === 'baru') {
                $msg .= ' Akun wali santri dibuat otomatis (Username: wali_' . $request->nis . ', Password: ' . $request->nis . ').';
            }

            $redirectRoute = auth()->user()->isAdmin() ? 'admin.santri.index' : 'bendahara.santri.index';
            return redirect()->route($redirectRoute)->with('success', $msg);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal menambahkan santri: ' . $e->getMessage());
        }
    }

    public function edit($nis)
    {
        $santri = Santri::with('wali')->findOrFail($nis);
        $programs = Program::all();
        $waliList = User::where('role', 'wali_santri')->orderBy('nama_lengkap')->get();

        return view('bendahara.santri.edit', compact('santri', 'programs', 'waliList'));
    }

    public function update(Request $request, $nis)
    {
        $santri = Santri::findOrFail($nis);

        $request->validate([
            'nisn' => 'nullable|string|max:20|unique:santri,nisn,' . $santri->nis . ',nis',
            'nama_santri' => 'required|string|max:100',
            'tingkat' => 'required|in:SMP,SMA',
            'kelas' => 'required|string|max:50',
            'jenis_kelamin' => 'required|in:L,P',
            'tgl_lahir' => 'nullable|date',
            'id_program' => 'required|exists:program,id_program',
            'id_wali' => 'nullable|exists:users,id_user',
            'status_aktif' => 'required|in:aktif,alumni,mutasi',
        ]);

        $santri->update($request->only([
            'nisn',
            'nama_santri',
            'tingkat',
            'kelas',
            'jenis_kelamin',
            'tgl_lahir',
            'id_program',
            'id_wali',
            'status_aktif',
        ]));

        ActivityLog::record('SANTRI_UPDATE', "Memperbarui data santri: {$santri->nama_santri} (NIS: {$santri->nis})");

        $redirectRoute = auth()->user()->isAdmin() ? 'admin.santri.index' : 'bendahara.santri.index';
        return redirect()->route($redirectRoute)->with('success', 'Data santri berhasil diperbarui.');
    }

    public function destroy($nis)
    {
        $santri = Santri::findOrFail($nis);
        $nama = "{$santri->nama_santri} (NIS: {$santri->nis})";
        $santri->delete();

        ActivityLog::record('SANTRI_DELETE', "Menghapus data santri: {$nama}");

        $redirectRoute = auth()->user()->isAdmin() ? 'admin.santri.index' : 'bendahara.santri.index';
        return redirect()->route($redirectRoute)->with('success', 'Data santri berhasil dihapus.');
    }
}

