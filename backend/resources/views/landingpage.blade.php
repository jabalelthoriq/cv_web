<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NEXUS.AI - Platform AI untuk Karir</title>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Space+Grotesk:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        /* --- STYLES --- */
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
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
        }

        body { 
            background-color: var(--bg-dark); 
            color: var(--text-main); 
            overflow-x: hidden; 
            margin: 0;
            font-family: 'Inter', sans-serif;
        }

        /* Ambient Glow Background */
        .ambient-bg {
            position: fixed;
            top: 0; left: 0; width: 100vw; height: 100vh;
            background: radial-gradient(circle at 15% 50%, rgba(0, 85, 255, 0.08), transparent 40%),
                        radial-gradient(circle at 85% 30%, rgba(0, 210, 255, 0.05), transparent 40%);
            z-index: -1;
            pointer-events: none;
        }

        /* Grid Background */
        .grid-bg {
            position: absolute;
            inset: 0;
            background-image: linear-gradient(rgba(255, 255, 255, 0.03) 1px, transparent 1px),
                              linear-gradient(90deg, rgba(255, 255, 255, 0.03) 1px, transparent 1px);
            background-size: 50px 50px;
            mask-image: radial-gradient(ellipse at center, black 20%, transparent 80%);
            -webkit-mask-image: radial-gradient(ellipse at center, black 20%, transparent 80%);
            opacity: 0.5;
            z-index: -1;
        }

        /* Glass Panel */
        .glass-panel {
            background: var(--bg-panel);
            border: 1px solid var(--border-glass);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-radius: 16px;
            transition: all 0.3s ease;
        }
        
        .glass-panel:hover {
            border-color: var(--border-glow);
            box-shadow: 0 0 30px rgba(0, 210, 255, 0.1);
            transform: translateY(-4px);
        }

        /* Glowing Button */
        .btn-primary {
            background: linear-gradient(90deg, var(--deep-blue), var(--neon-blue));
            color: #fff;
            font-weight: 600;
            padding: 12px 28px;
            border-radius: 8px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 0 20px rgba(0, 210, 255, 0.3);
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            font-family: inherit;
        }
        .btn-primary:hover {
            box-shadow: 0 0 40px rgba(0, 210, 255, 0.5);
            transform: scale(1.02);
        }

        .btn-secondary {
            background: transparent;
            color: var(--text-main);
            font-weight: 500;
            padding: 12px 28px;
            border-radius: 8px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 1px solid var(--border-glass);
            transition: all 0.3s ease;
            cursor: pointer;
            font-family: inherit;
        }
        .btn-secondary:hover {
            border-color: var(--border-glow);
            color: var(--neon-blue);
        }

        /* Neon Text Glow */
        .text-glow {
            text-shadow: 0 0 20px rgba(0, 210, 255, 0.5);
        }

        /* Navbar Blur */
        .nav-blur {
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            background: rgba(2, 4, 10, 0.8);
            border-bottom: 1px solid var(--border-glass);
        }

        /* Section Layout */
        .section-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 100px 24px;
            position: relative;
        }

        /* Typography Utilities */
        .heading-xl {
            font-size: clamp(40px, 6vw, 72px);
            line-height: 1.1;
            font-weight: 700;
            letter-spacing: -0.02em;
            margin-bottom: 24px;
            font-family: 'Space Grotesk', sans-serif;
        }
        
        .heading-lg {
            font-size: clamp(32px, 4vw, 48px);
            line-height: 1.2;
            font-weight: 700;
            letter-spacing: -0.01em;
            margin-bottom: 16px;
            font-family: 'Space Grotesk', sans-serif;
        }

        .heading-md {
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 12px;
            font-family: 'Space Grotesk', sans-serif;
        }

        .text-body {
            font-size: 18px;
            color: var(--text-muted);
            line-height: 1.6;
            margin-bottom: 40px;
            max-width: 600px;
        }

        /* SVG Icons */
        svg.icon {
            width: 24px;
            height: 24px;
            stroke: var(--neon-blue);
            stroke-width: 2;
            fill: none;
            stroke-linecap: round;
            stroke-linejoin: round;
        }

        /* Badge */
        .badge {
            display: inline-block;
            padding: 6px 16px;
            background: rgba(0, 210, 255, 0.1);
            border: 1px solid var(--border-glass);
            border-radius: 100px;
            color: var(--neon-blue);
            font-size: 14px;
            font-weight: 500;
            margin-bottom: 24px;
            letter-spacing: 0.05em;
            font-family: 'Space Grotesk', sans-serif;
        }

        /* Step Number */
        .step-number {
            font-size: 48px;
            font-weight: 800;
            background: linear-gradient(180deg, var(--neon-blue), transparent);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            opacity: 0.8;
            font-family: 'Space Grotesk', sans-serif;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .desktop-nav { display: none !important; }
            .mobile-col { grid-template-columns: 1fr !important; }
            .section-container { padding: 60px 20px; }
        }
        
        @media (min-width: 769px) {
            .desktop-nav { display: flex; }
        }
    </style>
