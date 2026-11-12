@extends('layouts.app')

@section('title', 'Rincian Tagihan & Riwayat Pembayaran')
@section('page_title', 'Rincian Tagihan & Riwayat Cicilan Santri')

@section('content')
<div class="mb-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
    <a href="{{ route('bendahara.tagihan.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar Tagihan
    </a>
    <div class="d-flex align-items-center gap-2">
        @if($tagihan->status_tagihan != 'lunas')
            <form action="{{ route('bendahara.tagihan.kirim-reminder-wa', $tagihan->id_tagihan) }}" method="POST" class="d-inline" onsubmit="return confirm('Kirim pesan pengingat tagihan resmi via WhatsApp Gateway ke wali santri?');">
                @csrf
                <button type="submit" class="btn btn-outline-success btn-sm fw-semibold shadow-sm">
                    <i class="bi bi-whatsapp me-1"></i> Kirim Pengingat WA
                </button>
            </form>
            <a href="{{ route('bendahara.pembayaran.kasir.create', ['id_tagihan' => $tagihan->id_tagihan]) }}" class="btn btn-success btn-sm fw-semibold shadow-sm">
                <i class="bi bi-cash-stack me-1"></i> Input Pembayaran Kasir Tunai
            </a>
        @endif
    </div>
</div>

