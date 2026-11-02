@extends('layouts.app')

@section('title', 'Laporan Pemasukan Keuangan')
@section('page_title', 'Laporan Rekapitulasi Pemasukan')

@section('content')
<div class="card border-0 shadow-sm rounded-3">
    <div class="card-header bg-white py-3 border-0 d-flex flex-wrap align-items-center justify-content-between gap-2">
        <div>
            <h6 class="mb-0 fw-bold">Rekapitulasi Pemasukan Pembayaran</h6>
            <small class="text-muted">Total Pembayaran Sah: <strong>Rp {{ number_format($totalNominal, 0, ',', '.') }}</strong></small>
        </div>
        <div>
            <a href="{{ route('bendahara.laporan.pemasukan.cetak', request()->query()) }}" target="_blank" class="btn btn-outline-danger btn-sm fw-semibold">
                <i class="bi bi-printer me-1"></i> Cetak / Export PDF
            </a>
        </div>
    </div>

    <!-- Filter Form -->
    <div class="p-3 border-top border-bottom bg-light">
        <form action="{{ route('bendahara.laporan.pemasukan') }}" method="GET" class="row g-2">
            <div class="col-md-3">
                <label class="form-label small text-muted mb-1">Dari Tanggal</label>
                <input type="date" name="tgl_mulai" class="form-control form-control-sm" value="{{ request('tgl_mulai') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label small text-muted mb-1">Sampai Tanggal</label>
                <input type="date" name="tgl_selesai" class="form-control form-control-sm" value="{{ request('tgl_selesai') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label small text-muted mb-1">Jenis Tagihan</label>
                <select name="id_jenis_tagihan" class="form-select form-select-sm">
                    <option value="">-- Semua Komponen --</option>
                    @foreach($jenisTagihanList as $j)
                        <option value="{{ $j->id_jenis_tagihan }}" {{ request('id_jenis_tagihan') == $j->id_jenis_tagihan ? 'selected' : '' }}>
                            {{ $j->nama_tagihan }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small text-muted mb-1">Metode</label>
                <select name="metode_bayar" class="form-select form-select-sm">
                    <option value="">-- Semua Metode --</option>
                    <option value="gateway" {{ request('metode_bayar') == 'gateway' ? 'selected' : '' }}>Payment Gateway</option>
                    <option value="tunai" {{ request('metode_bayar') == 'tunai' ? 'selected' : '' }}>Tunai</option>
                </select>
            </div>
            <div class="col-md-1 d-flex align-items-end">
                <button type="submit" class="btn btn-primary btn-sm w-100"><i class="bi bi-filter"></i></button>
            </div>
        </form>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr class="small text-muted text-uppercase">
                    <th>No</th>
                    <th>No Kwitansi</th>
                    <th>Tanggal</th>
                    <th>Santri & NIS</th>
                    <th>Komponen Tagihan</th>
                    <th>Channel / Metode</th>
                    <th class="text-end">Jumlah Bersih Kas</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pemasukan as $index => $p)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>
                            <span class="badge bg-light text-dark border fw-bold">{{ $p->kwitansi->no_kwitansi ?? '-' }}</span>
                        </td>
                        <td class="small">{{ date('d/m/Y H:i', strtotime($p->tgl_bayar)) }}</td>
                        <td>
                            <div class="fw-semibold">{{ $p->tagihan->santri->nama_santri ?? '-' }}</div>
                            <small class="text-muted">NIS: {{ $p->tagihan->santri->nis ?? '-' }}</small>
                        </td>
                        <td>
                            <div>{{ $p->tagihan->jenisTagihan->nama_tagihan ?? '-' }}</div>
                            <small class="text-muted">{{ $p->tagihan->bulan ? $p->tagihan->nama_bulan . ' ' : '' }}{{ $p->tagihan->tahun_ajaran }}</small>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border">{{ $p->channel_bayar ?? ucfirst($p->metode_bayar) }}</span>
                        </td>
                        <td class="text-end fw-bold text-success">Rp {{ number_format($p->jumlah_bayar, 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">Tidak ada data pemasukan pada periode yang dipilih.</td>
                    </tr>
                @endforelse
            </tbody>
            @if($pemasukan->isNotEmpty())
                <tfoot class="table-light fw-bold">
                    <tr>
                        <td colspan="6" class="text-end text-uppercase">Total Penerimaan:</td>
                        <td class="text-end text-success fs-6">Rp {{ number_format($totalNominal, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            @endif
        </table>
    </div>
</div>
@endsection