</head>
<body>

<div class="ambient-bg"></div>

<!-- Navbar -->
<nav id="navbar" style="position: fixed; top: 0; left: 0; right: 0; z-index: 50; transition: all 0.3s ease; padding: 24px 0;">
    <div style="max-width: 1200px; margin: 0 auto; padding: 0 24px; display: flex; justify-content: space-between; align-items: center;">
        <!-- Logo -->
        <a href="#beranda" style="text-decoration: none; display: flex; align-items: center; gap: 12px;">
            <div style="width: 32px; height: 32px; border-radius: 8px; background: linear-gradient(135deg, var(--neon-blue), var(--deep-blue)); display: flex; align-items: center; justify-content: center;">
                <svg viewBox="0 0 24 24" style="width: 20px; height: 20px; stroke: #fff; stroke-width: 2; fill: none;">
                    <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5" />
                </svg>
            </div>
            <span style="font-size: 20px; font-weight: 700; color: #fff; letter-spacing: -0.02em; font-family: 'Space Grotesk', sans-serif;">
                NEXUS<span style="color: var(--neon-blue);">.AI</span>
            </span>
        </a>

        <!-- Desktop Links -->
        <div class="desktop-nav" style="gap: 32px;">
            <a href="#beranda" style="color: var(--text-muted); text-decoration: none; font-size: 15px; font-weight: 500; transition: color 0.2s;">Beranda</a>
            <a href="#fitur" style="color: var(--text-muted); text-decoration: none; font-size: 15px; font-weight: 500; transition: color 0.2s;">Fitur</a>
            <a href="#cara-kerja" style="color: var(--text-muted); text-decoration: none; font-size: 15px; font-weight: 500; transition: color 0.2s;">Cara Kerja</a>
            <a href="#harga" style="color: var(--text-muted); text-decoration: none; font-size: 15px; font-weight: 500; transition: color 0.2s;">Harga</a>
            <a href="#tentang" style="color: var(--text-muted); text-decoration: none; font-size: 15px; font-weight: 500; transition: color 0.2s;">Tentang</a>
        </div>

        <!-- Auth Buttons -->
        <div style="display: flex; gap: 16px; align-items: center;">
            <a href="/login" class="desktop-nav" style="color: #fff; text-decoration: none; font-size: 15px; font-weight: 500;">Masuk</a>
            <a href="/login" class="btn-primary" style="padding: 8px 20px; font-size: 14px;">Mulai Gratis</a>
        </div>
    </div>
</nav>

<script>
    // Navbar scroll effect
    window.addEventListener('scroll', function() {
        const navbar = document.getElementById('navbar');
        if (window.scrollY > 20) {
            navbar.classList.add('nav-blur');
            navbar.style.padding = '16px 0';
        } else {
            navbar.classList.remove('nav-blur');
            navbar.style.padding = '24px 0';
        }
    });
</script>

