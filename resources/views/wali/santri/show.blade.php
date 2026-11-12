@extends('layouts.app')

@section('title', 'Detail Tagihan — ' . $santri->nama_santri)
@section('page_title', 'Detail Tagihan Santri')

@section('content')

{{-- Breadcrumb --}}
<nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('wali.dashboard') }}" class="text-decoration-none">Portal Wali</a></li>
        <li class="breadcrumb-item active">{{ $santri->nama_santri }}</li>
    </ol>
</nav>

{{-- ============================================================ --}}
{{-- PROFIL SANTRI & STATISTIK BESAR --}}
{{-- ============================================================ --}}
@php
    $colorMap = [
        'PRG-TFZ' => ['#d97706','#fef3c7'],
        'PRG-ARB' => ['#2563eb','#dbeafe'],
        'PRG-ING' => ['#7c3aed','#ede9fe'],
    ];
    $progColor = $colorMap[$santri->id_program ?? ''] ?? ['#15803d','#dcfce7'];
    $kepatuhan = $kepatuhan ?? $santri->getKepatuhanInfo();
    $labelStatus = $kepatuhan['status'];
    $subStatus = $kepatuhan['sub'];
    $colorStatus = $kepatuhan['color'];
    $iconStatus = $kepatuhan['icon'];
@endphp

