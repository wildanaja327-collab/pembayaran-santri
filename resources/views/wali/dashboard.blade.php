@extends('layouts.app')

@section('title', 'Portal Wali Santri')
@section('page_title', 'Portal Tagihan & Pembayaran Santri')

@section('content')

{{-- ============================================================ --}}
{{-- ALERT JATUH TEMPO — COUNTDOWN MENDESAK --}}
{{-- ============================================================ --}}
@if($tagihanMendesak->count() > 0)
<div class="alert alert-danger border-danger border-2 shadow-sm mb-4 py-3" id="alertJatuhTempo">
    <div class="d-flex align-items-center gap-3 flex-wrap">
        <div class="text-danger fs-3 flex-shrink-0">
            <i class="bi bi-alarm-fill"></i>
        </div>
        <div class="flex-grow-1">
            <div class="fw-bold text-danger mb-1">
                ⚠️ {{ $tagihanMendesak->count() }} Tagihan Menjelang Jatuh Tempo!
            </div>
            <div class="small">
                @foreach($tagihanMendesak as $tm)
                    <span class="badge bg-danger me-1 mb-1">
                        {{ $tm->santri->nama_santri }} — {{ $tm->jenisTagihan->nama_tagihan }}
                        (Rp {{ number_format($tm->sisa_tagihan, 0, ',', '.') }})
                        — jatuh tempo: <strong>{{ \Carbon\Carbon::parse($tm->jatuh_tempo)->translatedFormat('d M Y') }}</strong>
                        <span class="badge bg-white text-danger ms-1 countdown-badge" data-due="{{ $tm->jatuh_tempo }}"></span>
                    </span>
                @endforeach
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
</div>
@endif

{{-- ============================================================ --}}
{{-- BANNER TOTAL KEWAJIBAN --}}
{{-- ============================================================ --}}
<div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
    <div class="card-body p-0">
        <div class="d-flex align-items-center justify-content-between flex-wrap"
             style="background: {{ $totalTunggakan > 0 ? 'linear-gradient(135deg, #dc2626 0%, #b91c1c 100%)' : 'linear-gradient(135deg, #16a34a 0%, #15803d 100%)' }}">
            <div class="p-3 p-sm-4 text-white flex-grow-1">
                <div class="small opacity-75 mb-1 text-uppercase fw-semibold" style="font-size: clamp(0.72rem, 2vw, 0.85rem);">
                    <i class="bi bi-wallet2 me-1"></i> Total Kewajiban Tagihan Aktif
                </div>
                <div class="fw-bold" style="font-size: clamp(1.4rem, 4vw, 2.2rem);">
                    Rp {{ number_format($totalTunggakan, 0, ',', '.') }}
                </div>
                <div class="small opacity-75 mt-1" style="font-size: clamp(0.75rem, 2vw, 0.85rem);">
                    @if($totalTunggakan > 0)
                        Terdapat tunggakan pembayaran yang perlu segera diselesaikan.
                    @else
                        <i class="bi bi-check-circle me-1"></i> Alhamdulillah! Seluruh tagihan sudah lunas.
                    @endif
                </div>
            </div>
            <div class="p-3 p-sm-4 text-white text-center d-none d-sm-block" style="min-width: 120px;">
                <div class="display-5">
                    <i class="bi {{ $totalTunggakan > 0 ? 'bi-exclamation-circle-fill' : 'bi-patch-check-fill' }}"></i>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ============================================================ --}}
{{-- CARD PER SANTRI — MEWAH DENGAN PROGRESS & STATISTIK --}}
{{-- ============================================================ --}}
<h6 class="fw-bold text-muted text-uppercase small mb-3">
    <i class="bi bi-people me-1"></i> Data Santri Yang Diampu