<!-- Hero Section -->
<section id="beranda" style="min-height: 100vh; display: flex; align-items: center; position: relative; overflow: hidden;">
    <div class="grid-bg"></div>
    <div class="section-container" style="text-align: center; display: flex; flex-direction: column; align-items: center; z-index: 10;">
        
        <div class="badge">✨ PLATFORM AI KARIR TERBAIK 2026</div>
        
        <h1 class="heading-xl">
            Tingkatkan Peluang Karirmu<br /> dengan <span style="color: var(--neon-blue);" class="text-glow">Kecerdasan Buatan</span>
        </h1>
        
        <p class="text-body" style="margin: 0 auto 40px auto; max-width: 650px; font-size: 20px;">
            Analisis mendalam CV Anda, dapatkan skor ATS real-time, rekomendasi pekerjaan hiper-akurat, dan berlatih wawancara langsung dengan agen AI kami.
        </p>

        <div style="display: flex; gap: 20px; flex-wrap: wrap; justify-content: center;">
            <a href="/login" class="btn-primary" style="font-size: 16px; padding: 16px 36px;">Upload CV Sekarang</a>
            <a href="#cara-kerja" class="btn-secondary" style="font-size: 16px; padding: 16px 36px;">Lihat Cara Kerja</a>
        </div>

        <!-- Dashboard Preview Graphic -->
        <div style="margin-top: 80px; width: 100%; max-width: 900px; position: relative;">
            <div style="position: absolute; inset: -10px; background: linear-gradient(180deg, var(--neon-blue), var(--deep-blue)); opacity: 0.2; filter: blur(40px); border-radius: 20px; z-index: -1;"></div>
            <div class="glass-panel" style="padding: 4px; border-radius: 24px; background: rgba(10, 14, 23, 0.4); border: 1px solid rgba(0, 210, 255, 0.3);">
                <img 
                    src="https://images.unsplash.com/photo-1551288049-bebda4e38f71?q=80&w=2070&auto=format&fit=crop" 
                    alt="Dashboard Preview" 
                    style="width: 100%; height: auto; border-radius: 20px; display: block; opacity: 0.8; filter: sepia(0.2) hue-rotate(180deg) saturate(1.5);"
                />
            </div>
        </div>

    </div>
</section>