<div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
    <div class="card-body p-0">
        <div class="d-flex align-items-center flex-wrap gap-0"
             style="background: linear-gradient(135deg, {{ $progColor[0] }} 0%, {{ $progColor[0] }}cc 100%)">
            {{-- Avatar besar --}}
            <div class="p-3 p-sm-4 d-flex align-items-center gap-3 gap-sm-4 flex-grow-1">
                <div class="rounded-4 d-flex align-items-center justify-content-center fw-bold"
                     style="width:clamp(60px, 15vw, 80px);height:clamp(60px, 15vw, 80px);font-size:clamp(1.4rem, 4vw, 2rem);background:rgba(255,255,255,0.2);color:#fff;flex-shrink:0;">
                    {{ strtoupper(substr($santri->nama_santri, 0, 1)) }}{{ strtoupper(substr(explode(' ', trim($santri->nama_santri))[1] ?? 'X', 0, 1)) }}
                </div>
                <div class="text-white min-w-0">
                    <h4 class="fw-bold mb-1 text-truncate" style="font-size: clamp(1.1rem, 3.5vw, 1.5rem);">{{ $santri->nama_santri }}</h4>
                    <div class="small opacity-75 mb-1" style="font-size: clamp(0.75rem, 2.2vw, 0.875rem);">
                        NIS: <strong>{{ $santri->nis }}</strong> &nbsp;|&nbsp;
                        {{ $santri->tingkat }} Kelas {{ $santri->kelas }} &nbsp;|&nbsp;
                        {{ $santri->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}
                    </div>
                    <span class="badge bg-white fw-semibold mb-1" style="color:{{ $progColor[0] }}">
                        <i class="bi bi-star me-1"></i>{{ $santri->program->nama_program ?? '-' }}
                    </span>
                    <span class="badge ms-1 mb-1" style="background:rgba(255,255,255,0.2);color:#fff;">
                        {{ ucfirst($santri->status_aktif) }}
                    </span>
                </div>
            </div>

            {{-- Status kepatuhan --}}
            <div class="p-3 p-sm-4 text-center text-white border-top border-sm-0 border-white border-opacity-10 w-100 w-sm-auto" style="min-width:150px;">
                <div class="small opacity-75 mb-1 text-uppercase fw-semibold" style="font-size: 0.75rem;">Status Kepatuhan</div>
                <div class="fw-bold d-flex align-items-center justify-content-center gap-1" style="font-size: clamp(1.2rem, 3vw, 1.6rem);">
                    <i class="bi {{ $iconStatus }} fs-5"></i> {{ $labelStatus }}
                </div>
                <div class="small opacity-90 fw-medium">{{ $subStatus }}</div>
            </div>
        </div>
    </div>
</div>

{{-- Statistik 4 kotak --}}
<div class="row g-2 g-sm-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body text-center p-2 p-sm-3">
                <div class="fw-bold text-dark text-truncate" style="font-size: clamp(0.95rem, 2.5vw, 1.4rem);">Rp {{ number_format($totalTagihan, 0, ',', '.') }}</div>
                <div class="small text-muted mt-1" style="font-size: 0.78rem;">Total Tagihan</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm rounded-4 h-100" style="border-top:3px solid #16a34a !important;">
            <div class="card-body text-center p-2 p-sm-3">
                <div class="fw-bold text-success text-truncate" style="font-size: clamp(0.95rem, 2.5vw, 1.4rem);">Rp {{ number_format($totalTerbayar, 0, ',', '.') }}</div>
                <div class="small text-muted mt-1" style="font-size: 0.78rem;">Total Terbayar</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm rounded-4 h-100" style="border-top:3px solid #dc2626 !important;">
            <div class="card-body text-center p-2 p-sm-3">
                <div class="fw-bold text-danger text-truncate" style="font-size: clamp(0.95rem, 2.5vw, 1.4rem);">Rp {{ number_format($totalSisa, 0, ',', '.') }}</div>
                <div class="small text-muted mt-1" style="font-size: 0.78rem;">Sisa Tagihan</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm rounded-4 h-100" style="border-top:3px solid {{ $progColor[0] }} !important;">
            <div class="card-body text-center p-2 p-sm-3">
                <div class="fw-bold" style="color:{{ $progColor[0] }};font-size: clamp(0.95rem, 2.5vw, 1.4rem);">{{ $persen }}%</div>
                <div class="small text-muted mt-1" style="font-size: 0.78rem;">Progres Lunas</div>
                <div class="progress mt-2 rounded-pill" style="height:6px;background:#e2e8f0;">
                    <div class="rounded-pill" style="width:{{ $persen }}%;background:{{ $progColor[0] }};"></div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ============================================================ --}}
{{-- TAGIHAN BELUM LUNAS — DESKTOP TABEL & HP KARTU RINCIAN       --}}
{{-- ============================================================ --}}
@if($tagihanBelumLunas->count() > 0)
<div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
    <div class="card-header bg-white py-3 px-4 border-0 d-flex flex-wrap align-items-center justify-content-between gap-2">
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-receipt text-danger fs-5"></i>
            <h6 class="fw-bold mb-0">Tagihan Aktif (Belum Lunas)</h6>
            <span class="badge bg-danger rounded-pill">{{ $tagihanBelumLunas->count() }}</span>
        </div>
        <a href="{{ route('wali.panduan') }}" class="btn btn-outline-success btn-sm py-1 px-3 rounded-pill fw-semibold shadow-sm" style="font-size: 0.78rem;">
            <i class="bi bi-question-circle me-1"></i> Panduan Cara Bayar (QRIS &amp; Transfer)
        </a>
    </div>

    {{-- TAMPILAN LAPTOP / DESKTOP (100% UTUH SEPERTI ASLINYA) --}}
    <div class="table-responsive d-none d-md-block">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr class="small text-muted text-uppercase">
                    <th class="ps-4">Komponen Tagihan</th>
                    <th>Periode</th>
                    <th>Jatuh Tempo</th>
                    <th>Total Biaya</th>
                    <th>Progress Cicilan</th>
                    <th>Sisa Tagihan</th>
                    <th>Status</th>
                    <th class="text-center pe-4">Bayar</th>
                </tr>
            </thead>
            <tbody>
                @foreach($tagihanBelumLunas as $t)
                    @php
                        $pctCicil = $t->nominal_tagihan > 0 ? round(($t->total_terbayar / $t->nominal_tagihan) * 100) : 0;
                        $hariLagi = $t->jatuh_tempo ? (int) now()->diffInDays(\Carbon\Carbon::parse($t->jatuh_tempo), false) : null;
                    @endphp
                    <tr>
                        <td class="ps-4">
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
                        <td>
                            @if($t->jatuh_tempo)
                                <span class="small {{ $hariLagi !== null && $hariLagi <= 7 ? 'text-danger fw-bold' : 'text-muted' }}">
                                    {{ \Carbon\Carbon::parse($t->jatuh_tempo)->translatedFormat('d M Y') }}
                                    @if($hariLagi !== null && $hariLagi <= 7 && $hariLagi >= 0)
                                        <br><small class="text-warning"><i class="bi bi-alarm me-1"></i>{{ $hariLagi }} hari lagi</small>
                                    @elseif($hariLagi !== null && $hariLagi < 0)
                                        <br><small class="text-danger"><i class="bi bi-clock-history me-1"></i>Lewat {{ abs($hariLagi) }} hari</small>
                                    @endif
                                </span>
                            @else
                                <span class="text-muted small">-</span>
                            @endif
                        </td>
                        <td>Rp {{ number_format($t->nominal_tagihan, 0, ',', '.') }}</td>
                        <td style="min-width:160px;">
                            <div class="d-flex align-items-center gap-2">
                                <div class="progress flex-grow-1 rounded-pill" style="height:8px;background:#e2e8f0;">
                                    <div class="bg-{{ $pctCicil >= 60 ? 'success' : ($pctCicil >= 30 ? 'warning' : 'danger') }} rounded-pill" style="width:{{ $pctCicil }}%;"></div>
                                </div>
                                <small class="fw-semibold text-muted">{{ $pctCicil }}%</small>
                            </div>
                            <small class="text-muted">Terbayar: Rp {{ number_format($t->total_terbayar, 0, ',', '.') }}</small>
                        </td>
                        <td class="fw-bold text-danger">Rp {{ number_format($t->sisa_tagihan, 0, ',', '.') }}</td>
                        <td>{!! $t->status_badge !!}</td>
                        <td class="text-center pe-4">
                            @if($t->status_tagihan != 'menunggu_verifikasi')
                                <div class="d-flex flex-column flex-xl-row align-items-center justify-content-center gap-1">
                                    <button type="button" class="btn btn-success btn-sm fw-bold btn-pay-midtrans"
                                            data-id="{{ $t->id_tagihan }}"
                                            data-title="{{ $t->jenisTagihan->nama_tagihan }}"
                                            data-sisa="{{ (int)$t->sisa_tagihan }}"
                                            data-nominal="{{ (int)$t->nominal_tagihan }}"
                                            data-terbayar="{{ (int)$t->total_terbayar }}"
                                            data-santri="{{ $santri->nama_santri }}">
                                        <i class="bi bi-credit-card me-1"></i> Bayar
                                    </button>
                                    <button type="button" class="btn {{ $t->kategori_tunggakan ? 'btn-outline-warning text-dark' : 'btn-outline-secondary' }} btn-sm fw-semibold btn-ajukan-penundaan"
                                            data-id="{{ $t->id_tagihan }}"
                                            data-santri="{{ $santri->nama_santri }}"
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
                            @else
                                <span class="badge bg-warning text-dark px-2 py-1">Menunggu Verifikasi</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- TAMPILAN KHUSUS HP (POLA 1: KARTU RINCIAN & TOMBOL BAYAR INSTAN) --}}
    <div class="d-md-none p-3 bg-light bg-opacity-50">
        @foreach($tagihanBelumLunas as $idx => $t)
            @php
                $pctCicil = $t->nominal_tagihan > 0 ? round(($t->total_terbayar / $t->nominal_tagihan) * 100) : 0;
                $colorCicil = $pctCicil >= 60 ? 'success' : ($pctCicil >= 30 ? 'warning' : 'danger');
                $hariLagi = $t->jatuh_tempo ? (int) now()->diffInDays(\Carbon\Carbon::parse($t->jatuh_tempo), false) : null;
                $collapseId = 'show_tagihan_detail_' . $t->id_tagihan . '_' . $idx;
            @endphp
            <div class="card border-0 shadow-sm rounded-4 mb-3 overflow-hidden border-start border-4 border-{{ $t->sisa_tagihan > 0 ? 'danger' : 'success' }}">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                        <div>
                            <span class="badge bg-success-subtle text-success small mb-1">
                                <i class="bi bi-tag me-1"></i>Tagihan Aktif
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

                    {{-- Baris Sisa Tagihan & Tombol Bayar --}}
                    <div class="bg-light rounded-3 p-2.5 p-sm-3 d-flex justify-content-between align-items-center mb-2.5">
                        <div>
                            <div class="small text-muted" style="font-size: 0.72rem;">Sisa Tagihan:</div>
                            <div class="fw-bold text-danger fs-5">
                                Rp {{ number_format($t->sisa_tagihan, 0, ',', '.') }}
                            </div>
                        </div>
                        <div>
                            @if($t->status_tagihan != 'menunggu_verifikasi')
                                <button type="button" class="btn btn-success fw-bold px-3 py-2 shadow-sm btn-pay-midtrans d-inline-flex align-items-center gap-1.5"
                                        data-id="{{ $t->id_tagihan }}"
                                        data-title="{{ $t->jenisTagihan->nama_tagihan }}"
                                        data-sisa="{{ (int)$t->sisa_tagihan }}"
                                        data-nominal="{{ (int)$t->nominal_tagihan }}"
                                        data-terbayar="{{ (int)$t->total_terbayar }}"
                                        data-santri="{{ $santri->nama_santri }}">
                                    <i class="bi bi-credit-card-2-front-fill fs-6"></i>
                                    <span>Bayar</span>
                                </button>
                            @else
                                <span class="badge bg-warning text-dark py-2 px-3 small">Verifikasi</span>
                            @endif
                        </div>
                    </div>

                    {{-- Warning Jatuh Tempo --}}
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
                                    data-santri="{{ $santri->nama_santri }}"
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
        @endforeach
    </div>