</h6>
<div class="row g-3 mb-4">
    @forelse($santriStats as $stat)
        @php
            $s = $stat->santri;
            $kepatuhan = $stat->kepatuhan ?? $s->getKepatuhanInfo();
            $pct = $kepatuhan['persen'];
            $colorBadge = $kepatuhan['color'];
            $colorProg = 'bg-' . $kepatuhan['color'];
            $labelStatus = $kepatuhan['status'];
            $iconStatus = $kepatuhan['icon'];
            $subStatus = $kepatuhan['sub'];

            // Warna avatar per program
            $colorMap = [
                'PRG-TFZ' => ['#d97706','#fef3c7'],
                'PRG-ARB' => ['#2563eb','#dbeafe'],
                'PRG-ING' => ['#7c3aed','#ede9fe'],
            ];
            $progColor = $colorMap[$s->id_program ?? ''] ?? ['#15803d','#dcfce7'];
        @endphp
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-4 h-100 card-santri-hover" style="border-left: 4px solid {{ $progColor[0] }} !important;">
                <div class="card-body p-4">
                    {{-- Header santri --}}
                    <div class="d-flex align-items-start gap-3 mb-3">
                        {{-- Avatar inisial --}}
                        <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0 fw-bold fs-5"
                             style="width:52px;height:52px;background:{{ $progColor[1] }};color:{{ $progColor[0] }};">
                            {{ strtoupper(substr($s->nama_santri, 0, 1)) }}{{ strtoupper(substr(explode(' ', trim($s->nama_santri))[1] ?? 'X', 0, 1)) }}
                        </div>
                        <div class="flex-grow-1 min-w-0">
                            <div class="d-flex align-items-center justify-content-between gap-2 flex-wrap">
                                <h6 class="fw-bold mb-0 text-dark text-truncate">{{ $s->nama_santri }}</h6>
                                <span class="badge bg-{{ $colorBadge }}-subtle text-{{ $colorBadge }} border border-{{ $colorBadge }}-subtle">
                                    <i class="bi {{ $iconStatus }} me-1"></i>{{ $labelStatus }}
                                </span>
                            </div>
                            <div class="small text-muted mt-1">
                                NIS: <strong>{{ $s->nis }}</strong> &nbsp;|&nbsp; {{ $s->tingkat }} Kelas {{ $s->kelas }}
                            </div>
                            <div class="small mt-1">
                                <span class="badge border" style="background:{{ $progColor[1] }};color:{{ $progColor[0] }};border-color:{{ $progColor[0] }}!important;">
                                    <i class="bi bi-star me-1"></i>{{ $s->program->nama_program ?? '-' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- Progress pelunasan --}}
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <small class="fw-semibold text-muted">Progres Pelunasan Tagihan</small>
                            <small class="fw-bold" style="color:{{ $progColor[0] }}">{{ $pct }}%</small>
                        </div>
                        <div class="progress rounded-pill" style="height:10px;background:#e2e8f0;">
                            <div class="{{ $colorProg }} rounded-pill" style="width:{{ $pct }}%; transition:width 1s ease;" role="progressbar"></div>
                        </div>
                    </div>

                    {{-- Mini statistik 3 kolom (Responsif Tanpa Truncate) --}}
                    <div class="row g-2 mb-3">
                        <div class="col-4">
                            <div class="rounded-3 p-2 text-center h-100 d-flex flex-column justify-content-center" style="background:#f8fafc;">
                                <div class="small text-muted" style="font-size: clamp(0.65rem, 1.8vw, 0.75rem);">Total Tagihan</div>
                                <div class="fw-bold text-dark lh-sm text-break mt-0.5" style="font-size: clamp(0.72rem, 2.2vw, 0.85rem);">Rp {{ number_format($stat->total_tagihan, 0, ',', '.') }}</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="rounded-3 p-2 text-center h-100 d-flex flex-column justify-content-center" style="background:#f0fdf4;">
                                <div class="small text-muted" style="font-size: clamp(0.65rem, 1.8vw, 0.75rem);">Terbayar</div>
                                <div class="fw-bold text-success lh-sm text-break mt-0.5" style="font-size: clamp(0.72rem, 2.2vw, 0.85rem);">Rp {{ number_format($stat->total_terbayar, 0, ',', '.') }}</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="rounded-3 p-2 text-center h-100 d-flex flex-column justify-content-center" style="background:#fff7ed;">
                                <div class="small text-muted" style="font-size: clamp(0.65rem, 1.8vw, 0.75rem);">Sisa</div>
                                <div class="fw-bold text-danger lh-sm text-break mt-0.5" style="font-size: clamp(0.72rem, 2.2vw, 0.85rem);">Rp {{ number_format($stat->total_sisa, 0, ',', '.') }}</div>
                            </div>
                        </div>
                    </div>

                    {{-- Jatuh tempo terdekat --}}
                    @if($stat->tagihan_terdekat)
                    <div class="d-flex align-items-center gap-2 p-2 rounded-3 mb-3" style="background:#fef9c3;">
                        <i class="bi bi-calendar-event text-warning"></i>
                        <small class="text-warning-emphasis">
                            Tagihan terdekat: <strong>{{ $stat->tagihan_terdekat->jenisTagihan->nama_tagihan }}</strong>
                            jatuh tempo <strong>{{ \Carbon\Carbon::parse($stat->tagihan_terdekat->jatuh_tempo)->translatedFormat('d M Y') }}</strong>
                        </small>
                    </div>
                    @endif

                    {{-- Tombol detail --}}
                    <a href="{{ route('wali.santri.show', $s->nis) }}" class="btn btn-sm w-100 fw-semibold"
                       style="background:{{ $progColor[0] }};color:#fff;border:none;">
                        <i class="bi bi-eye me-1"></i> Lihat Detail Tagihan & Riwayat
                    </a>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="alert alert-warning rounded-4">
                <i class="bi bi-info-circle me-2"></i>
                Belum ada data santri yang ditautkan ke akun Anda. Silakan hubungi bendahara pesantren.
            </div>
        </div>
    @endforelse
</div>

