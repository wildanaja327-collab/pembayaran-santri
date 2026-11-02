@extends('layouts.app')

@section('title', 'Generator Tagihan Massal')
@section('page_title', 'Generator Tagihan Massal Santri')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-header bg-white py-3 border-0">
                <h6 class="mb-0 fw-bold"><i class="bi bi-lightning-charge-fill me-2 text-warning"></i>Generate Tagihan Santri Sekaligus</h6>
            </div>
            <div class="card-body p-4">
                <div class="alert alert-info py-3 mb-4">
                    <div class="d-flex align-items-center gap-3">
                        <i class="bi bi-info-circle-fill fs-3 text-primary"></i>
                        <div>
                            <div class="fw-bold">Efisiensi Administrasi Keuangan (356 Santri)</div>
                            <small>Fitur ini memungkinkan bendahara men-generate tagihan SPP bulanan atau uang kegiatan untuk seluruh santri aktif dalam hitungan detik tanpa harus menginput satu per satu.</small>
                        </div>
                    </div>
                </div>

                <form action="{{ route('bendahara.tagihan.generate.post') }}" method="POST">
                    @csrf

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Pilih Komponen Tagihan <span class="text-danger">*</span></label>
                            <select name="id_jenis_tagihan" id="select_jenis" class="form-select" required onchange="handleJenisChange()">
                                <option value="">-- Pilih Jenis Tagihan --</option>
                                @foreach($jenisTagihanList as $j)
                                    <option value="{{ $j->id_jenis_tagihan }}" data-nominal="{{ (int)$j->nominal_standar }}" data-tipe="{{ $j->tipe }}">
                                        {{ $j->nama_tagihan }} ({{ ucfirst($j->tipe) }}) - Rp {{ number_format($j->nominal_standar, 0, ',', '.') }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Tahun Ajaran <span class="text-danger">*</span></label>
                            <input type="text" name="tahun_ajaran" class="form-control" value="2025/2026" placeholder="Contoh: 2025/2026" required>
                        </div>

                        <div class="col-md-6" id="wrapper_bulan">
                            <label class="form-label small fw-semibold">Bulan Tagihan <span class="text-danger">*</span></label>
                            <select name="bulan" id="select_bulan" class="form-select">
                                @for($m = 1; $m <= 12; $m++)
                                    <option value="{{ $m }}" {{ date('n') == $m ? 'selected' : '' }}>
                                        {{ DateTime::createFromFormat('!m', $m)->format('F') }} (Bulan ke-{{ $m }})
                                    </option>
                                @endfor
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Target Santri <span class="text-danger">*</span></label>
                            <select name="tingkat" class="form-select" required>
                                <option value="semua">Semua Tingkat (Total {{ $totalSantriAktif }} Santri)</option>
                                <option value="SMP">Khusus Santri SMP ({{ $smpCount }} Santri)</option>
                                <option value="SMA">Khusus Santri SMA ({{ $smaCount }} Santri)</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Besaran Nominal Tagihan (Rp) <span class="text-danger">*</span></label>
                            <input type="number" name="nominal_tagihan" id="input_nominal" class="form-control" placeholder="500000" min="1" required>
                            <small class="text-muted">Dapat disesuaikan jika berbeda dari tarif standar.</small>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Tanggal Jatuh Tempo</label>
                            <input type="date" name="jatuh_tempo" class="form-control" value="{{ date('Y-m-10', strtotime('+1 month')) }}">
                            <small class="text-muted">Standar jatuh tempo pembayaran (misal tanggal 10).</small>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between pt-3 border-top">
                        <a href="{{ route('bendahara.tagihan.index') }}" class="btn btn-outline-secondary">Batal</a>
                        <button type="submit" class="btn btn-warning text-dark px-4 fw-bold" onclick="return confirm('Apakah Anda yakin ingin men-generate tagihan massal untuk santri yang dipilih?')">
                            <i class="bi bi-lightning-charge-fill me-1"></i> Eksekusi Generate Massal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function handleJenisChange() {
        const select = document.getElementById('select_jenis');
        const selectedOpt = select.options[select.selectedIndex];
        const nominal = selectedOpt.getAttribute('data-nominal');
        const tipe = selectedOpt.getAttribute('data-tipe');

        if (nominal) {
            document.getElementById('input_nominal').value = nominal;
        }

        const wrapperBulan = document.getElementById('wrapper_bulan');
        if (tipe === 'insidental') {
            wrapperBulan.style.display = 'none';
        } else {
            wrapperBulan.style.display = 'block';
        }
    }
</script>
@endpush
@endsection

