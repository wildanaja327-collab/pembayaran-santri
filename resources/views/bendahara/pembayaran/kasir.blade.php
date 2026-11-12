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
                        
                        <!-- Hidden input untuk submit form -->
                        <input type="hidden" name="id_tagihan" id="input_selected_tagihan" value="{{ old('id_tagihan', $selectedTagihanId) }}" required>

                        <!-- Custom Searchable Combobox Container -->
                        <div class="position-relative" id="tagihan_search_wrapper">
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0 text-muted">
                                    <i class="bi bi-search" id="icon_search_tagihan"></i>
                                </span>
                                <input type="text" class="form-control border-start-0 ps-0" id="input_search_tagihan" 
                                       placeholder="🔍 Cari nama santri, NIS, atau jenis tagihan..." 
                                       autocomplete="off">
                                <button class="btn btn-outline-secondary border-start-0" type="button" id="btn_toggle_tagihan_dropdown" title="Tampilkan semua tagihan aktif">
                                    <i class="bi bi-chevron-down"></i>
                                </button>
                            </div>

                            <!-- Card Tagihan Terpilih -->
                            <div id="card_selected_tagihan" class="mt-2 p-3 rounded-3 bg-light border border-success-subtle align-items-center justify-content-between" style="display: none;">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center fw-bold" style="width: 40px; height: 40px;" id="tagihan_selected_avatar">
                                        <i class="bi bi-receipt"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark" id="tagihan_selected_santri">-</div>
                                        <div class="text-muted small" id="tagihan_selected_info">-</div>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-danger py-1 px-2" id="btn_reset_tagihan" title="Ganti Tagihan">
                                    <i class="bi bi-x-lg me-1"></i>Ganti
                                </button>
                            </div>

                            <!-- Dropdown Hasil Pencarian Tagihan (Live AJAX) -->
                            <div class="dropdown-menu shadow-lg p-0 rounded-3 border position-absolute w-100 mt-1" 
                                 id="dropdown_tagihan_results" 
                                 style="display: none; max-height: 290px; overflow-y: auto; z-index: 1060;">
                                <div class="p-2 border-bottom bg-light small text-muted d-flex justify-content-between align-items-center">
                                    <span id="tagihan_count_label">Menampilkan daftar tagihan aktif</span>
                                    <span class="spinner-border spinner-border-sm text-success" id="tagihan_loading_spinner" style="display: none;"></span>
                                </div>
                                <div id="tagihan_results_list">
                                    <!-- Items diisi via JS / AJAX -->
                                </div>
                            </div>
                        </div>
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

@php
    $tagihanJson = $tagihanList->map(function($t) {
        return [
            'id_tagihan' => $t->id_tagihan,
            'nis' => $t->nis,
            'nama_santri' => $t->santri->nama_santri ?? '-',
            'jenis_tagihan' => $t->jenisTagihan->nama_tagihan ?? '-',
            'bulan' => $t->bulan ? 'Bulan ' . $t->nama_bulan : '',
            'tahun_ajaran' => $t->tahun_ajaran,
            'nominal_tagihan' => (int) $t->nominal_tagihan,
            'sisa_tagihan' => (int) $t->sisa_tagihan,
            'total_terbayar' => (int) $t->total_terbayar,
            'sisa_format' => 'Rp ' . number_format($t->sisa_tagihan, 0, ',', '.'),
            'nominal_format' => 'Rp ' . number_format($t->nominal_tagihan, 0, ',', '.'),
        ];
    })->values();
@endphp

