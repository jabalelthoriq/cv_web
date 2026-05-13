<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>NEXUS.AI - Platform AI untuk Karir</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Space+Grotesk:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script 
    src="https://app.sandbox.midtrans.com/snap/snap.js"
    data-client-key="{{ config('midtrans.client_key') }}">
</script>
    
    <style>
        :root {
            --bg-dark: #02040a;
            --bg-panel: rgba(10, 14, 23, 0.7);
            --neon-blue: #00d2ff;
            --deep-blue: #0055ff;
            --text-main: #f0f4f8;
            --text-muted: #8b9bb4;
            --border-glass: rgba(0, 210, 255, 0.15);
            --border-glow: rgba(0, 210, 255, 0.6);
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        html { scroll-behavior: smooth; }
        body { background-color: var(--bg-dark); color: var(--text-main); overflow-x: hidden; margin: 0; font-family: 'Inter', sans-serif; }
        .ambient-bg { position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: radial-gradient(circle at 15% 50%, rgba(0, 85, 255, 0.08), transparent 40%), radial-gradient(circle at 85% 30%, rgba(0, 210, 255, 0.05), transparent 40%); z-index: -1; pointer-events: none; }
        .grid-bg { position: absolute; inset: 0; background-image: linear-gradient(rgba(255, 255, 255, 0.03) 1px, transparent 1px), linear-gradient(90deg, rgba(255, 255, 255, 0.03) 1px, transparent 1px); background-size: 50px 50px; mask-image: radial-gradient(ellipse at center, black 20%, transparent 80%); -webkit-mask-image: radial-gradient(ellipse at center, black 20%, transparent 80%); opacity: 0.5; z-index: -1; }
        .glass-panel { background: var(--bg-panel); border: 1px solid var(--border-glass); backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px); border-radius: 16px; transition: all 0.3s ease; }
        .glass-panel:hover { border-color: var(--border-glow); box-shadow: 0 0 30px rgba(0, 210, 255, 0.1); transform: translateY(-4px); }
        .btn-primary { background: linear-gradient(90deg, var(--deep-blue), var(--neon-blue)); color: #fff; font-weight: 600; padding: 12px 28px; border-radius: 8px; text-decoration: none; display: inline-flex; align-items: center; justify-content: center; box-shadow: 0 0 20px rgba(0, 210, 255, 0.3); transition: all 0.3s ease; border: none; cursor: pointer; font-family: inherit; }
        .btn-primary:hover { box-shadow: 0 0 40px rgba(0, 210, 255, 0.5); transform: scale(1.02); }
        .btn-secondary { background: transparent; color: var(--text-main); font-weight: 500; padding: 12px 28px; border-radius: 8px; text-decoration: none; display: inline-flex; align-items: center; justify-content: center; border: 1px solid var(--border-glass); transition: all 0.3s ease; cursor: pointer; font-family: inherit; }
        .btn-secondary:hover { border-color: var(--border-glow); color: var(--neon-blue); }
        .text-glow { text-shadow: 0 0 20px rgba(0, 210, 255, 0.5); }
        .nav-blur { backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px); background: rgba(2, 4, 10, 0.8); border-bottom: 1px solid var(--border-glass); }
        .section-container { max-width: 1350px; margin: 0 auto; padding: 100px 24px; position: relative; }
        .heading-xl { font-size: clamp(40px, 6vw, 72px); line-height: 1.1; font-weight: 700; letter-spacing: -0.02em; margin-bottom: 24px; font-family: 'Space Grotesk', sans-serif; }
        .heading-lg { font-size: clamp(32px, 4vw, 48px); line-height: 1.2; font-weight: 700; letter-spacing: -0.01em; margin-bottom: 16px; font-family: 'Space Grotesk', sans-serif; }
        .heading-md { font-size: 24px; font-weight: 600; margin-bottom: 12px; font-family: 'Space Grotesk', sans-serif; }
        .text-body { font-size: 18px; color: var(--text-muted); line-height: 1.6; margin-bottom: 40px; max-width: 600px; }
        svg.icon { width: 24px; height: 24px; stroke: var(--neon-blue); stroke-width: 2; fill: none; stroke-linecap: round; stroke-linejoin: round; }
        .badge { display: inline-block; padding: 6px 16px; background: rgba(0, 210, 255, 0.1); border: 1px solid var(--border-glass); border-radius: 100px; color: var(--neon-blue); font-size: 14px; font-weight: 500; margin-bottom: 24px; letter-spacing: 0.05em; font-family: 'Space Grotesk', sans-serif; }
        .step-number { font-size: 48px; font-weight: 800; background: linear-gradient(180deg, var(--neon-blue), transparent); -webkit-background-clip: text; background-clip: text; color: transparent; opacity: 0.8; font-family: 'Space Grotesk', sans-serif; }
        .spinner { display: inline-block; width: 20px; height: 20px; border: 2px solid rgba(255,255,255,0.3); border-radius: 50%; border-top-color: #00d2ff; animation: spin 0.8s linear infinite; }
        @keyframes spin { to { transform: rotate(360deg); } }
        .score-updated { animation: scorePulse 0.6s ease-in-out; }
        @keyframes scorePulse { 0% { transform: scale(1); } 40% { transform: scale(1.25); color: #fff; text-shadow: 0 0 20px var(--neon-blue); } 100% { transform: scale(1); color: var(--neon-blue); } }
        .refresh-spinning { animation: refreshSpin 0.7s linear infinite !important; }
        @keyframes refreshSpin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
        #refreshScoreBtn:disabled { opacity: 0.4 !important; cursor: not-allowed; pointer-events: none; }
        .debug-console { position: fixed; bottom: 10px; right: 10px; width: 300px; background: rgba(0,0,0,0.8); border: 1px solid #00d2ff; border-radius: 8px; padding: 8px; font-size: 10px; font-family: monospace; color: #0f0; z-index: 9999; display: none; }
        .debug-console.show { display: block; }
        @media (max-width: 768px) { .desktop-nav { display: none !important; } .mobile-col { grid-template-columns: 1fr !important; } .section-container { padding: 60px 20px; } }
        @media (min-width: 769px) { .desktop-nav { display: flex; } }
        @media (max-width: 1100px) {
    #beranda .section-container {
        grid-template-columns: 1fr !important;
        gap: 60px !important;
        text-align: center !important;
        padding: 100px 24px !important;
    }

    #beranda .section-container > div:first-child {
        max-width: 100% !important;
        text-align: center !important;
    }

    #beranda .text-body {
        margin-left: auto !important;
        margin-right: auto !important;
    }

    #beranda .section-container div[style*="display: flex; gap: 20px"] {
        justify-content: center !important;
    }
}
    </style>
