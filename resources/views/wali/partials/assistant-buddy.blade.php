{{-- ============================================================ --}}
{{-- ASISTEN VIRTUAL WALI SANTRI: KANG SANTRI                     --}}
{{-- Maskot Interaktif Panduan Pembayaran & FAQ                   --}}
{{-- ============================================================ --}}

<style>
/* Floating Buddy Container */
#kang-santri-buddy {
    position: fixed;
    bottom: 24px;
    right: 24px;
    z-index: 1040;
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    font-family: 'Plus Jakarta Sans', sans-serif;
    user-select: none;
}

/* Speech Bubble Prompt */
.santri-bubble-prompt {
    background: #ffffff;
    color: #1e293b;
    padding: 0.6rem 0.85rem;
    border-radius: 1rem 1rem 0.25rem 1rem;
    box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.15), 0 0 0 1px rgba(0, 0, 0, 0.05);
    font-size: 0.8rem;
    font-weight: 600;
    margin-bottom: 0.6rem;
    max-width: 240px;
    line-height: 1.35;
    position: relative;
    cursor: pointer;
    animation: bubbleBounce 4s ease-in-out infinite;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    transition: transform 0.2s ease, opacity 0.3s ease;
}
.santri-bubble-prompt:hover {
    transform: translateY(-2px) scale(1.02);
}
.santri-bubble-close {
    background: transparent;
    border: none;
    color: #94a3b8;
    font-size: 0.9rem;
    cursor: pointer;
    padding: 0 0.15rem;
    line-height: 1;
}
.santri-bubble-close:hover {
    color: #ef4444;
}

@keyframes bubbleBounce {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-4px); }
}

/* Avatar Button */
.santri-avatar-trigger {
    width: 64px;
    height: 64px;
    border-radius: 50%;
    background: linear-gradient(135deg, #059669 0%, #047857 60%, #022c22 100%);
    box-shadow: 0 10px 20px -3px rgba(4, 120, 87, 0.4), 0 0 0 4px #ffffff, 0 0 0 6px rgba(16, 185, 129, 0.3);
    border: none;
    cursor: pointer;
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: transform 0.25s cubic-bezier(0.34, 1.56, 0.64, 1), box-shadow 0.25s ease;
    animation: floatMascot 3.5s ease-in-out infinite;
}
.santri-avatar-trigger:hover {
    transform: scale(1.08) rotate(-4deg);
    box-shadow: 0 14px 28px -2px rgba(4, 120, 87, 0.5), 0 0 0 4px #ffffff, 0 0 0 7px rgba(16, 185, 129, 0.5);
}
.santri-avatar-trigger:active {
    transform: scale(0.95);
}

@keyframes floatMascot {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-6px); }
}

/* Pulsing Badge Status */
.santri-online-dot {
    position: absolute;
    top: 2px;
    right: 2px;
    width: 14px;
    height: 14px;
    background-color: #22c55e;
    border: 2.5px solid #ffffff;
    border-radius: 50%;
    box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.7);
    animation: statusPulse 2s infinite;
}
@keyframes statusPulse {
    0% { box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.7); }
    70% { box-shadow: 0 0 0 8px rgba(34, 197, 94, 0); }
    100% { box-shadow: 0 0 0 0 rgba(34, 197, 94, 0); }
}

/* Assistant Panel Drawer / Modalite */
.santri-assistant-panel {
    position: fixed;
    bottom: 96px;
    right: 24px;
    width: 360px;
    max-width: calc(100vw - 32px);
    height: 520px;
    max-height: calc(100vh - 120px);
    background: #ffffff;
    border-radius: 1.25rem;
    box-shadow: 0 20px 40px -10px rgba(15, 23, 42, 0.25), 0 0 0 1px rgba(0, 0, 0, 0.06);
    overflow: hidden;
    display: flex;
    flex-direction: column;
    z-index: 1045;
    opacity: 0;
    pointer-events: none;
    transform: translateY(20px) scale(0.95);
    transition: opacity 0.25s cubic-bezier(0.16, 1, 0.3, 1), transform 0.25s cubic-bezier(0.16, 1, 0.3, 1);
}
.santri-assistant-panel.is-open {
    opacity: 1;
    pointer-events: auto;
    transform: translateY(0) scale(1);
}

