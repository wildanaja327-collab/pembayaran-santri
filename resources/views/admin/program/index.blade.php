@extends('layouts.app')

@section('title', 'Master Program Unggulan')
@section('page_title', 'Master Program Unggulan Pesantren')

@section('content')
<div class="row g-4">
    <!-- Form Tambah Baru -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-header bg-white py-3 border-0">
                <h6 class="mb-0 fw-bold"><i class="bi bi-plus-circle me-2 text-primary"></i>Tambah Program Unggulan</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.program.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Kode Program (ID) <span class="text-danger">*</span></label>
                        <input type="text" name="id_program" class="form-control" placeholder="Contoh: PRG-TFZ" maxlength="10" required>
                        <small class="text-muted">Maksimal 10 karakter huruf/angka unik.</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Nama Program <span class="text-danger">*</span></label>
                        <input type="text" name="nama_program" class="form-control" placeholder="Contoh: Program Tahfidzul Qur'an" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Keterangan / Deskripsi</label>
                        <textarea name="keterangan" class="form-control" rows="3" placeholder="Fokus kompetensi santri..."></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 fw-semibold">
                        <i class="bi bi-save me-1"></i> Simpan Program
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Tabel Master Program -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-header bg-white py-3 border-0">
                <h6 class="mb-0 fw-bold"><i class="bi bi-mortarboard me-2 text-success"></i>Daftar Program Unggulan PP Darul Muttaqin</h6>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr class="small text-muted text-uppercase">
                            <th>Kode</th>
                            <th>Nama Program</th>
                            <th>Keterangan</th>
                            <th>Jumlah Santri</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($programs as $p)
                            <tr>
                                <td><code>{{ $p->id_program }}</code></td>
                                <td class="fw-bold">{{ $p->nama_program }}</td>
                                <td class="small text-muted">{{ $p->keterangan ?? '-' }}</td>
                                <td>
                                    <span class="badge bg-light text-dark border">{{ $p->santri_count }} Santri</span>
                                </td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-outline-secondary me-1" data-bs-toggle="modal" data-bs-target="#editProgModal{{ $p->id_program }}" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    @if($p->santri_count == 0)
                                        <form action="{{ route('admin.program.destroy', $p->id_program) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus program ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">Belum ada data program unggulan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Modals Edit Program (Di luar table) -->
            @foreach($programs as $p)
                <div class="modal fade" id="editProgModal{{ $p->id_program }}" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form action="{{ route('admin.program.update', $p->id_program) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="modal-header">
                                    <h6 class="modal-title fw-bold">Edit Program: {{ $p->id_program }}</h6>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body text-start">
                                    <div class="mb-3">
                                        <label class="form-label small fw-semibold">Nama Program</label>
                                        <input type="text" name="nama_program" class="form-control" value="{{ $p->nama_program }}" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label small fw-semibold">Keterangan</label>
                                        <textarea name="keterangan" class="form-control" rows="3">{{ $p->keterangan }}</textarea>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                                    <button type="submit" class="btn btn-primary btn-sm">Simpan Perubahan</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection

