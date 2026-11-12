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

                            <!-- Hidden input untuk nilai submit form -->
                            <input type="hidden" name="nis" id="input_selected_nis" value="{{ old('nis') }}" required>

                            <!-- Custom Searchable Combobox Container -->
                            <div class="position-relative" id="santri_search_wrapper">
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0 text-muted">
                                        <i class="bi bi-search" id="icon_search_santri"></i>
                                    </span>
                                    <input type="text" class="form-control border-start-0 ps-0" id="input_search_santri" 
                                           placeholder="🔍 Ketik nama santri atau NIS untuk mencari..." 
                                           autocomplete="off">
                                    <button class="btn btn-outline-secondary border-start-0" type="button" id="btn_toggle_dropdown" title="Tampilkan semua santri">
                                        <i class="bi bi-chevron-down"></i>
                                    </button>
                                </div>

                                <!-- Card Santri Terpilih -->
                                <div id="card_selected_santri" class="mt-2 p-2 px-3 rounded-3 bg-light border border-success-subtle align-items-center justify-content-between" style="display: none;">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center fw-bold" style="width: 36px; height: 36px; font-size: 0.85rem;" id="selected_avatar">
                                            SW
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark small" id="selected_nama">-</div>
                                            <div class="text-muted" style="font-size: 0.75rem;" id="selected_meta">NIS: -</div>
                                        </div>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-outline-danger py-1 px-2" id="btn_reset_santri" title="Ganti Santri">
                                        <i class="bi bi-x-lg me-1"></i>Ganti
                                    </button>
                                </div>

                                <!-- Dropdown Hasil Pencarian (Live AJAX) -->
                                <div class="dropdown-menu shadow-lg p-0 rounded-3 border position-absolute w-100 mt-1" 
                                     id="dropdown_santri_results" 
                                     style="display: none; max-height: 290px; overflow-y: auto; z-index: 1060;">
                                    <div class="p-2 border-bottom bg-light small text-muted d-flex justify-content-between align-items-center">
                                        <span id="results_count_label">Menampilkan daftar santri</span>
                                        <span class="spinner-border spinner-border-sm text-success" id="search_loading_spinner" style="display: none;"></span>
                                    </div>
                                    <div id="results_list_container">
                                        <!-- Item diisi via JavaScript / AJAX -->
                                    </div>
                                </div>
                            </div>
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
                            <select name="tahun_ajaran" class="form-select" required>
                                @foreach($tahunAjaranList as $ta)
                                    <option value="{{ $ta->nama_tahun_ajaran }}" {{ (old('tahun_ajaran', $tahunAjaranAktif?->nama_tahun_ajaran) == $ta->nama_tahun_ajaran) ? 'selected' : '' }}>
                                        {{ $ta->nama_tahun_ajaran }} ({{ $ta->semester }}) {{ $ta->is_aktif ? '— Aktif' : '' }}
                                    </option>
                                @endforeach
                            </select>
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

@php
    $santriJson = $santriList->map(function($s) {
        $parts = explode(' ', trim($s->nama_santri));
        $inisial = strtoupper(substr($parts[0] ?? 'S', 0, 1)) . strtoupper(substr($parts[1] ?? ($parts[0] ?? 'X'), 0, 1));
        return [
            'nis' => $s->nis,
            'nama' => $s->nama_santri,
            'tingkat' => $s->tingkat,
            'kelas' => $s->kelas,
            'program' => $s->program->nama_program ?? '-',
            'inisial' => $inisial,
        ];
    })->values();
@endphp