<!-- Features Section -->
<section id="fitur" style="position: relative;">
    <div class="section-container">
        <div style="text-align: center; margin-bottom: 64px;">
            <div class="badge">MODUL UNGGULAN</div>
            <h2 class="heading-lg">Fitur Lengkap Untuk<br/> <span style="color: var(--neon-blue);">Melesatkan Karirmu</span></h2>
            <p class="text-body" style="margin: 0 auto;">Semua alat yang Anda butuhkan untuk mendapatkan pekerjaan impian, ditenagai oleh AI Generatif tercanggih.</p>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 24px;">
            <!-- Feature 1 -->
            <div class="glass-panel" style="padding: 32px;">
                <div style="width: 48px; height: 48px; border-radius: 12px; background: rgba(0, 210, 255, 0.1); display: flex; align-items: center; justify-content: center; margin-bottom: 24px; border: 1px solid rgba(0, 210, 255, 0.2);">
                    <svg class="icon" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                </div>
                <h3 class="heading-md">Analisis Skor ATS</h3>
                <p style="color: var(--text-muted); line-height: 1.6; margin: 0;">Ketahui seberapa ramah CV Anda terhadap mesin pelacak pelamar (ATS). Kami akan menyoroti kata kunci yang hilang.</p>
            </div>

            <!-- Feature 2 -->
            <div class="glass-panel" style="padding: 32px;">
                <div style="width: 48px; height: 48px; border-radius: 12px; background: rgba(0, 210, 255, 0.1); display: flex; align-items: center; justify-content: center; margin-bottom: 24px; border: 1px solid rgba(0, 210, 255, 0.2);">
                    <svg class="icon" viewBox="0 0 24 24"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3 8 12 13 21 8"/></svg>
                </div>
                <h3 class="heading-md">Rekomendasi Pintar</h3>
                <p style="color: var(--text-muted); line-height: 1.6; margin: 0;">Kami mencocokkan keterampilan dan pengalaman Anda dengan ribuan lowongan terkini yang paling relevan.</p>
            </div>

            <!-- Feature 3 -->
            <div class="glass-panel" style="padding: 32px;">
                <div style="width: 48px; height: 48px; border-radius: 12px; background: rgba(0, 210, 255, 0.1); display: flex; align-items: center; justify-content: center; margin-bottom: 24px; border: 1px solid rgba(0, 210, 255, 0.2);">
                    <svg class="icon" viewBox="0 0 24 24"><path d="M12 2a3 3 0 0 0-3 3v7a3 3 0 0 0 6 0V5a3 3 0 0 0-3-3Z"/><path d="M19 10v3a7 7 0 0 1-14 0v-3"/><line x1="12" y1="19" x2="12" y2="22"/></svg>
                </div>
                <h3 class="heading-md">Simulasi Wawancara</h3>
                <p style="color: var(--text-muted); line-height: 1.6; margin: 0;">Latih kemampuan bicara Anda dengan pewawancara AI real-time, lengkap dengan feedback intonasi dan jawaban.</p>
            </div>

            <!-- Feature 4 -->
            <div class="glass-panel" style="padding: 32px;">
                <div style="width: 48px; height: 48px; border-radius: 12px; background: rgba(0, 210, 255, 0.1); display: flex; align-items: center; justify-content: center; margin-bottom: 24px; border: 1px solid rgba(0, 210, 255, 0.2);">
                    <svg class="icon" viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.12 2.12 0 0 1 3 3L12 15l-4 1 1-4Z"/></svg>
                </div>
                <h3 class="heading-md">Revisi CV Otomatis</h3>
                <p style="color: var(--text-muted); line-height: 1.6; margin: 0;">AI kami menulis ulang deskripsi pengalaman Anda menjadi lebih kuat, profesional, dan berorientasi hasil.</p>
            </div>

            <!-- Feature 5 -->
            <div class="glass-panel" style="padding: 32px;">
                <div style="width: 48px; height: 48px; border-radius: 12px; background: rgba(0, 210, 255, 0.1); display: flex; align-items: center; justify-content: center; margin-bottom: 24px; border: 1px solid rgba(0, 210, 255, 0.2);">
                    <svg class="icon" viewBox="0 0 24 24"><path d="M2 12h4l2-9 5 18 2-9h5"/></svg>
                </div>
                <h3 class="heading-md">Pelacakan Lamaran</h3>
                <p style="color: var(--text-muted); line-height: 1.6; margin: 0;">Pantau semua status lamaran kerja Anda dalam satu dashboard intuitif. Tidak ada lagi lamaran yang terlupakan.</p>
            </div>

            <!-- Feature 6 -->
            <div class="glass-panel" style="padding: 32px;">
                <div style="width: 48px; height: 48px; border-radius: 12px; background: rgba(0, 210, 255, 0.1); display: flex; align-items: center; justify-content: center; margin-bottom: 24px; border: 1px solid rgba(0, 210, 255, 0.2);">
                    <svg class="icon" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 8v4M12 16h.01"/></svg>
                </div>
                <h3 class="heading-md">Umpan Balik Personal</h3>
                <p style="color: var(--text-muted); line-height: 1.6; margin: 0;">Evaluasi komprehensif apa yang menjadi kekuatan Anda dan mana area yang masih bisa ditingkatkan.</p>
            </div>
        </div>
    </div>
</section>

