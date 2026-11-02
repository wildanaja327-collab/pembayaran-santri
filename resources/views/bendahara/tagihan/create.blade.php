@extends('layouts.app')

@section('title', 'Buat Tagihan Tunggal')
@section('page_title', 'Pembuatan Tagihan Khusus')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-header bg-white py-3 border-0">
                <h6 class="mb-0 fw-bold"><i class="bi bi-file-earmark-plus me-2 text-success"></i>Formulir Tagihan Perorangan</h6>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('bendahara.tagihan.store') }}" method="POST">
                    @csrf

                    <div class="row g-3 mb-4">
                        <div class="col-md-12">
                            <label class="form-label small fw-semibold">Pilih Santri <span class="text-danger">*</span></label>
                            <select name="nis" class="form-select" required>
                                <option value="">-- Pilih Santri --</option>
                                @foreach($santriList as $s)
                                    <option value="{{ $s->nis }}" {{ old('nis') == $s->nis ? 'selected' : '' }}>
                                        {{ $s->nis }} - {{ $s->nama_santri }} ({{ $s->tingkat }} {{ $s->kelas }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Jenis Tagihan <span class="text-danger">*</span></label>
                            <select name="id_jenis_tagihan" id="select_jenis_single" class="form-select" required onchange="handleSingleJenisChange()">
                                <option value="">-- Pilih Jenis --</option>
                                @foreach($jenisTagihanList as $j)
                                    <option value="{{ $j->id_jenis_tagihan }}" data-nominal="{{ (int)$j->nominal_standar }}" data-tipe="{{ $j->tipe }}">
                                        {{ $j->nama_tagihan }} (Rp {{ number_format($j->nominal_standar, 0, ',', '.') }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Tahun Ajaran <span class="text-danger">*</span></label>
                            <input type="text" name="tahun_ajaran" class="form-control" value="{{ old('tahun_ajaran', '2025/2026') }}" required>
                        </div>

                        <div class="col-md-6" id="wrapper_bulan_single">
                            <label class="form-label small fw-semibold">Bulan (Untuk Biaya Bulanan)</label>
                            <select name="bulan" class="form-select">
                                <option value="">-- Tanpa Bulan --</option>
                                @for($m = 1; $m <= 12; $m++)
                                    <option value="{{ $m }}">
                                        {{ DateTime::createFromFormat('!m', $m)->format('F') }}
                                    </option>
                                @endfor
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Nominal Tagihan (Rp) <span class="text-danger">*</span></label>
                            <input type="number" name="nominal_tagihan" id="input_nominal_single" class="form-control" min="1" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Tanggal Jatuh Tempo</label>
                            <input type="date" name="jatuh_tempo" class="form-control">
                        </div>
                    </div>

                    <div class="d-flex justify-content-between pt-3 border-top">
                        <a href="{{ route('bendahara.tagihan.index') }}" class="btn btn-outline-secondary">Kembali</a>
                        <button type="submit" class="btn btn-success px-4 fw-semibold"><i class="bi bi-save me-1"></i> Simpan Tagihan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function handleSingleJenisChange() {
        const select = document.getElementById('select_jenis_single');
        const selectedOpt = select.options[select.selectedIndex];
        const nominal = selectedOpt.getAttribute('data-nominal');
        const tipe = selectedOpt.getAttribute('data-tipe');

        if (nominal) {
            document.getElementById('input_nominal_single').value = nominal;
        }

        const wrapper = document.getElementById('wrapper_bulan_single');
        if (tipe === 'insidental') {
            wrapper.style.display = 'none';
        } else {
            wrapper.style.display = 'block';
        }
    }
</script>
@endpush
@endsection

