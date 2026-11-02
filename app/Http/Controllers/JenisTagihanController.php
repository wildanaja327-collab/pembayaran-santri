<?php

namespace App\Http\Controllers;

use App\Models\JenisTagihan;
use Illuminate\Http\Request;

class JenisTagihanController extends Controller
{
    public function index()
    {
        $jenisTagihan = JenisTagihan::withCount('tagihan')->orderBy('nama_tagihan')->get();
        return view('bendahara.jenis_tagihan.index', compact('jenisTagihan'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_tagihan' => 'required|string|max:100',
            'tipe' => 'required|in:bulanan,insidental',
            'nominal_standar' => 'required|numeric|min:0',
            'keterangan' => 'nullable|string|max:255',
        ]);

        JenisTagihan::create($request->all());

        return redirect()->route('bendahara.jenis-tagihan.index')->with('success', 'Jenis tagihan berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $jenis = JenisTagihan::findOrFail($id);

        $request->validate([
            'nama_tagihan' => 'required|string|max:100',
            'tipe' => 'required|in:bulanan,insidental',
            'nominal_standar' => 'required|numeric|min:0',
            'keterangan' => 'nullable|string|max:255',
        ]);

        $jenis->update($request->all());

        return redirect()->route('bendahara.jenis-tagihan.index')->with('success', 'Jenis tagihan berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $jenis = JenisTagihan::findOrFail($id);
        if ($jenis->tagihan()->count() > 0) {
            return back()->with('error', 'Tidak dapat menghapus jenis tagihan karena sudah digunakan dalam data tagihan santri.');
        }

        $jenis->delete();
        return redirect()->route('bendahara.jenis-tagihan.index')->with('success', 'Jenis tagihan berhasil dihapus.');
    }
}