/* Panel Header */
.assistant-header {
    background: linear-gradient(135deg, #065f46 0%, #047857 100%);
    color: #ffffff;
    padding: 1rem 1.25rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.assistant-header-profile {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}
.assistant-header-title {
    font-size: 0.95rem;
    font-weight: 700;
    margin: 0;
    line-height: 1.2;
}
.assistant-header-sub {
    font-size: 0.72rem;
    color: #a7f3d0;
    display: flex;
    align-items: center;
    gap: 0.3rem;
}
.assistant-header-btn {
    background: rgba(255, 255, 255, 0.15);
    border: none;
    color: #ffffff;
    width: 30px;
    height: 30px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    font-size: 0.85rem;
    transition: background 0.2s ease;
}
.assistant-header-btn:hover {
    background: rgba(255, 255, 255, 0.3);
}

/* Panel Body */
.assistant-body {
    flex: 1;
    overflow-y: auto;
    padding: 1.25rem;
    background: #f8fafc;
    scroll-behavior: smooth;
}

/* Welcome Card */
.assistant-welcome-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 1rem;
    padding: 1rem;
    margin-bottom: 1rem;
    box-shadow: 0 2px 4px rgba(0,0,0,0.03);
}

/* Action Menu Cards */
.assistant-action-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 0.85rem;
    padding: 0.85rem 1rem;
    margin-bottom: 0.65rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
    cursor: pointer;
    transition: all 0.2s ease;
    text-align: left;
    width: 100%;
}
.assistant-action-card:hover {
    background: #f0fdf4;
    border-color: #86efac;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(21, 128, 61, 0.1);
}
.assistant-card-icon {
    width: 38px;
    height: 38px;
    border-radius: 0.6rem;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.15rem;
    flex-shrink: 0;
}

/* Detail Views Inside Drawer */
.assistant-view-content {
    display: none;
    animation: fadeInSlide 0.25s ease forwards;
}
.assistant-view-content.active {
    display: block;
}
@keyframes fadeInSlide {
    0% { opacity: 0; transform: translateX(8px); }
    100% { opacity: 1; transform: translateX(0); }
}

.step-box {
    background: #ffffff;
    border-left: 3px solid #10b981;
    border-radius: 0.5rem;
    padding: 0.75rem 0.85rem;
    margin-bottom: 0.6rem;
    font-size: 0.82rem;
    box-shadow: 0 1px 3px rgba(0,0,0,0.04);
}

/* Accordion FAQ */
.faq-item {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 0.65rem;
    margin-bottom: 0.5rem;
    overflow: hidden;
}
.faq-question {
    padding: 0.75rem 0.85rem;
    font-size: 0.82rem;
    font-weight: 600;
    cursor: pointer;
    display: flex;
    justify-content: space-between;
    align-items: center;
    color: #0f172a;
}
.faq-question:hover {
    background: #f8fafc;
}
.faq-answer {
    padding: 0 0.85rem 0.75rem;
    font-size: 0.78rem;
    color: #475569;
    line-height: 1.45;
    display: none;
}
.faq-item.open .faq-answer {
    display: block;
}
.faq-item.open .faq-chevron {
    transform: rotate(180deg);
}

/* Panel Footer */
.assistant-footer {
    padding: 0.75rem 1rem;
    background: #ffffff;
    border-top: 1px solid #e2e8f0;
    display: flex;
    align-items: center;
    justify-content: space-between;
    font-size: 0.72rem;
    color: #64748b;
}

/* Responsive tweaks */
.santri-assistant-panel {
    max-height: calc(100dvh - 120px);
}
@media (max-width: 575.98px) {
    #kang-santri-buddy {
        bottom: 16px;
        right: 16px;
    }
    .santri-bubble-prompt {
        max-width: min(240px, calc(100vw - 96px));
    }
    .santri-avatar-trigger {
        width: 54px;
        height: 54px;
    }
    .santri-assistant-panel {
        right: 8px;
        left: 8px;
        bottom: 78px;
        width: auto;
        max-width: none;
        height: 480px;
        max-height: calc(100dvh - 96px);
    }
}
/* HP landscape / layar pendek: panel memenuhi tinggi layar agar tidak terpotong */
@media (max-height: 520px) {
    .santri-assistant-panel {
        bottom: 8px;
        right: 84px;
        height: calc(100dvh - 16px);
        max-height: calc(100dvh - 16px);
    }
    .santri-bubble-prompt { display: none; }
}
@media (hover: none) {
    .santri-avatar-trigger:hover { transform: none; }
}
</style>