</div>
@endif

{{-- ============================================================ --}}
{{-- TAGIHAN LUNAS — DESKTOP TABEL & HP KARTU RINGKAS             --}}
{{-- ============================================================ --}}
@if($tagihanLunas->count() > 0)
<div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
    <div class="card-header bg-white py-3 px-4 border-0 d-flex align-items-center gap-2">
        <i class="bi bi-patch-check-fill text-success fs-5"></i>
        <h6 class="fw-bold mb-0">Tagihan Sudah Lunas</h6>
        <span class="badge bg-success rounded-pill ms-auto">{{ $tagihanLunas->count() }}</span>
    </div>

    {{-- TAMPILAN LAPTOP / DESKTOP (100% UTUH) --}}
    <div class="table-responsive d-none d-md-block">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr class="small text-muted text-uppercase">
                    <th class="ps-4">Komponen Tagihan</th>
                    <th>Periode</th>
                    <th>Total Biaya</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($tagihanLunas as $t)
                    <tr>
                        <td class="ps-4">
                            <div class="fw-semibold">{{ $t->jenisTagihan->nama_tagihan }}</div>
                            @if($t->kategori_info)
                                <div class="mt-1 d-flex flex-wrap align-items-center gap-1">
                                    {!! $t->kategori_badge !!}
                                    @if($t->tgl_janji_bayar)
                                        <span class="badge bg-light text-dark border small" style="font-size: 0.72rem;">
                                            <i class="bi bi-calendar-event text-warning me-1"></i>Janji: {{ date('d/m/Y', strtotime($t->tgl_janji_bayar)) }}
                                        </span>
                                    @endif
                                </div>
                            @endif
                        </td>
                        <td class="small text-muted">{{ $t->bulan ? $t->nama_bulan . ' ' : '' }}{{ $t->tahun_ajaran }}</td>
                        <td>Rp {{ number_format($t->nominal_tagihan, 0, ',', '.') }}</td>
                        <td><span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Lunas</span></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- TAMPILAN KHUSUS HP --}}
    <div class="d-md-none p-3 bg-light bg-opacity-50">
        @foreach($tagihanLunas as $t)
            <div class="card border-0 shadow-sm rounded-4 mb-2 p-3 border-start border-4 border-success">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="fw-bold mb-1 text-dark">{{ $t->jenisTagihan->nama_tagihan }}</h6>
                        <small class="text-muted d-block">{{ $t->bulan ? $t->nama_bulan . ' ' : '' }}{{ $t->tahun_ajaran }}</small>
                        <div class="fw-bold text-success mt-1">Rp {{ number_format($t->nominal_tagihan, 0, ',', '.') }}</div>
                    </div>
                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1.5 rounded-pill">
                        <i class="bi bi-check-circle me-1"></i>Lunas
                    </span>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endif