{{-- ============================================================ --}}
{{-- TAGIHAN BELUM LUNAS — TABEL DESKTOP & KARTU RESPONSIF HP    --}}
{{-- ============================================================ --}}
<div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
    <div class="card-header bg-white py-3 px-4 border-0 d-flex align-items-center justify-content-between">
        <h6 class="mb-0 fw-bold">
            <i class="bi bi-receipt text-danger me-2"></i>Tagihan Aktif — Bayar via Payment Gateway
        </h6>
        @if($tagihanBelumLunas->count() > 0)
            <span class="badge bg-danger rounded-pill">{{ $tagihanBelumLunas->count() }} Belum Lunas</span>
        @endif
    </div>

    {{-- TAMPILAN LAPTOP / DESKTOP (100% UTUH SEPERTI ASLINYA, TIDAK BERUBAH) --}}
    <div class="table-responsive d-none d-md-block">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr class="small text-muted text-uppercase">
                    <th class="ps-4">Santri</th>
                    <th>Komponen Tagihan</th>
                    <th>Periode</th>
                    <th>Total Biaya</th>
                    <th>Progress Pelunasan</th>
                    <th>Sisa Tagihan</th>
                    <th>Status</th>
                    <th class="text-center pe-4">Bayar</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tagihanBelumLunas as $t)
                    @php
                        $pctCicil = $t->nominal_tagihan > 0 ? round(($t->total_terbayar / $t->nominal_tagihan) * 100) : 0;
                        $colorCicil = $pctCicil >= 60 ? 'success' : ($pctCicil >= 30 ? 'warning' : 'danger');
                        $hariLagi = $t->jatuh_tempo ? (int) now()->diffInDays(\Carbon\Carbon::parse($t->jatuh_tempo), false) : null;
                    @endphp
                    <tr>
                        <td class="ps-4 fw-bold">{{ $t->santri->nama_santri }}</td>
                        <td>
                            <div class="fw-semibold">{{ $t->jenisTagihan->nama_tagihan }}</div>
                            @if($t->kategori_info || $t->alasan_tunggakan || $t->catatan_bendahara)
                                <div class="mt-1 d-flex flex-wrap align-items-center gap-1">
                                    @if($t->kategori_info)
                                        {!! $t->kategori_badge !!}
                                    @endif
                                    @if($t->tgl_janji_bayar)
                                        <span class="badge bg-light text-dark border small" style="font-size: 0.72rem;">
                                            <i class="bi bi-calendar-event text-warning me-1"></i>Janji: {{ date('d/m/Y', strtotime($t->tgl_janji_bayar)) }}
                                        </span>
                                    @endif
                                </div>
                                @if($t->alasan_tunggakan)
                                    <div class="small text-muted mt-1" style="font-size: 0.76rem;">
                                        <i class="bi bi-chat-left-quote me-1 text-secondary"></i>"{{ $t->alasan_tunggakan }}"
                                    </div>
                                @endif
                                @if($t->catatan_bendahara)
                                    <div class="mt-1 px-2 py-1 rounded-2 small" style="background: #eff6ff; border-left: 3px solid #3b82f6; font-size: 0.74rem;">
                                        <strong class="text-primary"><i class="bi bi-patch-check-fill me-1"></i>Tanggapan Bendahara:</strong>
                                        <span class="text-dark">{{ $t->catatan_bendahara }}</span>
                                    </div>
                                @endif
                            @endif
                        </td>
                        <td class="small text-muted">{{ $t->bulan ? $t->nama_bulan . ' ' : '' }}{{ $t->tahun_ajaran }}</td>
                        <td>Rp {{ number_format($t->nominal_tagihan, 0, ',', '.') }}</td>
                        <td style="min-width: 180px;">
                            <div class="d-flex align-items-center gap-2">
                                <div class="progress flex-grow-1 rounded-pill" style="height:8px;background:#e2e8f0;">
                                    <div class="bg-{{ $colorCicil }} rounded-pill" style="width:{{ $pctCicil }}%;"></div>
                                </div>
                                <small class="text-muted fw-semibold" style="min-width:30px;">{{ $pctCicil }}%</small>
                            </div>
                            <div class="small text-muted mt-1">
                                Terbayar: <span class="text-success fw-semibold">Rp {{ number_format($t->total_terbayar, 0, ',', '.') }}</span>
                            </div>
                        </td>
                        <td class="fw-bold text-danger">Rp {{ number_format($t->sisa_tagihan, 0, ',', '.') }}</td>
                        <td>
                            {!! $t->status_badge !!}
                            @if($hariLagi !== null)
                                @if($hariLagi < 0)
                                    <div><small class="text-danger fw-bold"><i class="bi bi-clock-history me-1"></i>Lewat {{ abs($hariLagi) }} hari</small></div>
                                @elseif($hariLagi <= 7)
                                    <div><small class="text-warning fw-bold"><i class="bi bi-alarm me-1"></i>{{ $hariLagi }} hari lagi</small></div>
                                @endif
                            @endif
                        </td>
                        <td class="text-center pe-4">
                            @if($t->status_tagihan == 'menunggu_verifikasi')
                                <span class="badge bg-warning text-dark px-3 py-2">
                                    <i class="bi bi-hourglass-split me-1"></i> Menunggu Verifikasi
                                </span>
                            @else
                                <div class="d-flex flex-column flex-xl-row align-items-center justify-content-center gap-1">
                                    <button type="button" class="btn btn-success btn-sm fw-bold px-3 shadow-sm btn-pay-midtrans"
                                            data-id="{{ $t->id_tagihan }}"
                                            data-title="{{ $t->jenisTagihan->nama_tagihan }}"
                                            data-sisa="{{ (int)$t->sisa_tagihan }}"
                                            data-nominal="{{ (int)$t->nominal_tagihan }}"
                                            data-terbayar="{{ (int)$t->total_terbayar }}"
                                            data-santri="{{ $t->santri->nama_santri }}">
                                        <i class="bi bi-credit-card me-1"></i> Bayar
                                    </button>
                                    <button type="button" class="btn {{ $t->kategori_tunggakan ? 'btn-outline-warning text-dark' : 'btn-outline-secondary' }} btn-sm fw-semibold btn-ajukan-penundaan"
                                            data-id="{{ $t->id_tagihan }}"
                                            data-santri="{{ $t->santri->nama_santri }}"
                                            data-title="{{ $t->jenisTagihan->nama_tagihan }}"
                                            data-periode="{{ ($t->bulan ? $t->nama_bulan . ' ' : '') . $t->tahun_ajaran }}"
                                            data-sisa="Rp {{ number_format($t->sisa_tagihan, 0, ',', '.') }}"
                                            data-kategori="{{ $t->kategori_tunggakan ?? '' }}"
                                            data-alasan="{{ $t->alasan_tunggakan ?? '' }}"
                                            data-tgl-janji="{{ $t->tgl_janji_bayar ?? '' }}"
                                            data-catatan-bendahara="{{ $t->catatan_bendahara ?? '' }}"
                                            title="{{ $t->kategori_tunggakan ? 'Ubah Pengajuan Penundaan' : 'Ajukan Penundaan / Lapor Kendala' }}">
                                        <i class="bi {{ $t->kategori_tunggakan ? 'bi-pencil-square text-warning' : 'bi-calendar2-plus' }} me-1"></i>{{ $t->kategori_tunggakan ? 'Ubah Alasan' : 'Ajukan Penundaan' }}
                                    </button>
                                </div>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <div class="py-3">
                                <i class="bi bi-patch-check-fill text-success fs-1 d-block mb-3"></i>
                                <h6 class="fw-bold text-success">Alhamdulillah! Seluruh tagihan sudah lunas.</h6>
                                <small>Tidak ada tagihan aktif saat ini. Terima kasih atas ketepatan pembayaran Anda.</small>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- TAMPILAN KHUSUS HP (POLA 1: KARTU RINGKAS DENGAN TOMBOL RINCIAN & TOMBOL BAYAR INSTAN) --}}
    <div class="d-md-none p-3 bg-light bg-opacity-50">
        @forelse($tagihanBelumLunas as $idx => $t)
            @php
                $pctCicil = $t->nominal_tagihan > 0 ? round(($t->total_terbayar / $t->nominal_tagihan) * 100) : 0;
                $colorCicil = $pctCicil >= 60 ? 'success' : ($pctCicil >= 30 ? 'warning' : 'danger');
                $hariLagi = $t->jatuh_tempo ? (int) now()->diffInDays(\Carbon\Carbon::parse($t->jatuh_tempo), false) : null;
                $collapseId = 'dash_tagihan_detail_' . $t->id_tagihan . '_' . $idx;
            @endphp
            <div class="card border-0 shadow-sm rounded-4 mb-3 overflow-hidden border-start border-4 border-{{ $t->sisa_tagihan > 0 ? 'danger' : 'success' }}">
                <div class="card-body p-3">
                    {{-- Header Kartu: Santri & Status Badge --}}
                    <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                        <div>
                            <span class="badge bg-secondary-subtle text-secondary small mb-1">
                                <i class="bi bi-person me-1"></i>{{ $t->santri->nama_santri }}
                            </span>
                            <h6 class="fw-bold mb-0 text-dark">{{ $t->jenisTagihan->nama_tagihan }}</h6>
                            @if($t->kategori_info || $t->alasan_tunggakan || $t->catatan_bendahara)
                                <div class="mt-1 d-flex flex-wrap align-items-center gap-1">
                                    @if($t->kategori_info)
                                        {!! $t->kategori_badge !!}
                                    @endif
                                    @if($t->tgl_janji_bayar)
                                        <span class="badge bg-light text-dark border small" style="font-size: 0.7rem;">
                                            <i class="bi bi-calendar-event text-warning me-1"></i>Janji: {{ date('d/m/Y', strtotime($t->tgl_janji_bayar)) }}
                                        </span>
                                    @endif
                                </div>
                                @if($t->alasan_tunggakan)
                                    <div class="small text-muted mt-1" style="font-size: 0.74rem;">
                                        <i class="bi bi-chat-left-quote me-1"></i>"{{ $t->alasan_tunggakan }}"
                                    </div>
                                @endif
                                @if($t->catatan_bendahara)
                                    <div class="mt-1 px-2 py-1 rounded-2 small" style="background: #eff6ff; border-left: 3px solid #3b82f6; font-size: 0.73rem;">
                                        <strong class="text-primary"><i class="bi bi-patch-check-fill me-1"></i>Bendahara:</strong>
                                        <span class="text-dark">{{ $t->catatan_bendahara }}</span>
                                    </div>
                                @endif
                            @endif
                        </div>
                        <div>
                            {!! $t->status_badge !!}
                        </div>
                    </div>

                    {{-- Baris Sisa Tagihan & Tombol Bayar Langsung --}}
                    <div class="bg-light rounded-3 p-2.5 p-sm-3 d-flex justify-content-between align-items-center mb-2.5">
                        <div>
                            <div class="small text-muted" style="font-size: 0.72rem;">Sisa Tagihan:</div>
                            <div class="fw-bold text-danger fs-5">
                                Rp {{ number_format($t->sisa_tagihan, 0, ',', '.') }}
                            </div>
                        </div>
                        <div>
                            @if($t->status_tagihan == 'menunggu_verifikasi')
                                <span class="badge bg-warning text-dark py-2 px-3 small">
                                    <i class="bi bi-hourglass-split me-1"></i> Verifikasi
                                </span>
                            @else
                                <button type="button" class="btn btn-success fw-bold px-3 py-2 shadow-sm btn-pay-midtrans d-inline-flex align-items-center gap-1.5"
                                        data-id="{{ $t->id_tagihan }}"
                                        data-title="{{ $t->jenisTagihan->nama_tagihan }}"
                                        data-sisa="{{ (int)$t->sisa_tagihan }}"
                                        data-nominal="{{ (int)$t->nominal_tagihan }}"
                                        data-terbayar="{{ (int)$t->total_terbayar }}"
                                        data-santri="{{ $t->santri->nama_santri }}">
                                    <i class="bi bi-credit-card-2-front-fill fs-6"></i>
                                    <span>Bayar</span>
                                </button>
                            @endif
                        </div>
                    </div>

                    {{-- Jatuh Tempo Warning jika mendesak --}}
                    @if($hariLagi !== null)
                        <div class="mb-2">
                            @if($hariLagi < 0)
                                <div class="small text-danger fw-bold"><i class="bi bi-clock-history me-1"></i>Lewat tempo {{ abs($hariLagi) }} hari</div>
                            @elseif($hariLagi <= 7)
                                <div class="small text-warning fw-bold"><i class="bi bi-alarm me-1"></i>Jatuh tempo {{ $hariLagi }} hari lagi</div>
                            @endif
                        </div>
                    @endif

                    {{-- Baris Tombol Ajukan Penundaan & Dropdown Rincian --}}
                    <div class="d-flex flex-wrap gap-2">
                        @if($t->status_tagihan != 'menunggu_verifikasi')
                            <button type="button"
                                    class="btn btn-sm {{ $t->kategori_tunggakan ? 'btn-outline-warning text-dark' : 'btn-outline-secondary' }} flex-grow-1 py-1.5 rounded-pill fw-semibold d-flex align-items-center justify-content-center gap-1 btn-ajukan-penundaan"
                                    data-id="{{ $t->id_tagihan }}"
                                    data-santri="{{ $t->santri->nama_santri }}"
                                    data-title="{{ $t->jenisTagihan->nama_tagihan }}"
                                    data-periode="{{ ($t->bulan ? $t->nama_bulan . ' ' : '') . $t->tahun_ajaran }}"
                                    data-sisa="Rp {{ number_format($t->sisa_tagihan, 0, ',', '.') }}"
                                    data-kategori="{{ $t->kategori_tunggakan ?? '' }}"
                                    data-alasan="{{ $t->alasan_tunggakan ?? '' }}"
                                    data-tgl-janji="{{ $t->tgl_janji_bayar ?? '' }}"
                                    data-catatan-bendahara="{{ $t->catatan_bendahara ?? '' }}"
                                    style="font-size: 0.76rem;">
                                <i class="bi {{ $t->kategori_tunggakan ? 'bi-pencil-square text-warning' : 'bi-calendar2-plus' }}"></i>
                                <span>{{ $t->kategori_tunggakan ? 'Ubah Alasan Penundaan' : 'Ajukan Penundaan' }}</span>
                            </button>
                        @endif
                        <button class="btn btn-sm btn-outline-secondary flex-grow-1 py-1.5 rounded-pill d-flex align-items-center justify-content-center gap-1" 
                                type="button" 
                                data-bs-toggle="collapse" 
                                data-bs-target="#{{ $collapseId }}" 
                                aria-expanded="false"
                                style="font-size: 0.76rem;">
                            <i class="bi bi-info-circle"></i>
                            <span>Rincian Cicilan</span>
                            <i class="bi bi-chevron-down small"></i>
                        </button>
                    </div>

                    {{-- Panel Rincian Tersembunyi (Expandable) --}}
                    <div class="collapse mt-2.5 pt-2 border-top" id="{{ $collapseId }}">
                        <div class="row g-2 small mb-2">
                            <div class="col-6">
                                <span class="text-muted d-block">Periode:</span>
                                <span class="fw-semibold">{{ $t->bulan ? $t->nama_bulan . ' ' : '' }}{{ $t->tahun_ajaran }}</span>
                            </div>
                            <div class="col-6 text-end">
                                <span class="text-muted d-block">Total Biaya:</span>
                                <span class="fw-semibold">Rp {{ number_format($t->nominal_tagihan, 0, ',', '.') }}</span>
                            </div>
                        </div>
                        
                        {{-- Progres Cicilan --}}
                        <div class="mb-1">
                            <div class="d-flex justify-content-between small text-muted mb-1">
                                <span>Progres Pelunasan</span>
                                <span class="fw-bold text-dark">{{ $pctCicil }}%</span>
                            </div>
                            <div class="progress rounded-pill" style="height: 7px; background: #e2e8f0;">
                                <div class="bg-{{ $colorCicil }} rounded-pill" style="width: {{ $pctCicil }}%;"></div>
                            </div>
                            <div class="d-flex justify-content-between small text-muted mt-1.5">
                                <span>Terbayar: <strong class="text-success">Rp {{ number_format($t->total_terbayar, 0, ',', '.') }}</strong></span>
                                @if($t->jatuh_tempo)
                                    <span>Tempo: {{ \Carbon\Carbon::parse($t->jatuh_tempo)->translatedFormat('d M Y') }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-4 text-muted">
                <i class="bi bi-patch-check-fill text-success fs-1 d-block mb-2"></i>
                <h6 class="fw-bold text-success mb-1">Alhamdulillah! Seluruh tagihan sudah lunas.</h6>
                <small>Tidak ada tagihan aktif saat ini.</small>
            </div>
        @endforelse
    </div>
</div>

{{-- ============================================================ --}}
{{-- RIWAYAT PEMBAYARAN TERBARU — DESKTOP TABEL & HP KARTU       --}}
{{-- ============================================================ --}}
<div class="card border-0 shadow-sm rounded-4 overflow-hidden">
    <div class="card-header bg-white py-3 px-4 border-0 d-flex align-items-center justify-content-between">
        <h6 class="mb-0 fw-bold">
            <i class="bi bi-clock-history text-primary me-2"></i>Riwayat Pembayaran Terbaru
        </h6>
        <a href="{{ route('wali.riwayat') }}" class="btn btn-outline-primary btn-sm">
            <i class="bi bi-list-ul me-1"></i> Lihat Semua Riwayat
        </a>
    </div>

    {{-- TAMPILAN LAPTOP / DESKTOP (100% UTUH) --}}
    <div class="table-responsive d-none d-md-block">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr class="small text-muted text-uppercase">
                    <th class="ps-4">No Kwitansi</th>
                    <th>Tanggal Bayar</th>
                    <th>Santri</th>
                    <th>Komponen Biaya</th>
                    <th>Metode</th>
                    <th>Jumlah Dibayar</th>
                    <th>Status</th>
                    <th class="text-center pe-4">Kwitansi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($riwayatPembayaran as $p)
                    <tr>
                        <td class="ps-4">
                            @if($p->kwitansi)
                                <span class="badge bg-light text-dark border font-monospace">{{ $p->kwitansi->no_kwitansi }}</span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td class="small">{{ date('d/m/Y H:i', strtotime($p->tgl_bayar)) }}</td>
                        <td class="fw-semibold">{{ $p->tagihan->santri->nama_santri }}</td>
                        <td>{{ $p->tagihan->jenisTagihan->nama_tagihan }}</td>
                        <td>
                            <span class="badge bg-light text-dark border">
                                {{ $p->channel_bayar ?? ucfirst($p->metode_bayar) }}
                            </span>
                        </td>
                        <td class="fw-bold text-success">Rp {{ number_format($p->jumlah_bayar, 0, ',', '.') }}</td>
                        <td>
                            @if($p->status_transaksi == 'settlement')
                                <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Berhasil</span>
                            @elseif($p->status_transaksi == 'pending')
                                <span class="badge bg-warning text-dark"><i class="bi bi-hourglass me-1"></i>Pending</span>
                            @else
                                <span class="badge bg-secondary">{{ ucfirst($p->status_transaksi) }}</span>
                            @endif
                        </td>
                        <td class="text-center pe-4">
                            @if($p->kwitansi)
                                <a href="{{ route('kwitansi.show', $p->id_pembayaran) }}" class="btn btn-sm btn-outline-success" target="_blank">
                                    <i class="bi bi-printer me-1"></i> Kwitansi
                                </a>
                            @else
                                <span class="small text-muted">-</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-4 text-muted">
                            <i class="bi bi-inbox me-2"></i>Belum ada riwayat transaksi.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- TAMPILAN KHUSUS HP (KARTU RIWAYAT PRAKTIS) --}}
    <div class="d-md-none p-3 bg-light bg-opacity-50">
        @forelse($riwayatPembayaran as $p)
            <div class="card border-0 shadow-sm rounded-4 mb-2.5 overflow-hidden">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                        <div>
                            <span class="badge bg-secondary-subtle text-secondary small mb-1">
                                <i class="bi bi-person me-1"></i>{{ $p->tagihan->santri->nama_santri }}
                            </span>
                            <h6 class="fw-bold mb-0 text-dark">{{ $p->tagihan->jenisTagihan->nama_tagihan }}</h6>
                        </div>
                        <div>
                            @if($p->status_transaksi == 'settlement')
                                <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Berhasil</span>
                            @elseif($p->status_transaksi == 'pending')
                                <span class="badge bg-warning text-dark"><i class="bi bi-hourglass me-1"></i>Pending</span>
                            @else
                                <span class="badge bg-secondary">{{ ucfirst($p->status_transaksi) }}</span>
                            @endif
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center py-2 px-2.5 bg-light rounded-3 mb-2">
                        <div>
                            <div class="small text-muted" style="font-size: 0.72rem;">Jumlah Bayar:</div>
                            <div class="fw-bold text-success fs-6">Rp {{ number_format($p->jumlah_bayar, 0, ',', '.') }}</div>
                        </div>
                        <div class="text-end">
                            <div class="small text-muted" style="font-size: 0.72rem;">Metode / Channel:</div>
                            <span class="badge bg-white text-dark border">{{ $p->channel_bayar ?? ucfirst($p->metode_bayar) }}</span>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center text-muted small">
                        <span style="font-size: 0.78rem;">
                            <i class="bi bi-calendar3 me-1"></i>{{ date('d/m/Y H:i', strtotime($p->tgl_bayar)) }}
                        </span>
                        @if($p->kwitansi)
                            <a href="{{ route('kwitansi.show', $p->id_pembayaran) }}" class="btn btn-sm btn-outline-success py-1 px-2.5 rounded-pill fw-semibold" target="_blank" style="font-size: 0.78rem;">
                                <i class="bi bi-printer me-1"></i> Kwitansi
                            </a>
                        @else
                            <span class="font-monospace small text-muted" style="font-size: 0.75rem;">{{ $p->order_id }}</span>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-4 text-muted">
                <i class="bi bi-inbox me-2"></i>Belum ada riwayat transaksi.
            </div>
        @endforelse
    </div>