<!-- ============================================================ -->
<!-- DOM ELEMEN MASKOT & DRAWER                                   -->
<!-- ============================================================ -->
<div id="kang-santri-buddy" aria-label="Asisten Bantuan Wali Santri">
    
    <!-- Bubble Prompt Melayang -->
    <div class="santri-bubble-prompt" id="santri_bubble_prompt" onclick="toggleKangSantriPanel()">
        <span>👋 Butuh bantuan cara bayar tagihan? Klik saya ya, Ayah/Bunda!</span>
        <button type="button" class="santri-bubble-close" onclick="closeBubblePrompt(event)" title="Tutup pesan">&times;</button>
    </div>

    <!-- Tombol Avatar Maskot Kartun Kang Santri -->
    <button type="button" class="santri-avatar-trigger" id="santri_avatar_btn" onclick="toggleKangSantriPanel()" title="Klik untuk membuka Asisten Panduan Kang Santri">
        <!-- SVG Maskot Kartun Santri Berpeci & Senyum Ramah -->
        <svg width="44" height="44" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
            <!-- Glow Aura -->
            <circle cx="32" cy="32" r="30" fill="rgba(255,255,255,0.1)"/>
            <!-- Baju Koko Hijau Zamrud & Putih -->
            <path d="M16 54C16 43 23 37 32 37C41 37 48 43 48 54V58H16V54Z" fill="#ffffff"/>
            <path d="M22 58L26 41H38L42 58H22Z" fill="#047857"/>
            <path d="M32 41V58" stroke="#10b981" stroke-width="1.5" stroke-dasharray="2 2"/>
            <!-- Leher & Kerah Baju -->
            <path d="M28 35H36V40C36 42 34 43 32 43C30 43 28 42 28 40V35Z" fill="#ffdfba"/>
            <!-- Wajah Santri Senyum Ramah -->
            <ellipse cx="32" cy="27" rx="13" ry="12" fill="#ffdfba"/>
            <!-- Pipi Merona Hangat (Blushing) -->
            <circle cx="23" cy="29" r="2.2" fill="#fca5a5" opacity="0.7"/>
            <circle cx="41" cy="29" r="2.2" fill="#fca5a5" opacity="0.7"/>
            <!-- Mata Berseri Ramah -->
            <ellipse cx="26" cy="26" rx="2" ry="2.5" fill="#1e293b"/>
            <ellipse cx="38" cy="26" rx="2" ry="2.5" fill="#1e293b"/>
            <circle cx="27" cy="25" r="0.8" fill="#ffffff"/>
            <circle cx="39" cy="25" r="0.8" fill="#ffffff"/>
            <!-- Alis Lembut -->
            <path d="M23 21C25 20.5 28 21 28 21" stroke="#334155" stroke-width="1.2" stroke-linecap="round"/>
            <path d="M41 21C39 20.5 36 21 36 21" stroke="#334155" stroke-width="1.2" stroke-linecap="round"/>
            <!-- Senyuman Lebar Tulus -->
            <path d="M28 30C29.5 33 34.5 33 36 30" stroke="#047857" stroke-width="1.8" stroke-linecap="round"/>
            <!-- Telinga -->
            <circle cx="19" cy="27" r="2.5" fill="#ffdfba"/>
            <circle cx="45" cy="27" r="2.5" fill="#ffdfba"/>
            <!-- Peci Santri Hitam Elegan (Songkok) -->
            <path d="M19 19C19 12 24 10 32 10C40 10 45 12 45 19H19Z" fill="#0f172a"/>
            <path d="M18 19C18 18 20 17 32 17C44 17 46 18 46 19C46 20 44 20.5 32 20.5C20 20.5 18 20 18 19Z" fill="#1e293b"/>
            <!-- Aksen Garis Emas Peci -->
            <path d="M22 18.5H42" stroke="#f59e0b" stroke-width="1.2" stroke-linecap="round"/>
        </svg>
        <span class="santri-online-dot"></span>
    </button>
</div>

