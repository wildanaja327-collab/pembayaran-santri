@extends('layouts.app')

@section('title', 'Master Jenis Tagihan')
@section('page_title', 'Master Jenis Tagihan & Tarif')

@section('content')
<div class="row g-4">
    <!-- Form Tambah Baru -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-header bg-white py-3 border-0">
                <h6 class="mb-0 fw-bold"><i class="bi bi-plus-circle me-2 text-success"></i>Tambah Jenis Tagihan</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('bendahara.jenis-tagihan.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Nama Tagihan <span class="text-danger">*</span></label>
                        <input type="text" name="nama_tagihan" class="form-control" placeholder="Contoh: Syahriah (SPP Bulanan)" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Tipe Pembayaran <span class="text-danger">*</span></label>
                        <select name="tipe" class="form-select" required>
                            <option value="bulanan">Bulanan (Ditagih Rutin Tiap Bulan)</option>
                            <option value="insidental">Insidental (Sekali Bayar / Non-Bulanan)</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Nominal Standar (Rp) <span class="text-danger">*</span></label>
                        <input type="number" name="nominal_standar" class="form-control" placeholder="500000" min="0" required>
                        <small class="text-muted">Nominal default saat tagihan di-generate.</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Keterangan</label>
                        <textarea name="keterangan" class="form-control" rows="2" placeholder="Catatan peruntukan biaya..."></textarea>
                    </div>

                    <button type="submit" class="btn btn-success w-100 fw-semibold">
                        <i class="bi bi-save me-1"></i> Simpan Tarif Tagihan
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Tabel Master -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-header bg-white py-3 border-0">
                <h6 class="mb-0 fw-bold"><i class="bi bi-tags me-2 text-primary"></i>Daftar Komponen Biaya Pesantren</h6>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr class="small text-muted text-uppercase">
                            <th>Nama Tagihan</th>
                            <th>Tipe</th>
                            <th>Nominal Standar</th>
                            <th>Keterangan</th>
                            <th>Total Digunakan</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($jenisTagihan as $j)
                            <tr>
                                <td class="fw-bold">{{ $j->nama_tagihan }}</td>
                                <td>
                                    @if($j->tipe == 'bulanan')
                                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle">Bulanan</span>
                                    @else
                                        <span class="badge bg-warning-subtle text-warning border border-warning-subtle text-dark">Insidental</span>
                                    @endif
                                </td>
                                <td class="fw-semibold text-success">Rp {{ number_format($j->nominal_standar, 0, ',', '.') }}</td>
                                <td class="small text-muted">{{ $j->keterangan ?? '-' }}</td>
                                <td>
                                    <span class="badge bg-light text-dark border">{{ $j->tagihan_count }} tagihan</span>
                                </td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-outline-secondary me-1" data-bs-toggle="modal" data-bs-target="#editModal{{ $j->id_jenis_tagihan }}" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    @if($j->tagihan_count == 0)
                                        <form action="{{ route('bendahara.jenis-tagihan.destroy', $j->id_jenis_tagihan) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus jenis tagihan ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>

                            <!-- Modal Edit -->
                            <div class="modal fade" id="editModal{{ $j->id_jenis_tagihan }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <form action="{{ route('bendahara.jenis-tagihan.update', $j->id_jenis_tagihan) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-header">
                                                <h6 class="modal-title fw-bold">Edit Jenis Tagihan</h6>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label class="form-label small fw-semibold">Nama Tagihan</label>
                                                    <input type="text" name="nama_tagihan" class="form-control" value="{{ $j->nama_tagihan }}" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label small fw-semibold">Tipe</label>
                                                    <select name="tipe" class="form-select" required>
                                                        <option value="bulanan" {{ $j->tipe == 'bulanan' ? 'selected' : '' }}>Bulanan</option>
                                                        <option value="insidental" {{ $j->tipe == 'insidental' ? 'selected' : '' }}>Insidental</option>
                                                    </select>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label small fw-semibold">Nominal Standar (Rp)</label>
                                                    <input type="number" name="nominal_standar" class="form-control" value="{{ (int)$j->nominal_standar }}" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label small fw-semibold">Keterangan</label>
                                                    <textarea name="keterangan" class="form-control" rows="2">{{ $j->keterangan }}</textarea>
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
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">Belum ada master tagihan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

