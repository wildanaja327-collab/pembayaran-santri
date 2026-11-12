@extends('layouts.app')

@section('title', 'Manajemen Tahun Ajaran')
@section('page_title', 'Manajemen Tahun Ajaran & Siklus Akademik')

@section('content')
<div class="row g-4">
    <!-- Tabel Daftar Tahun Ajaran -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-header bg-white py-3 border-0 d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="mb-0 fw-bold"><i class="bi bi-calendar-range text-primary me-2"></i>Daftar Periode Tahun Ajaran</h6>
                    <small class="text-muted">Hanya satu tahun ajaran yang berstatus aktif sebagai acuan penerbitan tagihan.</small>
                </div>
                <button type="button" class="btn btn-primary btn-sm fw-semibold" data-bs-toggle="modal" data-bs-target="#modalTambahTahun">
                    <i class="bi bi-plus-circle me-1"></i> Tambah Periode
                </button>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr class="small text-muted text-uppercase">
                            <th>Tahun Ajaran</th>
                            <th>Semester</th>
                            <th>Rentang Waktu</th>
                            <th>Status Siklus</th>
                            <th class="text-center">Aksi & Kontrol</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tahunAjarans as $ta)
                            <tr class="{{ $ta->is_aktif ? 'table-success bg-opacity-10' : '' }}">
                                <td>
                                    <span class="fw-bold text-dark fs-6">{{ $ta->nama_tahun_ajaran }}</span>
                                </td>
                                <td>
                                    <span class="badge {{ $ta->semester == 'Ganjil' ? 'bg-primary' : 'bg-info text-dark' }}">
                                        Semester {{ $ta->semester }}
                                    </span>
                                </td>
                                <td class="small text-muted">
                                    @if($ta->tgl_mulai && $ta->tgl_selesai)
                                        {{ $ta->tgl_mulai->format('d M Y') }} s/d {{ $ta->tgl_selesai->format('d M Y') }}
                                    @else
                                        <span class="fst-italic">- Belum disetel -</span>
                                    @endif
                                </td>
                                <td>
                                    @if($ta->is_aktif)
                                        <span class="badge bg-success px-3 py-2">
                                            <i class="bi bi-check-circle-fill me-1"></i> AKTIF SEKARANG
                                        </span>
                                    @else
                                        <span class="badge bg-secondary-subtle text-secondary px-2 py-1">
                                            Tidak Aktif
                                        </span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-1">
                                        @if(!$ta->is_aktif)
                                            <form action="{{ route('admin.tahun-ajaran.set-aktif', $ta->id_tahun_ajaran) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-success fw-semibold" title="Jadikan Tahun Ajaran Aktif">
                                                    <i class="bi bi-check2-circle me-1"></i>Aktifkan
                                                </button>
                                            </form>
                                        @endif

                                        <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#modalEditTahun{{ $ta->id_tahun_ajaran }}" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </button>

                                        @if(!$ta->is_aktif)
                                            <form action="{{ route('admin.tahun-ajaran.destroy', $ta->id_tahun_ajaran) }}" method="POST" onsubmit="return confirm('Hapus periode tahun ajaran ini?')" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">Belum ada data tahun ajaran.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Modals Edit Tahun Ajaran (Di luar table) -->
            @foreach($tahunAjarans as $ta)
                <div class="modal fade" id="modalEditTahun{{ $ta->id_tahun_ajaran }}" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form action="{{ route('admin.tahun-ajaran.update', $ta->id_tahun_ajaran) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="modal-header">
                                    <h6 class="modal-title fw-bold">Edit Tahun Ajaran: {{ $ta->nama_tahun_ajaran }}</h6>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body text-start">
                                    <div class="mb-3">
                                        <label class="form-label small fw-semibold">Nama Tahun Ajaran <span class="text-danger">*</span></label>
                                        <input type="text" name="nama_tahun_ajaran" class="form-control" value="{{ $ta->nama_tahun_ajaran }}" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label small fw-semibold">Semester <span class="text-danger">*</span></label>
                                        <select name="semester" class="form-select" required>
                                            <option value="Ganjil" {{ $ta->semester == 'Ganjil' ? 'selected' : '' }}>Semester Ganjil</option>
                                            <option value="Genap" {{ $ta->semester == 'Genap' ? 'selected' : '' }}>Semester Genap</option>
                                        </select>
                                    </div>
                                    <div class="row g-2 mb-3">
                                        <div class="col-6">
                                            <label class="form-label small fw-semibold">Tanggal Mulai</label>
                                            <input type="date" name="tgl_mulai" class="form-control" value="{{ $ta->tgl_mulai ? $ta->tgl_mulai->format('Y-m-d') : '' }}">
                                        </div>
                                        <div class="col-6">
                                            <label class="form-label small fw-semibold">Tanggal Selesai</label>
                                            <input type="date" name="tgl_selesai" class="form-control" value="{{ $ta->tgl_selesai ? $ta->tgl_selesai->format('Y-m-d') : '' }}">
                                        </div>
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

    <!-- Informasi & Standarisasi -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm rounded-3 mb-3">
            <div class="card-header bg-white py-3 border-0">
                <h6 class="mb-0 fw-bold"><i class="bi bi-info-circle text-info me-2"></i>Peranan Tahun Ajaran</h6>
            </div>
            <div class="card-body small text-muted">
                <p>
                    Pengaturan tahun ajaran aktif digunakan otomatis oleh sistem saat:
                </p>
                <ol class="ps-3 mb-3">
                    <li class="mb-2"><strong>Generator Tagihan Massal:</strong> Menghubungkan tagihan SPP & infaq dengan tahun buku operasional yang sedang berjalan.</li>
                    <li class="mb-2"><strong>Rekapitulasi Keuangan:</strong> Filter laporan pemasukan dan tunggakan berdasarkan siklus akademik santri.</li>
                    <li class="mb-0"><strong>Kwitansi Resmi:</strong> Menampilkan konteks semester pada kuitansi digital pembayaran santri.</li>
                </ol>
                <div class="alert alert-warning mb-0 border-0 small">
                    <i class="bi bi-exclamation-triangle-fill me-1"></i>
                    Mengaktifkan periode baru tidak menghapus riwayat transaksi pada periode-periode sebelumnya.
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah Tahun Ajaran -->
<div class="modal fade" id="modalTambahTahun" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.tahun-ajaran.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h6 class="modal-title fw-bold"><i class="bi bi-plus-circle me-1"></i>Tambah Tahun Ajaran Baru</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-start">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Nama Tahun Ajaran <span class="text-danger">*</span></label>
                        <input type="text" name="nama_tahun_ajaran" class="form-control" placeholder="Contoh: 2026/2027" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Semester <span class="text-danger">*</span></label>
                        <select name="semester" class="form-select" required>
                            <option value="Ganjil">Semester Ganjil (Juli - Desember)</option>
                            <option value="Genap">Semester Genap (Januari - Juni)</option>
                        </select>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-semibold">Tanggal Mulai (Opsional)</label>
                            <input type="date" name="tgl_mulai" class="form-control">
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-semibold">Tanggal Selesai (Opsional)</label>
                            <input type="date" name="tgl_selesai" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm">Simpan Tahun Ajaran</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