<!-- How It Works Section -->
<section id="cara-kerja">
    <div class="section-container">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 64px; align-items: center;" class="mobile-col">
            
            <div>
                <div class="badge">ALUR KERJA</div>
                <h2 class="heading-lg">Tiga Langkah Sederhana Menuju <span style="color: var(--neon-blue);">Kesuksesan</span></h2>
                <p class="text-body">Tidak perlu pengaturan rumit. Kami mendesain prosesnya semudah mungkin agar Anda bisa fokus pada karir Anda.</p>
                
                <div style="margin-top: 40px; display: flex; flex-direction: column; gap: 32px;">
                    <div style="display: flex; gap: 24px;">
                        <div class="step-number">01</div>
                        <div>
                            <h3 class="heading-md" style="margin-bottom: 8px;">Unggah CV & Profil</h3>
                            <p style="color: var(--text-muted); line-height: 1.6; margin: 0;">Upload CV PDF/Word Anda, atau isi profil secara manual. Sistem akan mengekstrak informasi dalam hitungan detik.</p>
                        </div>
                    </div>
                    
                    <div style="display: flex; gap: 24px;">
                        <div class="step-number">02</div>
                        <div>
                            <h3 class="heading-md" style="margin-bottom: 8px;">Analisis & Optimasi AI</h3>
                            <p style="color: var(--text-muted); line-height: 1.6; margin: 0;">AI menganalisis metrik kelengkapan, keterbacaan ATS, dan kualitas kalimat. Anda akan mendapat saran perbaikan praktis.</p>
                        </div>
                    </div>
                    
                    <div style="display: flex; gap: 24px;">
                        <div class="step-number">03</div>
                        <div>
                            <h3 class="heading-md" style="margin-bottom: 8px;">Dapatkan Pekerjaan</h3>
                            <p style="color: var(--text-muted); line-height: 1.6; margin: 0;">Terapkan revisi, daftar ke rekomendasi pekerjaan yang cocok, dan latih posisi tersebut di modul wawancara AI.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div style="position: relative;">
                <div class="glass-panel" style="padding: 40px; display: flex; flex-direction: column; gap: 24px; position: relative; z-index: 2;">
                    <!-- Mock UI Elements -->
                    <div style="padding: 20px; background: rgba(0,0,0,0.3); border-radius: 12px; border: 1px dashed var(--border-glow); text-align: center;">
                        <svg viewBox="0 0 24 24" style="width: 32px; height: 32px; stroke: var(--neon-blue); fill: none; stroke-width: 2; margin-bottom: 12px;">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4M17 8l-5-5-5 5M12 3v12" />
                        </svg>
                        <div style="color: #fff; font-weight: 500; margin-bottom: 4px;">Tarik & Lepas CV Anda (PDF)</div>
                        <div style="color: var(--text-muted); font-size: 13px;">Maksimal ukuran file 5MB</div>
                    </div>
                    
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 16px 20px; background: rgba(0, 210, 255, 0.05); border-radius: 12px; border: 1px solid var(--border-glass);">
                        <div>
                            <div style="color: #fff; font-weight: 500; margin-bottom: 4px;">ATS Score</div>
                            <div style="color: var(--text-muted); font-size: 13px;">Tingkat keterbacaan mesin</div>
                        </div>
                        <div style="font-size: 24px; font-weight: 700; color: var(--neon-blue); font-family: 'Space Grotesk', sans-serif;">92%</div>
                    </div>

                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 16px 20px; background: rgba(0, 210, 255, 0.05); border-radius: 12px; border: 1px solid var(--border-glass);">
                        <div>
                            <div style="color: #fff; font-weight: 500; margin-bottom: 4px;">Rekomendasi</div>
                            <div style="color: var(--text-muted); font-size: 13px;">Frontend Developer, Tech Indo</div>
                        </div>
                        <div style="font-size: 14px; font-weight: 600; color: var(--neon-blue); padding: 6px 12px; background: rgba(0, 210, 255, 0.1); border-radius: 8px;">Cocok</div>
                    </div>
                </div>

                <!-- Decorative background blur -->
                <div style="position: absolute; inset: 0; background: var(--neon-blue); opacity: 0.15; filter: blur(60px); border-radius: 50%; z-index: 1; transform: scale(1.2);"></div>
            </div>

        </div>
    </div>
</section>