<div class="row g-4">
    <!-- Kolom Kiri: Profil Santri & Status Finansial -->
    <div class="col-lg-4">
        <!-- Kartu Identitas Santri -->
        <div class="card border-0 shadow-sm rounded-3 mb-4">
            <div class="card-header bg-white py-3 border-0">
                <h6 class="mb-0 fw-bold"><i class="bi bi-person-badge text-primary me-2"></i>Informasi Santri</h6>
            </div>
            <div class="card-body">
                <div class="text-center mb-3">
                    <div class="bg-primary-subtle text-primary rounded-circle d-inline-flex align-items-center justify-content-center mb-2" style="width: 64px; height: 64px;">
                        <i class="bi bi-person fs-1"></i>
                    </div>
                    <h5 class="fw-bold mb-0 text-dark">{{ $tagihan->santri->nama_santri ?? '-' }}</h5>
                    <small class="text-muted">NIS: <code>{{ $tagihan->nis }}</code></small>
                </div>

                <ul class="list-group list-group-flush small">
                    <li class="list-group-item d-flex justify-content-between px-0">
                        <span class="text-muted">Tingkat & Kelas</span>
                        <span class="fw-semibold">{{ $tagihan->santri->tingkat ?? '-' }} - {{ $tagihan->santri->kelas ?? '-' }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between px-0">
                        <span class="text-muted">Program Unggulan</span>
                        <span class="fw-semibold text-primary">{{ $tagihan->santri->program->nama_program ?? '-' }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between px-0">
                        <span class="text-muted">Nama Wali</span>
                        <span class="fw-semibold">{{ $tagihan->santri->wali->nama_lengkap ?? '-' }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between px-0">
                        <span class="text-muted">Kontak Wali</span>
                        <span>
                            @if($tagihan->santri->wali?->no_telp)
                                <a href="https://wa.me/{{ preg_replace('/^0/', '62', $tagihan->santri->wali->no_telp) }}" target="_blank" class="text-success text-decoration-none fw-semibold">
                                    <i class="bi bi-whatsapp me-1"></i>{{ $tagihan->santri->wali->no_telp }}
                                </a>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </span>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Kartu Ringkasan Status Tagihan -->
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-header bg-white py-3 border-0">
                <h6 class="mb-0 fw-bold"><i class="bi bi-calculator text-success me-2"></i>Status Rekapitulasi Tagihan</h6>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text-muted small">Status Saat Ini</span>
                    <div>{!! $tagihan->status_badge !!}</div>
                </div>

                <div class="mb-2">
                    <div class="d-flex justify-content-between small mb-1">
                        <span class="text-muted">Progres Pelunasan</span>
                        @php
                            $persenLunas = $tagihan->nominal_tagihan > 0 ? round(($tagihan->total_terbayar / $tagihan->nominal_tagihan) * 100) : 0;
                        @endphp
                        <strong class="text-success">{{ $persenLunas }}%</strong>
                    </div>
                    <div class="progress" style="height: 10px;">
                        <div class="progress-bar bg-success" role="progressbar" style="width: {{ $persenLunas }}%"></div>
                    </div>
                </div>

                <hr class="my-3 text-muted">

                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted small">Nominal Pokok:</span>
                    <strong class="text-dark">Rp {{ number_format($tagihan->nominal_tagihan, 0, ',', '.') }}</strong>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted small">Total Telah Dibayar:</span>
                    <strong class="text-success">Rp {{ number_format($tagihan->total_terbayar, 0, ',', '.') }}</strong>
                </div>
                <div class="d-flex justify-content-between mb-3">
                    <span class="text-muted small">Sisa Kekurangan:</span>
                    <strong class="text-danger fs-6">Rp {{ number_format($tagihan->sisa_tagihan, 0, ',', '.') }}</strong>
                </div>

                <div class="p-2 bg-light rounded text-center small text-muted mb-3">
                    <i class="bi bi-calendar-event me-1"></i> Jatuh Tempo: 
                    <strong>{{ $tagihan->jatuh_tempo ? date('d F Y', strtotime($tagihan->jatuh_tempo)) : 'Tidak Ditentukan' }}</strong>
                </div>

                @if($tagihan->status_tagihan != 'lunas')
                    <div class="border rounded-3 p-3 bg-light">
                        <div class="fw-semibold small text-dark mb-1"><i class="bi bi-whatsapp text-success me-1"></i>Notifikasi Tagihan WhatsApp</div>
                        <p class="text-muted small mb-2" style="font-size: 0.78rem;">Kirimkan surat pengingat rincian tagihan ini langsung ke nomor WhatsApp wali santri via gateway otomatis.</p>
                        <form action="{{ route('bendahara.tagihan.kirim-reminder-wa', $tagihan->id_tagihan) }}" method="POST" onsubmit="return confirm('Kirim pengingat tagihan via WhatsApp Gateway?');">
                            @csrf
                            <button type="submit" class="btn btn-outline-success btn-sm w-100 fw-semibold">
                                <i class="bi bi-send me-1"></i> Kirim Reminder WA Gateway
                            </button>
                        </form>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Kolom Kanan: Rincian Tagihan & Riwayat Transaksi / Cicilan -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-3 mb-4">
            <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="mb-0 fw-bold"><i class="bi bi-receipt-cutoff text-primary me-2"></i>Informasi Komponen Kewajiban</h6>
                    <small class="text-muted">Detail jenis tagihan dan tahun ajaran akademik</small>
                </div>
                <span class="badge bg-light text-dark border font-monospace">ID Tagihan #{{ $tagihan->id_tagihan }}</span>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="small text-muted d-block">Komponen Pembayaran</label>
                        <h6 class="fw-bold text-primary mb-0">{{ $tagihan->jenisTagihan->nama_tagihan ?? '-' }}</h6>
                        <small class="text-muted">{{ $tagihan->jenisTagihan->keterangan ?? '-' }}</small>
                    </div>
                    <div class="col-md-3">
                        <label class="small text-muted d-block">Tahun Ajaran</label>
                        <div class="fw-bold text-dark">{{ $tagihan->tahun_ajaran }}</div>
                        <small class="badge bg-secondary">{{ ucfirst($tagihan->jenisTagihan->tipe ?? 'insidental') }}</small>
                    </div>
                    <div class="col-md-3">
                        <label class="small text-muted d-block">Bulan Tagihan</label>
                        <div class="fw-bold text-dark">{{ $tagihan->bulan ? $tagihan->nama_bulan : 'Non-Bulanan' }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Kartu Catatan Alasan Belum Bayar & Dispensasi -->
        <div class="card border-0 shadow-sm rounded-3 mb-4 {{ $tagihan->status_tagihan == 'lunas' ? 'border-start border-4 border-success' : 'border-start border-4 border-warning' }}">
            <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                    <h6 class="mb-0 fw-bold">
                        <i class="bi bi-journal-text text-warning me-2"></i>Catatan Alasan Belum Bayar &amp; Dispensasi
                    </h6>
                    <small class="text-muted">Keterangan status penundaan, komitmen bayar &amp; alasan wali santri</small>
                </div>
                <button type="button" class="btn btn-warning btn-sm fw-semibold shadow-sm" data-bs-toggle="modal" data-bs-target="#modalEditAlasanShow">
                    <i class="bi bi-pencil-square me-1"></i> {{ $tagihan->kategori_tunggakan ? 'Perbarui Alasan' : 'Catat Alasan' }}
                </button>
            </div>
            <div class="card-body pt-0">
                <div class="row g-3 align-items-center">
                    <div class="col-md-4">
                        <label class="small text-muted d-block mb-1">Klasifikasi Alasan</label>
                        <div>{!! $tagihan->kategori_badge !!}</div>
                    </div>
                    <div class="col-md-4">
                        <label class="small text-muted d-block mb-1">Tanggal Janji Bayar</label>
                        @if($tagihan->tgl_janji_bayar)
                            <div class="fw-bold text-dark">
                                <i class="bi bi-calendar-event text-warning me-1"></i>{{ date('d F Y', strtotime($tagihan->tgl_janji_bayar)) }}
                            </div>
                        @else
                            <span class="text-muted small fst-italic">Tidak ada janji bayar</span>
                        @endif
                    </div>
                    <div class="col-md-4">
                        <label class="small text-muted d-block mb-1">Terakhir Diperbarui</label>
                        <span class="small text-muted">
                            {{ $tagihan->updated_alasan_at ? date('d/m/Y H:i', strtotime($tagihan->updated_alasan_at)) . ' WIB' : '-' }}
                        </span>
                    </div>
                    <div class="col-12">
                        <label class="small text-muted d-block mb-1">Keterangan Alasan (Tercetak di Laporan)</label>
                        <div class="p-2 bg-light rounded small text-dark">
                            {{ $tagihan->alasan_tunggakan ?: 'Belum ada keterangan alasan yang dicatat.' }}
                        </div>
                    </div>
                    @if($tagihan->catatan_bendahara)
                        <div class="col-12">
                            <label class="small text-muted d-block mb-1">Catatan Internal Bendahara</label>
                            <div class="p-2 bg-light-subtle border rounded small text-secondary">
                                <i class="bi bi-lock-fill text-muted me-1"></i>{{ $tagihan->catatan_bendahara }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Tabel Riwayat Pembayaran & Cicilan -->
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="mb-0 fw-bold"><i class="bi bi-clock-history text-success me-2"></i>Riwayat Pembayaran & Pembayaran Bertahap (Cicilan)</h6>
                    <small class="text-muted">Seluruh mutasi setoran cicilan yang tercatat untuk tagihan ini</small>
                </div>
                <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1">
                    {{ $tagihan->pembayaran->count() }} Kali Pembayaran
                </span>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr class="small text-muted text-uppercase">
                            <th>No</th>
                            <th>No Kwitansi</th>
                            <th>Waktu Setoran</th>
                            <th>Metode / Channel</th>
                            <th class="text-end">Jumlah Bayar</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Kwitansi Sah</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tagihan->pembayaran as $index => $p)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    @if($p->kwitansi)
                                        <span class="badge bg-light text-dark border font-monospace fw-bold">{{ $p->kwitansi->no_kwitansi }}</span>
                                    @else
                                        <span class="text-muted small">-</span>
                                    @endif
                                    <div class="small text-muted" style="font-size: 0.72rem;">{{ $p->order_id }}</div>
                                </td>
                                <td>
                                    <div>{{ date('d/m/Y', strtotime($p->tgl_bayar)) }}</div>
                                    <small class="text-muted">{{ date('H:i', strtotime($p->tgl_bayar)) }} WIB</small>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border">
                                        {{ $p->channel_bayar ?? ucfirst($p->metode_bayar) }}
                                    </span>
                                    @if($p->verifier)
                                        <div class="small text-muted mt-1" style="font-size: 0.72rem;">
                                            <i class="bi bi-person-check me-1"></i>{{ $p->verifier->nama_lengkap }}
                                        </div>
                                    @endif
                                </td>
                                <td class="text-end fw-bold text-success fs-6">
                                    Rp {{ number_format($p->jumlah_bayar, 0, ',', '.') }}
                                </td>
                                <td class="text-center">
                                    @if($p->status_transaksi == 'settlement')
                                        <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Settlement</span>
                                    @elseif($p->status_transaksi == 'pending')
                                        <span class="badge bg-warning text-dark"><i class="bi bi-hourglass me-1"></i>Pending</span>
                                    @else
                                        <span class="badge bg-secondary">{{ ucfirst($p->status_transaksi) }}</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($p->kwitansi)
                                        <div class="d-flex justify-content-center gap-1">
                                            <a href="{{ route('kwitansi.show', $p->id_pembayaran) }}" class="btn btn-sm btn-outline-primary" title="Lihat Kwitansi">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="{{ route('kwitansi.cetak', $p->id_pembayaran) }}" target="_blank" class="btn btn-sm btn-outline-success" title="Cetak Kwitansi">
                                                <i class="bi bi-printer"></i>
                                            </a>
                                        </div>
                                    @elseif($p->status_transaksi == 'pending')
                                        <form action="{{ route('bendahara.pembayaran.simulate', $p->id_pembayaran) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success" title="Simulasi Settlement">
                                                <i class="bi bi-play-circle me-1"></i> Settle
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-muted small">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="bi bi-wallet2 fs-2 d-block mb-2 text-muted"></i>
                                    Belum ada catatan setoran pembayaran atau cicilan untuk tagihan ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if($tagihan->pembayaran->where('status_transaksi', 'settlement')->isNotEmpty())
                        <tfoot class="table-light fw-bold">
                            <tr>
                                <td colspan="4" class="text-end text-uppercase">Total Akumulasi Pembayaran:</td>
                                <td class="text-end text-success fs-6">Rp {{ number_format($tagihan->total_terbayar, 0, ',', '.') }}</td>
                                <td colspan="2"></td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Edit Catatan Alasan Tunggakan -->
<div class="modal fade" id="modalEditAlasanShow" tabindex="-1" aria-labelledby="modalEditAlasanShowLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4">
            <div class="modal-header bg-light py-3 border-bottom">
                <div>
                    <h6 class="modal-title fw-bold text-dark mb-0" id="modalEditAlasanShowLabel">
                        <i class="bi bi-journal-text text-warning me-2"></i>Catat Alasan Tunggakan &amp; Dispensasi
                    </h6>
                    <small class="text-muted">Pencatatan alasan belum bayar santri untuk arsip &amp; laporan pimpinan</small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('bendahara.tagihan.update-alasan', $tagihan->id_tagihan) }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <!-- Kategori Alasan -->
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark">
                            <i class="bi bi-tag-fill text-primary me-1"></i>Kategori Alasan / Kondisi
                        </label>
                        <select name="kategori_tunggakan" class="form-select form-select-sm" required>
                            <option value="">-- Pilih Kategori Alasan --</option>
                            @foreach(\App\Models\Tagihan::KATEGORI_TUNGGAKAN as $key => $kat)
                                <option value="{{ $key }}" {{ $tagihan->kategori_tunggakan == $key ? 'selected' : '' }}>
                                    {{ $kat['label'] }} - {{ $kat['deskripsi'] }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Tanggal Janji Bayar -->
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark">
                            <i class="bi bi-calendar-event text-warning me-1"></i>Tanggal Janji Bayar / Toleransi (Opsional)
                        </label>
                        <input type="date" name="tgl_janji_bayar" class="form-control form-control-sm" value="{{ $tagihan->tgl_janji_bayar }}">
                    </div>

                    <!-- Alasan / Penjelasan Rinci -->
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark">
                            <i class="bi bi-chat-left-dots text-info me-1"></i>Keterangan Alasan (Tercetak di Laporan)
                        </label>
                        <input type="text" name="alasan_tunggakan" class="form-control form-control-sm" placeholder="Contoh: Menunggu panen padi pertengahan bulan" value="{{ $tagihan->alasan_tunggakan }}" maxlength="255">
                    </div>

                    <!-- Catatan Internal Bendahara -->
                    <div class="mb-2">
                        <label class="form-label small fw-bold text-dark">
                            <i class="bi bi-lock-fill text-secondary me-1"></i>Catatan Internal Bendahara (Opsional)
                        </label>
                        <textarea name="catatan_bendahara" rows="2" class="form-control form-control-sm" placeholder="Catatan tindak lanjut bendahara...">{{ $tagihan->catatan_bendahara }}</textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light py-2">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm fw-semibold shadow-sm">
                        <i class="bi bi-save me-1"></i> Simpan Catatan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