</head>
<body>

<div class="ambient-bg"></div>

<div id="debugConsole" class="debug-console">
    <div><strong>🔍 Debug Console</strong> <span id="debugClose" style="float:right;cursor:pointer;">✕</span></div>
    <div id="debugLogs" style="max-height:150px;overflow-y:auto;"></div>
</div>

<script>
    let debugEnabled = false;
    function debugLog(msg) {
        console.log('[DEBUG]', msg);
        if (debugEnabled) {
            const logsDiv = document.getElementById('debugLogs');
            if (logsDiv) {
                const time = new Date().toLocaleTimeString();
                logsDiv.innerHTML += `<div>[${time}] ${msg}</div>`;
                logsDiv.scrollTop = logsDiv.scrollHeight;
            }
        }
    }
    document.addEventListener('keydown', (e) => {
        if (e.ctrlKey && e.shiftKey && e.key === 'D') {
            debugEnabled = !debugEnabled;
            const consoleDiv = document.getElementById('debugConsole');
            if (consoleDiv) consoleDiv.classList.toggle('show', debugEnabled);
            debugLog(`Debug mode ${debugEnabled ? 'ON' : 'OFF'}`);
        }
    });
    document.getElementById('debugClose')?.addEventListener('click', () => {
        document.getElementById('debugConsole').classList.remove('show');
        debugEnabled = false;
    });
    
</script>

<!-- Navbar -->
<nav id="navbar" style="position: fixed; top: 0; left: 0; right: 0; z-index: 50; transition: all 0.3s ease; padding: 24px 0;">
    <div style="max-width: 1200px; margin: 0 auto; padding: 0 24px; display: flex; justify-content: space-between; align-items: center;">
        <div  style="text-decoration: none; display: flex; align-items: center; gap: 12px;">
            <div style="width: 32px; height: 32px; border-radius: 8px; background: linear-gradient(135deg, var(--neon-blue), var(--deep-blue)); display: flex; align-items: center; justify-content: center;">
                <svg viewBox="0 0 24 24" style="width: 20px; height: 20px; stroke: #fff; stroke-width: 2; fill: none;"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5" /></svg>
            </div>
            <span style="font-size: 20px; font-weight: 700; color: #fff; letter-spacing: -0.02em; font-family: 'Space Grotesk', sans-serif;">NEXUS<span style="color: var(--neon-blue);">.AI</span></span>
</div>
        <div class="desktop-nav" style="gap: 32px;">
            <a href="#beranda" style="color: var(--text-muted); text-decoration: none; font-size: 15px; font-weight: 500;">Beranda</a>
            <a href="#fitur" style="color: var(--text-muted); text-decoration: none; font-size: 15px; font-weight: 500;">Fitur</a>
            <a href="#cara-kerja" style="color: var(--text-muted); text-decoration: none; font-size: 15px; font-weight: 500;">Cara Kerja</a>
            <a href="#harga" style="color: var(--text-muted); text-decoration: none; font-size: 15px; font-weight: 500;">Harga</a>
            <a href="#tentang" style="color: var(--text-muted); text-decoration: none; font-size: 15px; font-weight: 500;">Tentang</a>
        </div>
       <div style="display: flex; gap: 16px; align-items: center;">
    @auth

        @if(auth()->user()->hasActiveSubscription())
            <!-- ✅ SUDAH SUBSCRIBE -->
            <a href="/dashboard" class="btn-primary" style="padding: 8px 20px; font-size: 14px;">
                Dashboard
            </a>
        @else
            <!-- 🔒 BELUM SUBSCRIBE -->
            <button onclick="blockedDashboard()" 
                class="btn-primary"
                style="padding: 8px 20px; font-size: 14px; opacity: 0.6; cursor: not-allowed;">
                Dashboard
            </button>
        @endif

    @else
        <a href="/login" class="desktop-nav" style="color: #fff; text-decoration: none; font-size: 15px; font-weight: 500;">
            Masuk
        </a>
        <a href="/register" class="btn-primary" style="padding: 8px 20px; font-size: 14px;">
            Mulai Gratis
        </a>
    @endauth
</div>
    </div>
</nav>

<script>
    window.addEventListener('scroll', function() {
        const navbar = document.getElementById('navbar');
        if (window.scrollY > 20) { navbar.classList.add('nav-blur'); navbar.style.padding = '16px 0'; }
        else { navbar.classList.remove('nav-blur'); navbar.style.padding = '24px 0'; }
    });
