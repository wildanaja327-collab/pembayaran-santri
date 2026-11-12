@extends('layouts.app')

@section('title', 'Rekapan Tunggakan Santri')
@section('page_title', 'Rekapitulasi Santri Menunggak & Piutang')

@section('content')
<div class="card border-0 shadow-sm rounded-3">
    <div class="card-header bg-white py-3 border-0 d-flex flex-wrap align-items-center justify-content-between gap-2">
        <div>
            <h6 class="mb-0 fw-bold text-danger"><i class="bi bi-exclamation-triangle-fill me-2"></i>Daftar Santri Belum Lunas (Tunggakan Piutang)</h6>
            <small class="text-muted" id="tunggakan_header_info">Total Kewajiban Tertunggak: <strong class="text-danger fs-6" id="tunggakan_total_nominal">Rp {{ number_format($totalTunggakan, 0, ',', '.') }}</strong> (<span id="tunggakan_count_label">{{ $tunggakan->count() }}</span> catatan tagihan)</small>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <!-- Dropdown Export Excel & CSV -->
            <div class="btn-group shadow-sm">
                <a href="{{ route('bendahara.laporan.tunggakan.excel', request()->query()) }}" id="btn_export_excel" class="btn btn-success btn-sm fw-semibold">
                    <i class="bi bi-file-earmark-excel-fill me-1"></i> Export Excel (.xls)
                </a>
                <button type="button" class="btn btn-success btn-sm dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-expanded="false" title="Pilih Format Lain">
                    <span class="visually-hidden">Pilih Format Unduh</span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow border-0 small">
                    <li>
                        <a class="dropdown-item py-2 d-flex align-items-center gap-2" href="{{ route('bendahara.laporan.tunggakan.excel', request()->query()) }}" id="btn_export_excel_dropdown">
                            <i class="bi bi-file-earmark-excel text-success fs-5"></i>
                            <div>
                                <div class="fw-bold">Microsoft Excel (.xls)</div>
                                <div class="text-muted" style="font-size: 0.72rem;">Format spreadsheet rapi berkolom &amp; berwana</div>
                            </div>
                        </a>
                    </li>
                    <li><hr class="dropdown-divider my-1"></li>
                    <li>
                        <a class="dropdown-item py-2 d-flex align-items-center gap-2" href="{{ route('bendahara.laporan.tunggakan.csv', request()->query()) }}" id="btn_export_csv">
                            <i class="bi bi-filetype-csv text-primary fs-5"></i>
                            <div>
                                <div class="fw-bold">Format CSV (.csv)</div>
                                <div class="text-muted" style="font-size: 0.72rem;">Format teks koma terpisah (Universal)</div>
                            </div>
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Tombol Cetak PDF / Print -->
            <a href="{{ route('bendahara.laporan.tunggakan.cetak', request()->query()) }}" id="btn_cetak_pdf" target="_blank" class="btn btn-outline-danger btn-sm fw-semibold shadow-sm">
                <i class="bi bi-printer me-1"></i> Cetak Rekapan Resmi
            </a>
        </div>
    </div>

    <!-- Filter Form Multi-Kriteria (Live AJAX Search Enabled) -->
    <div class="p-3 border-top border-bottom bg-light">
        <form action="{{ route('bendahara.laporan.tunggakan') }}" method="GET" id="filter_form_tunggakan" class="row g-2 align-items-end">
            <div class="col-md-2">
                <label class="form-label small text-muted mb-1">Cari Santri / NIS</label>
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" name="search" id="input_tunggakan_search" class="form-control border-start-0 ps-0" placeholder="Nama / NIS..." value="{{ request('search') }}" autocomplete="off">
                </div>
            </div>
            <div class="col-md-2">
                <label class="form-label small text-muted mb-1">Tingkat</label>
                <select name="tingkat" class="form-select form-select-sm filter-select">
                    <option value="">-- Semua Tingkat --</option>
                    <option value="SMP" {{ request('tingkat') == 'SMP' ? 'selected' : '' }}>SMP</option>
                    <option value="SMA" {{ request('tingkat') == 'SMA' ? 'selected' : '' }}>SMA</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small text-muted mb-1">Program</label>
                <select name="id_program" class="form-select form-select-sm filter-select">
                    <option value="">-- Semua Program --</option>
                    @foreach($programList as $prg)
                        <option value="{{ $prg->id_program }}" {{ request('id_program') == $prg->id_program ? 'selected' : '' }}>
                            {{ $prg->nama_program }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small text-muted mb-1">Komponen</label>
                <select name="id_jenis_tagihan" class="form-select form-select-sm filter-select">
                    <option value="">-- Semua Komponen --</option>
                    @foreach($jenisTagihanList as $j)
                        <option value="{{ $j->id_jenis_tagihan }}" {{ request('id_jenis_tagihan') == $j->id_jenis_tagihan ? 'selected' : '' }}>
                            {{ $j->nama_tagihan }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small text-muted mb-1">Tahun Ajaran</label>
                <select name="tahun_ajaran" class="form-select form-select-sm filter-select">
                    <option value="">-- Semua Periode --</option>
                    @foreach($tahunAjaranList as $ta)
                        <option value="{{ $ta->nama_tahun_ajaran }}" {{ request('tahun_ajaran') == $ta->nama_tahun_ajaran ? 'selected' : '' }}>
                            {{ $ta->nama_tahun_ajaran }} ({{ $ta->semester }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small text-muted mb-1">Status Alasan Tunggakan</label>
                <select name="kategori_tunggakan" class="form-select form-select-sm filter-select">
                    <option value="">-- Semua Status Alasan --</option>
                    <option value="belum_ada" {{ request('kategori_tunggakan') == 'belum_ada' ? 'selected' : '' }}>⚠️ Belum Ada Catatan</option>
                    @foreach(\App\Models\Tagihan::KATEGORI_TUNGGAKAN as $key => $kat)
                        <option value="{{ $key }}" {{ request('kategori_tunggakan') == $key ? 'selected' : '' }}>
                            {{ $kat['label'] }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 d-flex justify-content-end gap-1 mt-1">
                <button type="button" id="btn_reset_tunggakan" class="btn btn-outline-secondary btn-sm" title="Reset Filter">
                    <i class="bi bi-arrow-counterclockwise me-1"></i> Reset Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Wrapper Table & Live AJAX Target -->
    <div id="tunggakan_table_wrapper" class="position-relative">
        <!-- Loading Overlay -->
        <div id="tunggakan_loading_overlay" class="position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center bg-white bg-opacity-75" style="display: none !important; z-index: 10;">
            <div class="text-center p-3">
                <div class="spinner-border spinner-border-sm text-danger mb-1" role="status"></div>
                <div class="small fw-semibold text-muted">Menyaring data tunggakan...</div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr class="small text-muted text-uppercase">
                        <th>No</th>
                        <th>Santri & NIS</th>
                        <th>Tingkat / Kelas</th>
                        <th>Program Unggulan</th>
                        <th>Komponen Tagihan</th>
                        <th>Jatuh Tempo</th>
                        <th class="text-end">Sisa Tunggakan</th>
                        <th>Alasan & Catatan Belum Bayar</th>
                        <th>Wali & Kontak</th>
                        <th class="text-center">Aksi Cepat</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tunggakan as $index => $t)
                        <tr id="row_tunggakan_{{ $t->id_tagihan }}">
                            <td>{{ $index + 1 }}</td>
                            <td>
                                <div class="fw-bold text-dark">{{ $t->santri->nama_santri ?? '-' }}</div>
                                <small class="text-muted">NIS: {{ $t->nis }}</small>
                            </td>
                            <td>
                                <span class="badge {{ ($t->santri->tingkat ?? '') == 'SMP' ? 'badge-smp' : 'badge-sma' }}">
                                    {{ $t->santri->tingkat ?? '-' }}
                                </span>
                                <div class="small text-muted">{{ $t->santri->kelas ?? '-' }}</div>
                            </td>
                            <td>
                                <small class="text-muted">{{ $t->santri->program->nama_program ?? '-' }}</small>
                            </td>
                            <td>
                                <div class="fw-semibold">{{ $t->jenisTagihan->nama_tagihan ?? '-' }}</div>
                                <small class="text-muted">{{ $t->bulan ? $t->nama_bulan . ' ' : '' }}{{ $t->tahun_ajaran }}</small>
                            </td>
                            <td>
                                @if($t->jatuh_tempo && strtotime($t->jatuh_tempo) < time())
                                    <span class="badge bg-danger">
                                        <i class="bi bi-exclamation-circle me-1"></i>Lewat: {{ date('d/m/Y', strtotime($t->jatuh_tempo)) }}
                                    </span>
                                @elseif($t->jatuh_tempo && strtotime($t->jatuh_tempo) <= strtotime('+7 days'))
                                    <span class="badge bg-warning text-dark">
                                        <i class="bi bi-clock me-1"></i>{{ date('d/m/Y', strtotime($t->jatuh_tempo)) }}
                                    </span>
                                @elseif($t->jatuh_tempo)
                                    <span class="small text-muted">{{ date('d/m/Y', strtotime($t->jatuh_tempo)) }}</span>
                                @else
                                    <span class="small text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-end fw-bold text-danger fs-6">Rp {{ number_format($t->sisa_tagihan, 0, ',', '.') }}</td>
                            <td style="min-width: 220px;">
                                <div id="badge_alasan_{{ $t->id_tagihan }}">
                                    {!! $t->kategori_badge !!}
                                </div>
                                <div class="small text-dark mt-1" id="text_alasan_{{ $t->id_tagihan }}" style="font-size: 0.8rem; line-height: 1.35;">
                                    @if($t->alasan_tunggakan)
                                        {{ $t->alasan_tunggakan }}
                                    @else
                                        <span class="text-muted fst-italic">Belum ada keterangan</span>
                                    @endif
                                </div>
                                <div class="small text-warning-emphasis fw-semibold mt-1 {{ $t->tgl_janji_bayar ? '' : 'd-none' }}" id="tgl_janji_{{ $t->id_tagihan }}" style="font-size: 0.75rem;">
                                    <i class="bi bi-calendar-event me-1"></i>Janji Bayar: <span class="tgl-val">{{ $t->tgl_janji_bayar ? date('d/m/Y', strtotime($t->tgl_janji_bayar)) : '' }}</span>
                                </div>
                                <div class="small text-primary mt-1 {{ $t->catatan_bendahara ? '' : 'd-none' }}" id="catatan_bendahara_{{ $t->id_tagihan }}" style="font-size: 0.74rem;">
                                    <i class="bi bi-patch-check-fill me-1"></i>Tanggapan: <span class="catatan-val text-dark">{{ $t->catatan_bendahara ?? '' }}</span>
                                </div>
                            </td>
                            <td>
                                <div class="small fw-semibold">{{ $t->santri->wali->nama_lengkap ?? '-' }}</div>
                                @if($t->santri->wali?->no_telp)
                                    @php
                                        $pesanWa = urlencode("Assalamu'alaikum Wr. Wb. Yth. Wali dari ananda {$t->santri->nama_santri}, mengingatkan bahwa terdapat kewajiban pembayaran {$t->jenisTagihan->nama_tagihan} periode {$t->tahun_ajaran} sebesar Rp " . number_format($t->sisa_tagihan, 0, ',', '.') . " yang belum diselesaikan. Pembayaran dapat dilakukan via Virtual Account portal santri atau kasir bendahara. Syukron. - Bendahara PP Darul Muttaqin");
                                    @endphp
                                    <a href="https://wa.me/{{ preg_replace('/^0/', '62', $t->santri->wali->no_telp) }}?text={{ $pesanWa }}" target="_blank" class="btn btn-outline-success btn-sm py-0 px-2 mt-1" style="font-size: 0.72rem;">
                                        <i class="bi bi-whatsapp me-1"></i> WA Wali
                                    </a>
                                @else
                                    <small class="text-muted">-</small>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-1">
                                    <button type="button" 
                                            class="btn btn-sm btn-outline-warning btn-catat-alasan" 
                                            data-id="{{ $t->id_tagihan }}"
                                            data-santri="{{ $t->santri->nama_santri ?? '-' }}"
                                            data-nis="{{ $t->nis }}"
                                            data-tagihan="{{ $t->jenisTagihan->nama_tagihan ?? '-' }}"
                                            data-periode="{{ $t->bulan ? $t->nama_bulan . ' ' : '' }}{{ $t->tahun_ajaran }}"
                                            data-sisa="Rp {{ number_format($t->sisa_tagihan, 0, ',', '.') }}"
                                            data-kategori="{{ $t->kategori_tunggakan ?? '' }}"
                                            data-alasan="{{ $t->alasan_tunggakan ?? '' }}"
                                            data-tgl-janji="{{ $t->tgl_janji_bayar ?? '' }}"
                                            data-catatan="{{ $t->catatan_bendahara ?? '' }}"
                                            title="Catat / Ubah Alasan Belum Bayar">
                                        <i class="bi bi-chat-left-text-fill"></i>
                                    </button>
                                    <a href="{{ route('bendahara.tagihan.show', $t->id_tagihan) }}" class="btn btn-sm btn-outline-primary" title="Rincian Tagihan & Cicilan">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('bendahara.pembayaran.kasir.create', ['id_tagihan' => $t->id_tagihan]) }}" class="btn btn-sm btn-success" title="Terima Bayar Tunai Kasir">
                                        <i class="bi bi-cash-stack"></i>
                                    </a>
                                    @if($t->santri->wali?->no_telp || $t->santri?->no_telp)
                                        <form action="{{ route('bendahara.tagihan.kirim-reminder-wa', $t->id_tagihan) }}" method="POST" class="d-inline" onsubmit="return confirm('Kirim pesan pengingat tunggakan otomatis via WhatsApp Gateway ke wali santri ({{ $t->santri->wali->nama_lengkap ?? $t->santri->nama_santri }})?');">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-success" title="Kirim Pengingat Tagihan via WhatsApp Gateway">
                                                <i class="bi bi-bell-fill"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center py-5 text-muted">
                                <i class="bi bi-check2-circle fs-2 text-success d-block mb-2"></i>
                                Alhamdulillah! Tidak ada tunggakan santri yang ditemukan sesuai pencarian/filter yang dipilih.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                @if($tunggakan->isNotEmpty())
                    <tfoot class="table-light fw-bold">
                        <tr>
                            <td colspan="6" class="text-end text-uppercase">Total Seluruh Piutang / Tunggakan:</td>
                            <td class="text-end text-danger fs-5">Rp {{ number_format($totalTunggakan, 0, ',', '.') }}</td>
                            <td colspan="3"></td>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL: CATAT ALASAN TUNGGAKAN & DISPENSASI (AJAX ENABLED)                -->
<!-- ========================================================================= -->
<div class="modal fade" id="modalAlasanTunggakan" tabindex="-1" aria-labelledby="modalAlasanTunggakanLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4">
            <div class="modal-header bg-light py-3 border-bottom">
                <div>
                    <h6 class="modal-title fw-bold text-dark mb-0" id="modalAlasanTunggakanLabel">
                        <i class="bi bi-journal-text text-warning me-2"></i>Catat Alasan Tunggakan & Dispensasi
                    </h6>
                    <small class="text-muted">Pencatatan alasan belum bayar santri untuk arsip & laporan pimpinan</small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formAlasanTunggakan" method="POST" action="">
                @csrf
                <div class="modal-body p-4">
                    <!-- Info Santri & Tagihan (Readonly Context) -->
                    <div class="p-3 bg-light rounded-3 border mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="text-muted small">Santri:</span>
                            <span class="fw-bold text-dark" id="modal_santri_nama">-</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="text-muted small">NIS:</span>
                            <span class="font-monospace text-muted small" id="modal_santri_nis">-</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="text-muted small">Komponen:</span>
                            <span class="fw-semibold text-primary" id="modal_tagihan_nama">-</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted small">Sisa Tunggakan:</span>
                            <span class="fw-bold text-danger fs-6" id="modal_sisa_nominal">-</span>
                        </div>
                    </div>

                    <!-- Kategori Alasan (Pilihan Cepat) -->
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark">
                            <i class="bi bi-tag-fill text-primary me-1"></i>Kategori Alasan / Kondisi
                        </label>
                        <select name="kategori_tunggakan" id="modal_kategori_select" class="form-select form-select-sm" required>
                            <option value="">-- Pilih Kategori Alasan --</option>
                            @foreach(\App\Models\Tagihan::KATEGORI_TUNGGAKAN as $key => $kat)
                                <option value="{{ $key }}" data-deskripsi="{{ $kat['deskripsi'] }}">
                                    {{ $kat['label'] }}
                                </option>
                            @endforeach
                        </select>
                        <div class="form-text small text-muted" id="modal_kategori_deskripsi" style="font-size: 0.75rem;">
                            Pilih klasifikasi alasan yang paling sesuai dengan kondisi wali santri.
                        </div>
                    </div>

                    <!-- Tanggal Janji Bayar -->
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark">
                            <i class="bi bi-calendar-event text-warning me-1"></i>Tanggal Janji Bayar / Tenggat Toleransi (Opsional)
                        </label>
                        <input type="date" name="tgl_janji_bayar" id="modal_tgl_janji" class="form-control form-control-sm">
                        <div class="form-text small text-muted" style="font-size: 0.75rem;">
                            Isi jika wali santri telah berkomitmen melunasi pada tanggal tertentu.
                        </div>
                    </div>

                    <!-- Alasan / Penjelasan Rinci -->
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark">
                            <i class="bi bi-chat-left-dots text-info me-1"></i>Keterangan Alasan (Tercetak di Laporan Resmi)
                        </label>
                        <input type="text" name="alasan_tunggakan" id="modal_alasan_text" class="form-control form-control-sm" placeholder="Contoh: Menunggu panen padi pertengahan bulan / Pengajuan dispensasi keringanan" maxlength="255">
                        <div class="form-text small text-muted" style="font-size: 0.75rem;">
                            Keterangan ringkas yang akan tercetak pada laporan resmi pimpinan & ekspor Excel.
                        </div>
                    </div>

                    <!-- Catatan Tindak Lanjut / Tanggapan Bendahara -->
                    <div class="mb-2">
                        <label class="form-label small fw-bold text-dark">
                            <i class="bi bi-patch-check-fill text-primary me-1"></i>Catatan Tindak Lanjut / Tanggapan Bendahara (Opsional)
                        </label>
                        <textarea name="catatan_bendahara" id="modal_catatan_internal" rows="2" class="form-control form-control-sm" placeholder="Contoh: Disetujui penundaan s/d tanggal 25 / Telah dikonfirmasi dengan wali santri"></textarea>
                        <div class="form-text small text-muted" style="font-size: 0.75rem;">
                            Tanggapan ini juga akan tampil di halaman tagihan akun Wali Santri.
                        </div>
                    </div>

                    <!-- Alert message container in modal -->
                    <div id="modal_alert_container" class="mt-2" style="display: none;"></div>
                </div>
                <div class="modal-footer bg-light py-2">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" id="btn_simpan_alasan" class="btn btn-primary btn-sm fw-semibold shadow-sm">
                        <i class="bi bi-save me-1"></i> Simpan Catatan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    let searchTimer = null;
    const form = document.getElementById('filter_form_tunggakan');
    const searchInput = document.getElementById('input_tunggakan_search');
    const wrapper = document.getElementById('tunggakan_table_wrapper');
    const overlay = document.getElementById('tunggakan_loading_overlay');
    const resetBtn = document.getElementById('btn_reset_tunggakan');

    function syncExportUrls() {
        if (!form) return;
        const formData = new FormData(form);
        const params = new URLSearchParams();
        for (const [key, val] of formData.entries()) {
            if (val.trim() !== '') {
                params.append(key, val.trim());
            }
        }
        const qs = params.toString() ? '?' + params.toString() : '';
        const excelBtn = document.getElementById('btn_export_excel');
        const excelDrop = document.getElementById('btn_export_excel_dropdown');
        const csvBtn = document.getElementById('btn_export_csv');
        const pdfBtn = document.getElementById('btn_cetak_pdf');

        const baseExcel = "{{ route('bendahara.laporan.tunggakan.excel') }}";
        const baseCsv = "{{ route('bendahara.laporan.tunggakan.csv') }}";
        const basePdf = "{{ route('bendahara.laporan.tunggakan.cetak') }}";

        if (excelBtn) excelBtn.setAttribute('href', baseExcel + qs);
        if (excelDrop) excelDrop.setAttribute('href', baseExcel + qs);
        if (csvBtn) csvBtn.setAttribute('href', baseCsv + qs);
        if (pdfBtn) pdfBtn.setAttribute('href', basePdf + qs);
    }

    function buildFilterUrl() {
        const formData = new FormData(form);
        const params = new URLSearchParams();
        for (const [key, val] of formData.entries()) {
            if (val.trim() !== '') {
                params.append(key, val.trim());
            }
        }
        return form.action + (params.toString() ? '?' + params.toString() : '');
    }

    function fetchTunggakan(url) {
        if (overlay) overlay.style.setProperty('display', 'flex', 'important');
        syncExportUrls();

        fetch(url, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => {
            if (!res.ok) throw new Error('HTTP ' + res.status);
            return res.text();
        })
        .then(html => {
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const newWrapper = doc.getElementById('tunggakan_table_wrapper');
            const newHeader = doc.getElementById('tunggakan_header_info');

            if (newWrapper && wrapper) {
                wrapper.innerHTML = newWrapper.innerHTML;
            }
            if (newHeader && document.getElementById('tunggakan_header_info')) {
                document.getElementById('tunggakan_header_info').innerHTML = newHeader.innerHTML;
            }

            syncExportUrls();
            window.history.pushState({}, '', url);
        })
        .catch(err => {
            console.error('AJAX Load tunggakan error:', err);
        })
        .finally(() => {
            const activeOverlay = document.getElementById('tunggakan_loading_overlay');
            if (activeOverlay) activeOverlay.style.setProperty('display', 'none', 'important');
        });
    }

    syncExportUrls();

    if (searchInput) {
        searchInput.addEventListener('input', function () {
            syncExportUrls();
            clearTimeout(searchTimer);
            searchTimer = setTimeout(() => {
                fetchTunggakan(buildFilterUrl());
            }, 300);
        });
    }

    const filterSelects = form.querySelectorAll('.filter-select');
    filterSelects.forEach(sel => {
        sel.addEventListener('change', function () {
            syncExportUrls();
            fetchTunggakan(buildFilterUrl());
        });
    });

    if (resetBtn) {
        resetBtn.addEventListener('click', function () {
            form.reset();
            if (searchInput) searchInput.value = '';
            syncExportUrls();
            fetchTunggakan(form.action);
        });
    }

    window.addEventListener('popstate', function () {
        fetchTunggakan(window.location.href);
    });

    // =========================================================================
    // MODAL & AJAX LOGIC: CATAT ALASAN TUNGGAKAN
    // =========================================================================
    const modalEl = document.getElementById('modalAlasanTunggakan');
    let alasanModal = null;
    if (modalEl && typeof bootstrap !== 'undefined') {
        alasanModal = new bootstrap.Modal(modalEl);
    }

    const formAlasan = document.getElementById('formAlasanTunggakan');
    const modalAlert = document.getElementById('modal_alert_container');
    const btnSimpanAlasan = document.getElementById('btn_simpan_alasan');
    let currentTagihanId = null;

    // Delegasi klik tombol catat alasan (mendukung live AJAX render)
    document.addEventListener('click', function (e) {
        const btn = e.target.closest('.btn-catat-alasan');
        if (!btn) return;

        currentTagihanId = btn.dataset.id;
        document.getElementById('modal_santri_nama').textContent = btn.dataset.santri || '-';
        document.getElementById('modal_santri_nis').textContent = btn.dataset.nis || '-';
        document.getElementById('modal_tagihan_nama').textContent = (btn.dataset.tagihan || '-') + ' (' + (btn.dataset.periode || '-') + ')';
        document.getElementById('modal_sisa_nominal').textContent = btn.dataset.sisa || '-';

        const kategoriSelect = document.getElementById('modal_kategori_select');
        kategoriSelect.value = btn.dataset.kategori || '';

        document.getElementById('modal_alasan_text').value = btn.dataset.alasan || '';
        document.getElementById('modal_tgl_janji').value = btn.dataset.tglJanji || '';
        document.getElementById('modal_catatan_internal').value = btn.dataset.catatan || '';

        updateKategoriDesc();

        if (modalAlert) {
            modalAlert.style.display = 'none';
            modalAlert.innerHTML = '';
        }

        formAlasan.action = '/bendahara/tagihan/' + currentTagihanId + '/update-alasan';

        if (!alasanModal && typeof bootstrap !== 'undefined') {
            alasanModal = new bootstrap.Modal(modalEl);
        }
        if (alasanModal) alasanModal.show();
    });

    const modalKategoriSelect = document.getElementById('modal_kategori_select');
    if (modalKategoriSelect) {
        modalKategoriSelect.addEventListener('change', updateKategoriDesc);
    }

    function updateKategoriDesc() {
        const sel = document.getElementById('modal_kategori_select');
        const descEl = document.getElementById('modal_kategori_deskripsi');
        if (!sel || !descEl) return;
        const opt = sel.options[sel.selectedIndex];
        if (opt && opt.dataset.deskripsi) {
            descEl.textContent = opt.dataset.deskripsi;
        } else {
            descEl.textContent = 'Pilih klasifikasi alasan yang paling sesuai dengan kondisi wali santri.';
        }
    }

    // Submit AJAX Form Alasan
    if (formAlasan) {
        formAlasan.addEventListener('submit', function (e) {
            e.preventDefault();
            if (!currentTagihanId) return;

            btnSimpanAlasan.disabled = true;
            btnSimpanAlasan.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status"></span> Menyimpan...';
            if (modalAlert) modalAlert.style.display = 'none';

            const formData = new FormData(formAlasan);

            fetch(formAlasan.action, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    // Update tampilan row tabel
                    const badgeContainer = document.getElementById('badge_alasan_' + currentTagihanId);
                    const textContainer = document.getElementById('text_alasan_' + currentTagihanId);
                    const tglContainer = document.getElementById('tgl_janji_' + currentTagihanId);

                    if (badgeContainer) badgeContainer.innerHTML = data.data.kategori_badge;
                    if (textContainer) {
                        textContainer.innerHTML = data.data.alasan_tunggakan 
                            ? data.data.alasan_tunggakan 
                            : '<span class="text-muted fst-italic">Belum ada keterangan</span>';
                    }
                    if (tglContainer) {
                        if (data.data.tgl_janji_bayar) {
                            tglContainer.classList.remove('d-none');
                            const spanVal = tglContainer.querySelector('.tgl-val');
                            if (spanVal) spanVal.textContent = data.data.tgl_janji_bayar;
                        } else {
                            tglContainer.classList.add('d-none');
                        }
                    }
                    const catatanContainer = document.getElementById('catatan_bendahara_' + currentTagihanId);
                    if (catatanContainer) {
                        if (data.data.catatan_bendahara) {
                            catatanContainer.classList.remove('d-none');
                            const spanCat = catatanContainer.querySelector('.catatan-val');
                            if (spanCat) spanCat.textContent = data.data.catatan_bendahara;
                        } else {
                            catatanContainer.classList.add('d-none');
                        }
                    }

                    // Update data-attributes pada tombol
                    const btn = document.querySelector(`.btn-catat-alasan[data-id="${currentTagihanId}"]`);
                    if (btn) {
                        btn.dataset.kategori = data.data.kategori_tunggakan || '';
                        btn.dataset.alasan = data.data.alasan_tunggakan || '';
                        btn.dataset.tglJanji = data.data.tgl_janji_bayar_raw || '';
                        btn.dataset.catatan = data.data.catatan_bendahara || '';
                    }

                    // Tutup modal
                    if (alasanModal) alasanModal.hide();

                    // Tampilkan notifikasi toast sukses mengambang
                    const successToast = document.createElement('div');
                    successToast.className = 'alert alert-success alert-dismissible fade show shadow position-fixed bottom-0 end-0 m-4';
                    successToast.style.zIndex = '9999';
                    successToast.innerHTML = `
                        <i class="bi bi-check-circle-fill me-2"></i> ${data.message}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    `;
                    document.body.appendChild(successToast);
                    setTimeout(() => successToast.remove(), 4000);
                } else {
                    throw new Error(data.message || 'Terjadi kesalahan saat menyimpan.');
                }
            })
            .catch(err => {
                console.error(err);
                if (modalAlert) {
                    modalAlert.style.display = 'block';
                    modalAlert.className = 'alert alert-danger py-2 px-3 small rounded-3 mt-3';
                    modalAlert.innerHTML = '<i class="bi bi-exclamation-triangle-fill me-1"></i> ' + (err.message || 'Gagal menyimpan catatan alasan.');
                }
            })
            .finally(() => {
                btnSimpanAlasan.disabled = false;
                btnSimpanAlasan.innerHTML = '<i class="bi bi-save me-1"></i> Simpan Catatan';
            });
        });
    }
});
</script>
@endpush
