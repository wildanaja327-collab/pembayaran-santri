<?php

namespace App\Http\Controllers;

use App\Models\Program;
use Illuminate\Http\Request;

class ProgramController extends Controller
{
    public function index()
    {
        $programs = Program::withCount('santri')->get();
        return view('admin.program.index', compact('programs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_program' => 'required|string|max:10|unique:program,id_program',
            'nama_program' => 'required|string|max:100',
            'keterangan' => 'nullable|string',
        ]);

        Program::create($request->all());

        return redirect()->route('admin.program.index')->with('success', 'Program unggulan berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $program = Program::findOrFail($id);

        $request->validate([
            'nama_program' => 'required|string|max:100',
            'keterangan' => 'nullable|string',
        ]);

        $program->update($request->only(['nama_program', 'keterangan']));

        return redirect()->route('admin.program.index')->with('success', 'Program unggulan berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $program = Program::findOrFail($id);

        if ($program->santri()->count() > 0) {
            return back()->with('error', 'Program tidak dapat dihapus karena masih diikuti oleh santri.');
        }

        $program->delete();

        return redirect()->route('admin.program.index')->with('success', 'Program berhasil dihapus.');
    }
}

