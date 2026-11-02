@extends('layouts.app')

@section('title', 'Tambah Santri Baru')
@section('page_title', 'Pendaftaran Santri Baru')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-9">
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-header bg-white py-3 border-0">
                <h6 class="mb-0 fw-bold"><i class="bi bi-person-plus me-2 text-success"></i>Formulir Data Santri</h6>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('bendahara.santri.store') }}" method="POST">
                    @csrf

                    <h6 class="text-uppercase text-muted fw-bold small mb-3">1. Identitas Pokok Santri</h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">NIS (Nomor Induk Santri) <span class="text-danger">*</span></label>
                            <input type="text" name="nis" class="form-control @error('nis') is-invalid @enderror" value="{{ old('nis') }}" placeholder="Contoh: 2026001" required>
                            @error('nis') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">NISN</label>
                            <input type="text" name="nisn" class="form-control @error('nisn') is-invalid @enderror" value="{{ old('nisn') }}" placeholder="Nomor Induk Siswa Nasional">
                            @error('nisn') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-12">
                            <label class="form-label small fw-semibold">Nama Lengkap Santri <span class="text-danger">*</span></label>
                            <input type="text" name="nama_santri" class="form-control @error('nama_santri') is-invalid @enderror" value="{{ old('nama_santri') }}" placeholder="Nama santri" required>
                            @error('nama_santri') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">Tingkat Pendidikan <span class="text-danger">*</span></label>
                            <select name="tingkat" class="form-select @error('tingkat') is-invalid @enderror" required>
                                <option value="">-- Pilih Tingkat --</option>
                                <option value="SMP" {{ old('tingkat') == 'SMP' ? 'selected' : '' }}>SMP</option>
                                <option value="SMA" {{ old('tingkat') == 'SMA' ? 'selected' : '' }}>SMA</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">Kelas / Kamar Asrama <span class="text-danger">*</span></label>
                            <input type="text" name="kelas" class="form-control @error('kelas') is-invalid @enderror" value="{{ old('kelas') }}" placeholder="Contoh: VII A / Asrama Abu Bakar" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">Jenis Kelamin <span class="text-danger">*</span></label>
                            <select name="jenis_kelamin" class="form-select @error('jenis_kelamin') is-invalid @enderror" required>
                                <option value="L" {{ old('jenis_kelamin') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                                <option value="P" {{ old('jenis_kelamin') == 'P' ? 'selected' : '' }}>Perempuan</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Tanggal Lahir</label>
                            <input type="date" name="tgl_lahir" class="form-control @error('tgl_lahir') is-invalid @enderror" value="{{ old('tgl_lahir') }}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Program Unggulan Pesantren <span class="text-danger">*</span></label>
                            <select name="id_program" class="form-select @error('id_program') is-invalid @enderror" required>
                                <option value="">-- Pilih Program --</option>
                                @foreach($programs as $p)
                                    <option value="{{ $p->id_program }}" {{ old('id_program') == $p->id_program ? 'selected' : '' }}>
                                        {{ $p->nama_program }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <h6 class="text-uppercase text-muted fw-bold small mb-3">2. Data Orang Tua / Wali Santri & Akun Login</h6>
                    
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Tipe Penautan Wali Santri</label>
                        <div class="d-flex gap-3">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="tipe_wali" id="wali_baru" value="baru" {{ old('tipe_wali', 'baru') == 'baru' ? 'checked' : '' }} onchange="toggleWaliSection()">
                                <label class="form-check-label" for="wali_baru">
                                    <strong>Wali Santri Baru</strong> (Sistem otomatis membuatkan akun login)
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="tipe_wali" id="wali_pilih" value="pilih" {{ old('tipe_wali') == 'pilih' ? 'checked' : '' }} onchange="toggleWaliSection()">
                                <label class="form-check-label" for="wali_pilih">
                                    <strong>Wali Yang Sudah Ada</strong> (Jika memiliki saudara kandung di pesantren)
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Input Wali Baru -->
                    <div id="section_wali_baru" class="p-3 bg-light rounded-3 mb-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">Nama Lengkap Wali Santri <span class="text-danger">*</span></label>
                                <input type="text" name="nama_wali" id="input_nama_wali" class="form-control" value="{{ old('nama_wali') }}" placeholder="Nama ayah / ibu / wali">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">Nomor WhatsApp Aktif <span class="text-danger">*</span></label>
                                <input type="text" name="no_telp_wali" id="input_no_telp_wali" class="form-control" value="{{ old('no_telp_wali') }}" placeholder="Contoh: 081234567890">
                                <small class="text-muted">Nomor ini akan digunakan untuk notifikasi pengingat jatuh tempo.</small>
                            </div>
                        </div>
                        <div class="alert alert-info py-2 small mt-3 mb-0">
                            <i class="bi bi-info-circle me-1"></i> Akun wali santri akan dibuat dengan <strong>Username:</strong> <code>wali_[NIS]</code> dan <strong>Password Default:</strong> <code>[NIS]</code>.
                        </div>
                    </div>

                    <!-- Pilih Wali yang Ada -->
                    <div id="section_wali_pilih" class="p-3 bg-light rounded-3 mb-4 d-none">
                        <label class="form-label small fw-semibold">Pilih Wali Santri Terdaftar</label>
                        <select name="id_wali" class="form-select">
                            <option value="">-- Pilih Akun Wali --</option>
                            @foreach($waliList as $w)
                                <option value="{{ $w->id_user }}" {{ old('id_wali') == $w->id_user ? 'selected' : '' }}>
                                    {{ $w->nama_lengkap }} (WA: {{ $w->no_telp }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="d-flex justify-content-between pt-3 border-top">
                        <a href="{{ route('bendahara.santri.index') }}" class="btn btn-outline-secondary">Kembali</a>
                        <button type="submit" class="btn btn-success px-4 fw-semibold"><i class="bi bi-save me-1"></i> Simpan Santri</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function toggleWaliSection() {
        const isBaru = document.getElementById('wali_baru').checked;
        const secBaru = document.getElementById('section_wali_baru');
        const secPilih = document.getElementById('section_wali_pilih');

        if (isBaru) {
            secBaru.classList.remove('d-none');
            secPilih.classList.add('d-none');
        } else {
            secBaru.classList.add('d-none');
            secPilih.classList.remove('d-none');
        }
    }
    document.addEventListener('DOMContentLoaded', toggleWaliSection);
</script>
@endpush
@endsection