</script>
<!-- Hero Section -->
<section id="beranda" style="min-height: 100vh; display: flex; align-items: center; position: relative; overflow: hidden;">

    <div class="grid-bg"></div>

    <div class="section-container"
         style="
            width: 100%;
            max-width: 1400px;
            margin: 0 auto;
            padding: 120px 40px;
            display: grid;
            grid-template-columns: 1.1fr 1fr;
            align-items: center;
            gap: 90px;
            position: relative;
            z-index: 10;
         ">

        <!-- LEFT CONTENT -->
        <div style="text-align: left; max-width: 680px;">

            <div class="badge">
                ✨ PLATFORM AI KARIR TERBAIK 2026
            </div>

            <h1 class="heading-xl" style="margin-bottom: 28px;">
                Tingkatkan Peluang Karirmu<br />
                dengan
                <span style="color: var(--neon-blue);" class="text-glow">
                    Kecerdasan Buatan
                </span>
            </h1>

            <p class="text-body"
               style="
                    max-width: 640px;
                    font-size: 20px;
                    line-height: 1.8;
                    margin-bottom: 42px;
               ">
                Analisis mendalam CV Anda, dapatkan skor ATS real-time,
                rekomendasi pekerjaan akurat, dan berlatih wawancara
                langsung dengan agen AI kami.
            </p>

            <div style="display: flex; gap: 20px; flex-wrap: wrap;">

                @auth
                    <a href="/dashboard"
                       class="btn-primary"
                       style="font-size: 16px; padding: 18px 38px;">
                        Upload CV Sekarang
                    </a>
                @else
                    <a href="/register"
                       class="btn-primary"
                       style="font-size: 16px; padding: 18px 38px;">
                        Upload CV Sekarang
                    </a>
                @endauth

                <a href="#cara-kerja"
                   class="btn-secondary"
                   style="font-size: 16px; padding: 18px 38px;">
                    Lihat Cara Kerja
                </a>

            </div>
        </div>

        <!-- RIGHT IMAGE -->
        <div style="position: relative; width: 100%;">

            <div style="
                position: absolute;
                inset: -20px;
                background: linear-gradient(180deg, var(--neon-blue), var(--deep-blue));
                opacity: 0.25;
                filter: blur(70px);
                border-radius: 30px;
                z-index: -1;
            "></div>

            <div class="glass-panel"
                 style="
                    padding: 6px;
                    border-radius: 28px;
                    background: rgba(10, 14, 23, 0.5);
                    border: 1px solid rgba(0, 210, 255, 0.25);
                 ">

                <img
                    src="https://images.unsplash.com/photo-1551288049-bebda4e38f71?q=80&w=2070&auto=format&fit=crop"
                    alt="Dashboard Preview"
                    style="
                        width: 100%;
                        height: auto;
                        border-radius: 22px;
                        display: block;
                        opacity: 0.9;
                        object-fit: cover;
                        filter: sepia(0.15) hue-rotate(180deg) saturate(1.4);
                    "
                />

            </div>
        </div>

    </div>
</section>

<!-- Features Section -->
<section id="fitur" style="position: relative;">
    <div class="section-container" style="max-width: 1350px;">
        
        <div style="text-align: center; margin-bottom: 72px;">
            <div class="badge">MODUL UNGGULAN</div>

            <h2 class="heading-lg">
                Fitur Lengkap Untuk<br/>
                <span style="color: var(--neon-blue);">
                    Melesatkan Karirmu
                </span>
            </h2>

            <p class="text-body" style="margin: 0 auto; max-width: 760px;">
                Semua alat yang Anda butuhkan untuk mendapatkan pekerjaan impian,
                ditenagai oleh AI Generatif tercanggih.
            </p>
        </div>

        <!-- GRID CARD -->
        <div style="
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(360px, 1fr));
            gap: 28px;
        ">

            <!-- CARD 1 -->
            <div class="glass-panel" style="padding: 38px;">
                <div style="
                    width: 58px;
                    height: 58px;
                    border-radius: 16px;
                    background: rgba(0,210,255,0.1);
                    display:flex;
                    align-items:center;
                    justify-content:center;
                    margin-bottom:24px;
                    border:1px solid rgba(0,210,255,0.2);
                ">
                    <svg class="icon" viewBox="0 0 24 24">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                        <polyline points="14 2 14 8 20 8"/>
                    </svg>
                </div>

                <h3 class="heading-md">Analisis Skor ATS</h3>

                <p style="color: var(--text-muted); line-height: 1.7; margin:0;">
                    Ketahui seberapa lengkap CV Anda terhadap sistem ATS perusahaan.
                </p>
            </div>

            <!-- CARD 2 -->
            <div class="glass-panel" style="padding: 38px;">
                <div style="
                    width: 58px;
                    height: 58px;
                    border-radius: 16px;
                    background: rgba(0,210,255,0.1);
                    display:flex;
                    align-items:center;
                    justify-content:center;
                    margin-bottom:24px;
                    border:1px solid rgba(0,210,255,0.2);
                ">
                    <svg class="icon" viewBox="0 0 24 24">
                        <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/>
                        <polyline points="3 8 12 13 21 8"/>
                    </svg>
                </div>

                <h3 class="heading-md">Rekomendasi Pintar</h3>

                <p style="color: var(--text-muted); line-height: 1.7; margin:0;">
                    AI mencocokkan skill Anda dengan lowongan kerja paling relevan.
                </p>
            </div>

            <!-- CARD 3 -->
            <div class="glass-panel" style="padding: 38px;">
                <div style="
                    width: 58px;
                    height: 58px;
                    border-radius: 16px;
                    background: rgba(0,210,255,0.1);
                    display:flex;
                    align-items:center;
                    justify-content:center;
                    margin-bottom:24px;
                    border:1px solid rgba(0,210,255,0.2);
                ">
                    <svg class="icon" viewBox="0 0 24 24">
                        <path d="M12 2a3 3 0 0 0-3 3v7a3 3 0 0 0 6 0V5a3 3 0 0 0-3-3Z"/>
                        <path d="M19 10v3a7 7 0 0 1-14 0v-3"/>
                        <line x1="12" y1="19" x2="12" y2="22"/>
                    </svg>
                </div>

                <h3 class="heading-md">Simulasi Wawancara</h3>

                <p style="color: var(--text-muted); line-height: 1.7; margin:0;">
                    Latihan interview realtime dengan AI interviewer interaktif.
                </p>
            </div>

            <!-- CARD 4 -->
            <div class="glass-panel" style="padding: 38px;">
                <div style="
                    width: 58px;
                    height: 58px;
                    border-radius: 16px;
                    background: rgba(0,210,255,0.1);
                    display:flex;
                    align-items:center;
                    justify-content:center;
                    margin-bottom:24px;
                    border:1px solid rgba(0,210,255,0.2);
                ">
                    <svg class="icon" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="10"/>
                        <path d="M12 6v6l4 2"/>
                    </svg>
                </div>

                <h3 class="heading-md">Analisis Cepat</h3>

                <p style="color: var(--text-muted); line-height: 1.7; margin:0;">
                    Dapatkan hasil analisis CV hanya dalam hitungan detik.
                </p>
            </div>

            <!-- CARD 5 -->
            <div class="glass-panel" style="padding: 38px;">
                <div style="
                    width: 58px;
                    height: 58px;
                    border-radius: 16px;
                    background: rgba(0,210,255,0.1);
                    display:flex;
                    align-items:center;
                    justify-content:center;
                    margin-bottom:24px;
                    border:1px solid rgba(0,210,255,0.2);
                ">
                    <svg class="icon" viewBox="0 0 24 24">
                        <path d="M3 12h18"/>
                        <path d="M12 3v18"/>
                    </svg>
                </div>

                <h3 class="heading-md">CV Improvement</h3>

                <p style="color: var(--text-muted); line-height: 1.7; margin:0;">
                    AI memberi saran perbaikan kalimat dan keyword profesional.
                </p>
            </div>

            <!-- CARD 6 -->
            <div class="glass-panel" style="padding: 38px;">
                <div style="
                    width: 58px;
                    height: 58px;
                    border-radius: 16px;
                    background: rgba(0,210,255,0.1);
                    display:flex;
                    align-items:center;
                    justify-content:center;
                    margin-bottom:24px;
                    border:1px solid rgba(0,210,255,0.2);
                ">
                    <svg class="icon" viewBox="0 0 24 24">
                        <path d="M22 12h-4l-3 9L9 3 6 12H2"/>
                    </svg>
                </div>

                <h3 class="heading-md">Tracking Progress</h3>

                <p style="color: var(--text-muted); line-height: 1.7; margin:0;">
                    Pantau perkembangan kualitas CV dan performa interview Anda.
                </p>
            </div>

        </div>
    </div>