{{-- ============================================================ --}}
{{-- RIWAYAT PEMBAYARAN SANTRI INI — DESKTOP TABEL & HP KARTU     --}}
{{-- ============================================================ --}}
<div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
    <div class="card-header bg-white py-3 px-4 border-0">
        <h6 class="fw-bold mb-0"><i class="bi bi-clock-history text-primary me-2"></i>Riwayat Pembayaran</h6>
    </div>

    {{-- TAMPILAN LAPTOP / DESKTOP (100% UTUH) --}}
    <div class="table-responsive d-none d-md-block">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr class="small text-muted text-uppercase">
                    <th class="ps-4">No Kwitansi</th>
                    <th>Tanggal Bayar</th>
                    <th>Komponen Biaya</th>
                    <th>Metode</th>
                    <th>Jumlah Dibayar</th>
                    <th>Status</th>
                    <th class="text-center pe-4">Kwitansi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($riwayat as $p)
                    <tr>
                        <td class="ps-4">
                            @if($p->kwitansi)
                                <span class="badge bg-light text-dark border font-monospace">{{ $p->kwitansi->no_kwitansi }}</span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td class="small">{{ date('d/m/Y H:i', strtotime($p->tgl_bayar)) }}</td>
                        <td>{{ $p->tagihan->jenisTagihan->nama_tagihan }}</td>
                        <td><span class="badge bg-light text-dark border">{{ $p->channel_bayar ?? ucfirst($p->metode_bayar) }}</span></td>
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
                                    <i class="bi bi-printer me-1"></i> Cetak
                                </a>
                            @else
                                <span class="small text-muted">-</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">Belum ada riwayat transaksi.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- TAMPILAN KHUSUS HP --}}
    <div class="d-md-none p-3 bg-light bg-opacity-50">
        @forelse($riwayat as $p)
            <div class="card border-0 shadow-sm rounded-4 mb-2.5 overflow-hidden">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                        <h6 class="fw-bold mb-0 text-dark">{{ $p->tagihan->jenisTagihan->nama_tagihan }}</h6>
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
                                <i class="bi bi-printer me-1"></i> Cetak Kwitansi
                            </a>
                        @else
                            <span class="font-monospace small text-muted" style="font-size: 0.75rem;">{{ $p->order_id }}</span>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-4 text-muted">Belum ada riwayat transaksi.</div>
        @endforelse
    </div>