</div>

{{-- ============================================================ --}}
{{-- MODAL PEMBAYARAN — UPGRADE DENGAN QUICK SELECT --}}
{{-- ============================================================ --}}
<div class="modal fade" id="modalPaymentConfirm" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow-lg">
            <div class="modal-header border-0 pb-0 px-4 pt-4">
                <div>
                    <h5 class="modal-title fw-bold"><i class="bi bi-shield-lock-fill text-success me-2"></i>Konfirmasi Pembayaran</h5>
                    <p class="text-muted small mb-0">Pembayaran diproses aman melalui <strong>Midtrans</strong> Payment Gateway</p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body px-4 py-3">
                {{-- Info tagihan --}}
                <div class="p-3 rounded-3 mb-3" style="background:#f1f5f9;">
                    <div class="row g-1">
                        <div class="col-4 small text-muted">Santri</div>
                        <div class="col-8 small fw-bold text-dark" id="modal_santri_name"></div>
                        <div class="col-4 small text-muted">Tagihan</div>
                        <div class="col-8 small fw-bold text-dark" id="modal_tagihan_name"></div>
                        <div class="col-4 small text-muted">Total Biaya</div>
                        <div class="col-8 small fw-bold text-dark" id="modal_nominal_text"></div>
                        <div class="col-4 small text-muted">Sudah Dibayar</div>
                        <div class="col-8 small fw-bold text-success" id="modal_terbayar_text"></div>
                        <div class="col-4 small text-muted">Sisa Tagihan</div>
                        <div class="col-8 small fw-bold text-danger" id="modal_sisa_text"></div>
                    </div>
                </div>

                {{-- Quick select nominal --}}
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Pilih Nominal Pembayaran</label>
                    <div class="d-flex gap-2 flex-wrap mb-2" id="quick_select_btns">
                        <button type="button" class="btn btn-outline-secondary btn-sm quick-select" data-pct="25">25%</button>
                        <button type="button" class="btn btn-outline-secondary btn-sm quick-select" data-pct="50">50%</button>
                        <button type="button" class="btn btn-outline-secondary btn-sm quick-select" data-pct="75">75%</button>
                        <button type="button" class="btn btn-success btn-sm quick-select" data-pct="100">Lunas Penuh 100%</button>
                    </div>
                    <div class="input-group">
                        <span class="input-group-text fw-bold">Rp</span>
                        <input type="number" id="modal_input_bayar" class="form-control form-control-lg fw-bold text-success" min="10000" placeholder="Masukkan nominal...">
                    </div>
                    <small class="text-muted">Dapat dibayar lunas atau sebagian (cicilan). Min. Rp 10.000</small>
                </div>

                {{-- Breakdown total --}}
                <div class="p-3 rounded-3" style="background:#f0fdf4;border:1px solid #bbf7d0;">
                    <div class="d-flex justify-content-between small mb-1">
                        <span class="text-muted">Nominal Tagihan</span>
                        <span class="fw-semibold" id="summary_nominal">-</span>
                    </div>
                    <div class="d-flex justify-content-between small mb-1">
                        <span class="text-muted">Biaya Layanan Gateway</span>
                        <span class="fw-semibold text-warning">+ Rp 2.500</span>
                    </div>
                    <hr class="my-1">
                    <div class="d-flex justify-content-between fw-bold">
                        <span>Total Yang Dibayarkan</span>
                        <span class="text-success fs-6" id="summary_total">-</span>
                    </div>
                </div>

                <div class="alert alert-info py-2 small mt-3 mb-0 rounded-3">
                    <i class="bi bi-info-circle me-1"></i>
                    Mendukung <strong>Virtual Account (BCA, BNI, BRI, Mandiri)</strong> dan <strong>QRIS</strong>.
                    Status lunas terverifikasi otomatis dalam hitungan detik!
                </div>
            </div>
            <div class="modal-footer border-0 pt-0 px-4 pb-4">
                <button type="button" class="btn btn-light fw-semibold" data-bs-dismiss="modal">Batal</button>
                <button type="button" id="btn_confirm_snap" class="btn btn-success fw-bold px-4">
                    <i class="bi bi-wallet2 me-1"></i> Lanjut Ke Pembayaran
                </button>
            </div>
        </div>
    </div>