<!-- Pricing Section -->
<section id="harga" style="position: relative;">
    <div style="position: absolute; top: 0; left: 0; right: 0; height: 1px; background: linear-gradient(90deg, transparent, var(--border-glass), transparent);"></div>
    <div class="section-container">
        <div style="text-align: center; margin-bottom: 64px;">
            <div class="badge">PAKET HARGA</div>
            <h2 class="heading-lg">Investasi Untuk <span style="color: var(--neon-blue);">Masa Depanmu</span></h2>
            <p class="text-body" style="margin: 0 auto;">Pilih paket yang sesuai dengan kebutuhan pencarian kerja Anda.</p>
        </div>

        <div style="display: flex; gap: 32px; justify-content: center; flex-wrap: wrap;">
            
            <!-- Free Plan -->
            <div class="glass-panel" style="flex: 1 1 300px; max-width: 400px; padding: 40px; display: flex; flex-direction: column;">
                <h3 class="heading-md" style="margin-bottom: 8px;">Dasar</h3>
                <p style="color: var(--text-muted); margin-bottom: 24px;">Untuk menjelajahi kemampuan AI kami.</p>
                <div style="margin-bottom: 32px;">
                    <span style="font-size: 48px; font-weight: 700; color: #fff; font-family: 'Space Grotesk', sans-serif;">Rp 0</span>
                    <span style="color: var(--text-muted);">/selamanya</span>
                </div>
                <ul style="list-style: none; padding: 0; margin: 0 0 40px 0; display: flex; flex-direction: column; gap: 16px;">
                    <li style="display: flex; align-items: center; gap: 12px;">
                        <svg viewBox="0 0 24 24" style="width: 18px; height: 18px; stroke: var(--neon-blue); fill: none; stroke-width: 2;"><path d="M20 6L9 17l-5-5" /></svg>
                        <span style="color: var(--text-main);">1x Analisis CV per bulan</span>
                    </li>
                    <li style="display: flex; align-items: center; gap: 12px;">
                        <svg viewBox="0 0 24 24" style="width: 18px; height: 18px; stroke: var(--neon-blue); fill: none; stroke-width: 2;"><path d="M20 6L9 17l-5-5" /></svg>
                        <span style="color: var(--text-main);">Skor ATS Dasar</span>
                    </li>
                    <li style="display: flex; align-items: center; gap: 12px;">
                        <svg viewBox="0 0 24 24" style="width: 18px; height: 18px; stroke: var(--neon-blue); fill: none; stroke-width: 2;"><path d="M20 6L9 17l-5-5" /></svg>
                        <span style="color: var(--text-main);">Rekomendasi Pekerjaan Terbatas</span>
                    </li>
                    <li style="display: flex; align-items: center; gap: 12px;">
                        <svg viewBox="0 0 24 24" style="width: 18px; height: 18px; stroke: var(--neon-blue); fill: none; stroke-width: 2;"><path d="M20 6L9 17l-5-5" /></svg>
                        <span style="color: var(--text-main);">Tanpa Simulasi Interview AI</span>
                    </li>
                </ul>
                <a href="/login" class="btn-secondary" style="width: 100%; margin-top: auto;">Mulai Gratis</a>
            </div>

            <!-- Pro Plan -->
            <div class="glass-panel" style="flex: 1 1 300px; max-width: 400px; padding: 40px; display: flex; flex-direction: column; border: 1px solid var(--border-glow); position: relative; transform: scale(1.05); z-index: 2; background: rgba(10, 20, 40, 0.8);">
                <div style="position: absolute; top: -14px; left: 50%; transform: translateX(-50%); background: var(--neon-blue); color: #000; font-weight: 700; font-size: 13px; padding: 4px 16px; border-radius: 100px; letter-spacing: 0.05em;">PALING POPULER</div>
                <h3 class="heading-md" style="margin-bottom: 8px;">Pro</h3>
                <p style="color: var(--text-muted); margin-bottom: 24px;">Akses penuh untuk jobseeker serius.</p>
                <div style="margin-bottom: 32px;">
                    <span style="font-size: 48px; font-weight: 700; color: #fff; font-family: 'Space Grotesk', sans-serif;">Rp 49rb</span>
                    <span style="color: var(--text-muted);">/bulan</span>
                </div>
                <ul style="list-style: none; padding: 0; margin: 0 0 40px 0; display: flex; flex-direction: column; gap: 16px;">
                    <li style="display: flex; align-items: center; gap: 12px;">
                        <svg viewBox="0 0 24 24" style="width: 18px; height: 18px; stroke: var(--neon-blue); fill: none; stroke-width: 2;"><path d="M20 6L9 17l-5-5" /></svg>
                        <span style="color: var(--text-main);">Analisis CV Tak Terbatas</span>
                    </li>
                    <li style="display: flex; align-items: center; gap: 12px;">
                        <svg viewBox="0 0 24 24" style="width: 18px; height: 18px; stroke: var(--neon-blue); fill: none; stroke-width: 2;"><path d="M20 6L9 17l-5-5" /></svg>
                        <span style="color: var(--text-main);">Skor ATS Mendetail + Keyword</span>
                    </li>
                    <li style="display: flex; align-items: center; gap: 12px;">
                        <svg viewBox="0 0 24 24" style="width: 18px; height: 18px; stroke: var(--neon-blue); fill: none; stroke-width: 2;"><path d="M20 6L9 17l-5-5" /></svg>
                        <span style="color: var(--text-main);">Revisi Otomatis AI</span>
                    </li>
                    <li style="display: flex; align-items: center; gap: 12px;">
                        <svg viewBox="0 0 24 24" style="width: 18px; height: 18px; stroke: var(--neon-blue); fill: none; stroke-width: 2;"><path d="M20 6L9 17l-5-5" /></svg>
                        <span style="color: var(--text-main);">Rekomendasi Tak Terbatas</span>
                    </li>
                    <li style="display: flex; align-items: center; gap: 12px;">
                        <svg viewBox="0 0 24 24" style="width: 18px; height: 18px; stroke: var(--neon-blue); fill: none; stroke-width: 2;"><path d="M20 6L9 17l-5-5" /></svg>
                        <span style="color: var(--text-main);">5x Simulasi Interview AI / bulan</span>
                    </li>
                </ul>
                <a href="/login" class="btn-primary" style="width: 100%; margin-top: auto;">Langganan Pro</a>
            </div>

        </div>
    </div>
