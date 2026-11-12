<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk Sistem - SIM Keuangan Pesantren Darul Muttaqin</title>
    <link rel="icon" type="image/jpeg" href="{{ asset('img/logo.jpg') }}">
    
    <!-- Google Fonts & CDN Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Amiri:ital,wght@0,700;1,400&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <style>
        :root {
            --primary-emerald: #059669;
            --primary-dark: #064e3b;
            --primary-deep: #022c22;
            --primary-accent: #10b981;
            --gold-accent: #f59e0b;
            --transition-smooth: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
            --transition-spring: all 0.5s cubic-bezier(0.34, 1.56, 0.64, 1);
            --curtain-easing: cubic-bezier(0.77, 0, 0.175, 1);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            background-color: #022c22;
            color: #1e293b;
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* Viewport Container */
        .auth-viewport {
            position: relative;
            width: 100%;
            min-height: 100vh;
            min-height: 100dvh;
            overflow: hidden;
            background: #011612;
        }

        /* ========================================================
           LAYER BELAKANG: TERBUKA SAAT TIRAI TERBELAH (REVEAL PORTAL)
           ======================================================== */
        .auth-curtain-reveal-bg {
            position: fixed;
            inset: 0;
            z-index: 1;
            background: radial-gradient(circle at center, #065f46 0%, #022c22 60%, #011612 100%);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: #ffffff;
            opacity: 0;
            transform: scale(0.9);
            transition: opacity 0.4s ease, transform 0.75s cubic-bezier(0.16, 1, 0.3, 1);
            pointer-events: none;
            padding: 2rem;
        }

        .auth-curtain-reveal-bg.active {
            opacity: 1;
            transform: scale(1);
            pointer-events: auto;
        }

        .portal-emblem-loader {
            position: relative;
            width: 125px;
            height: 125px;
            margin-bottom: 1.5rem;
        }
        .portal-spin-ring {
            position: absolute;
            inset: -8px;
            border: 3.5px dashed rgba(52, 211, 153, 0.65);
            border-radius: 50%;
            animation: portalSpin 12s linear infinite;
        }
        @keyframes portalSpin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        .portal-inner-circle {
            position: relative;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #059669 0%, #047857 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 0 35px rgba(52, 211, 153, 0.4), inset 0 0 15px rgba(255, 255, 255, 0.2);
        }

        .portal-badge-role {
            background: rgba(255, 255, 255, 0.12);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: #a7f3d0;
            font-size: 0.85rem;
            font-weight: 600;
        }

        .portal-progress-bar {
            width: 260px;
            height: 5px;
            background: rgba(255, 255, 255, 0.15);
            border-radius: 999px;
            overflow: hidden;
            position: relative;
        }
        .portal-progress-inner {
            position: absolute;
            top: 0;
            left: 0;
            bottom: 0;
            width: 0%;
            background: linear-gradient(90deg, #34d399, #10b981, #6ee7b7);
            border-radius: 999px;
            transition: width 0.8s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .auth-curtain-reveal-bg.active .portal-progress-inner {
            width: 100%;
        }

        /* ========================================================
           LAYER DEPAN: DUA TIRAI UTAMA (SPLIT CURTAIN SYSTEM)
           ======================================================== */
        .auth-container {
            min-height: 100vh;
            display: flex;
            width: 100%;
            position: relative;
            z-index: 10;
        }

        /* TIRAI KIRI: HIJAU ZAMRUD */
        .auth-showcase {
            flex: 1.15;
            background: linear-gradient(145deg, #022c22 0%, #064e3b 45%, #065f46 75%, #047857 100%);
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            align-items: center;
            text-align: center;
            padding: 3.5rem 3.5rem;
            color: #ffffff;
            overflow: hidden;
            will-change: transform;
            transition: transform 0.85s var(--curtain-easing), box-shadow 0.85s ease;
        }

        /* TIRAI KANAN: PUTIH BERSIH */
        .auth-form-pane {
            flex: 0.95;
            background-color: #ffffff;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: clamp(2rem, 3.5vw, 3.5rem) clamp(2rem, 4vw, 4rem);
            position: relative;
            box-shadow: -15px 0 35px rgba(0, 0, 0, 0.08);
            will-change: transform;
            transition: transform 0.85s var(--curtain-easing), box-shadow 0.85s ease;
            overflow-y: auto;
            max-height: 100vh;
        }

        /* EFEK TIRAI TERBELAH KIRI & KANAN (THE CURTAIN SPLIT REVEAL) */
        .curtains-open .auth-showcase {
            transform: translateX(-105%);
            box-shadow: 25px 0 60px rgba(0, 0, 0, 0.7);
        }

        .curtains-open .auth-form-pane {
            transform: translateX(105%);
            box-shadow: -25px 0 60px rgba(0, 0, 0, 0.7);
        }

        /* Ambient Glowing Background Orbs */
        .ambient-orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(90px);
            opacity: 0.3;
            pointer-events: none;
            z-index: 1;
        }
        .orb-1 {
            width: 450px;
            height: 450px;
            background: #10b981;
            top: -100px;
            left: -100px;
            animation: orbFloat1 18s ease-in-out infinite alternate;
        }
        .orb-2 {
            width: 380px;
            height: 380px;
            background: #047857;
            bottom: -50px;
            right: -50px;
            animation: orbFloat2 14s ease-in-out infinite alternate;
        }
        .orb-3 {
            width: 250px;
            height: 250px;
            background: #f59e0b;
            top: 45%;
            left: 20%;
            opacity: 0.12;
            animation: orbFloat1 12s ease-in-out infinite alternate-reverse;
        }

        @keyframes orbFloat1 {
            0% { transform: translate(0, 0) scale(1); }
            100% { transform: translate(50px, 35px) scale(1.1); }
        }
        @keyframes orbFloat2 {
            0% { transform: translate(0, 0) scale(1); }
            100% { transform: translate(-45px, -25px) scale(1.12); }
        }

        /* Geometric Subtle Grid */
        .pattern-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            opacity: 0.035;
            background-image: radial-gradient(#ffffff 1px, transparent 1px), radial-gradient(#ffffff 1px, #022c22 1px);
            background-size: 32px 32px;
            background-position: 0 0, 16px 16px;
            pointer-events: none;
            z-index: 1;
        }

        /* Top Bar Showcase */
        .showcase-top {
            position: relative;
            z-index: 2;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .system-pill {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.15);
            padding: 0.45rem 1rem;
            border-radius: 999px;
            font-size: 0.78rem;
            font-weight: 600;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }
        .pulse-dot {
            width: 8px;
            height: 8px;
            background-color: #34d399;
            border-radius: 50%;
            box-shadow: 0 0 0 0 rgba(52, 211, 153, 0.7);
            animation: pulseGlow 2s infinite;
        }
        @keyframes pulseGlow {
            0% { box-shadow: 0 0 0 0 rgba(52, 211, 153, 0.7); }
            70% { box-shadow: 0 0 0 10px rgba(52, 211, 153, 0); }
            100% { box-shadow: 0 0 0 0 rgba(52, 211, 153, 0); }
        }
        /* ========================================================
           ARABIC FLOWING MARQUEE WITH GOLDEN SHIMMER
           ======================================================== */
        .arabic-marquee-wrapper {
            position: relative;
            max-width: 440px;
            width: 100%;
            overflow: hidden;
            user-select: none;
            display: flex;
            align-items: center;
            /* Masking gradasi halus di tepi kiri & kanan */
            -webkit-mask-image: linear-gradient(to right, transparent 0%, black 12%, black 88%, transparent 100%);
            mask-image: linear-gradient(to right, transparent 0%, black 12%, black 88%, transparent 100%);
            padding: 0.35rem 0;
            cursor: default;
        }

        .arabic-marquee-track {
            display: flex;
            width: max-content;
            white-space: nowrap;
            animation: arabicMarqueeScroll 28s linear infinite;
            will-change: transform;
        }

        .arabic-marquee-wrapper:hover .arabic-marquee-track {
            animation-play-state: paused;
        }

        .arabic-marquee-group {
            display: flex;
            align-items: center;
            gap: 1.5rem;
            padding-left: 1.5rem;
        }

        .arabic-marquee-text {
            font-family: 'Amiri', serif;
            font-size: 1.35rem;
            font-weight: 700;
            letter-spacing: 0.5px;
            direction: rtl;
            line-height: 1.4;
            /* Efek Kilau Emas Mewah (Golden Shimmer) */
            background: linear-gradient(
                115deg,
                #fef08a 0%,
                #fde047 22%,
                #ffffff 42%,
                #fde047 62%,
                #fbbf24 82%,
                #fef08a 100%
            );
            background-size: 250% 100%;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            animation: goldenShimmerSheen 4.5s ease-in-out infinite;
            text-shadow: 0 0 16px rgba(253, 224, 71, 0.32);
        }

        .arabic-marquee-sep {
            color: #f59e0b;
            font-size: 0.95rem;
            opacity: 0.9;
            text-shadow: 0 0 12px rgba(245, 158, 11, 0.7);
        }

        @keyframes arabicMarqueeScroll {
            0% {
                transform: translateX(0);
            }
            100% {
                transform: translateX(-50%);
            }
        }

        @keyframes goldenShimmerSheen {
            0% {
                background-position: 250% 0;
            }
            100% {
                background-position: -50% 0;
            }
        }

        /* Center Body Showcase */
        .showcase-center {
            position: relative;
            z-index: 2;
            max-width: 520px;
            margin: auto 0;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .emblem-wrapper {
            position: relative;
            width: clamp(115px, 11vw, 140px);
            height: clamp(115px, 11vw, 140px);
            margin-bottom: 1.5rem;
        }
        .emblem-glow {
            position: absolute;
            inset: -12px;
            background: radial-gradient(circle, rgba(52, 211, 153, 0.45) 0%, rgba(5, 150, 105, 0) 70%);
            border-radius: 50%;
            animation: pulseGlow 3s infinite;
        }
        .emblem-circle {
            position: relative;
            width: 100%;
            height: 100%;
            background: #ffffff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 16px 36px rgba(0, 0, 0, 0.3), 0 0 0 5px rgba(255, 255, 255, 0.2);
            transition: var(--transition-spring);
        }
        .emblem-wrapper:hover .emblem-circle {
            transform: scale(1.06) rotate(3deg);
        }

        .brand-pesantren-sub {
            text-transform: uppercase;
            font-size: 0.85rem;
            font-weight: 700;
            color: #a7f3d0;
            letter-spacing: 2px;
            margin-bottom: 0.35rem;
        }
        .brand-pesantren-title {
            font-size: 2rem;
            font-weight: 800;
            line-height: 1.25;
            color: #ffffff;
            margin-bottom: 0.75rem;
            letter-spacing: -0.5px;
        }
        .brand-tagline {
            font-size: 1.25rem;
            font-weight: 700;
            color: #6ee7b7;
            background: linear-gradient(120deg, #d1fae5, #6ee7b7, #34d399);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 1rem;
        }
        .brand-desc {
            font-size: 0.95rem;
            color: rgba(255, 255, 255, 0.82);
            line-height: 1.65;
            margin-bottom: 2rem;
            font-weight: 400;
        }

        .institusi-badges {
            display: flex;
            align-items: center;
            justify-content: center;
            flex-wrap: wrap;
            gap: 0.75rem;
        }
        .institusi-badge-pill {
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.15);
            padding: 0.45rem 1rem;
            border-radius: 999px;
            font-size: 0.78rem;
            font-weight: 600;
            color: rgba(255, 255, 255, 0.9);
            transition: var(--transition-smooth);
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
        }
        .institusi-badge-pill:hover {
            background: rgba(255, 255, 255, 0.16);
            transform: translateY(-2px);
            color: #ffffff;
        }

        .showcase-footer {
            position: relative;
            z-index: 2;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 1.5rem;
            font-size: 0.75rem;
            color: rgba(255, 255, 255, 0.65);
            border-top: 1px solid rgba(255, 255, 255, 0.12);
            padding-top: 1.25rem;
        }

        /* Formulir Login Wrapper */
        .login-wrapper {
            max-width: 430px;
            width: 100%;
            margin: 0 auto;
            animation: fadeInRight 0.5s cubic-bezier(0.16, 1, 0.3, 1) both;
        }

        @keyframes fadeInRight {
            0% {
                opacity: 0;
                transform: translateX(25px);
            }
            100% {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .form-header-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            background: #ecfdf5;
            color: #065f46;
            border: 1px solid #a7f3d0;
            padding: 0.35rem 0.85rem;
            border-radius: 999px;
            font-size: 0.75rem;
            font-weight: 700;
            margin-bottom: 0.75rem;
        }
        .form-title {
            font-size: 1.85rem;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 0.35rem;
            letter-spacing: -0.5px;
        }
        .form-subtitle {
            font-size: 0.875rem;
            color: #64748b;
            margin-bottom: 1.5rem;
            line-height: 1.5;
        }

        /* Segmented Control with Glider */
        .role-switcher-wrapper {
            margin-bottom: 1.25rem;
        }
        .role-switcher-card {
            background: #f1f5f9;
            border-radius: 0.85rem;
            padding: 0.35rem;
            display: flex;
            position: relative;
            border: 1px solid #e2e8f0;
            user-select: none;
        }

        .role-glider {
            position: absolute;
            top: 0.35rem;
            bottom: 0.35rem;
            width: calc((100% - 0.7rem) / 3);
            background: #ffffff;
            border-radius: 0.65rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08), 0 1px 3px rgba(0, 0, 0, 0.05);
            transition: transform 0.38s cubic-bezier(0.34, 1.45, 0.64, 1);
            z-index: 1;
            pointer-events: none;
        }

        .role-btn {
            flex: 1;
            padding: 0.65rem 0.5rem;
            font-size: 0.8rem;
            font-weight: 700;
            border-radius: 0.65rem;
            border: none;
            background: transparent;
            color: #64748b;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.45rem;
            transition: color 0.3s ease;
            cursor: pointer;
            position: relative;
            z-index: 2;
        }
        .role-btn:hover {
            color: #0f172a;
        }
        .role-btn.active {
            color: #065f46;
        }
        .role-btn.active .role-badge-icon {
            color: #059669;
            transform: scale(1.15);
        }
        .role-badge-icon {
            font-size: 0.95rem;
            transition: var(--transition-spring);
        }

        .role-hint-banner {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: 0.6rem 0.85rem;
            border-radius: 0.65rem;
            font-size: 0.75rem;
            color: #475569;
            margin-bottom: 1.5rem;
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            transition: var(--transition-smooth);
        }
        .auto-fill-tag {
            font-size: 0.68rem;
            background: #d1fae5;
            color: #065f46;
            font-weight: 700;
            padding: 0.15rem 0.45rem;
            border-radius: 999px;
            letter-spacing: 0.3px;
            flex-shrink: 0;
        }

        .custom-form-group {
            margin-bottom: 1.25rem;
        }
        .form-label-custom {
            font-size: 0.8rem;
            font-weight: 700;
            color: #334155;
            margin-bottom: 0.45rem;
            display: block;
        }
        .input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
            transition: var(--transition-smooth);
        }
        .input-icon-left {
            position: absolute;
            left: 1rem;
            color: #94a3b8;
            font-size: 1.1rem;
            pointer-events: none;
            transition: var(--transition-smooth);
        }
        .form-control-custom {
            width: 100%;
            height: 48px;
            padding: 0.65rem 2.75rem 0.65rem 2.85rem;
            font-size: 0.9rem;
            font-weight: 500;
            color: #0f172a;
            background: #f8fafc;
            border: 1.5px solid #e2e8f0;
            border-radius: 0.75rem;
            transition: var(--transition-smooth);
        }
        .form-control-custom:focus {
            outline: none;
            background: #ffffff;
            border-color: #10b981;
            box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.15);
        }
        .form-control-custom:focus ~ .input-icon-left {
            color: #059669;
            transform: scale(1.1);
        }

        @keyframes fieldFlash {
            0% { background-color: #d1fae5; }
            100% { background-color: #ffffff; }
        }
        .flash-active {
            animation: fieldFlash 0.5s ease-out;
        }

        .btn-toggle-pwd {
            position: absolute;
            right: 0.6rem;
            background: transparent;
            border: none;
            color: #94a3b8;
            padding: 0.5rem;
            font-size: 1.1rem;
            cursor: pointer;
            border-radius: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: var(--transition-smooth);
        }
        .btn-toggle-pwd:hover {
            color: #059669;
            background: #f1f5f9;
        }
        .btn-toggle-pwd i {
            transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        }
        .btn-toggle-pwd:active i {
            transform: scale(0.85) rotate(15deg);
        }

        .auth-actions {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.5rem;
            font-size: 0.8rem;
        }
        .custom-check {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            cursor: pointer;
            user-select: none;
            color: #64748b;
            font-weight: 500;
        }
        .custom-check input[type="checkbox"] {
            width: 17px;
            height: 17px;
            accent-color: #059669;
            cursor: pointer;
            border-radius: 4px;
        }
        .help-link {
            color: #059669;
            text-decoration: none;
            font-weight: 600;
            transition: var(--transition-smooth);
        }
        .help-link:hover {
            color: #047857;
            text-decoration: underline;
        }

        .btn-submit-login {
            width: 100%;
            height: 50px;
            background: linear-gradient(135deg, #059669 0%, #047857 100%);
            border: none;
            border-radius: 0.75rem;
            color: #ffffff;
            font-size: 0.95rem;
            font-weight: 700;
            letter-spacing: 0.3px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.6rem;
            box-shadow: 0 10px 20px -5px rgba(5, 150, 105, 0.4), 0 4px 6px -2px rgba(5, 150, 105, 0.1);
            transition: var(--transition-smooth);
            position: relative;
            overflow: hidden;
        }
        .btn-submit-login:hover {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            box-shadow: 0 14px 25px -4px rgba(5, 150, 105, 0.5);
            transform: translateY(-2px);
        }
        .btn-submit-login:active {
            transform: scale(0.98);
            box-shadow: 0 4px 10px rgba(5, 150, 105, 0.3);
        }
        .btn-submit-login i.btn-icon {
            transition: transform 0.3s ease;
        }
        .btn-submit-login:hover i.btn-icon {
            transform: translateX(4px);
        }

        .btn-submit-login.loading {
            pointer-events: none;
            opacity: 0.85;
        }

        .auth-footer {
            margin-top: 2rem;
            padding-top: 1.25rem;
            border-top: 1px solid #f1f5f9;
            text-align: center;
            font-size: 0.75rem;
            color: #94a3b8;
        }

        /* Responsive Breakpoints */
        @media (max-width: 991.98px) {
            .auth-viewport {
                overflow-y: auto !important;
                height: auto !important;
                min-height: 100vh;
            }
            .auth-container {
                flex-direction: column;
                min-height: 100vh;
                height: auto;
            }
            .auth-showcase {
                padding: 2.5rem 1.5rem 2rem;
                flex: none;
            }
            .emblem-wrapper {
                width: 100px;
                height: 100px;
                margin-bottom: 1.25rem;
            }
            .brand-pesantren-title {
                font-size: 1.6rem;
            }
            .auth-form-pane {
                padding: 2rem 1.5rem 3rem;
                flex: 1;
                max-height: none;
                overflow-y: visible;
                box-shadow: none;
            }
            .curtains-open .auth-showcase {
                transform: translateY(-105%);
            }
            .curtains-open .auth-form-pane {
                transform: translateY(105%);
            }
        }

        /* ---------- Tablet portrait & HP besar ---------- */
        @media (max-width: 767.98px) {
            .auth-showcase { padding: 1.75rem 1.25rem 1.5rem; }
            .showcase-center { margin: 0.5rem 0; max-width: 100%; }
            .brand-pesantren-title { font-size: 1.4rem; }
            .brand-desc { font-size: 0.88rem; margin-bottom: 1.25rem; }
            .showcase-footer { flex-wrap: wrap; gap: 0.5rem 1rem; font-size: 0.7rem; padding-top: 0.9rem; }
            .form-title { font-size: 1.55rem; }
            .login-wrapper { max-width: 100%; }
            .portal-progress-bar { width: min(260px, 70vw); }
            .auth-curtain-reveal-bg h2 { font-size: 1.5rem; }
        }

        /* ---------- HP (<= 575px): ringkas bagian hijau agar form login langsung terlihat ---------- */
        @media (max-width: 575.98px) {
            .auth-showcase { padding: 1.25rem 1rem 1rem; gap: 0.5rem; }
            .showcase-top { width: 100%; justify-content: center; }
            .emblem-wrapper { width: 84px; height: 84px; margin-bottom: 0.75rem; }
            .brand-pesantren-sub { font-size: 0.72rem; letter-spacing: 1.5px; }
            .brand-pesantren-title { font-size: 1.2rem; margin-bottom: 0.4rem; }
            .brand-tagline { font-size: 0.78rem; }
            .brand-desc, .showcase-footer { display: none; }
            .institusi-badges { gap: 0.4rem; margin-top: 0.5rem; }
            .institusi-badge-pill { padding: 0.3rem 0.7rem; font-size: 0.7rem; }
            /* Logo ganda (emblem hijau di atas + badge form) -> cukup satu */
            .auth-form-pane .d-md-none { display: none !important; }
            .auth-form-pane { padding: 1.5rem 1.1rem 2.25rem; border-radius: 1.25rem 1.25rem 0 0; margin-top: -0.75rem; position: relative; z-index: 3; }
            .form-title { font-size: 1.4rem; }
            .form-subtitle { font-size: 0.82rem; margin-bottom: 1.1rem; }
            .role-btn { padding: 0.6rem 0.25rem; font-size: 0.72rem; gap: 0.25rem; flex-direction: column; }
            .role-hint-banner { font-size: 0.72rem; padding: 0.5rem 0.65rem; flex-wrap: wrap; }
            .form-control-custom { height: 50px; font-size: 16px; } /* 16px: mencegah zoom otomatis di iPhone */
            .auth-actions { flex-wrap: wrap; gap: 0.5rem; }
            .btn-submit-login { height: 52px; }
            .auth-footer { margin-top: 1.25rem; }
            .portal-emblem-loader { width: 104px; height: 104px; }
            .auth-curtain-reveal-bg { padding: 1.25rem; }
        }

        /* ---------- HP kecil (<= 359px) ---------- */
        @media (max-width: 359.98px) {
            .role-btn span { font-size: 0.68rem; }
            .institusi-badges { display: none; }
            .auth-form-pane { padding-left: 0.85rem; padding-right: 0.85rem; }
        }

        /* ---------- HP landscape / layar pendek ---------- */
        @media (max-height: 520px) and (max-width: 991.98px) {
            .emblem-wrapper { width: 64px; height: 64px; margin-bottom: 0.5rem; }
            .brand-desc, .institusi-badges, .showcase-footer { display: none; }
            .auth-showcase { padding-top: 1rem; padding-bottom: 1rem; }
        }
    </style>
</head>
<body>

    <div class="auth-viewport">

        <!-- ========================================================
             LAYER BELAKANG: TERBUKA SAAT TIRAI TERBELAH (REVEAL PORTAL)
             ======================================================== -->
        <div class="auth-curtain-reveal-bg" id="curtain_reveal_layer">
            <div class="portal-emblem-loader">
                <div class="portal-spin-ring"></div>
                <div class="portal-inner-circle bg-white p-2 shadow">
                    <img src="{{ asset('img/logo.jpg') }}" alt="Logo PP Darul Muttaqin" class="rounded-circle" style="width: 100%; height: 100%; object-fit: contain;">
                </div>
            </div>
            
            <div class="text-uppercase small fw-bold mb-1" style="color: #6ee7b7; letter-spacing: 2px; font-size: 0.8rem;">
                Pondok Pesantren Darul Muttaqin
            </div>
            <h2 class="fw-bold mb-2 text-white" id="reveal_welcome_title" style="letter-spacing: -0.5px;">
                Selamat Datang
            </h2>
            <div class="portal-badge-role px-3 py-1 rounded-pill mb-4" id="reveal_role_badge">
                <i class="bi bi-shield-check me-1 text-success"></i> Membuka Portal Keuangan...
            </div>
            
            <!-- Sleek Loading Progress Bar -->
            <div class="portal-progress-bar">
                <div class="portal-progress-inner"></div>
            </div>
        </div>

        <!-- ========================================================
             LAYER DEPAN: DUA TIRAI UTAMA (SPLIT CURTAIN SYSTEM)
             ======================================================== -->
        <div class="auth-container" id="auth_split_container">
            
            <!-- TIRAI KIRI: HIJAU ZAMRUD -->
            <div class="auth-showcase" id="curtain_left">
                <!-- Glowing Background Orbs -->
                <div class="ambient-orb orb-1"></div>
                <div class="ambient-orb orb-2"></div>
                <div class="ambient-orb orb-3"></div>
                <div class="pattern-overlay"></div>

                <!-- Top Header -->
                <div class="showcase-top">
                    <div class="system-pill">
                        <span class="pulse-dot"></span>
                        <span>Sistem Keuangan Santri v2.6</span>
                    </div>
                    <div class="arabic-marquee-wrapper d-none d-sm-flex" title="Kalam Hikmah & Ayat Pesantren (Arahkan kursor untuk menjeda)">
                        <div class="arabic-marquee-track">
                            <div class="arabic-marquee-group">
                                <span class="arabic-marquee-text">بِسْمِ اللَّهِ الرَّحْمَٰنِ الرَّحِيمِ</span>
                                <span class="arabic-marquee-sep">✦</span>
                                <span class="arabic-marquee-text">طَلَبُ الْعِلْمِ فَرِيْضَةٌ عَلَى كُلِّ مُسْلِمٍ</span>
                                <span class="arabic-marquee-sep">✦</span>
                                <span class="arabic-marquee-text">يَرْفَعِ اللّٰهُ الَّذِيْنَ اٰمَنُوْا مِنْكُمْ وَالَّذِيْنَ اُوْتُوا الْعِلْمَ دَرَجٰتٍ</span>
                                <span class="arabic-marquee-sep">✦</span>
                                <span class="arabic-marquee-text">إِنَّ اللّٰهَ يَأْمُرُكُمْ أَنْ تُؤَدُّوا الْأَمَانَاتِ إِلَىٰ أَهْلِهَا</span>
                                <span class="arabic-marquee-sep">✦</span>
                                <span class="arabic-marquee-text">مَنْ سَلَكَ طَرِيقًا يَلْتَمِسُ فِيهِ عِلْمًا سَهَّلَ اللّٰهُ لَهُ بِهِ طَرِيقًا إِلَى الْجَنَّةِ</span>
                                <span class="arabic-marquee-sep">✦</span>
                                <span class="arabic-marquee-text">اَلْحَمْدُ لِلّٰهِ رَبِّ الْعَالَمِيْنَ</span>
                                <span class="arabic-marquee-sep">✦</span>
                            </div>
                            <div class="arabic-marquee-group" aria-hidden="true">
                                <span class="arabic-marquee-text">بِسْمِ اللَّهِ الرَّحْمَٰنِ الرَّحِيمِ</span>
                                <span class="arabic-marquee-sep">✦</span>
                                <span class="arabic-marquee-text">طَلَبُ الْعِلْمِ فَرِيْضَةٌ عَلَى كُلِّ مُسْلِمٍ</span>
                                <span class="arabic-marquee-sep">✦</span>
                                <span class="arabic-marquee-text">يَرْفَعِ اللّٰهُ الَّذِيْنَ اٰمَنُوْا مِنْكُمْ وَالَّذِيْنَ اُوْتُوا الْعِلْمَ دَرَجٰتٍ</span>
                                <span class="arabic-marquee-sep">✦</span>
                                <span class="arabic-marquee-text">إِنَّ اللّٰهَ يَأْمُرُكُمْ أَنْ تُؤَدُّوا الْأَمَانَاتِ إِلَىٰ أَهْلِهَا</span>
                                <span class="arabic-marquee-sep">✦</span>
                                <span class="arabic-marquee-text">مَنْ سَلَكَ طَرِيقًا يَلْتَمِسُ فِيهِ عِلْمًا سَهَّلَ اللّٰهُ لَهُ بِهِ طَرِيقًا إِلَى الْجَنَّةِ</span>
                                <span class="arabic-marquee-sep">✦</span>
                                <span class="arabic-marquee-text">اَلْحَمْدُ لِلّٰهِ رَبِّ الْعَالَمِيْنَ</span>
                                <span class="arabic-marquee-sep">✦</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Center Body -->
                <div class="showcase-center">
                    <div class="emblem-wrapper">
                        <div class="emblem-glow"></div>
                        <div class="emblem-circle p-2">
                            <img src="{{ asset('img/logo.jpg') }}" alt="Logo PP Darul Muttaqin" class="rounded-circle" style="width: 100%; height: 100%; object-fit: contain;">
                        </div>
                    </div>

                    <div class="brand-pesantren-sub">Pondok Pesantren</div>
                    <h1 class="brand-pesantren-title">Darul Muttaqin Al Islami</h1>
                    
                    <div class="brand-tagline">
                        Digitalisasi Tata Kelola Keuangan Santri
                    </div>
                    
                    <p class="brand-desc">
                        Platform administrasi keuangan dan pembayaran santri berbasis akuntabilitas, transparansi, serta kemudahan transaksi terpadu bagi wali murid dan pengelola pesantren.
                    </p>

                    <div class="institusi-badges">
                        <div class="institusi-badge-pill">
                            <i class="bi bi-patch-check-fill text-warning"></i>
                            <span>Lembaga Terakreditasi</span>
                        </div>
                        <div class="institusi-badge-pill">
                            <i class="bi bi-shield-check text-success"></i>
                            <span>Akuntabel &amp; Aman</span>
                        </div>
                        <div class="institusi-badge-pill">
                            <i class="bi bi-credit-card-2-front text-info"></i>
                            <span>Pembayaran Digital</span>
                        </div>
                    </div>
                </div>

                <!-- Bottom Footer -->
                <div class="showcase-footer">
                    <div><i class="bi bi-shield-lock-fill text-success me-1"></i> 256-Bit TLS Security</div>
                    <div><i class="bi bi-check2-circle text-warning me-1"></i> Terintegrasi Midtrans &amp; WhatsApp</div>
                    <div><i class="bi bi-buildings-fill text-info me-1"></i> Darul Muttaqin &copy; 2026</div>
                </div>
            </div>

            <!-- TIRAI KANAN: PUTIH BERSIH -->
            <div class="auth-form-pane" id="curtain_right">
                <div class="login-wrapper">

                    <!-- Header Form (Rata Tengah) -->
                    <div class="text-center">
                        <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-white shadow-sm border mb-2 p-1 d-md-none" style="width: 70px; height: 70px;">
                            <img src="{{ asset('img/logo.jpg') }}" alt="Logo PP Darul Muttaqin" class="rounded-circle" style="width: 100%; height: 100%; object-fit: contain;">
                        </div>
                        <div class="form-header-badge">
                            <i class="bi bi-shield-lock-fill"></i> Portal Autentikasi Pengguna
                        </div>
                        <h2 class="form-title">Selamat Datang</h2>
                        <p class="form-subtitle">Pilih role pengujian atau masukkan kredensial akun terdaftar Anda.</p>
                    </div>

                    <!-- AJAX Dynamic Alert Box -->
                    <div id="js_alert_box" style="display: none;"></div>

                    <!-- Session Flash Messages -->
                    @if(session('info'))
                        <div class="alert alert-info py-2 px-3 small rounded-3 mb-3 d-flex align-items-center justify-content-center gap-2 text-center">
                            <i class="bi bi-info-circle-fill"></i>
                            <span>{{ session('info') }}</span>
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger py-2 px-3 small rounded-3 mb-3 d-flex align-items-center justify-content-center gap-2 text-center">
                            <i class="bi bi-exclamation-triangle-fill flex-shrink-0"></i>
                            <span>{{ $errors->first() }}</span>
                        </div>
                    @endif

                    <!-- Role Selector Tabs (Physical Morphing Glider) -->
                    <div class="role-switcher-wrapper">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <label class="form-label-custom mb-0">Role Akun Demo Cepat:</label>
                            <span class="small text-muted" style="font-size: 0.72rem;"><i class="bi bi-lightning-charge-fill text-warning"></i> 1-Klik Isi Form</span>
                        </div>
                        
                        <div class="role-switcher-card" id="role_card_container">
                            <div class="role-glider" id="role_glider"></div>
                            <button type="button" class="role-btn active" id="btn_role_admin" onclick="switchRole('admin', 0)">
                                <i class="bi bi-shield-shaded role-badge-icon"></i>
                                <span>Admin</span>
                            </button>
                            <button type="button" class="role-btn" id="btn_role_bendahara" onclick="switchRole('bendahara', 1)">
                                <i class="bi bi-wallet2 role-badge-icon"></i>
                                <span>Bendahara</span>
                            </button>
                            <button type="button" class="role-btn" id="btn_role_wali" onclick="switchRole('wali', 2)">
                                <i class="bi bi-people-fill role-badge-icon"></i>
                                <span>Wali Santri</span>
                            </button>
                        </div>

                        <!-- Role Dynamic Context Hint (Rata Tengah) -->
                        <div class="role-hint-banner mt-2" id="role_hint_box">
                            <span id="role_hint_text">
                                <i class="bi bi-shield-check text-success me-1"></i>
                                Administrator: Hak akses penuh, audit trail, &amp; gateway.
                            </span>
                            <span class="auto-fill-tag">Auto-Filled</span>
                        </div>
                    </div>

                    <!-- Form Login -->
                    <form action="{{ route('login.post') }}" method="POST" id="form_login" onsubmit="return handleFormSubmit(event)">
                        @csrf

                        <!-- Input Username -->
                        <div class="custom-form-group">
                            <label class="form-label-custom" for="input_username">Username / Identitas Pengguna</label>
                            <div class="input-wrapper">
                                <i class="bi bi-person-fill input-icon-left"></i>
                                <input type="text" name="username" id="input_username" 
                                       class="form-control-custom" 
                                       placeholder="Masukkan username Anda" 
                                       value="{{ old('username', 'admin') }}" 
                                       required autofocus autocomplete="username">
                            </div>
                        </div>

                        <!-- Input Password -->
                        <div class="custom-form-group">
                            <label class="form-label-custom" for="input_password">Kata Sandi (Password)</label>
                            <div class="input-wrapper">
                                <i class="bi bi-lock-fill input-icon-left"></i>
                                <input type="password" name="password" id="input_password" 
                                       class="form-control-custom" 
                                       placeholder="Masukkan kata sandi" 
                                       value="admin123"
                                       required autocomplete="current-password">
                                <button type="button" class="btn-toggle-pwd" id="btn_toggle_password" onclick="togglePasswordVisibility()" title="Tampilkan/Sembunyikan Kata Sandi">
                                <i class="bi bi-eye-fill" id="icon_pwd_toggle"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Remember & Action Link -->
                    <div class="auth-actions">
                        <label class="custom-check">
                            <input type="checkbox" name="remember" id="remember" checked>
                            <span>Ingat sesi masuk</span>
                        </label>
                        <a href="javascript:void(0)" onclick="showHelpModal()" class="help-link">
                            <i class="bi bi-question-circle me-1"></i>Bantuan Akses
                        </a>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="btn-submit-login" id="btn_login_submit">
                        <span class="btn-text">Masuk ke Sistem</span>
                        <i class="bi bi-arrow-right-circle-fill fs-5 btn-icon"></i>
                    </button>
                </form>

                <!-- Footer & Security Badge -->
                <div class="auth-footer">
                    <div class="d-flex align-items-center justify-content-center gap-2 mb-1">
                        <i class="bi bi-shield-fill-check text-success"></i>
                        <span class="fw-semibold text-secondary">Aplikasi Terlindungi Sistem Keamanan Berlapis</span>
                    </div>
                    <div>&copy; {{ date('Y') }} Pondok Pesantren Darul Muttaqin Al Islami</div>
                </div>

            </div>
        </div>

    </div>

    <!-- Modal Petunjuk Akses / Bantuan -->
    <div class="modal fade" id="modalHelp" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="modal-header bg-success text-white py-3 px-4">
                    <h6 class="modal-title fw-bold">
                        <i class="bi bi-info-circle me-2"></i>Informasi &amp; Kredensial Pengujian
                    </h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <p class="small text-muted mb-3">
                        Sistem ini menggunakan 3 tingkatan hak akses wewenang (*Separation of Duties*). Gunakan akun pengujian berikut saat demonstrasi sidang:
                    </p>
                    <div class="list-group list-group-flush small">
                        <div class="list-group-item px-0 py-2 d-flex justify-content-between align-items-center">
                            <div>
                                <span class="badge bg-dark me-1">Administrator</span>
                                <div class="fw-bold mt-1">Username: <code>admin</code></div>
                                <div class="text-muted">Password: <code>admin123</code></div>
                            </div>
                            <button class="btn btn-sm btn-outline-secondary" onclick="switchRole('admin', 0); bootstrap.Modal.getInstance(document.getElementById('modalHelp')).hide();">Pilih</button>
                        </div>
                        <div class="list-group-item px-0 py-2 d-flex justify-content-between align-items-center">
                            <div>
                                <span class="badge bg-success me-1">Bendahara</span>
                                <div class="fw-bold mt-1">Username: <code>bendahara</code></div>
                                <div class="text-muted">Password: <code>bendahara123</code></div>
                            </div>
                            <button class="btn btn-sm btn-outline-secondary" onclick="switchRole('bendahara', 1); bootstrap.Modal.getInstance(document.getElementById('modalHelp')).hide();">Pilih</button>
                        </div>
                        <div class="list-group-item px-0 py-2 d-flex justify-content-between align-items-center">
                            <div>
                                <span class="badge bg-info text-dark me-1">Wali Santri (Demo WA)</span>
                                <div class="fw-bold mt-1">Username: <code>wali_0002</code></div>
                                <div class="text-muted">Password: <code>wali123</code></div>
                            </div>
                            <button class="btn btn-sm btn-outline-secondary" onclick="switchRole('wali', 2); bootstrap.Modal.getInstance(document.getElementById('modalHelp')).hide();">Pilih</button>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light py-2 px-4 border-0">
                    <button type="button" class="btn btn-sm btn-secondary rounded-pill px-3" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- JavaScript Interaktif (Morphing Glider & Split Curtain Reveal Transition) -->
    <script>
        const roleData = {
            admin: {
                username: 'admin',
                password: 'admin123',
                hint: '<i class="bi bi-shield-check text-success me-1"></i> Administrator: Hak akses penuh, audit trail, &amp; gateway.',
                btnId: 'btn_role_admin'
            },
            bendahara: {
                username: 'bendahara',
                password: 'bendahara123',
                hint: '<i class="bi bi-wallet2 text-success me-1"></i> Bendahara: Kasir tunai, rekonsiliasi Midtrans, &amp; laporan.',
                btnId: 'btn_role_bendahara'
            },
            wali: {
                username: 'wali_0002',
                password: 'wali123',
                hint: '<i class="bi bi-whatsapp text-success me-1"></i> Wali Santri: Portal multi-anak (Fatimah &amp; Farhan) + Notif WA.',
                btnId: 'btn_role_wali'
            }
        };

        // Morphing Slider Transition
        function switchRole(roleKey, roleIndex) {
            const data = roleData[roleKey];
            if (!data) return;

            // Animate Glider Physically (Morph translation with spring bounce)
            const glider = document.getElementById('role_glider');
            if (glider) {
                glider.style.transform = `translateX(${roleIndex * 100}%)`;
            }

            // Update Tab Active Text States
            document.querySelectorAll('.role-btn').forEach(btn => btn.classList.remove('active'));
            const activeBtn = document.getElementById(data.btnId);
            if (activeBtn) activeBtn.classList.add('active');

            // View Transitions API (Native Web Morph support if available)
            const updateFields = () => {
                const hintText = document.getElementById('role_hint_text');
                hintText.innerHTML = data.hint;

                const userInput = document.getElementById('input_username');
                const passInput = document.getElementById('input_password');

                userInput.value = data.username;
                passInput.value = data.password;

                // Trigger soft flash transition
                userInput.classList.remove('flash-active');
                passInput.classList.remove('flash-active');
                void userInput.offsetWidth;
                void passInput.offsetWidth;
                userInput.classList.add('flash-active');
                passInput.classList.add('flash-active');
            };

            if (document.startViewTransition) {
                document.startViewTransition(updateFields);
            } else {
                updateFields();
            }
        }

        // Toggle Password Visibility
        function togglePasswordVisibility() {
            const pwdInput = document.getElementById('input_password');
            const icon = document.getElementById('icon_pwd_toggle');

            if (pwdInput.type === 'password') {
                pwdInput.type = 'text';
                icon.className = 'bi bi-eye-slash-fill';
                icon.style.color = '#059669';
            } else {
                pwdInput.type = 'password';
                icon.className = 'bi bi-eye-fill';
                icon.style.color = '#94a3b8';
            }
        }

        // ========================================================
        // SPLIT CURTAIN REVEAL LOGIN HANDLER
        // ========================================================
        async function handleFormSubmit(e) {
            if (e) e.preventDefault();

            const form = document.getElementById('form_login');
            const btn = document.getElementById('btn_login_submit');
            const alertBox = document.getElementById('js_alert_box');

            if (alertBox) alertBox.style.display = 'none';

            // Loading state
            btn.classList.add('loading');
            btn.innerHTML = `
                <span class="spinner-border spinner-border-sm text-white me-2" role="status" aria-hidden="true"></span>
                <span>Memverifikasi Kredensial...</span>
            `;

            const formData = new FormData(form);

            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    // Update reveal screen greeting
                    const revealTitle = document.getElementById('reveal_welcome_title');
                    const revealRole = document.getElementById('reveal_role_badge');
                    
                    if (revealTitle && data.name) {
                        revealTitle.innerText = `Selamat Datang, ${data.name}`;
                    }
                    if (revealRole && data.role) {
                        revealRole.innerHTML = `<i class="bi bi-shield-check me-1 text-success"></i> Membuka Portal ${data.role}...`;
                    }

                    // 1. Tampilkan Layer Portal Belakang
                    const revealLayer = document.getElementById('curtain_reveal_layer');
                    if (revealLayer) {
                        revealLayer.classList.add('active');
                    }

                    // 2. TRIGGER EFEK TERBELAH KIRI & KANAN (SPLIT CURTAIN)
                    const splitContainer = document.getElementById('auth_split_container');
                    if (splitContainer) {
                        splitContainer.classList.add('curtains-open');
                    }

                    // 3. Pindah Halaman Setelah Animasi Mulus Selesai (800ms)
                    setTimeout(() => {
                        window.location.href = data.redirect;
                    }, 800);

                } else {
                    // Gagal login
                    btn.classList.remove('loading');
                    btn.innerHTML = `
                        <span class="btn-text">Masuk ke Sistem</span>
                        <i class="bi bi-arrow-right-circle-fill fs-5 btn-icon"></i>
                    `;

                    if (alertBox) {
                        alertBox.className = 'alert alert-danger py-2 px-3 small rounded-3 mb-3 d-flex align-items-center justify-content-center gap-2 text-center';
                        alertBox.innerHTML = `<i class="bi bi-exclamation-triangle-fill flex-shrink-0"></i><span>${data.message || 'Username atau password yang dimasukkan salah.'}</span>`;
                        alertBox.style.display = 'flex';
                    }
                }
            } catch (err) {
                // Fallback standard submit
                form.submit();
            }

            return false;
        }

        // Modal Help Helper
        function showHelpModal() {
            const helpModal = new bootstrap.Modal(document.getElementById('modalHelp'));
            helpModal.show();
        }
    </script>
</body>
</html>