</section>

<!-- How It Works -->
<section id="cara-kerja">
    <div class="section-container" style="max-width: 1350px;">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 64px; align-items: center;" class="mobile-col">
            <div>
                <div class="badge">ALUR KERJA</div>
                <h2 class="heading-lg">Tiga Langkah Sederhana Menuju <span style="color: var(--neon-blue);">Kesuksesan</span></h2>
                <p class="text-body">Tidak perlu pengaturan rumit. Kami mendesain prosesnya semudah mungkin agar Anda bisa fokus pada karir Anda.</p>
                <div style="margin-top: 40px; display: flex; flex-direction: column; gap: 32px;">
                    <div style="display: flex; gap: 24px;"><div class="step-number">01</div><div><h3 class="heading-md" style="margin-bottom: 8px;">Unggah CV & Profil</h3><p style="color: var(--text-muted); line-height: 1.6; margin: 0;">Upload CV PDF Anda, sistem akan mengekstrak informasi dalam hitungan detik.</p></div></div>
                    <div style="display: flex; gap: 24px;"><div class="step-number">02</div><div><h3 class="heading-md" style="margin-bottom: 8px;">Analisis & Optimasi AI</h3><p style="color: var(--text-muted); line-height: 1.6; margin: 0;">AI menganalisis kelengkapan, keterbacaan ATS, dan kualitas kalimat.</p></div></div>
                    <div style="display: flex; gap: 24px;"><div class="step-number">03</div><div><h3 class="heading-md" style="margin-bottom: 8px;">Dapatkan Pekerjaan</h3><p style="color: var(--text-muted); line-height: 1.6; margin: 0;">Terapkan revisi, daftar ke rekomendasi pekerjaan, dan latih wawancara AI.</p></div></div>
                </div>
            </div>

            <div style="position: relative;">
                <div class="glass-panel" style="padding: 40px; display: flex; flex-direction: column; gap: 24px; position: relative; z-index: 2;">
                    <!-- Upload Area -->
                    <div style="padding: 20px; background: rgba(0,0,0,0.3); border-radius: 12px; border: 1px dashed var(--border-glow); text-align: center;">
                        <input type="file" id="cvFileInput" style="display:none" accept="application/pdf" onchange="handleUploadCV(this)">
                        <div onclick="document.getElementById('cvFileInput').click()" style="cursor:pointer;">
                            <svg viewBox="0 0 24 24" style="width: 32px; height: 32px; stroke: var(--neon-blue); fill: none; stroke-width: 2; margin-bottom: 12px;"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4M17 8l-5-5-5 5M12 3v12" /></svg>
                            <div style="color: #fff;">Klik untuk Upload CV</div>
                            <div style="color: var(--text-muted); font-size: 12px; margin-top: 8px;">PDF max 2MB</div>
                        </div>
                    </div>

                    <!-- Score Display -->
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 16px 20px; background: rgba(0, 210, 255, 0.05); border-radius: 12px; position: relative;">
                        <div>
                            <div style="color: #fff;">Skor Kelengkapan CV</div>
                            <div style="color: var(--text-muted); font-size: 13px;">Analisis otomatis</div>
                        </div>
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <div id="cvScoreDisplay" style="font-size: 24px; font-weight: 700; color: var(--neon-blue);">-
                                @auth
                                    {{ optional(App\Models\Cv::where('user_id', auth()->id())->latest()->first())->score ?? '-' }}%
                                @else
                                    -
                                @endauth
                            </div>
                            <!-- Tombol Refresh -->
                            <button
                                id="refreshScoreBtn"
                                onclick="refreshCard()"
                                title="Refresh skor CV"
                                style="
                                    width: 32px; height: 32px;
                                    border-radius: 50%;
                                    border: 1px solid rgba(0, 210, 255, 0.35);
                                    background: rgba(0, 210, 255, 0.08);
                                    display: flex; align-items: center; justify-content: center;
                                    cursor: pointer;
                                    transition: all 0.25s ease;
                                    padding: 0;
                                    flex-shrink: 0;
                                "
                                onmouseenter="this.style.background='rgba(0,210,255,0.18)';this.style.borderColor='rgba(0,210,255,0.7)';this.style.boxShadow='0 0 12px rgba(0,210,255,0.3)'"
                                onmouseleave="this.style.background='rgba(0,210,255,0.08)';this.style.borderColor='rgba(0,210,255,0.35)';this.style.boxShadow='none'"
                            >
                                <svg id="refreshIcon" viewBox="0 0 24 24" style="width:15px;height:15px;stroke:var(--neon-blue);fill:none;stroke-width:2.2;stroke-linecap:round;stroke-linejoin:round;transition:transform 0.4s ease;">
                                    <polyline points="23 4 23 10 17 10"/>
                                    <polyline points="1 20 1 14 7 14"/>
                                    <path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <button id="unlockBtn" onclick="payForAnalysis()" class="btn-primary" disabled style="opacity: 0.5;">
                        Buka Detail Lengkap (Rp10.000)
                    </button>
                    
                    <div id="uploadStatus" style="display: none; text-align: center; font-size: 12px;"></div>
                    <div id="pollingStatus" style="display: none; text-align: center; font-size: 11px; color: #ffaa00;"></div>
                </div>
                <div style="position: absolute; inset: 0; background: var(--neon-blue); opacity: 0.15; filter: blur(60px); border-radius: 50%; z-index: 1; transform: scale(1.2);"></div>
            </div>
        </div>
    </div>