<!-- ============================================================ -->
<!-- DRAWER ASISTEN INTERAKTIF                                    -->
<!-- ============================================================ -->
<div class="santri-assistant-panel" id="santri_assistant_panel" role="dialog" aria-modal="true" aria-labelledby="assistant_title">
    
    <!-- Header -->
    <div class="assistant-header">
        <div class="assistant-header-profile">
            <div class="rounded-circle bg-white p-1 d-flex align-items-center justify-content-center shadow-sm" style="width: 36px; height: 36px;">
                <span class="fs-5">🧕</span>
            </div>
            <div>
                <h6 class="assistant-header-title" id="assistant_title">Kang Santri</h6>
                <div class="assistant-header-sub">
                    <span style="display:inline-block;width:6px;height:6px;background:#34d399;border-radius:50%;"></span>
                    <span>Asisten Keuangan Santri</span>
                </div>
            </div>
        </div>
        <button type="button" class="assistant-header-btn" onclick="toggleKangSantriPanel()" title="Tutup Asisten">
            <i class="bi bi-x-lg"></i>
        </button>
    </div>

    <!-- Body Navigable -->
    <div class="assistant-body" id="assistant_body">
        
        <!-- ======================================================== -->
        <!-- VIEW 1: HOME MENU (Default) -->
        <!-- ======================================================== -->
        <div class="assistant-view-content active" id="view_home">
            <div class="assistant-welcome-card">
                <div class="fw-bold text-dark small mb-1">Assalamu'alaikum, Ayah/Bunda! 🌸</div>
                <div class="small text-muted" style="font-size: 0.78rem;">
                    Selamat datang di Portal Keuangan. Kang Santri siap memandu kemudahan pembayaran tagihan ananda tercinta.
                </div>
            </div>

            <div class="small fw-bold text-muted text-uppercase mb-2" style="font-size: 0.7rem; letter-spacing: 0.5px;">
                Pilih Bantuan Yang Anda Butuhkan:
            </div>

            <!-- Action 1: Panduan Bayar -->
            <button type="button" class="assistant-action-card" onclick="openAssistantView('view_guide')">
                <div class="assistant-card-icon bg-success-subtle text-success">
                    <i class="bi bi-credit-card-2-front-fill"></i>
                </div>
                <div class="flex-grow-1">
                    <div class="fw-bold text-dark small">Cara Bayar Online (Cepat)</div>
                    <div class="text-muted small" style="font-size: 0.72rem;">Panduan QRIS &amp; Transfer Virtual Account</div>
                </div>
                <i class="bi bi-chevron-right text-muted small"></i>
            </button>

            <!-- Action Panduan Lengkap Bergambar -->
            <a href="{{ route('wali.panduan') }}" class="assistant-action-card text-decoration-none border-success-subtle" style="background: #f0fdf4;">
                <div class="assistant-card-icon bg-success text-white shadow-sm">
                    <i class="bi bi-book-half"></i>
                </div>
                <div class="flex-grow-1">
                    <div class="fw-bold text-success small">Panduan Lengkap Bergambar</div>
                    <div class="text-muted small" style="font-size: 0.72rem;">Tutorial visual interaktif &amp; per bank</div>
                </div>
                <i class="bi bi-arrow-right text-success small"></i>
            </a>

            <!-- Action 2: Tanya Jawab / FAQ -->
            <button type="button" class="assistant-action-card" onclick="openAssistantView('view_faq')">
                <div class="assistant-card-icon bg-primary-subtle text-primary">
                    <i class="bi bi-question-circle-fill"></i>
                </div>
                <div class="flex-grow-1">
                    <div class="fw-bold text-dark small">Tanya Jawab Populer (FAQ)</div>
                    <div class="text-muted small" style="font-size: 0.72rem;">Cicilan, konfirmasi struk, &amp; kwitansi</div>
                </div>
                <i class="bi bi-chevron-right text-muted small"></i>
            </button>

            <!-- Action 3: Kwitansi Resmi -->
            <button type="button" class="assistant-action-card" onclick="openAssistantView('view_kwitansi')">
                <div class="assistant-card-icon bg-warning-subtle text-warning-emphasis">
                    <i class="bi bi-file-earmark-pdf-fill"></i>
                </div>
                <div class="flex-grow-1">
                    <div class="fw-bold text-dark small">Unduh Kwitansi PDF Sah</div>
                    <div class="text-muted small" style="font-size: 0.72rem;">Cara cetak bukti kwitansi resmi pondok</div>
                </div>
                <i class="bi bi-chevron-right text-muted small"></i>
            </button>

            <!-- Action 4: Hubungi Bendahara CS -->
            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', \App\Models\Pengaturan::get('pesantren_telepon', '081234567890')) }}?text={{ urlencode('Assalamu\'alaikum Bendahara Pondok Pesantren, saya wali santri ingin bertanya mengenai tagihan santri.') }}" target="_blank" class="assistant-action-card text-decoration-none">
                <div class="assistant-card-icon bg-emerald-subtle text-success" style="background:#dcfce7;">
                    <i class="bi bi-whatsapp"></i>
                </div>
                <div class="flex-grow-1">
                    <div class="fw-bold text-dark small">Hubungi Bendahara Pesantren</div>
                    <div class="text-muted small" style="font-size: 0.72rem;">Layanan resmi via WhatsApp</div>
                </div>
                <i class="bi bi-box-arrow-up-right text-muted small"></i>
            </a>
        </div>

        <!-- ======================================================== -->
        <!-- VIEW 2: PANDUAN CARA BAYAR -->
        <!-- ======================================================== -->
        <div class="assistant-view-content" id="view_guide">
            <button type="button" class="btn btn-outline-secondary btn-sm mb-3 py-1 px-2" style="font-size: 0.75rem;" onclick="openAssistantView('view_home')">
                <i class="bi bi-arrow-left me-1"></i> Kembali ke Menu
            </button>

            <h6 class="fw-bold text-dark mb-2">4 Langkah Pembayaran Mudah:</h6>

            <div class="step-box">
                <div class="fw-bold text-success mb-1">1. Pilih Tagihan Santri</div>
                <div>Di halaman santri ananda, klik tombol hijau <strong>"Bayar Sekarang"</strong> pada tagihan yang ingin dilunasi.</div>
            </div>

            <div class="step-box">
                <div class="fw-bold text-success mb-1">2. Tentukan Nominal Bayar</div>
                <div>Anda bisa langsung membayar <strong>Lunas Penuh</strong> atau <strong>Cicil Sebagian</strong> sesuai kemampuan anggaran saat ini.</div>
            </div>

            <div class="step-box">
                <div class="fw-bold text-success mb-1">3. Pilih Metode (QRIS / VA Bank)</div>
                <div>Pilih <strong>QRIS</strong> untuk aplikasi e-wallet (BCA, Mandiri, Dana, GoPay, ShopeePay) atau pilih <strong>Virtual Account Bank</strong>.</div>
            </div>

            <div class="step-box">
                <div class="fw-bold text-success mb-1">4. Otomatis Lunas &amp; Kwitansi Sah</div>
                <div>Setelah transfer, sistem langsung <strong>LUNAS otomatis</strong> tanpa perlu kirim struk! Notifikasi kwitansi resmi dikirim ke WhatsApp Anda.</div>
            </div>

            <div class="mt-3 text-center">
                <a href="{{ route('wali.panduan') }}" class="btn btn-success btn-sm w-100 fw-semibold shadow-sm py-2" style="font-size: 0.78rem;">
                    <i class="bi bi-images me-1"></i> Buka Panduan Lengkap Bergambar &rarr;
                </a>
            </div>
        </div>

        <!-- ======================================================== -->
        <!-- VIEW 3: FAQ POPULER -->
        <!-- ======================================================== -->
        <div class="assistant-view-content" id="view_faq">
            <button type="button" class="btn btn-outline-secondary btn-sm mb-3 py-1 px-2" style="font-size: 0.75rem;" onclick="openAssistantView('view_home')">
                <i class="bi bi-arrow-left me-1"></i> Kembali ke Menu
            </button>

            <h6 class="fw-bold text-dark mb-2">Tanya Jawab Populer (FAQ):</h6>

            <div class="faq-item">
                <div class="faq-question" onclick="toggleFaq(this)">
                    <span>Apakah saya bisa mencicil tagihan?</span>
                    <i class="bi bi-chevron-down faq-chevron"></i>
                </div>
                <div class="faq-answer">
                    <strong>Bisa sekali!</strong> Masukkan nominal cicilan yang diinginkan pada formulir pembayaran, lalu klik lanjut pembayaran. Status tagihan akan berubah menjadi "Dicicil" hingga lunas.
                </div>
            </div>

            <div class="faq-item">
                <div class="faq-question" onclick="toggleFaq(this)">
                    <span>Apakah harus kirim struk ke bendahara?</span>
                    <i class="bi bi-chevron-down faq-chevron"></i>
                </div>
                <div class="faq-answer">
                    <strong>Tidak perlu!</strong> Sistem pembayaran online terhubung langsung ke Payment Gateway Bank &amp; Midtrans. Begitu uang masuk, sistem langsung memvalidasi secara otomatis detik itu juga.
                </div>
            </div>

            <div class="faq-item">
                <div class="faq-question" onclick="toggleFaq(this)">
                    <span>Bagaimana jika punya lebih dari 1 anak santri?</span>
                    <i class="bi bi-chevron-down faq-chevron"></i>
                </div>
                <div class="faq-answer">
                    Seluruh ananda yang Anda ampu otomatis tampil di Dashboard Wali. Anda cukup mengklik nama ananda untuk melihat tagihan masing-masing.
                </div>
            </div>

            <div class="faq-item">
                <div class="faq-question" onclick="toggleFaq(this)">
                    <span>Berapa lama kwitansi resmi terbit?</span>
                    <i class="bi bi-chevron-down faq-chevron"></i>
                </div>
                <div class="faq-answer">
                    Seketika (1–2 detik). Kwitansi resmi ber-cap digital sah langsung dapat diunduh dalam format PDF di menu Riwayat Pembayaran.
                </div>
            </div>
        </div>

        <!-- ======================================================== -->
        <!-- VIEW 4: PANDUAN KWITANSI -->
        <!-- ======================================================== -->
        <div class="assistant-view-content" id="view_kwitansi">
            <button type="button" class="btn btn-outline-secondary btn-sm mb-3 py-1 px-2" style="font-size: 0.75rem;" onclick="openAssistantView('view_home')">
                <i class="bi bi-arrow-left me-1"></i> Kembali ke Menu
            </button>

            <h6 class="fw-bold text-dark mb-2">Unduh Bukti Kwitansi Sah:</h6>
            
            <p class="small text-muted mb-3" style="font-size: 0.8rem;">
                Setiap pembayaran yang berhasil memiliki dokumen kwitansi PDF berstempel resmi yang dapat diunduh kapan saja sebagai arsip sah:
            </p>

            <div class="card border-0 bg-light rounded-3 p-3 mb-3">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <i class="bi bi-clock-history text-primary fs-5"></i>
                    <span class="small fw-bold">Menu Riwayat Pembayaran</span>
                </div>
                <div class="small text-muted" style="font-size: 0.75rem;">
                    Buka menu navigasi <strong>"Riwayat Pembayaran"</strong> di samping kiri (atau tombol di halaman santri). Di tabel riwayat transaksi, klik tombol <strong>"Lihat Kwitansi"</strong>.
                </div>
            </div>

            <div class="card border-0 bg-light rounded-3 p-3">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <i class="bi bi-whatsapp text-success fs-5"></i>
                    <span class="small fw-bold">Notifikasi WhatsApp Otomatis</span>
                </div>
                <div class="small text-muted" style="font-size: 0.75rem;">
                    Tautan dokumen PDF kwitansi resmi juga dikirimkan secara otomatis ke nomor WhatsApp Ayah/Bunda saat pembayaran berhasil diterima.
                </div>
            </div>
        </div>

    </div>

    <!-- Footer -->
    <div class="assistant-footer">
        <span>PP Darul Muttaqin Al Islami</span>
        <span class="badge bg-success-subtle text-success border border-success-subtle">Online 24 Jam</span>
    </div>