</div>
    @if($riwayat->hasPages())
        <div class="card-footer bg-white border-0 d-flex justify-content-center py-3">
            {{ $riwayat->links() }}
        </div>
    @endif
</div>

<div class="text-center mb-4">
    <a href="{{ route('wali.dashboard') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Kembali ke Dashboard
    </a>
</div>

{{-- Modal Pembayaran (sama persis dengan dashboard) --}}
<div class="modal fade" id="modalPaymentConfirm" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow-lg">
            <div class="modal-header border-0 pb-0 px-4 pt-4">
                <div>
                    <h5 class="modal-title fw-bold"><i class="bi bi-shield-lock-fill text-success me-2"></i>Konfirmasi Pembayaran</h5>
                    <p class="text-muted small mb-0">Pembayaran diproses aman melalui <strong>Midtrans</strong></p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body px-4 py-3">
                <div class="p-3 rounded-3 mb-3" style="background:#f1f5f9;">
                    <div class="row g-1">
                        <div class="col-4 small text-muted">Santri</div>
                        <div class="col-8 small fw-bold text-dark" id="modal_santri_name"></div>
                        <div class="col-4 small text-muted">Tagihan</div>
                        <div class="col-8 small fw-bold text-dark" id="modal_tagihan_name"></div>
                        <div class="col-4 small text-muted">Sisa Tagihan</div>
                        <div class="col-8 small fw-bold text-danger" id="modal_sisa_text"></div>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Nominal Pembayaran</label>
                    <div class="d-flex gap-2 flex-wrap mb-2">
                        <button type="button" class="btn btn-outline-secondary btn-sm quick-select" data-pct="25">25%</button>
                        <button type="button" class="btn btn-outline-secondary btn-sm quick-select" data-pct="50">50%</button>
                        <button type="button" class="btn btn-outline-secondary btn-sm quick-select" data-pct="75">75%</button>
                        <button type="button" class="btn btn-success btn-sm quick-select" data-pct="100">Lunas 100%</button>
                    </div>
                    <div class="input-group">
                        <span class="input-group-text fw-bold">Rp</span>
                        <input type="number" id="modal_input_bayar" class="form-control form-control-lg fw-bold text-success" min="10000">
                    </div>
                </div>
                <div class="p-3 rounded-3" style="background:#f0fdf4;border:1px solid #bbf7d0;">
                    <div class="d-flex justify-content-between small mb-1">
                        <span class="text-muted">Nominal Bayar</span>
                        <span class="fw-semibold" id="summary_nominal">-</span>
                    </div>
                    <div class="d-flex justify-content-between small mb-1">
                        <span class="text-muted">Biaya Layanan</span>
                        <span class="fw-semibold text-warning">+ Rp 2.500</span>
                    </div>
                    <hr class="my-1">
                    <div class="d-flex justify-content-between fw-bold">
                        <span>Total Dibayarkan</span>
                        <span class="text-success" id="summary_total">-</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0 px-4 pb-4">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
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
    let activeTagihanId = null, activeSisa = 0;
    const adminFee = {{ (int) \App\Models\Pengaturan::get('midtrans_biaya_admin', 2500) }};
    const modal = new bootstrap.Modal(document.getElementById('modalPaymentConfirm'));
    function formatRp(n) { return 'Rp ' + Number(n).toLocaleString('id-ID'); }
    function updateSummary() {
        const v = parseInt(document.getElementById('modal_input_bayar').value) || 0;
        document.getElementById('summary_nominal').textContent = formatRp(v);
        document.getElementById('summary_total').textContent = formatRp(v + adminFee);
    }
    document.getElementById('modal_input_bayar').addEventListener('input', updateSummary);
    document.querySelectorAll('.quick-select').forEach(btn => {
        btn.addEventListener('click', function() {
            const val = Math.round(activeSisa * parseInt(this.getAttribute('data-pct')) / 100);
            document.getElementById('modal_input_bayar').value = val;
            updateSummary();
            document.querySelectorAll('.quick-select').forEach(b => { b.classList.remove('btn-success'); b.classList.add('btn-outline-secondary'); });
            this.classList.remove('btn-outline-secondary'); this.classList.add('btn-success');
        });
    });
    document.querySelectorAll('.btn-pay-midtrans').forEach(btn => {
        btn.addEventListener('click', function() {
            activeTagihanId = this.getAttribute('data-id');
            activeSisa = parseInt(this.getAttribute('data-sisa'));
            document.getElementById('modal_santri_name').textContent = this.getAttribute('data-santri');
            document.getElementById('modal_tagihan_name').textContent = this.getAttribute('data-title');
            document.getElementById('modal_sisa_text').textContent = formatRp(activeSisa);
            document.getElementById('modal_input_bayar').value = activeSisa;
            document.getElementById('modal_input_bayar').max = activeSisa;
            updateSummary();
            modal.show();
        });
    });
    document.getElementById('btn_confirm_snap').addEventListener('click', function() {
        if (!activeTagihanId) return;
        const nominal = document.getElementById('modal_input_bayar').value;
        if (!nominal || parseInt(nominal) < 10000) {
            alert('Nominal pembayaran minimal Rp 10.000');
            return;
        }

        const btn = this;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Menghubungkan...';

        fetch(`/wali/tagihan/${activeTagihanId}/pay`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ nominal_bayar: nominal })
        })
        .then(res => {
            const contentType = res.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                throw new Error(`Server mengembalikan respon tidak valid (HTTP ${res.status}). Silakan coba lagi.`);
            }
            return res.json();
        })
        .then(data => {
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-wallet2 me-1"></i> Lanjut Ke Pembayaran';
            modal.hide();

            if (data.error) {
                alert('Gagal: ' + data.error);
                return;
            }

            if (!data.snap_token) {
                alert('Gagal mendapatkan token pembayaran. Silakan coba lagi.');
                return;
            }

            // Mode Demo / Sandbox fallback
            if (data.is_demo || typeof window.snap === 'undefined') {
                if (confirm('Simulasikan penyelesaian pembayaran secara instan untuk keperluan demo?')) {
                    fetch(`/wali/pembayaran/${data.id_pembayaran}/simulate-settlement`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(r => r.json())
                    .then(resSim => {
                        alert(resSim.message || 'Pembayaran berhasil disimulasikan!');
                        window.location.reload();
                    });
                }
                return;
            }

            window.snap.pay(data.snap_token, {
                onSuccess: (result) => {
                    fetch(`/wali/pembayaran/${data.id_pembayaran}/sync`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify(result || {})
                    }).finally(() => {
                        alert('Alhamdulillah! Pembayaran berhasil diterima.');
                        window.location.reload();
                    });
                },
                onPending: (result) => {
                    fetch(`/wali/pembayaran/${data.id_pembayaran}/sync`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify(result || {})
                    }).finally(() => {
                        alert('Menunggu pembayaran VA/QRIS. Silakan selesaikan pembayaran.');
                        window.location.reload();
                    });
                },
                onError: () => {
                    alert('Pembayaran gagal atau dibatalkan.');
                    window.location.reload();
                },
                onClose: () => {
                    fetch(`/wali/pembayaran/${data.id_pembayaran}/sync`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
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
            alert('Terjadi kesalahan: ' + err.message);
        });
    });
</script>
@endpush
@endsection

