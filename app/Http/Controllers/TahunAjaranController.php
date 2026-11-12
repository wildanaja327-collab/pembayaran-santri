<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;

class TahunAjaranController extends Controller
{
    public function index()
    {
        $tahunAjarans = TahunAjaran::orderBy('id_tahun_ajaran', 'desc')->get();
        return view('admin.tahun_ajaran.index', compact('tahunAjarans'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_tahun_ajaran' => 'required|string|max:20',
            'semester' => 'required|in:Ganjil,Genap',
            'tgl_mulai' => 'nullable|date',
            'tgl_selesai' => 'nullable|date',
        ]);

        $tahun = TahunAjaran::create([
            'nama_tahun_ajaran' => $request->nama_tahun_ajaran,
            'semester' => $request->semester,
            'tgl_mulai' => $request->tgl_mulai,
            'tgl_selesai' => $request->tgl_selesai,
            'is_aktif' => 0,
        ]);

        ActivityLog::record('TAHUN_AJARAN_CREATE', "Menambahkan periode tahun ajaran: {$tahun->nama_tahun_ajaran} ({$tahun->semester})");

        return redirect()->route('admin.tahun-ajaran.index')->with('success', 'Tahun ajaran baru berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $tahun = TahunAjaran::findOrFail($id);

        $request->validate([
            'nama_tahun_ajaran' => 'required|string|max:20',
            'semester' => 'required|in:Ganjil,Genap',
            'tgl_mulai' => 'nullable|date',
            'tgl_selesai' => 'nullable|date',
        ]);

        $tahun->update([
            'nama_tahun_ajaran' => $request->nama_tahun_ajaran,
            'semester' => $request->semester,
            'tgl_mulai' => $request->tgl_mulai,
            'tgl_selesai' => $request->tgl_selesai,
        ]);

        ActivityLog::record('TAHUN_AJARAN_UPDATE', "Memperbarui tahun ajaran: {$tahun->nama_tahun_ajaran} ({$tahun->semester})");

        return redirect()->route('admin.tahun-ajaran.index')->with('success', 'Data tahun ajaran berhasil diperbarui.');
    }

    public function setAktif($id)
    {
        // Nonaktifkan semua tahun ajaran lain terlebih dahulu
        TahunAjaran::query()->update(['is_aktif' => 0]);

        $tahun = TahunAjaran::findOrFail($id);
        $tahun->is_aktif = 1;
        $tahun->save();

        ActivityLog::record('TAHUN_AJARAN_AKTIF', "Menetapkan tahun ajaran aktif: {$tahun->nama_tahun_ajaran} ({$tahun->semester})");

        return redirect()->route('admin.tahun-ajaran.index')->with('success', "Tahun ajaran {$tahun->nama_tahun_ajaran} ({$tahun->semester}) sekarang aktif sebagai acuan sistem.");
    }

    public function destroy($id)
    {
        $tahun = TahunAjaran::findOrFail($id);

        if ($tahun->is_aktif) {
            return back()->with('error', 'Tahun ajaran yang sedang aktif tidak boleh dihapus.');
        }

        $nama = "{$tahun->nama_tahun_ajaran} ({$tahun->semester})";
        $tahun->delete();

        ActivityLog::record('TAHUN_AJARAN_DELETE', "Menghapus periode tahun ajaran: {$nama}");

        return redirect()->route('admin.tahun-ajaran.index')->with('success', 'Tahun ajaran berhasil dihapus.');
    }
}

