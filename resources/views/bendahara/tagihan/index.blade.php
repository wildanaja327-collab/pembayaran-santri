@extends('layouts.app')

@section('title', 'Data Tagihan Santri')
@section('page_title', 'Manajemen Tagihan & Piutang Santri')

@section('content')
<div class="card border-0 shadow-sm rounded-3">
    <div class="card-header bg-white py-3 border-0 d-flex flex-wrap align-items-center justify-content-between gap-2">
        <div>
            <h6 class="mb-0 fw-bold">Daftar Tagihan Santri</h6>
            <small class="text-muted">Total: {{ $tagihan->total() }} catatan kewajiban pembayaran</small>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('bendahara.tagihan.generate.show') }}" class="btn btn-warning text-dark btn-sm fw-semibold shadow-sm">
                <i class="bi bi-lightning-charge me-1"></i> Generator Massal (356 Santri)
            </a>
            <a href="{{ route('bendahara.tagihan.create') }}" class="btn btn-success btn-sm fw-semibold shadow-sm">
                <i class="bi bi-plus-circle me-1"></i> Tagihan Tunggal
            </a>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="p-3 border-top border-bottom bg-light">
        <form action="{{ route('bendahara.tagihan.index') }}" method="GET" class="row g-2">
            <div class="col-md-3">
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Cari nama atau NIS santri..." value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <select name="status_tagihan" class="form-select form-select-sm">
                    <option value="">-- Semua Status --</option>
                    <option value="belum_bayar" {{ request('status_tagihan') == 'belum_bayar' ? 'selected' : '' }}>Belum Bayar</option>
                    <option value="dicicil" {{ request('status_tagihan') == 'dicicil' ? 'selected' : '' }}>Dicicil</option>
                    <option value="lunas" {{ request('status_tagihan') == 'lunas' ? 'selected' : '' }}>Lunas</option>
                </select>
            </div>
            <div class="col-md-2">
                <select name="id_jenis_tagihan" class="form-select form-select-sm">
                    <option value="">-- Semua Jenis --</option>
                    @foreach($jenisTagihanList as $j)
                        <option value="{{ $j->id_jenis_tagihan }}" {{ request('id_jenis_tagihan') == $j->id_jenis_tagihan ? 'selected' : '' }}>
                            {{ $j->nama_tagihan }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="tingkat" class="form-select form-select-sm">
                    <option value="">-- Tingkat --</option>
                    <option value="SMP" {{ request('tingkat') == 'SMP' ? 'selected' : '' }}>SMP</option>
                    <option value="SMA" {{ request('tingkat') == 'SMA' ? 'selected' : '' }}>SMA</option>
                </select>
            </div>
            <div class="col-md-2">
                <select name="bulan" class="form-select form-select-sm">
                    <option value="">-- Bulan --</option>
                    @for($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ request('bulan') == $m ? 'selected' : '' }}>
                            {{ DateTime::createFromFormat('!m', $m)->format('F') }}
                        </option>
                    @endfor
                </select>
            </div>
            <div class="col-md-1 d-flex gap-1">
                <button type="submit" class="btn btn-primary btn-sm w-100"><i class="bi bi-search"></i></button>
                <a href="{{ route('bendahara.tagihan.index') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-counterclockwise"></i></a>
            </div>
        </form>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr class="small text-muted text-uppercase">
                    <th>Santri</th>
                    <th>Jenis Tagihan & Periode</th>
                    <th>Nominal Tagihan</th>
                    <th>Terbayar</th>
                    <th>Sisa Tagihan</th>
                    <th>Jatuh Tempo</th>
                    <th>Status</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tagihan as $t)
                    <tr>
                        <td>
                            <div class="fw-semibold">{{ $t->santri->nama_santri ?? '-' }}</div>
                            <small class="text-muted">
                                NIS: {{ $t->nis }} | 
                                <span class="badge {{ ($t->santri->tingkat ?? '') == 'SMP' ? 'badge-smp' : 'badge-sma' }}">{{ $t->santri->tingkat ?? '-' }}</span> 
                                {{ $t->santri->kelas ?? '' }}
                            </small>
                        </td>
                        <td>
                            <div class="fw-bold">{{ $t->jenisTagihan->nama_tagihan ?? '-' }}</div>
                            <small class="text-muted">
                                {{ $t->bulan ? $t->nama_bulan . ' ' : '' }}{{ $t->tahun_ajaran }}
                            </small>
                        </td>
                        <td class="fw-bold text-dark">Rp {{ number_format($t->nominal_tagihan, 0, ',', '.') }}</td>
                        <td class="text-success fw-semibold">Rp {{ number_format($t->total_terbayar, 0, ',', '.') }}</td>
                        <td class="text-danger fw-bold">Rp {{ number_format($t->sisa_tagihan, 0, ',', '.') }}</td>
                        <td class="small text-muted">
                            {{ $t->jatuh_tempo ? date('d/m/Y', strtotime($t->jatuh_tempo)) : '-' }}
                        </td>
                        <td>{!! $t->status_badge !!}</td>
                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-1">
                                @if($t->status_tagihan != 'lunas')
                                    <a href="{{ route('bendahara.pembayaran.kasir.create', ['id_tagihan' => $t->id_tagihan]) }}" class="btn btn-sm btn-success" title="Bayar Kasir Tunai">
                                        <i class="bi bi-cash-stack"></i>
                                    </a>
                                @endif
                                <form action="{{ route('bendahara.tagihan.destroy', $t->id_tagihan) }}" method="POST" onsubmit="return confirm('Hapus tagihan ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">Tidak ada data tagihan ditemukan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($tagihan->hasPages())
        <div class="p-3 border-top d-flex justify-content-end">
            {{ $tagihan->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>
@endsection