</section>

<!-- CTA Section -->
<section style="padding: 100px 24px;">
    <div class="glass-panel" style="max-width: 1000px; margin: 0 auto; padding: 80px 40px; text-align: center; position: relative; overflow: hidden; border: 1px solid var(--border-glow); background: linear-gradient(180deg, rgba(0,210,255,0.05), transparent);">
        <div style="position: absolute; inset: 0; background: radial-gradient(circle at center, rgba(0, 210, 255, 0.1), transparent 60%); pointer-events: none;"></div>
        
        <h2 class="heading-lg" style="margin-bottom: 24px;">Siap Membuka <span style="color: var(--neon-blue);">Potensi Penuhmu?</span></h2>
        <p class="text-body" style="margin: 0 auto 40px auto;">Bergabunglah dengan ribuan pencari kerja lainnya yang telah menemukan karir impian mereka bersama Nexus AI.</p>
        
        <a href="/login" class="btn-primary" style="font-size: 18px; padding: 20px 48px; box-shadow: 0 0 40px rgba(0, 210, 255, 0.4);">
            Buat Akun Gratis Sekarang
        </a>
    </div>
</section>

<!-- Footer -->
<footer id="tentang" style="border-top: 1px solid var(--border-glass); background: rgba(0,0,0,0.5); padding: 64px 24px 32px 24px;">
    <div style="max-width: 1200px; margin: 0 auto; display: flex; flex-wrap: wrap; justify-content: space-between; gap: 48px; margin-bottom: 64px;">
        
        <div style="max-width: 300px;">
            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 20px;">
                <div style="width: 28px; height: 28px; border-radius: 6px; background: linear-gradient(135deg, var(--neon-blue), var(--deep-blue)); display: flex; align-items: center; justify-content: center;">
                    <svg viewBox="0 0 24 24" style="width: 16px; height: 16px; stroke: #fff; stroke-width: 2; fill: none;"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5" /></svg>
                </div>
                <span style="font-size: 18px; font-weight: 700; color: #fff; letter-spacing: -0.02em; font-family: 'Space Grotesk', sans-serif;">NEXUS<span style="color: var(--neon-blue);">.AI</span></span>
            </div>
            <p style="color: var(--text-muted); line-height: 1.6; font-size: 15px;">Memanfaatkan AI generatif untuk merevolusi proses rekrutmen dan pencarian kerja di Indonesia.</p>
        </div>

        <div style="display: flex; gap: 64px; flex-wrap: wrap;">
            <div>
                <h4 style="color: #fff; font-weight: 600; margin-bottom: 20px; font-size: 16px;">Platform</h4>
                <div style="display: flex; flex-direction: column; gap: 12px;">
                    <a href="#" style="color: var(--text-muted); text-decoration: none; font-size: 14px; transition: color 0.2s;">Analisis CV</a>
                    <a href="#" style="color: var(--text-muted); text-decoration: none; font-size: 14px; transition: color 0.2s;">Simulasi Interview</a>
                    <a href="#" style="color: var(--text-muted); text-decoration: none; font-size: 14px; transition: color 0.2s;">ATS Checker</a>
                    <a href="#" style="color: var(--text-muted); text-decoration: none; font-size: 14px; transition: color 0.2s;">Harga</a>
                </div>
            </div>
            <div>
                <h4 style="color: #fff; font-weight: 600; margin-bottom: 20px; font-size: 16px;">Perusahaan</h4>
                <div style="display: flex; flex-direction: column; gap: 12px;">
                    <a href="#" style="color: var(--text-muted); text-decoration: none; font-size: 14px; transition: color 0.2s;">Tentang Kami</a>
                    <a href="#" style="color: var(--text-muted); text-decoration: none; font-size: 14px; transition: color 0.2s;">Hubungi Kami</a>
                    <a href="#" style="color: var(--text-muted); text-decoration: none; font-size: 14px; transition: color 0.2s;">Kebijakan Privasi</a>
                    <a href="#" style="color: var(--text-muted); text-decoration: none; font-size: 14px; transition: color 0.2s;">Syarat & Ketentuan</a>
                </div>
            </div>
        </div>

    </div>
    
    <div style="max-width: 1200px; margin: 0 auto; border-top: 1px solid var(--border-glass); padding-top: 32px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
        <p style="color: var(--text-muted); font-size: 14px; margin: 0;">&copy; 2026 Nexus AI. All rights reserved.</p>
        <div style="display: flex; gap: 16px;">
            <a href="#" style="color: var(--text-muted); font-size: 14px; text-decoration: none; transition: color 0.2s;">Twitter</a>
            <a href="#" style="color: var(--text-muted); font-size: 14px; text-decoration: none; transition: color 0.2s;">LinkedIn</a>
            <a href="#" style="color: var(--text-muted); font-size: 14px; text-decoration: none; transition: color 0.2s;">Instagram</a>
        </div>
    </div>
</footer>

<script>
    // Hover effects for footer links
    document.querySelectorAll('footer a').forEach(link => {
        link.addEventListener('mouseenter', function() {
            this.style.color = '#fff';
        });
        link.addEventListener('mouseleave', function() {
            this.style.color = 'var(--text-muted)';
        });
    });
    
    // Hover effects for nav links
    document.querySelectorAll('#navbar a').forEach(link => {
        link.addEventListener('mouseenter', function() {
            if (this.style.color !== 'rgb(255, 255, 255)') {
                this.style.color = '#fff';
            }
        });
        link.addEventListener('mouseleave', function() {
            if (this.classList.contains('btn-primary')) return;
            if (this.classList.contains('desktop-nav')) {
                this.style.color = 'var(--text-muted)';
            }
        });
    });
</script>

</body>
</html>