@push('scripts')
<script>
    // Data santri preloaded dari server
    const preloadedSantri = {!! json_encode($santriJson) !!};

    const inputNis = document.getElementById('input_selected_nis');
    const searchInput = document.getElementById('input_search_santri');
    const dropdownMenu = document.getElementById('dropdown_santri_results');
    const listContainer = document.getElementById('results_list_container');
    const countLabel = document.getElementById('results_count_label');
    const loadingSpinner = document.getElementById('search_loading_spinner');
    const cardSelected = document.getElementById('card_selected_santri');
    const btnToggle = document.getElementById('btn_toggle_dropdown');
    const btnReset = document.getElementById('btn_reset_santri');

    let debounceTimer = null;

    function renderResults(list, query = '') {
        listContainer.innerHTML = '';
        if (!list || list.length === 0) {
            listContainer.innerHTML = '<div class="p-3 text-center text-muted small"><i class="bi bi-info-circle me-1"></i>Tidak ada santri yang cocok dengan pencarian</div>';
            countLabel.textContent = '0 santri ditemukan';
            return;
        }

        countLabel.textContent = `${list.length} santri ditemukan`;

        list.forEach(item => {
            const row = document.createElement('div');
            row.className = 'dropdown-item p-2 d-flex align-items-center justify-content-between gap-2 border-bottom border-light';
            row.style.cursor = 'pointer';

            // Highlight query
            let namaHighlighted = item.nama;
            let nisHighlighted = item.nis;
            if (query) {
                const reg = new RegExp(`(${query})`, 'gi');
                namaHighlighted = namaHighlighted.replace(reg, '<mark class="p-0 bg-warning-subtle fw-bold">$1</mark>');
                nisHighlighted = nisHighlighted.replace(reg, '<mark class="p-0 bg-warning-subtle fw-bold">$1</mark>');
            }

            row.innerHTML = `
                <div class="d-flex align-items-center gap-2 min-w-0">
                    <div class="rounded-circle bg-success-subtle text-success d-flex align-items-center justify-content-center fw-bold flex-shrink-0" style="width: 32px; height: 32px; font-size: 0.75rem;">
                        ${item.inisial}
                    </div>
                    <div class="text-truncate">
                        <div class="fw-semibold text-dark small text-truncate">${namaHighlighted}</div>
                        <div class="text-muted" style="font-size: 0.72rem;">NIS: ${nisHighlighted} &bull; ${item.tingkat} ${item.kelas}</div>
                    </div>
                </div>
                <span class="badge bg-light text-secondary border flex-shrink-0" style="font-size: 0.68rem;">
                    ${item.program}
                </span>
            `;

            row.addEventListener('click', () => {
                selectSantri(item);
            });

            listContainer.appendChild(row);
        });
    }

    function selectSantri(item) {
        inputNis.value = item.nis;
        searchInput.value = '';
        dropdownMenu.style.display = 'none';

        document.getElementById('selected_avatar').textContent = item.inisial;
        document.getElementById('selected_nama').textContent = item.nama;
        document.getElementById('selected_meta').textContent = `NIS: ${item.nis} | ${item.tingkat} ${item.kelas} | ${item.program}`;

        cardSelected.style.display = 'flex';
        searchInput.parentElement.style.display = 'none';
    }

    function resetSantriSelection() {
        inputNis.value = '';
        cardSelected.style.display = 'none';
        searchInput.parentElement.style.display = 'flex';
        searchInput.value = '';
        searchInput.focus();
        renderResults(preloadedSantri.slice(0, 20));
        dropdownMenu.style.display = 'block';
    }

    // Live search AJAX + fallback
    function performSearch(q) {
        if (!q || q.trim() === '') {
            renderResults(preloadedSantri.slice(0, 20));
            dropdownMenu.style.display = 'block';
            return;
        }

        loadingSpinner.style.display = 'inline-block';

        // AJAX search ke backend
        fetch(`/bendahara/santri/search-ajax?q=${encodeURIComponent(q)}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            loadingSpinner.style.display = 'none';
            renderResults(data, q);
            dropdownMenu.style.display = 'block';
        })
        .catch(err => {
            loadingSpinner.style.display = 'none';
            // Fallback client-side search jika offline
            const qLower = q.toLowerCase();
            const filtered = preloadedSantri.filter(s => 
                s.nama.toLowerCase().includes(qLower) || 
                s.nis.toLowerCase().includes(qLower) || 
                (s.kelas && s.kelas.toLowerCase().includes(qLower))
            );
            renderResults(filtered, q);
            dropdownMenu.style.display = 'block';
        });
    }

    searchInput.addEventListener('input', function() {
        clearTimeout(debounceTimer);
        const q = this.value;
        debounceTimer = setTimeout(() => performSearch(q), 250);
    });

    searchInput.addEventListener('focus', function() {
        performSearch(this.value);
    });

    btnToggle.addEventListener('click', function(e) {
        e.stopPropagation();
        if (dropdownMenu.style.display === 'block') {
            dropdownMenu.style.display = 'none';
        } else {
            performSearch(searchInput.value);
        }
    });

    btnReset.addEventListener('click', resetSantriSelection);

    // Close dropdown when clicked outside
    document.addEventListener('click', function(e) {
        if (!document.getElementById('santri_search_wrapper').contains(e.target)) {
            dropdownMenu.style.display = 'none';
        }
    });

    // Check old value on reload
    document.addEventListener('DOMContentLoaded', function() {
        const initialNis = inputNis.value;
        if (initialNis) {
            const found = preloadedSantri.find(s => s.nis === initialNis);
            if (found) {
                selectSantri(found);
            }
        }
    });

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