</div>

<script>
// ============================================================
// LOGIKA JAVASCRIPT ASISTEN KANG SANTRI
// ============================================================
function toggleKangSantriPanel() {
    const panel = document.getElementById('santri_assistant_panel');
    const bubble = document.getElementById('santri_bubble_prompt');
    if (!panel) return;

    panel.classList.toggle('is-open');

    // Sembunyikan bubble saat panel terbuka
    if (bubble && panel.classList.contains('is-open')) {
        bubble.style.display = 'none';
    }
}

function closeBubblePrompt(e) {
    if (e) e.stopPropagation();
    const bubble = document.getElementById('santri_bubble_prompt');
    if (bubble) bubble.style.display = 'none';
}

function openAssistantView(viewId) {
    const allViews = document.querySelectorAll('.assistant-view-content');
    allViews.forEach(v => v.classList.remove('active'));

    const target = document.getElementById(viewId);
    if (target) {
        target.classList.add('active');
        const body = document.getElementById('assistant_body');
        if (body) body.scrollTop = 0;
    }
}

function toggleFaq(questionEl) {
    const item = questionEl.closest('.faq-item');
    if (item) {
        item.classList.toggle('open');
    }
}

// Tutup panel asisten jika menekan tombol Escape di keyboard
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        const panel = document.getElementById('santri_assistant_panel');
        if (panel && panel.classList.contains('is-open')) {
            panel.classList.remove('is-open');
        }
    }
});

// Sembunyikan bubble secara otomatis setelah 12 detik jika diabaikan agar tidak mengganggu
setTimeout(() => {
    const bubble = document.getElementById('santri_bubble_prompt');
    if (bubble) {
        bubble.style.opacity = '0';
        setTimeout(() => bubble.style.display = 'none', 300);
    }
}, 12000);
</script>