</section>

<!-- Pricing -->
<section id="harga" style="position: relative;">
    <div style="position: absolute; top: 0; left: 0; right: 0; height: 1px; background: linear-gradient(90deg, transparent, var(--border-glass), transparent);"></div>
    
    <div class="section-container" style="max-width: 1350px;">
        <div style="text-align: center; margin-bottom: 64px;">
            <div class="badge">PAKET HARGA</div>
            <h2 class="heading-lg">Investasi Untuk <span style="color: var(--neon-blue);">Masa Depanmu</span></h2>
            <p class="text-body" style="margin: 0 auto;">Pilih paket yang sesuai dengan kebutuhan pencarian kerja Anda.</p>
        </div>

        <div style="display: flex; gap: 60px; justify-content: center; flex-wrap: wrap;">
            
            <!-- FREE -->
            <div class="glass-panel" style="flex: 1 1 280px; max-width: 360px; padding: 40px; display: flex; flex-direction: column;">
                <h3 class="heading-md">Free</h3>
                <p style="color: var(--text-muted); margin-bottom: 24px;">Mulai eksplorasi AI.</p>
                
                <div style="margin-bottom: 32px;">
                    <span style="font-size: 48px; font-weight: 700;">Rp 0</span>
                    <span style="color: var(--text-muted);">/selamanya</span>
                </div>

                <ul style="list-style: none; margin-bottom: 40px; display: flex; flex-direction: column; gap: 12px;">
                    
                    <li>✅ Skor ATS Dasar</li>
                    <li>❌ Rekomendasi Pekerjaan</li>
                    <li>❌ Simulasi Interview</li>
                </ul>

                
                <a href="/#cara-kerja" class="btn-secondary" style="width: 100%;">Coba Gratis</a>
                
            </div>

            <!-- PLUS -->
            <div class="glass-panel" style="flex: 1 1 280px; max-width: 360px; padding: 40px; display: flex; flex-direction: column; border: 1px solid var(--border-glass);">
                <h3 class="heading-md">Plus</h3>
                <p style="color: var(--text-muted); margin-bottom: 24px;">Upgrade untuk hasil lebih optimal.</p>
                
                <div style="margin-bottom: 32px;">
                    <span style="font-size: 48px; font-weight: 700;">Rp 2rb</span>
                    <span style="color: var(--text-muted);">/bulan</span>
                </div>

                <ul style="list-style: none; margin-bottom: 40px; display: flex; flex-direction: column; gap: 12px;">
                    <li>✅ Analisis CV / bulan</li>
                    <li>✅ Skor ATS + Keyword</li>
                    <li>✅ 12 Rekomendasi Pekerjaan</li>
                    <li>❌ Simulasi Interview Terbatas</li>
                </ul>

                @auth
                <button onclick="subscribePlan('plus')" class="btn-secondary" style="width:100%;">
                    Langganan Plus
                </button>
                @else
                <a href="/login" class="btn-secondary" style="width:100%; text-align:center;">
                    Login untuk Langganan
                </a>
                @endauth
            </div>

            <!-- PRO -->
            <div class="glass-panel" style="flex: 1 1 280px; max-width: 360px; padding: 40px; display: flex; flex-direction: column; border: 1px solid var(--border-glow); position: relative; transform: scale(1.05); background: rgba(10, 20, 40, 0.8);">
                
                <div style="position: absolute; top: -14px; left: 50%; transform: translateX(-50%); background: var(--neon-blue); color: #000; font-weight: 700; font-size: 13px; padding: 4px 16px; border-radius: 100px;">
                    PALING POPULER
                </div>

                <h3 class="heading-md">Pro</h3>
                <p style="color: var(--text-muted); margin-bottom: 24px;">Untuk jobseeker serius.</p>
                
                <div style="margin-bottom: 32px;">
                    <span style="font-size: 48px; font-weight: 700;">Rp 5rb</span>
                    <span style="color: var(--text-muted);">/bulan</span>
                </div>

                <ul style="list-style: none; margin-bottom: 40px; display: flex; flex-direction: column; gap: 12px;">
                    <li>✅ Analisis CV Tak Terbatas</li>
                    <li>✅ ATS Mendetail + Keyword</li>
                    <li>✅ 12 Rekomendasi Pekerjaan</li>
                    <li>✅ Simulasi Interview / bulan</li>
                </ul>

                @auth
                <button onclick="subscribePlan('pro')" class="btn-primary" style="width:100%;">
                    Langganan Pro
                </button>
                @else
                <a href="/login" class="btn-primary" style="width:100%; text-align:center;">
                    Login untuk Langganan
                </a>
                @endauth
            </div>

        </div>
    </div>
</section>

<!-- CTA -->
<section style="padding: 100px 24px;">
    <div class="glass-panel" style="max-width: 1000px; margin: 0 auto; padding: 80px 40px; text-align: center;">
        <h2 class="heading-lg">Siap Membuka <span style="color: var(--neon-blue);">Potensi Penuhmu?</span></h2>
        <p class="text-body" style="margin: 0 auto 40px auto;">Bergabunglah dengan ribuan pencari kerja lainnya yang telah menemukan karir impian mereka bersama Nexus AI.</p>
        @auth <a href="/dashboard" class="btn-primary" style="font-size: 18px; padding: 20px 48px;">Buka Dashboard</a>@else <a href="/register" class="btn-primary" style="font-size: 18px; padding: 20px 48px;">Buat Akun Gratis Sekarang</a>@endauth
    </div>
</section>