</div>

@include('wali.partials.modal-penundaan')

@push('scripts')
<script>
    // ================================================
    // COUNTDOWN JATUH TEMPO
    // ================================================
    document.querySelectorAll('.countdown-badge').forEach(function(el) {
        const due = new Date(el.getAttribute('data-due'));
        const now = new Date();
        const diff = Math.ceil((due - now) / (1000 * 60 * 60 * 24));
        if (diff > 0) el.textContent = diff + ' hari lagi';
        else if (diff === 0) el.textContent = 'Hari ini!';
        else el.textContent = Math.abs(diff) + ' hari lewat';
    });

    // ================================================
    // MODAL PEMBAYARAN — QUICK SELECT & SUMMARY
    // ================================================
    let activeTagihanId = null;
    let activeSisa = 0;
    const adminFee = {{ (int) \App\Models\Pengaturan::get('midtrans_biaya_admin', 2500) }};
    const modalPaymentConfirm = new bootstrap.Modal(document.getElementById('modalPaymentConfirm'));

    function formatRp(num) {
        return 'Rp ' + Number(num).toLocaleString('id-ID');
    }

    function updateSummary() {
        const val = parseInt(document.getElementById('modal_input_bayar').value) || 0;
        document.getElementById('summary_nominal').textContent = formatRp(val);
        document.getElementById('summary_total').textContent = formatRp(val + adminFee);
    }

    document.getElementById('modal_input_bayar').addEventListener('input', updateSummary);

    // Quick select buttons
    document.querySelectorAll('.quick-select').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const pct = parseInt(this.getAttribute('data-pct'));
            const val = Math.round(activeSisa * pct / 100);
            document.getElementById('modal_input_bayar').value = val;
            updateSummary();
            document.querySelectorAll('.quick-select').forEach(b => {
                b.classList.remove('btn-success', 'btn-primary');
                b.classList.add('btn-outline-secondary');
            });
            this.classList.remove('btn-outline-secondary');
            this.classList.add('btn-success');
        });
    });

    // Buka modal
    document.querySelectorAll('.btn-pay-midtrans').forEach(function(btn) {
        btn.addEventListener('click', function() {
            activeTagihanId = this.getAttribute('data-id');
            const title = this.getAttribute('data-title');
            activeSisa = parseInt(this.getAttribute('data-sisa'));
            const nominal = parseInt(this.getAttribute('data-nominal'));
            const terbayar = parseInt(this.getAttribute('data-terbayar'));
            const santri = this.getAttribute('data-santri');

            document.getElementById('modal_santri_name').textContent = santri;
            document.getElementById('modal_tagihan_name').textContent = title;
            document.getElementById('modal_nominal_text').textContent = formatRp(nominal);
            document.getElementById('modal_terbayar_text').textContent = formatRp(terbayar);
            document.getElementById('modal_sisa_text').textContent = formatRp(activeSisa);
            document.getElementById('modal_input_bayar').value = activeSisa;
            document.getElementById('modal_input_bayar').max = activeSisa;
            updateSummary();

            // Reset quick select
            document.querySelectorAll('.quick-select').forEach(b => {
                b.classList.remove('btn-success', 'btn-primary');
                b.classList.add('btn-outline-secondary');
            });
            document.querySelector('.quick-select[data-pct="100"]').classList.remove('btn-outline-secondary');
            document.querySelector('.quick-select[data-pct="100"]').classList.add('btn-success');

            modalPaymentConfirm.show();
        });
    });

    // Konfirmasi bayar
    document.getElementById('btn_confirm_snap').addEventListener('click', function() {
        if (!activeTagihanId) return;

        const nominalBayar = document.getElementById('modal_input_bayar').value;
        if (!nominalBayar || parseInt(nominalBayar) < 10000) {
            alert('Nominal pembayaran minimal Rp 10.000');
            return;
        }

        const btn = this;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Menghubungkan Gateway...';

        fetch(`/wali/tagihan/${activeTagihanId}/pay`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ nominal_bayar: nominalBayar })
        })
        .then(res => {
            // Pastikan response adalah JSON sebelum di-parse
            const contentType = res.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                throw new Error(`Server mengembalikan respon tidak valid (HTTP ${res.status}). Silakan coba lagi atau hubungi bendahara.`);
            }
            return res.json();
        })
        .then(data => {
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-wallet2 me-1"></i> Lanjut Ke Pembayaran';
            modalPaymentConfirm.hide();

            if (data.error) {
                showToast('Gagal: ' + data.error, 'danger');
                return;
            }

            if (!data.snap_token) {
                showToast('Gagal mendapatkan token pembayaran. Silakan coba lagi.', 'danger');
                return;
            }

            // Mode Demo: Jika Midtrans key belum valid (sandbox 401)
            if (data.is_demo) {
                modalPaymentConfirm.hide();
                const idPembayaran = data.id_pembayaran;
                const totalBayar = new Intl.NumberFormat('id-ID').format(data.total_bayar);
                if (confirm(
                    `⚠️ MODE DEMO PRESENTASI\n\n` +
                    `Midtrans Sandbox offline atau key demo digunakan.\n` +
                    `Total pembayaran: Rp ${totalBayar}\n\n` +
                    `Klik OK untuk SIMULASI PELUNASAN (Status jadi Lunas & Kwitansi Resmi terbit otomatis).\n` +
                    `Klik Batal untuk kembali.`
                )) {
                    showToast('Memproses simulasi pembayaran...', 'warning');
                    fetch(`/wali/pembayaran/${idPembayaran}/simulate-settlement`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(r => r.json())
                    .then(resSim => {
                        showToast(resSim.message || 'Pembayaran berhasil disimulasikan!', 'success');
                        setTimeout(() => window.location.reload(), 1500);
                    })
                    .catch(errSim => {
                        showToast('Error simulasi: ' + errSim.message, 'danger');
                    });
                } else {
                    showToast('Pembayaran dibatalkan.', 'warning');
                    setTimeout(() => window.location.reload(), 1500);
                }
                return;
            }

            // Periksa ketersediaan library Midtrans Snap
            if (typeof window.snap === 'undefined') {
                showToast('Library Midtrans belum termuat. Membuka simulasi pembayaran...', 'warning');
                if (confirm('Koneksi Midtrans Snap script tidak terdeteksi. Ingin menyelesaikan dengan simulasi settlement untuk demo?')) {
                    fetch(`/wali/pembayaran/${data.id_pembayaran}/simulate-settlement`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(r => r.json())
                    .then(resSim => {
                        showToast('Alhamdulillah! ' + resSim.message, 'success');
                        setTimeout(() => window.location.reload(), 1500);
                    });
                }
                return;
            }

            // Pembayaran normal via Midtrans Snap Popup
            window.snap.pay(data.snap_token, {
                onSuccess: function(result) {
                    showToast('Alhamdulillah! Pembayaran berhasil. Menyinkronkan status...', 'success');
                    fetch(`/wali/pembayaran/${data.id_pembayaran}/sync`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify(result || {})
                    })
                    .finally(() => {
                        setTimeout(() => window.location.reload(), 1200);
                    });
                },
                onPending: function(result) {
                    showToast('Instruksi pembayaran Virtual Account/QRIS diterbitkan. Menunggu pembayaran.', 'warning');
                    fetch(`/wali/pembayaran/${data.id_pembayaran}/sync`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify(result || {})
                    }).finally(() => {
                        setTimeout(() => window.location.reload(), 1500);
                    });
                },
                onError: function(result) {
                    showToast('Pembayaran gagal atau dibatalkan.', 'danger');
                    window.location.reload();
                },
                onClose: function() {
                    // Cek status jika popup ditutup
                    fetch(`/wali/pembayaran/${data.id_pembayaran}/sync`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    }).finally(() => {
                        window.location.reload();
                    });
                }
            });
        })
        .catch(err => {
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-wallet2 me-1"></i> Lanjut Ke Pembayaran';
            showToast('Error: ' + err.message, 'danger');
        });
    });

    // Toast notification
    function showToast(msg, type) {
        const toast = document.createElement('div');
        toast.className = `alert alert-${type} position-fixed shadow-lg fw-semibold`;
        toast.style.cssText = 'bottom:20px;right:12px;left:12px;margin:0 auto;z-index:9999;width:min(380px,calc(100vw - 24px));animation:fadeIn .3s ease;';
        toast.innerHTML = `<i class="bi bi-${type === 'success' ? 'check-circle' : type === 'warning' ? 'hourglass' : 'x-circle'}-fill me-2"></i>${msg}`;
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 4000);
    }
</script>
@endpush
@endsection
