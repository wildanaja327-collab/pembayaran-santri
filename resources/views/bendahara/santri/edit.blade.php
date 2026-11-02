@extends('layouts.app')

@section('title', 'Edit Data Santri')
@section('page_title', 'Perbarui Data Santri')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-9">
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-header bg-white py-3 border-0">
                <h6 class="mb-0 fw-bold"><i class="bi bi-pencil-square me-2 text-primary"></i>Edit Data Santri (NIS: {{ $santri->nis }})</h6>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('bendahara.santri.update', $santri->nis) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">NIS</label>
                            <input type="text" class="form-control bg-light" value="{{ $santri->nis }}" disabled>
                            <small class="text-muted">NIS adalah Primary Key dan tidak dapat diubah.</small>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">NISN</label>
                            <input type="text" name="nisn" class="form-control" value="{{ old('nisn', $santri->nisn) }}">
                        </div>

                        <div class="col-md-12">
                            <label class="form-label small fw-semibold">Nama Lengkap Santri <span class="text-danger">*</span></label>
                            <input type="text" name="nama_santri" class="form-control" value="{{ old('nama_santri', $santri->nama_santri) }}" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">Tingkat Pendidikan <span class="text-danger">*</span></label>
                            <select name="tingkat" class="form-select" required>
                                <option value="SMP" {{ old('tingkat', $santri->tingkat) == 'SMP' ? 'selected' : '' }}>SMP</option>
                                <option value="SMA" {{ old('tingkat', $santri->tingkat) == 'SMA' ? 'selected' : '' }}>SMA</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">Kelas / Kamar Asrama <span class="text-danger">*</span></label>
                            <input type="text" name="kelas" class="form-control" value="{{ old('kelas', $santri->kelas) }}" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">Jenis Kelamin <span class="text-danger">*</span></label>
                            <select name="jenis_kelamin" class="form-select" required>
                                <option value="L" {{ old('jenis_kelamin', $santri->jenis_kelamin) == 'L' ? 'selected' : '' }}>Laki-laki</option>
                                <option value="P" {{ old('jenis_kelamin', $santri->jenis_kelamin) == 'P' ? 'selected' : '' }}>Perempuan</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">Tanggal Lahir</label>
                            <input type="date" name="tgl_lahir" class="form-control" value="{{ old('tgl_lahir', $santri->tgl_lahir) }}">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">Program Unggulan <span class="text-danger">*</span></label>
                            <select name="id_program" class="form-select" required>
                                @foreach($programs as $p)
                                    <option value="{{ $p->id_program }}" {{ old('id_program', $santri->id_program) == $p->id_program ? 'selected' : '' }}>
                                        {{ $p->nama_program }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">Status Santri <span class="text-danger">*</span></label>
                            <select name="status_aktif" class="form-select" required>
                                <option value="aktif" {{ old('status_aktif', $santri->status_aktif) == 'aktif' ? 'selected' : '' }}>Aktif</option>
                                <option value="alumni" {{ old('status_aktif', $santri->status_aktif) == 'alumni' ? 'selected' : '' }}>Alumni</option>
                                <option value="mutasi" {{ old('status_aktif', $santri->status_aktif) == 'mutasi' ? 'selected' : '' }}>Mutasi / Keluar</option>
                            </select>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label small fw-semibold">Akun Wali Santri Terkait</label>
                            <select name="id_wali" class="form-select">
                                <option value="">-- Tanpa Akun Wali --</option>
                                @foreach($waliList as $w)
                                    <option value="{{ $w->id_user }}" {{ old('id_wali', $santri->id_wali) == $w->id_user ? 'selected' : '' }}>
                                        {{ $w->nama_lengkap }} (WA: {{ $w->no_telp }} | User: {{ $w->username }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between pt-3 border-top">
                        <a href="{{ route('bendahara.santri.index') }}" class="btn btn-outline-secondary">Kembali</a>
                        <button type="submit" class="btn btn-primary px-4 fw-semibold"><i class="bi bi-save me-1"></i> Perbarui Santri</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