<!-- Footer -->
<footer id="tentang" style="border-top: 1px solid var(--border-glass); background: rgba(0,0,0,0.5); padding: 64px 24px 32px;">
    <div style="max-width: 1200px; margin: 0 auto; display: flex; flex-wrap: wrap; justify-content: space-between;">
        <div style="max-width: 300px;">
            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 20px;"><span style="font-size: 18px; font-weight: 700;">NEXUS<span style="color: var(--neon-blue);">.AI</span></span></div>
            <p style="color: var(--text-muted); font-size: 15px;">Memanfaatkan AI generatif untuk merevolusi proses rekrutmen dan pencarian kerja di Indonesia.</p>
        </div>
        <div><h4 style="color: #fff;">Platform</h4><div><a href="#" style="color: var(--text-muted);">Analisis CV</a></div></div>
        <div><h4 style="color: #fff;">Perusahaan</h4><div><a href="#" style="color: var(--text-muted);">Tentang Kami</a></div></div>
    </div>
    <div style="max-width: 1200px; margin: 32px auto 0; border-top: 1px solid var(--border-glass); padding-top: 32px; text-align: center;">
        <p style="color: var(--text-muted);">&copy; 2026 Nexus AI. All rights reserved.</p>
    </div>
</footer>


<script>
   
// ========================================
// GLOBAL VARIABLES
// ========================================
let currentCvId = null;
let pollingInterval = null;

function blockedDashboard() {
    Swal.fire({
        title: 'Akses Terkunci',
        text: 'Silakan berlangganan terlebih dahulu untuk membuka dashboard',
        icon: 'warning',
        confirmButtonText: 'Lihat Paket',
        background: '#07111f',
        color: '#fff'
    }).then((result) => {
        if (result.isConfirmed) {
            // scroll ke pricing
            document.getElementById('harga').scrollIntoView({
                behavior: 'smooth'
            });
        }
    });
}
async function fetchScoreFromDB(cvId) {
    if (!cvId) {
        debugLog('fetchScoreFromDB: cvId kosong');
        return;
    }

    try {
        debugLog(`Fetch score dari DB untuk CV ID: ${cvId}`);

        const response = await fetch(`/cv/${cvId}/score`, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            credentials: 'same-origin'
        });

        if (!response.ok) {
            debugLog(`Fetch error: ${response.status}`);
            return;
        }

        const data = await response.json();
        debugLog(`DB Score response: ${JSON.stringify(data)}`);

        if (data.score !== null && data.score !== undefined) {
            updateScoreCard(data.score);
        }

    } catch (err) {
        debugLog(`Fetch DB error: ${err.message}`);
    }
}
// ========================================
// API HELPER
// ========================================
async function apiRequest(url, options = {}) {
    const defaultOptions = {
        method: 'GET',
        credentials: 'same-origin',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        }
    };
    const mergedOptions = { ...defaultOptions, ...options };
    if (mergedOptions.body && !(mergedOptions.body instanceof FormData)) {
        mergedOptions.headers['Content-Type'] = 'application/json';
        mergedOptions.body = JSON.stringify(mergedOptions.body);
    }
    debugLog(`API Request: ${mergedOptions.method} ${url}`);
    try {
        const response = await fetch(url, mergedOptions);
        debugLog(`Response status: ${response.status}`);
        if (response.redirected && response.url.includes('/login')) throw new Error('Sesi Anda telah habis. Silakan refresh halaman dan login kembali.');
        const contentType = response.headers.get('content-type');
        if (!contentType || !contentType.includes('application/json')) {
            const text = await response.text();
            debugLog(`Non-JSON response: ${text.substring(0, 200)}`);
            if (response.status === 419) throw new Error('CSRF token mismatch. Refresh halaman.');
            if (response.status === 401 || response.status === 403) throw new Error('Unauthorized. Silakan login.');
            throw new Error(`Server error: ${response.status}`);
        }
        const data = await response.json();
        if (!response.ok) throw new Error(data.message || `Error ${response.status}`);
        return data;
    } catch (err) {
        debugLog(`API Error: ${err.message}`);
        throw err;
    }
}

// ========================================
// UPLOAD CV
// ========================================
async function handleUploadCV(inputElement) {
    const file = inputElement.files[0];
    if (!file) return;
    debugLog(`File selected: ${file.name}, size: ${file.size}, type: ${file.type}`);
    if (file.type !== 'application/pdf') {
        Swal.fire({ title: 'Error', text: 'Harus file PDF', icon: 'error', background: '#07111f', color: '#fff' });
        inputElement.value = '';
        return;
    }
    if (file.size > 2 * 1024 * 1024) {
        Swal.fire({ title: 'Error', text: 'Ukuran file maksimal 2MB', icon: 'error', background: '#07111f', color: '#fff' });
        inputElement.value = '';
        return;
    }
    Swal.fire({ title: 'Uploading...', text: 'AI sedang memproses CV kamu', allowOutsideClick: false, didOpen: () => Swal.showLoading(), background: '#07111f', color: '#fff' });
    const formData = new FormData();
    formData.append('cv_file', file);
    try {
        const response = await fetch('/upload-cv', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' },
            credentials: 'same-origin',
            body: formData
        });
        debugLog(`Upload response status: ${response.status}`);
        const contentType = response.headers.get('content-type');
        if (!contentType || !contentType.includes('application/json')) {
            if (response.redirected) throw new Error('Sesi habis. Silakan refresh halaman.');
            const text = await response.text();
            debugLog(`Non-JSON upload response: ${text.substring(0, 200)}`);
            throw new Error('Server error: Invalid response');
        }
        const data = await response.json();
        debugLog(`Upload response data: ${JSON.stringify(data)}`);
        if (!response.ok) throw new Error(data.message || 'Upload gagal');

        currentCvId = data.data.id;
        debugLog(`CV uploaded with ID: ${currentCvId}`);

        // Reset UI
        document.getElementById('cvScoreDisplay').innerText = '...';
        document.getElementById('uploadStatus').style.display = 'none';
        document.getElementById('pollingStatus').style.display = 'block';
        document.getElementById('pollingStatus').innerHTML = 'Menganalisis CV...';

        startPollingScore();

        Swal.fire({ title: 'Berhasil!', text: 'CV sedang dianalisis AI...', icon: 'success', timer: 1500, showConfirmButton: false, background: '#07111f', color: '#fff' });
    } catch (err) {
        debugLog(`Upload error: ${err.message}`);
        Swal.fire({ title: 'Error', text: err.message, icon: 'error', background: '#07111f', color: '#fff' });
        inputElement.value = '';
    }
}