@push('scripts')
<script>
    // Preload data tagihan aktif dari server
    const preloadedTagihan = {!! json_encode($tagihanJson) !!};

    const inputTagihan = document.getElementById('input_selected_tagihan');
    const searchInput = document.getElementById('input_search_tagihan');
    const dropdownMenu = document.getElementById('dropdown_tagihan_results');
    const listContainer = document.getElementById('tagihan_results_list');
    const countLabel = document.getElementById('tagihan_count_label');
    const loadingSpinner = document.getElementById('tagihan_loading_spinner');
    const cardSelected = document.getElementById('card_selected_tagihan');
    const btnToggle = document.getElementById('btn_toggle_tagihan_dropdown');
    const btnReset = document.getElementById('btn_reset_tagihan');
    const inputJumlahBayar = document.getElementById('input_jumlah_bayar');

    let debounceTimer = null;

    function renderTagihanResults(list, query = '') {
        listContainer.innerHTML = '';
        if (!list || list.length === 0) {
            listContainer.innerHTML = '<div class="p-3 text-center text-muted small"><i class="bi bi-info-circle me-1"></i>Tidak ada tagihan yang cocok</div>';
            countLabel.textContent = '0 tagihan ditemukan';
            return;
        }

        countLabel.textContent = `${list.length} tagihan aktif ditemukan`;

        list.forEach(item => {
            const row = document.createElement('div');
            row.className = 'dropdown-item p-2 d-flex align-items-center justify-content-between gap-2 border-bottom border-light';
            row.style.cursor = 'pointer';

            let santriText = `${item.nama_santri} (NIS: ${item.nis})`;
            let tagihanText = `${item.jenis_tagihan} ${item.bulan ? item.bulan + ' ' : ''}(${item.tahun_ajaran})`;

            if (query) {
                const reg = new RegExp(`(${query})`, 'gi');
                santriText = santriText.replace(reg, '<mark class="p-0 bg-warning-subtle fw-bold">$1</mark>');
                tagihanText = tagihanText.replace(reg, '<mark class="p-0 bg-warning-subtle fw-bold">$1</mark>');
            }

            row.innerHTML = `
                <div class="min-w-0">
                    <div class="fw-semibold text-dark small text-truncate">${santriText}</div>
                    <div class="text-muted" style="font-size: 0.75rem;">${tagihanText}</div>
                </div>
                <div class="text-end flex-shrink-0">
                    <div class="fw-bold text-danger small">${item.sisa_format}</div>
                    <div class="text-muted" style="font-size: 0.68rem;">Total: ${item.nominal_format}</div>
                </div>
            `;

            row.addEventListener('click', () => {
                selectTagihan(item);
            });

            listContainer.appendChild(row);
        });
    }

    function selectTagihan(item) {
        inputTagihan.value = item.id_tagihan;
        searchInput.value = '';
        dropdownMenu.style.display = 'none';

        document.getElementById('tagihan_selected_santri').textContent = `${item.nama_santri} (NIS: ${item.nis})`;
        document.getElementById('tagihan_selected_info').textContent = `${item.jenis_tagihan} ${item.bulan ? item.bulan + ' ' : ''}(${item.tahun_ajaran})`;

        document.getElementById('display_sisa').innerText = item.sisa_format;
        document.getElementById('display_nominal').innerText = item.nominal_format;

        inputJumlahBayar.value = item.sisa_tagihan;
        inputJumlahBayar.max = item.sisa_tagihan;

        cardSelected.style.display = 'flex';
        searchInput.parentElement.style.display = 'none';
    }

    function resetTagihanSelection() {
        inputTagihan.value = '';
        cardSelected.style.display = 'none';
        searchInput.parentElement.style.display = 'flex';
        searchInput.value = '';
        searchInput.focus();

        document.getElementById('display_sisa').innerText = 'Rp 0';
        document.getElementById('display_nominal').innerText = 'Rp 0';
        inputJumlahBayar.value = '';

        renderTagihanResults(preloadedTagihan.slice(0, 20));
        dropdownMenu.style.display = 'block';
    }

    function performTagihanSearch(q) {
        if (!q || q.trim() === '') {
            renderTagihanResults(preloadedTagihan.slice(0, 20));
            dropdownMenu.style.display = 'block';
            return;
        }

        loadingSpinner.style.display = 'inline-block';

        // AJAX search ke backend
        fetch(`/bendahara/tagihan/search-ajax?q=${encodeURIComponent(q)}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            loadingSpinner.style.display = 'none';
            renderTagihanResults(data, q);
            dropdownMenu.style.display = 'block';
        })
        .catch(err => {
            loadingSpinner.style.display = 'none';
            const qLower = q.toLowerCase();
            const filtered = preloadedTagihan.filter(t => 
                t.nama_santri.toLowerCase().includes(qLower) || 
                t.nis.toLowerCase().includes(qLower) || 
                t.jenis_tagihan.toLowerCase().includes(qLower)
            );
            renderTagihanResults(filtered, q);
            dropdownMenu.style.display = 'block';
        });
    }

    searchInput.addEventListener('input', function() {
        clearTimeout(debounceTimer);
        const q = this.value;
        debounceTimer = setTimeout(() => performTagihanSearch(q), 250);
    });

    searchInput.addEventListener('focus', function() {
        performTagihanSearch(this.value);
    });

    btnToggle.addEventListener('click', function(e) {
        e.stopPropagation();
        if (dropdownMenu.style.display === 'block') {
            dropdownMenu.style.display = 'none';
        } else {
            performTagihanSearch(searchInput.value);
        }
    });

    btnReset.addEventListener('click', resetTagihanSelection);

    document.addEventListener('click', function(e) {
        if (!document.getElementById('tagihan_search_wrapper').contains(e.target)) {
            dropdownMenu.style.display = 'none';
        }
    });

    document.addEventListener('DOMContentLoaded', function() {
        const initialId = parseInt(inputTagihan.value);
        if (initialId) {
            const found = preloadedTagihan.find(t => t.id_tagihan === initialId);
            if (found) {
                selectTagihan(found);
            }
        }
    });
</script>
@endpush
@endsection

