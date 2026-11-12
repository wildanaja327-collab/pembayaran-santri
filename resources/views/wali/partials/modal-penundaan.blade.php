{{-- ============================================================ --}}
{{-- MODAL AJUKAN PENUNDAAN / KONFIRMASI KENDALA BAYAR (WALI)    --}}
{{-- ============================================================ --}}
<div class="modal fade" id="modalAjukanPenundaan" tabindex="-1" aria-labelledby="modalAjukanPenundaanLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow-lg">
            <div class="modal-header border-0 pb-0 px-4 pt-4">
                <div>
                    <h5 class="modal-title fw-bold text-dark" id="modalAjukanPenundaanLabel">
                        <i class="bi bi-calendar2-plus-fill text-warning me-2"></i>Ajukan Penundaan Pembayaran
                    </h5>
                    <p class="text-muted small mb-0">
                        Sampaikan kendala atau estimasi tanggal bayar kepada <strong>Bendahara Pondok</strong>
                    </p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>

            <form id="formAjukanPenundaan" method="POST" action="">
                @csrf
                <div class="modal-body px-4 py-3">
                    {{-- Ringkasan Tagihan --}}
                    <div class="p-3 rounded-3 mb-3 border" style="background:#f8fafc;">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="small text-muted">Nama Santri</span>
                            <span class="small fw-bold text-dark" id="penundaan_santri_name">-</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="small text-muted">Komponen Tagihan</span>
                            <span class="small fw-semibold text-primary text-end" id="penundaan_tagihan_name">-</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="small text-muted">Sisa Tagihan</span>
                            <span class="fw-bold text-danger" id="penundaan_sisa_text">-</span>
                        </div>
                    </div>

                    {{-- Tanggapan Bendahara (Muncul jika Bendahara sudah memberi catatan) --}}
                    <div id="penundaan_catatan_bendahara_box" class="p-3 rounded-3 mb-3 d-none" style="background:#eff6ff; border: 1px solid #bfdbfe;">
                        <div class="small fw-bold text-primary mb-1">
                            <i class="bi bi-patch-check-fill me-1"></i>Tanggapan / Catatan Bendahara Pondok:
                        </div>
                        <div class="small text-dark" id="penundaan_catatan_bendahara_text">-</div>
                    </div>

                    {{-- Kategori Kendala --}}
                    <div class="mb-3">
                        <label for="penundaan_kategori" class="form-label small fw-bold text-dark">
                            Kategori Alasan / Kendala <span class="text-danger">*</span>
                        </label>
                        <select name="kategori_tunggakan" id="penundaan_kategori" class="form-select" required>
                            <option value="">-- Pilih Kategori Alasan --</option>
                            @foreach(\App\Models\Tagihan::KATEGORI_TUNGGAKAN as $key => $kat)
                                @if($key !== 'belum_respon')
                                    <option value="{{ $key }}" data-deskripsi="{{ $kat['deskripsi'] }}">
                                        {{ $kat['label'] }}
                                    </option>
                                @endif
                            @endforeach
                        </select>
                        <div class="form-text small text-muted" id="penundaan_kategori_desc" style="font-size: 0.75rem;">
                            Pilih kondisi yang paling sesuai dengan keadaan Anda saat ini.
                        </div>
                    </div>

                    {{-- Tanggal Estimasi / Janji Bayar --}}
                    <div class="mb-3">
                        <label for="penundaan_tgl_janji" class="form-label small fw-bold text-dark">
                            <i class="bi bi-calendar-event text-warning me-1"></i>Estimasi Tanggal Sanggup Bayar <span class="text-muted fw-normal">(Opsional)</span>
                        </label>
                        <input type="date" name="tgl_janji_bayar" id="penundaan_tgl_janji" class="form-control" min="{{ date('Y-m-d') }}">
                        <div class="form-text small text-muted" style="font-size: 0.75rem;">
                            Isi perkiraan tanggal pembayaran agar Bendahara dapat mencatat jadwal janji bayar.
                        </div>
                    </div>

                    {{-- Keterangan Alasan --}}
                    <div class="mb-2">
                        <label for="penundaan_alasan_text" class="form-label small fw-bold text-dark">
                            Keterangan / Alasan Lengkap <span class="text-danger">*</span>
                        </label>
                        <textarea name="alasan_tunggakan" id="penundaan_alasan_text" rows="2" class="form-control" placeholder="Tuliskan alasan singkat pengajuan penundaan pembayaran..." maxlength="255" required></textarea>

                        {{-- Pilihan Cepat (Quick Chips) --}}
                        <div class="mt-2">
                            <span class="d-block text-muted mb-1" style="font-size: 0.72rem;">Contoh pengisian cepat (klik untuk memilih):</span>
                            <div class="d-flex flex-wrap gap-1">
                                <button type="button" class="btn btn-light border btn-sm py-0 px-2 text-secondary penundaan-quick-chip" style="font-size: 0.72rem;" data-kat="gaji_tertunda" data-text="Mohon izin menunda pembayaran, menunggu gajian awal bulan depan">
                                    Menunggu gajian bulan depan
                                </button>
                                <button type="button" class="btn btn-light border btn-sm py-0 px-2 text-secondary penundaan-quick-chip" style="font-size: 0.72rem;" data-kat="panen" data-text="Menunggu hasil panen selesai dalam waktu dekat">
                                    Menunggu hasil panen
                                </button>
                                <button type="button" class="btn btn-light border btn-sm py-0 px-2 text-secondary penundaan-quick-chip" style="font-size: 0.72rem;" data-kat="keringanan" data-text="Mohon izin keringanan untuk melunasi secara bertahap/dicicil">
                                    Izin dicicil bertahap
                                </button>
                                <button type="button" class="btn btn-light border btn-sm py-0 px-2 text-secondary penundaan-quick-chip" style="font-size: 0.72rem;" data-kat="musibah" data-text="Sedang ada kendala kesehatan/keperluan mendesak keluarga">
                                    Kendala darurat keluarga
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-0 pt-0 px-4 pb-4">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" id="btn_submit_penundaan" class="btn btn-warning text-dark fw-bold px-4 shadow-sm">
                        <i class="bi bi-send-check-fill me-1"></i> Kirim ke Bendahara
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const modalPenundaanEl = document.getElementById('modalAjukanPenundaan');
    if (!modalPenundaanEl) return;

    const modalPenundaan = new bootstrap.Modal(modalPenundaanEl);
    const formPenundaan = document.getElementById('formAjukanPenundaan');
    const selectKat = document.getElementById('penundaan_kategori');
    const descKat = document.getElementById('penundaan_kategori_desc');
    const inputTgl = document.getElementById('penundaan_tgl_janji');
    const inputAlasan = document.getElementById('penundaan_alasan_text');
    const boxCatatan = document.getElementById('penundaan_catatan_bendahara_box');
    const textCatatan = document.getElementById('penundaan_catatan_bendahara_text');

    function updatePenundaanDesc() {
        if (!selectKat || !descKat) return;
        const opt = selectKat.options[selectKat.selectedIndex];
        if (opt && opt.dataset.deskripsi) {
            descKat.textContent = opt.dataset.deskripsi;
        } else {
            descKat.textContent = 'Pilih kondisi yang paling sesuai dengan keadaan Anda saat ini.';
        }
    }

    if (selectKat) {
        selectKat.addEventListener('change', updatePenundaanDesc);
    }

    document.querySelectorAll('.penundaan-quick-chip').forEach(chip => {
        chip.addEventListener('click', function () {
            const text = this.getAttribute('data-text');
            const kat = this.getAttribute('data-kat');
            if (inputAlasan && text) inputAlasan.value = text;
            if (selectKat && kat && !selectKat.value) {
                selectKat.value = kat;
                updatePenundaanDesc();
            }
        });
    });

    document.addEventListener('click', function (e) {
        const btn = e.target.closest('.btn-ajukan-penundaan');
        if (!btn) return;

        const idTagihan = btn.getAttribute('data-id');
        const santri = btn.getAttribute('data-santri') || '-';
        const title = btn.getAttribute('data-title') || '-';
        const periode = btn.getAttribute('data-periode') || '';
        const sisa = btn.getAttribute('data-sisa') || '-';
        const kategori = btn.getAttribute('data-kategori') || '';
        const alasan = btn.getAttribute('data-alasan') || '';
        const tglJanji = btn.getAttribute('data-tgl-janji') || '';
        const catatanBendahara = btn.getAttribute('data-catatan-bendahara') || '';

        document.getElementById('penundaan_santri_name').textContent = santri;
        document.getElementById('penundaan_tagihan_name').textContent = periode ? `${title} (${periode})` : title;
        document.getElementById('penundaan_sisa_text').textContent = sisa;

        formPenundaan.action = `/wali/tagihan/${idTagihan}/ajukan-penundaan`;

        if (selectKat) selectKat.value = kategori;
        if (inputAlasan) inputAlasan.value = alasan;
        if (inputTgl) inputTgl.value = tglJanji;
        updatePenundaanDesc();

        if (catatanBendahara && catatanBendahara.trim() !== '') {
            boxCatatan.classList.remove('d-none');
            textCatatan.textContent = catatanBendahara;
        } else {
            boxCatatan.classList.add('d-none');
            textCatatan.textContent = '-';
        }

        modalPenundaan.show();
    });

    if (formPenundaan) {
        formPenundaan.addEventListener('submit', function () {
            const btnSubmit = document.getElementById('btn_submit_penundaan');
            if (btnSubmit) {
                btnSubmit.disabled = true;
                btnSubmit.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Mengirim...';
            }
        });
    }
});
</script>
@endpush

