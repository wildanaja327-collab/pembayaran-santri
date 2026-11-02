@extends('layouts.app')

@section('title', 'Kasir Tunai Pesantren')
@section('page_title', 'Loket Pembayaran Tunai (Kantor Pesantren)')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-header bg-white py-3 border-0">
                <h6 class="mb-0 fw-bold"><i class="bi bi-cash-stack me-2 text-success"></i>Pencatatan Pembayaran Tunai Langsung</h6>
            </div>
            <div class="card-body p-4">
                <div class="alert alert-light border py-3 mb-4">
                    <div class="d-flex align-items-center gap-3">
                        <i class="bi bi-person-badge fs-3 text-success"></i>
                        <div>
                            <div class="fw-bold">Kasir Bendahara Pesantren</div>
                            <small class="text-muted">Gunakan form ini jika santri atau wali santri membayar langsung secara tunai di kantor administrasi pondok pesantren.</small>
                        </div>
                    </div>
                </div>

                <form action="{{ route('bendahara.pembayaran.kasir.store') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Pilih Tagihan Santri <span class="text-danger">*</span></label>
                        <select name="id_tagihan" id="select_tagihan_kasir" class="form-select" required onchange="handleTagihanKasirChange()">
                            <option value="">-- Pilih Tagihan Santri --</option>
                            @foreach($tagihanList as $t)
                                <option value="{{ $t->id_tagihan }}" 
                                        data-sisa="{{ (int)$t->sisa_tagihan }}" 
                                        data-nominal="{{ (int)$t->nominal_tagihan }}"
                                        data-nama="{{ $t->santri->nama_santri }}"
                                        data-nis="{{ $t->nis }}"
                                        {{ (old('id_tagihan') == $t->id_tagihan || $selectedTagihanId == $t->id_tagihan) ? 'selected' : '' }}>
                                    {{ $t->santri->nama_santri }} (NIS: {{ $t->nis }}) - {{ $t->jenisTagihan->nama_tagihan }} {{ $t->bulan ? 'Bulan ' . $t->nama_bulan : '' }} | Sisa: Rp {{ number_format($t->sisa_tagihan, 0, ',', '.') }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="p-3 bg-light rounded-3 mb-4">
                        <div class="row g-2">
                            <div class="col-6">
                                <small class="text-muted d-block">Sisa Kewajiban Tagihan:</small>
                                <h5 class="fw-bold text-danger mb-0" id="display_sisa">Rp 0</h5>
                            </div>
                            <div class="col-6">
                                <small class="text-muted d-block">Total Nominal Tagihan:</small>
                                <h6 class="fw-bold text-dark mb-0" id="display_nominal">Rp 0</h6>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Jumlah Uang Yang Diterima (Rp) <span class="text-danger">*</span></label>
                        <input type="number" name="jumlah_bayar" id="input_jumlah_bayar" class="form-control form-control-lg fw-bold text-success" min="1" placeholder="Masukkan jumlah tunai..." required>
                        <small class="text-muted">Bisa dibayar lunas sesuai sisa tagihan atau dicicil sesuai uang yang diserahkan.</small>
                    </div>

                    <div class="mb-4">
                        <label class="form-label small fw-semibold">Catatan Transaksi (Opsional)</label>
                        <input type="text" name="catatan" class="form-control" placeholder="Contoh: Titipan wali santri saat kunjungan jenguk">
                    </div>

                    <div class="d-flex justify-content-between pt-3 border-top">
                        <a href="{{ route('bendahara.pembayaran.index') }}" class="btn btn-outline-secondary">Batal</a>
                        <button type="submit" class="btn btn-success px-4 fw-bold">
                            <i class="bi bi-printer me-1"></i> Simpan & Terbitkan Kwitansi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function handleTagihanKasirChange() {
        const select = document.getElementById('select_tagihan_kasir');
        const selectedOpt = select.options[select.selectedIndex];
        const sisa = selectedOpt.getAttribute('data-sisa');
        const nominal = selectedOpt.getAttribute('data-nominal');

        if (sisa) {
            document.getElementById('display_sisa').innerText = 'Rp ' + Number(sisa).toLocaleString('id-ID');
            document.getElementById('display_nominal').innerText = 'Rp ' + Number(nominal).toLocaleString('id-ID');
            document.getElementById('input_jumlah_bayar').value = sisa;
            document.getElementById('input_jumlah_bayar').max = sisa;
        } else {
            document.getElementById('display_sisa').innerText = 'Rp 0';
            document.getElementById('display_nominal').innerText = 'Rp 0';
            document.getElementById('input_jumlah_bayar').value = '';
        }
    }
    document.addEventListener('DOMContentLoaded', handleTagihanKasirChange);
</script>
@endpush
@endsection

