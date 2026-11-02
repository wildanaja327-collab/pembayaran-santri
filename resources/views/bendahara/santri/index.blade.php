@extends('layouts.app')

@section('title', 'Data Santri')
@section('page_title', 'Data Santri Pondok Pesantren')

@section('content')
<div class="card border-0 shadow-sm rounded-3">
    <div class="card-header bg-white py-3 border-0 d-flex flex-wrap align-items-center justify-content-between gap-2">
        <div>
            <h6 class="mb-0 fw-bold">Master Santri (SMP & SMA)</h6>
            <small class="text-muted">Total terdata: {{ $santri->total() }} santri</small>
        </div>
        <a href="{{ route('bendahara.santri.create') }}" class="btn btn-success btn-sm fw-semibold">
            <i class="bi bi-person-plus me-1"></i> Tambah Santri
        </a>
    </div>

    <!-- Filter Bar -->
    <div class="p-3 border-top border-bottom bg-light">
        <form action="{{ route('bendahara.santri.index') }}" method="GET" class="row g-2">
            <div class="col-md-3">
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Cari nama, NIS, atau NISN..." value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <select name="tingkat" class="form-select form-select-sm">
                    <option value="">-- Semua Tingkat --</option>
                    <option value="SMP" {{ request('tingkat') == 'SMP' ? 'selected' : '' }}>SMP (243)</option>
                    <option value="SMA" {{ request('tingkat') == 'SMA' ? 'selected' : '' }}>SMA (113)</option>
                </select>
            </div>
            <div class="col-md-2">
                <input type="text" name="kelas" class="form-control form-control-sm" placeholder="Kelas (misal: VII A)..." value="{{ request('kelas') }}">
            </div>
            <div class="col-md-3">
                <select name="id_program" class="form-select form-select-sm">
                    <option value="">-- Semua Program Unggulan --</option>
                    @foreach($programs as $prg)
                        <option value="{{ $prg->id_program }}" {{ request('id_program') == $prg->id_program ? 'selected' : '' }}>
                            {{ $prg->nama_program }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 d-flex gap-1">
                <button type="submit" class="btn btn-primary btn-sm flex-grow-1"><i class="bi bi-search me-1"></i> Filter</button>
                <a href="{{ route('bendahara.santri.index') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-counterclockwise"></i></a>
            </div>
        </form>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr class="small text-muted text-uppercase">
                    <th>NIS / NISN</th>
                    <th>Nama Santri</th>
                    <th>Tingkat & Kelas</th>
                    <th>Program Unggulan</th>
                    <th>Wali Santri & Kontak</th>
                    <th>Status</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($santri as $s)
                    <tr>
                        <td>
                            <span class="fw-bold text-dark">{{ $s->nis }}</span>
                            <div class="small text-muted">NISN: {{ $s->nisn ?? '-' }}</div>
                        </td>
                        <td>
                            <div class="fw-semibold">{{ $s->nama_santri }}</div>
                            <small class="text-muted"><i class="bi bi-gender-{{ $s->jenis_kelamin == 'L' ? 'male text-primary' : 'female text-danger' }} me-1"></i>{{ $s->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</small>
                        </td>
                        <td>
                            <span class="badge {{ $s->tingkat == 'SMP' ? 'badge-smp' : 'badge-sma' }}">{{ $s->tingkat }}</span>
                            <div class="small text-muted mt-1">{{ $s->kelas }}</div>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border">{{ $s->program->nama_program ?? '-' }}</span>
                        </td>
                        <td>
                            <div class="fw-semibold">{{ $s->wali->nama_lengkap ?? '-' }}</div>
                            <small class="text-muted"><i class="bi bi-whatsapp me-1 text-success"></i>{{ $s->wali->no_telp ?? '-' }}</small>
                        </td>
                        <td>
                            @if($s->status_aktif == 'aktif')
                                <span class="badge bg-success-subtle text-success border border-success-subtle">Aktif</span>
                            @else
                                <span class="badge bg-secondary">{{ ucfirst($s->status_aktif) }}</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-1">
                                <a href="{{ route('bendahara.santri.edit', $s->nis) }}" class="btn btn-sm btn-outline-secondary" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('bendahara.santri.destroy', $s->nis) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus santri ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">Tidak ada data santri ditemukan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($santri->hasPages())
        <div class="p-3 border-top d-flex justify-content-end">
            {{ $santri->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>
@endsection

