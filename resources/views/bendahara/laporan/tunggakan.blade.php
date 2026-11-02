@extends('layouts.app')

@section('title', 'Rekapan Tunggakan Santri')
@section('page_title', 'Rekapitulasi Santri Menunggak')

@section('content')
<div class="card border-0 shadow-sm rounded-3">
    <div class="card-header bg-white py-3 border-0 d-flex flex-wrap align-items-center justify-content-between gap-2">
        <div>
            <h6 class="mb-0 fw-bold text-danger"><i class="bi bi-exclamation-triangle-fill me-2"></i>Daftar Santri Belum Lunas (Tunggakan)</h6>
            <small class="text-muted">Total Kewajiban Tertunggak: <strong class="text-danger">Rp {{ number_format($totalTunggakan, 0, ',', '.') }}</strong> ({{ $tunggakan->count() }} catatan tagihan)</small>
        </div>
        <div>
            <a href="{{ route('bendahara.laporan.tunggakan.cetak', request()->query()) }}" target="_blank" class="btn btn-outline-danger btn-sm fw-semibold">
                <i class="bi bi-printer me-1"></i> Cetak / Export PDF
            </a>
        </div>
    </div>

    <!-- Filter Form -->
    <div class="p-3 border-top border-bottom bg-light">
        <form action="{{ route('bendahara.laporan.tunggakan') }}" method="GET" class="row g-2">
            <div class="col-md-2">
                <label class="form-label small text-muted mb-1">Tingkat</label>
                <select name="tingkat" class="form-select form-select-sm">
                    <option value="">-- Semua --</option>
                    <option value="SMP" {{ request('tingkat') == 'SMP' ? 'selected' : '' }}>SMP (243)</option>
                    <option value="SMA" {{ request('tingkat') == 'SMA' ? 'selected' : '' }}>SMA (113)</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small text-muted mb-1">Kelas</label>
                <input type="text" name="kelas" class="form-control form-control-sm" placeholder="Contoh: VII A" value="{{ request('kelas') }}">
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
                <label class="form-label small text-muted mb-1">Bulan</label>
                <select name="bulan" class="form-select form-select-sm">
                    <option value="">-- Semua Bulan --</option>
                    @for($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ request('bulan') == $m ? 'selected' : '' }}>
                            {{ DateTime::createFromFormat('!m', $m)->format('F') }}
                        </option>
                    @endfor
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small text-muted mb-1">Tahun Ajaran</label>
                <input type="text" name="tahun_ajaran" class="form-control form-control-sm" placeholder="2025/2026" value="{{ request('tahun_ajaran') }}">
            </div>
            <div class="col-md-1 d-flex align-items-end">
                <button type="submit" class="btn btn-danger btn-sm w-100"><i class="bi bi-filter"></i></button>
            </div>
        </form>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr class="small text-muted text-uppercase">
                    <th>No</th>
                    <th>Santri & NIS</th>
                    <th>Tingkat / Kelas</th>
                    <th>Komponen Tagihan</th>
                    <th>Jatuh Tempo</th>
                    <th>Wali & No WA</th>
                    <th>Total Biaya</th>
                    <th>Sisa Tunggakan</th>
                    <th class="text-center">Aksi Cepat</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tunggakan as $index => $t)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>
                            <div class="fw-bold">{{ $t->santri->nama_santri ?? '-' }}</div>
                            <small class="text-muted">NIS: {{ $t->nis }}</small>
                        </td>
                        <td>
                            <span class="badge {{ ($t->santri->tingkat ?? '') == 'SMP' ? 'badge-smp' : 'badge-sma' }}">{{ $t->santri->tingkat ?? '-' }}</span>
                            <div class="small text-muted">{{ $t->santri->kelas ?? '-' }}</div>
                        </td>
                        <td>
                            <div class="fw-semibold">{{ $t->jenisTagihan->nama_tagihan ?? '-' }}</div>
                            <small class="text-muted">{{ $t->bulan ? $t->nama_bulan . ' ' : '' }}{{ $t->tahun_ajaran }}</small>
                        </td>
                        <td>
                            @if($t->jatuh_tempo && strtotime($t->jatuh_tempo) < time())
                                <span class="badge bg-danger">Lewat: {{ date('d/m/Y', strtotime($t->jatuh_tempo)) }}</span>
                            @elseif($t->jatuh_tempo)
                                <span class="small text-muted">{{ date('d/m/Y', strtotime($t->jatuh_tempo)) }}</span>
                            @else
                                <span class="small text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            <div>{{ $t->santri->wali->nama_lengkap ?? '-' }}</div>
                            @if($t->santri->wali?->no_telp)
                                @php
                                    $pesanWa = urlencode("Assalamu'alaikum Wr. Wb. Yth. Wali dari ananda {$t->santri->nama_santri}, mengingatkan bahwa terdapat tagihan {$t->jenisTagihan->nama_tagihan} sebesar Rp " . number_format($t->sisa_tagihan, 0, ',', '.') . " yang belum diselesaikan. Pembayaran dapat dilakukan via portal santri. Syukron. - Bendahara PP Darul Muttaqin");
                                @endphp
                                <a href="https://wa.me/{{ preg_replace('/^0/', '62', $t->santri->wali->no_telp) }}?text={{ $pesanWa }}" target="_blank" class="btn btn-outline-success btn-sm py-0 px-2 mt-1" style="font-size: 0.75rem;">
                                    <i class="bi bi-whatsapp me-1"></i> Hubungi WA
                                </a>
                            @else
                                <small class="text-muted">-</small>
                            @endif
                        </td>
                        <td class="text-muted">Rp {{ number_format($t->nominal_tagihan, 0, ',', '.') }}</td>
                        <td class="fw-bold text-danger">Rp {{ number_format($t->sisa_tagihan, 0, ',', '.') }}</td>
                        <td class="text-center">
                            <a href="{{ route('bendahara.pembayaran.kasir.create', ['id_tagihan' => $t->id_tagihan]) }}" class="btn btn-sm btn-success" title="Terima Bayar Tunai Kasir">
                                <i class="bi bi-cash-stack"></i>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center py-5 text-muted">
                            <i class="bi bi-check-circle fs-3 text-success d-block mb-2"></i>
                            Alhamdulillah! Tidak ada tunggakan santri yang ditemukan sesuai filter.
                        </td>
                    </tr>
                @endforelse
            </tbody>
            @if($tunggakan->isNotEmpty())
                <tfoot class="table-light fw-bold">
                    <tr>
                        <td colspan="7" class="text-end text-uppercase">Total Seluruh Tunggakan:</td>
                        <td class="text-danger fs-6">Rp {{ number_format($totalTunggakan, 0, ',', '.') }}</td>
                        <td></td>
                    </tr>
                </tfoot>
            @endif
        </table>
    </div>
</div>
@endsection