// ========================================
// UPDATE SCORE CARD — animasi counter naik
// ========================================
function updateScoreCard(score) {
    const scoreEl = document.getElementById('cvScoreDisplay');
    if (!scoreEl) return;

    const target = parseInt(score) || 0;
    let current = 0;

    scoreEl.innerText = '0%';

    const step = Math.max(1, Math.ceil(target / 60));
    const interval = setInterval(() => {
        current = Math.min(current + step, target);
        scoreEl.innerText = current + '%';
        if (current >= target) {
            clearInterval(interval);
            scoreEl.classList.add('score-updated');
            setTimeout(() => scoreEl.classList.remove('score-updated'), 600);
        }
    }, 16);
}

// ========================================
// POLLING SCORE — robust JSON parse
// ========================================
function startPollingScore() {
    if (pollingInterval) clearInterval(pollingInterval);

    let attempts = 0;
    const maxAttempts = 30;

    pollingInterval = setInterval(async () => {
        attempts++;

        debugLog(`Polling attempt ${attempts}/${maxAttempts} for CV ID: ${currentCvId}`);

        if (!currentCvId) {
            clearInterval(pollingInterval);
            pollingInterval = null;
            return;
        }

        try {
            const response = await fetch(`/cv/${currentCvId}/score`, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector(
                        'meta[name="csrf-token"]'
                    ).content
                },
                credentials: 'same-origin'
            });

            if (!response.ok) {
                debugLog(`Score API error: ${response.status}`);
                return;
            }

            const data = await response.json();

            debugLog(`Score response: ${JSON.stringify(data)}`);

            const score = data.score;

            // =========================
            // FIX UTAMA
            // cukup cek score dari DB
            // =========================
            if (score !== null && score !== undefined) {
                clearInterval(pollingInterval);
                pollingInterval = null;

                debugLog(`Score ditemukan dari DB: ${score}%`);

                // update score UI
                updateScoreCard(score);

                // aktifkan tombol payment
                const unlockBtn = document.getElementById('unlockBtn');
                if (unlockBtn) {
                    unlockBtn.disabled = false;
                    unlockBtn.style.opacity = '1';
                    debugLog('Unlock button berhasil diaktifkan');
                }

                // sembunyikan status polling
                const pollingStatus = document.getElementById('pollingStatus');
                if (pollingStatus) {
                    pollingStatus.style.display = 'none';
                }

                // warning jika score 0
                if (parseInt(score) === 0) {
                    const statusDiv = document.getElementById('uploadStatus');
                    if (statusDiv) {
                        statusDiv.style.display = 'block';
                        statusDiv.innerHTML =
                            '<span style="color:#ffaa00;">⚠️ Skor 0% — CV mungkin kosong atau tidak terbaca. Coba upload ulang.</span>';
                    }
                }

                return;
            }

            // jika score masih null
            const pollingStatus = document.getElementById('pollingStatus');
            if (pollingStatus) {
                pollingStatus.style.display = 'block';
                pollingStatus.innerHTML =
                    `Menganalisis CV`;
            }

        } catch (err) {
            debugLog(`Polling error: ${err.message}`);
        }

        // timeout
        if (attempts >= maxAttempts) {
            clearInterval(pollingInterval);
            pollingInterval = null;

            const pollingStatus = document.getElementById('pollingStatus');
            if (pollingStatus) {
                pollingStatus.style.display = 'none';
            }

            const statusDiv = document.getElementById('uploadStatus');
            if (statusDiv) {
                statusDiv.style.display = 'block';
                statusDiv.innerHTML =
                    '<span style="color:#ffaa00;">⏳ Skor belum tersedia. Silakan refresh beberapa saat lagi.</span>';
            }
        }

    }, 2000);
}

// ========================================
// REFRESH CARD — reset UI ke kondisi awal
// ========================================
function refreshCard() {
    if (!currentCvId) {
        debugLog('Tidak ada CV ID untuk refresh');
        return;
    }

    const refreshBtn  = document.getElementById('refreshScoreBtn');
    const refreshIcon = document.getElementById('refreshIcon');

    refreshBtn.disabled = true;
    refreshIcon.classList.add('refresh-spinning');

    setTimeout(async () => {
        await fetchScoreFromDB(currentCvId);

        refreshIcon.classList.remove('refresh-spinning');
        refreshBtn.disabled = false;

        debugLog('Score berhasil di-refresh dari DB');
    }, 600);
}

// ========================================
// PAYMENT
// ========================================
async function payForAnalysis() {
    if (!currentCvId) {
        Swal.fire({
            title: 'Peringatan',
            text: 'Upload CV terlebih dahulu',
            icon: 'warning',
            background: '#07111f',
            color: '#fff'
        });
        return;
    }

    debugLog(`Starting payment for CV ID: ${currentCvId}`);

    Swal.fire({
        title: 'Memproses pembayaran...',
        text: 'Menyiapkan Payment',
        allowOutsideClick: false,
        didOpen: () => Swal.showLoading(),
        background: '#07111f',
        color: '#fff'
    });

    try {
        const response = await fetch(`/payment/${currentCvId}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector(
                    'meta[name="csrf-token"]'
                ).content,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            credentials: 'same-origin'
        });

        const data = await response.json();

        debugLog(`Payment response: ${JSON.stringify(data)}`);

        Swal.close();

        if (!data.snap_token || !data.order_id) {
            throw new Error('Snap token / order_id tidak valid');
        }

        if (typeof snap === 'undefined') {
            throw new Error('Midtrans Snap belum ter-load');
        }

        snap.pay(data.snap_token, {
            onSuccess: async function(result) {
                debugLog(`Payment success: ${JSON.stringify(result)}`);

                Swal.fire({
                    title: 'Pembayaran Berhasil!',
                    text: 'Mengambil detail analisis premium...',
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading(),
                    background: '#07111f',
                    color: '#fff'
                });

                await waitForPremiumResult(data.order_id);
            },

            onPending: function(result) {
                debugLog(`Payment pending: ${JSON.stringify(result)}`);

                Swal.fire({
                    title: 'Menunggu Pembayaran',
                    text: 'Silakan selesaikan pembayaran Anda',
                    icon: 'info',
                    background: '#07111f',
                    color: '#fff'
                });
            },

            onError: function(error) {
                debugLog(`Payment error: ${JSON.stringify(error)}`);

                Swal.fire({
                    title: 'Pembayaran Gagal',
                    text: 'Terjadi kesalahan saat pembayaran',
                    icon: 'error',
                    background: '#07111f',
                    color: '#fff'
                });
            },

            onClose: function() {
                debugLog('User closed payment popup');

                Swal.fire({
                    title: 'Pembayaran Dibatalkan',
                    text: 'Popup pembayaran ditutup',
                    icon: 'warning',
                    background: '#07111f',
                    color: '#fff'
                });
            }
        });

    } catch (err) {
        debugLog(`Payment error: ${err.message}`);

        Swal.fire({
            title: 'Error',
            text: err.message,
            icon: 'error',
            background: '#07111f',
            color: '#fff'
        });
    }
}
let isPollingResult = false;

async function waitForPremiumResult(orderId) {
    if (isPollingResult) {
        debugLog('Polling sudah berjalan, skip...');
        return;
    }

    isPollingResult = true;

    let attempts = 0;
    const maxAttempts = 20;

    const interval = setInterval(async () => {
        attempts++;

        debugLog(`Checking premium result (${attempts}/${maxAttempts})`);

        try {
            const response = await fetch(`/payment/result/${orderId}`);
            const data = await response.json();

            debugLog(`Premium result response: ${JSON.stringify(data)}`);

            if (data.status === 'success' && data.link) {
                clearInterval(interval);

                Swal.fire({
                    title: 'Akses Premium Dibuka!',
                    text: 'Membuka hasil analisis...',
                    icon: 'success',
                    timer: 1500,
                    showConfirmButton: false,
                    background: '#07111f',
                    color: '#fff'
                });

                // 🔥 FIX NGROK + ASYNC
                setTimeout(async () => {
                    try {
                        // warmup request ke ngrok
                        await fetch(data.link, {
                            headers: {
                                "ngrok-skip-browser-warning": "true"
                            }
                        });

                        // baru buka tab
                        window.open(data.link, '_blank');

                    } catch (e) {
                        debugLog('Warmup gagal, tetap buka link');
                        window.open(data.link, '_blank');
                    }
                }, 1200);

                isPollingResult = false;
                return;
            }

        } catch (err) {
            debugLog(`Polling error: ${err.message}`);
        }

        if (attempts >= maxAttempts) {
            clearInterval(interval);
            isPollingResult = false;

            Swal.fire({
                title: 'Timeout',
                text: 'Server lama memproses hasil.',
                icon: 'info',
                background: '#07111f',
                color: '#fff'
            });
        }

    }, 3000);
}

async function subscribePlan(plan) {
    debugLog(`Subscribe plan: ${plan}`);

    Swal.fire({
        title: 'Memproses...',
        text: 'Menyiapkan pembayaran',
        allowOutsideClick: false,
        didOpen: () => Swal.showLoading(),
        background: '#07111f',
        color: '#fff'
    });

    try {
        const response = await fetch('/subscribe', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ plan })
        });

        const data = await response.json();
        Swal.close();

        if (!data.snap_token) {
            throw new Error('Snap token tidak ada');
        }

        snap.pay(data.snap_token, {
            onSuccess: function(result) {
    Swal.fire({
        title: 'Berhasil!',
        text: 'Langganan aktif',
        icon: 'success',
        background: '#07111f',
        color: '#fff'
    }).then(() => {
        // 🔥 WAJIB reload biar auth()->user() update
        window.location.href = '/';
setTimeout(() => {
    location.reload();
}, 500);
    });
},

            onPending: function() {
                Swal.fire({
                    title: 'Menunggu Pembayaran',
                    icon: 'info',
                    background: '#07111f',
                    color: '#fff'
                });
            },

            onError: function() {
                Swal.fire({
                    title: 'Gagal',
                    text: 'Pembayaran error',
                    icon: 'error',
                    background: '#07111f',
                    color: '#fff'
                });
            }
        });

    } catch (err) {
        Swal.fire({
            title: 'Error',
            text: err.message,
            icon: 'error',
            background: '#07111f',
            color: '#fff'
        });
    }
}

document.querySelectorAll('#navbar a:not(.btn-primary)').forEach(link => {

    // setup awal
    link.style.position = 'relative';
    link.style.transition = 'all .28s ease';

    // underline glow
    const underline = document.createElement('span');

    underline.style.position = 'absolute';
    underline.style.left = '50%';
    underline.style.bottom = '-6px';
    underline.style.width = '0';
    underline.style.height = '2px';
    underline.style.borderRadius = '999px';

    underline.style.background =
        'linear-gradient(90deg, #00d2ff, #3b82f6)';

    underline.style.transition =
        'all .3s cubic-bezier(.4,0,.2,1)';

    underline.style.boxShadow =
        '0 0 12px rgba(0,210,255,.55)';

    link.appendChild(underline);

    // hover masuk
    link.addEventListener('mouseenter', function () {

        if (this.classList.contains('nav-active')) return;

        this.style.color = '#00d2ff';
        this.style.transform = 'translateY(-2px)';

        underline.style.width = '100%';
        underline.style.left = '0';

    });

    // hover keluar
    link.addEventListener('mouseleave', function () {

        if (this.classList.contains('nav-active')) return;

        this.style.color = 'var(--text-muted)';
        this.style.transform = 'translateY(0)';

        underline.style.width = '0';
        underline.style.left = '50%';

    });

    // click active
    link.addEventListener('click', function () {

        // reset semua
        document.querySelectorAll('#navbar a').forEach(l => {
            l.classList.remove('nav-active');

            const ul = l.querySelector('span');

            if (ul) {
                ul.style.width = '0';
                ul.style.left = '50%';
            }

            l.style.color = 'var(--text-muted)';
            l.style.transform = 'translateY(0)';
        });

        // active sekarang
        this.classList.add('nav-active');

        this.style.color = '#00d2ff';
        this.style.transform = 'translateY(-2px)';

        underline.style.width = '100%';
        underline.style.left = '0';

    });

});

debugLog('Page loaded and ready');

</script>
</body>
</html>