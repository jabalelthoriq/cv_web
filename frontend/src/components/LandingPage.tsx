"use client";

import { useState, useEffect } from "react";
import Link from "next/link";
import { Inter, Space_Grotesk } from "next/font/google";

const inter = Inter({ subsets: ["latin"] });
const spaceGrotesk = Space_Grotesk({ subsets: ["latin"] });

/* --- STYLES --- */
function GlobalStyles() {
  return (
    <style>{`
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
      
      html {
        scroll-behavior: smooth;
      }

      body { 
        background-color: var(--bg-dark); 
        color: var(--text-main); 
        overflow-x: hidden; 
        margin: 0;
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
      }
      
      .heading-lg {
        font-size: clamp(32px, 4vw, 48px);
        line-height: 1.2;
        font-weight: 700;
        letter-spacing: -0.01em;
        margin-bottom: 16px;
      }

      .heading-md {
        font-size: 24px;
        font-weight: 600;
        margin-bottom: 12px;
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
      }

      /* Step Number */
      .step-number {
        font-size: 48px;
        font-weight: 800;
        background: linear-gradient(180deg, var(--neon-blue), transparent);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        opacity: 0.8;
      }
    `}</style>
  );
}

/* --- COMPONENTS --- */

function Navbar() {
  const [scrolled, setScrolled] = useState(false);

  useEffect(() => {
    const handleScroll = () => setScrolled(window.scrollY > 20);
    window.addEventListener("scroll", handleScroll);
    return () => window.removeEventListener("scroll", handleScroll);
  }, []);

  const navLinks = [
    { name: "Beranda", href: "#beranda" },
    { name: "Fitur", href: "#fitur" },
    { name: "Cara Kerja", href: "#cara-kerja" },
    { name: "Harga", href: "#harga" },
    { name: "Tentang", href: "#tentang" },
  ];

  return (
    <nav
      className={scrolled ? "nav-blur" : ""}
      style={{
        position: "fixed",
        top: 0, left: 0, right: 0,
        zIndex: 50,
        transition: "all 0.3s ease",
        padding: scrolled ? "16px 0" : "24px 0",
        borderBottom: scrolled ? "" : "1px solid transparent"
      }}
    >
      <div style={{ maxWidth: 1200, margin: "0 auto", padding: "0 24px", display: "flex", justifyContent: "space-between", alignItems: "center" }}>
        {/* Logo */}
        <Link href="#beranda" style={{ textDecoration: "none", display: "flex", alignItems: "center", gap: 12 }}>
          <div style={{ width: 32, height: 32, borderRadius: 8, background: "linear-gradient(135deg, var(--neon-blue), var(--deep-blue))", display: "flex", alignItems: "center", justifyContent: "center" }}>
            <svg viewBox="0 0 24 24" style={{ width: 20, height: 20, stroke: "#fff", strokeWidth: 2, fill: "none" }}>
              <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5" />
            </svg>
          </div>
          <span className={spaceGrotesk.className} style={{ fontSize: 20, fontWeight: 700, color: "#fff", letterSpacing: "-0.02em" }}>
            NEXUS<span style={{ color: "var(--neon-blue)" }}>.AI</span>
          </span>
        </Link>

        {/* Desktop Links */}
        <div style={{ display: "none", gap: 32 }} className="desktop-nav">
          <style>{`@media(min-width: 768px) { .desktop-nav { display: flex !important; } }`}</style>
          {navLinks.map((link) => (
            <a
              key={link.name}
              href={link.href}
              style={{
                color: "var(--text-muted)",
                textDecoration: "none",
                fontSize: 15,
                fontWeight: 500,
                transition: "color 0.2s",
              }}
              onMouseEnter={(e) => (e.currentTarget.style.color = "#fff")}
              onMouseLeave={(e) => (e.currentTarget.style.color = "var(--text-muted)")}
            >
              {link.name}
            </a>
          ))}
        </div>

        {/* Auth Buttons */}
        <div style={{ display: "flex", gap: 16, alignItems: "center" }}>
          <Link href="/login" style={{ color: "#fff", textDecoration: "none", fontSize: 15, fontWeight: 500, display: "none" }} className="desktop-nav">
            Masuk
          </Link>
          <Link href="/login" className="btn-primary" style={{ padding: "8px 20px", fontSize: 14 }}>
            Mulai Gratis
          </Link>
        </div>
      </div>
    </nav>
  );
}

function HeroSection() {
  return (
    <section id="beranda" style={{ minHeight: "100vh", display: "flex", alignItems: "center", position: "relative", overflow: "hidden" }}>
      <div className="grid-bg" />
      <div className="section-container" style={{ textAlign: "center", display: "flex", flexDirection: "column", alignItems: "center", zIndex: 10 }}>
        
        <div className={`badge ${spaceGrotesk.className}`}>✨ PLATFORM AI KARIR TERBAIK 2026</div>
        
        <h1 className={`heading-xl ${spaceGrotesk.className}`}>
          Tingkatkan Peluang Karirmu<br /> dengan <span style={{ color: "var(--neon-blue)" }} className="text-glow">Kecerdasan Buatan</span>
        </h1>
        
        <p className="text-body" style={{ margin: "0 auto 40px auto", maxWidth: 650, fontSize: 20 }}>
          Analisis mendalam CV Anda, dapatkan skor ATS real-time, rekomendasi pekerjaan hiper-akurat, dan berlatih wawancara langsung dengan agen AI kami.
        </p>

        <div style={{ display: "flex", gap: 20, flexWrap: "wrap", justifyContent: "center" }}>
          <Link href="/login" className="btn-primary" style={{ fontSize: 16, padding: "16px 36px" }}>
            Upload CV Sekarang
          </Link>
          <a href="#cara-kerja" className="btn-secondary" style={{ fontSize: 16, padding: "16px 36px" }}>
            Lihat Cara Kerja
          </a>
        </div>

        {/* Dashboard Preview Graphic */}
        <div style={{ marginTop: 80, width: "100%", maxWidth: 900, position: "relative" }}>
          <div style={{ position: "absolute", inset: -10, background: "linear-gradient(180deg, var(--neon-blue), var(--deep-blue))", opacity: 0.2, filter: "blur(40px)", borderRadius: 20, zIndex: -1 }} />
          <div className="glass-panel" style={{ padding: 4, borderRadius: 24, background: "rgba(10, 14, 23, 0.4)", border: "1px solid rgba(0, 210, 255, 0.3)" }}>
             <img 
               src="https://images.unsplash.com/photo-1551288049-bebda4e38f71?q=80&w=2070&auto=format&fit=crop" 
               alt="Dashboard Preview" 
               style={{ width: "100%", height: "auto", borderRadius: 20, display: "block", opacity: 0.8, filter: "sepia(0.2) hue-rotate(180deg) saturate(1.5)" }}
             />
          </div>
        </div>

      </div>
    </section>
  );
}

function FeaturesSection() {
  const features = [
    {
      icon: <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>,
      title: "Analisis Skor ATS",
      desc: "Ketahui seberapa ramah CV Anda terhadap mesin pelacak pelamar (ATS). Kami akan menyoroti kata kunci yang hilang."
    },
    {
      icon: <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>,
      title: "Rekomendasi Pintar",
      desc: "Kami mencocokkan keterampilan dan pengalaman Anda dengan ribuan lowongan terkini yang paling relevan."
    },
    {
      icon: <path d="M12 2a3 3 0 0 0-3 3v7a3 3 0 0 0 6 0V5a3 3 0 0 0-3-3Z"></path>,
      title: "Simulasi Wawancara",
      desc: "Latih kemampuan bicara Anda dengan pewawancara AI real-time, lengkap dengan feedback intonasi dan jawaban."
    },
    {
      icon: <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>,
      title: "Revisi CV Otomatis",
      desc: "AI kami menulis ulang deskripsi pengalaman Anda menjadi lebih kuat, profesional, dan berorientasi hasil."
    },
    {
      icon: <path d="M2 12h4l2-9 5 18 2-9h5"></path>,
      title: "Pelacakan Lamaran",
      desc: "Pantau semua status lamaran kerja Anda dalam satu dashboard intuitif. Tidak ada lagi lamaran yang terlupakan."
    },
    {
      icon: <circle cx="12" cy="12" r="10"></circle>,
      title: "Umpan Balik Personal",
      desc: "Evaluasi komprehensif apa yang menjadi kekuatan Anda dan mana area yang masih bisa ditingkatkan."
    }
  ];

  return (
    <section id="fitur" style={{ position: "relative" }}>
      <div className="section-container">
        <div style={{ textAlign: "center", marginBottom: 64 }}>
          <div className={`badge ${spaceGrotesk.className}`}>MODUL UNGGULAN</div>
          <h2 className={`heading-lg ${spaceGrotesk.className}`}>Fitur Lengkap Untuk<br/> <span style={{ color: "var(--neon-blue)" }}>Melesatkan Karirmu</span></h2>
          <p className="text-body" style={{ margin: "0 auto" }}>Semua alat yang Anda butuhkan untuk mendapatkan pekerjaan impian, ditenagai oleh AI Generatif tercanggih.</p>
        </div>

        <div style={{ display: "grid", gridTemplateColumns: "repeat(auto-fit, minmax(300px, 1fr))", gap: 24 }}>
          {features.map((f, i) => (
            <div key={i} className="glass-panel" style={{ padding: 32 }}>
              <div style={{ width: 48, height: 48, borderRadius: 12, background: "rgba(0, 210, 255, 0.1)", display: "flex", alignItems: "center", justifyContent: "center", marginBottom: 24, border: "1px solid rgba(0, 210, 255, 0.2)" }}>
                <svg className="icon" viewBox="0 0 24 24">{f.icon}</svg>
              </div>
              <h3 className={`heading-md ${spaceGrotesk.className}`}>{f.title}</h3>
              <p style={{ color: "var(--text-muted)", lineHeight: 1.6, margin: 0 }}>{f.desc}</p>
            </div>
          ))}
        </div>
      </div>
    </section>
  );
}

function HowItWorksSection() {
  const steps = [
    { title: "Unggah CV & Profil", desc: "Upload CV PDF/Word Anda, atau isi profil secara manual. Sistem akan mengekstrak informasi dalam hitungan detik." },
    { title: "Analisis & Optimasi AI", desc: "AI menganalisis metrik kelengkapan, keterbacaan ATS, dan kualitas kalimat. Anda akan mendapat saran perbaikan praktis." },
    { title: "Dapatkan Pekerjaan", desc: "Terapkan revisi, daftar ke rekomendasi pekerjaan yang cocok, dan latih posisi tersebut di modul wawancara AI." }
  ];

  return (
    <section id="cara-kerja">
      <div className="section-container">
        <div style={{ display: "grid", gridTemplateColumns: "1fr 1fr", gap: 64, alignItems: "center" }} className="mobile-col">
          <style>{`@media(max-width: 900px) { .mobile-col { grid-template-columns: 1fr !important; } }`}</style>
          
          <div>
            <div className={`badge ${spaceGrotesk.className}`}>ALUR KERJA</div>
            <h2 className={`heading-lg ${spaceGrotesk.className}`}>Tiga Langkah Sederhana Menuju <span style={{ color: "var(--neon-blue)" }}>Kesuksesan</span></h2>
            <p className="text-body">Tidak perlu pengaturan rumit. Kami mendesain prosesnya semudah mungkin agar Anda bisa fokus pada karir Anda.</p>
            
            <div style={{ marginTop: 40, display: "flex", flexDirection: "column", gap: 32 }}>
              {steps.map((s, i) => (
                <div key={i} style={{ display: "flex", gap: 24 }}>
                  <div className={`step-number ${spaceGrotesk.className}`}>0{i + 1}</div>
                  <div>
                    <h3 className={`heading-md ${spaceGrotesk.className}`} style={{ marginBottom: 8 }}>{s.title}</h3>
                    <p style={{ color: "var(--text-muted)", lineHeight: 1.6, margin: 0 }}>{s.desc}</p>
                  </div>
                </div>
              ))}
            </div>
          </div>

          <div style={{ position: "relative" }}>
            <div className="glass-panel" style={{ padding: 40, display: "flex", flexDirection: "column", gap: 24, position: "relative", zIndex: 2 }}>
              {/* Mock UI Elements */}
              <div style={{ padding: 20, background: "rgba(0,0,0,0.3)", borderRadius: 12, border: "1px dashed var(--border-glow)", textAlign: "center" }}>
                <svg viewBox="0 0 24 24" style={{ width: 32, height: 32, stroke: "var(--neon-blue)", fill: "none", strokeWidth: 2, marginBottom: 12 }}>
                  <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4M17 8l-5-5-5 5M12 3v12" />
                </svg>
                <div style={{ color: "#fff", fontWeight: 500, marginBottom: 4 }}>Tarik & Lepas CV Anda (PDF)</div>
                <div style={{ color: "var(--text-muted)", fontSize: 13 }}>Maksimal ukuran file 5MB</div>
              </div>
              
              <div style={{ display: "flex", justifyContent: "space-between", alignItems: "center", padding: "16px 20px", background: "rgba(0, 210, 255, 0.05)", borderRadius: 12, border: "1px solid var(--border-glass)" }}>
                <div>
                  <div style={{ color: "#fff", fontWeight: 500, marginBottom: 4 }}>ATS Score</div>
                  <div style={{ color: "var(--text-muted)", fontSize: 13 }}>Tingkat keterbacaan mesin</div>
                </div>
                <div className={spaceGrotesk.className} style={{ fontSize: 24, fontWeight: 700, color: "var(--neon-blue)" }}>92%</div>
              </div>

              <div style={{ display: "flex", justifyContent: "space-between", alignItems: "center", padding: "16px 20px", background: "rgba(0, 210, 255, 0.05)", borderRadius: 12, border: "1px solid var(--border-glass)" }}>
                <div>
                  <div style={{ color: "#fff", fontWeight: 500, marginBottom: 4 }}>Rekomendasi</div>
                  <div style={{ color: "var(--text-muted)", fontSize: 13 }}>Frontend Developer, Tech Indo</div>
                </div>
                <div className={spaceGrotesk.className} style={{ fontSize: 14, fontWeight: 600, color: "var(--neon-blue)", padding: "6px 12px", background: "rgba(0, 210, 255, 0.1)", borderRadius: 8 }}>Cocok</div>
              </div>
            </div>

            {/* Decorative background blur */}
            <div style={{ position: "absolute", inset: 0, background: "var(--neon-blue)", opacity: 0.15, filter: "blur(60px)", borderRadius: "50%", zIndex: 1, transform: "scale(1.2)" }} />
          </div>

        </div>
      </div>
    </section>
  );
}

function PricingSection() {
  return (
    <section id="harga" style={{ position: "relative" }}>
      <div style={{ position: "absolute", top: 0, left: 0, right: 0, height: "1px", background: "linear-gradient(90deg, transparent, var(--border-glass), transparent)" }} />
      <div className="section-container">
        <div style={{ textAlign: "center", marginBottom: 64 }}>
          <div className={`badge ${spaceGrotesk.className}`}>PAKET HARGA</div>
          <h2 className={`heading-lg ${spaceGrotesk.className}`}>Investasi Untuk <span style={{ color: "var(--neon-blue)" }}>Masa Depanmu</span></h2>
          <p className="text-body" style={{ margin: "0 auto" }}>Pilih paket yang sesuai dengan kebutuhan pencarian kerja Anda.</p>
        </div>

        <div style={{ display: "flex", gap: 32, justifyContent: "center", flexWrap: "wrap" }}>
          
          {/* Free Plan */}
          <div className="glass-panel" style={{ flex: "1 1 300px", maxWidth: 400, padding: 40, display: "flex", flexDirection: "column" }}>
            <h3 className={`heading-md ${spaceGrotesk.className}`} style={{ marginBottom: 8 }}>Dasar</h3>
            <p style={{ color: "var(--text-muted)", marginBottom: 24 }}>Untuk menjelajahi kemampuan AI kami.</p>
            <div style={{ marginBottom: 32 }}>
              <span className={spaceGrotesk.className} style={{ fontSize: 48, fontWeight: 700, color: "#fff" }}>Rp 0</span>
              <span style={{ color: "var(--text-muted)" }}>/selamanya</span>
            </div>
            <ul style={{ listStyle: "none", padding: 0, margin: "0 0 40px 0", display: "flex", flexDirection: "column", gap: 16 }}>
              {["1x Analisis CV per bulan", "Skor ATS Dasar", "Rekomendasi Pekerjaan Terbatas", "Tanpa Simulasi Interview AI"].map(text => (
                <li key={text} style={{ display: "flex", alignItems: "center", gap: 12 }}>
                  <svg viewBox="0 0 24 24" style={{ width: 18, height: 18, stroke: "var(--neon-blue)", fill: "none", strokeWidth: 2 }}><path d="M20 6L9 17l-5-5" /></svg>
                  <span style={{ color: "var(--text-main)" }}>{text}</span>
                </li>
              ))}
            </ul>
            <Link href="/login" className="btn-secondary" style={{ width: "100%", marginTop: "auto" }}>Mulai Gratis</Link>
          </div>

          {/* Pro Plan */}
          <div className="glass-panel" style={{ flex: "1 1 300px", maxWidth: 400, padding: 40, display: "flex", flexDirection: "column", border: "1px solid var(--border-glow)", position: "relative", transform: "scale(1.05)", zIndex: 2, background: "rgba(10, 20, 40, 0.8)" }}>
            <div style={{ position: "absolute", top: -14, left: "50%", transform: "translateX(-50%)", background: "var(--neon-blue)", color: "#000", fontWeight: 700, fontSize: 13, padding: "4px 16px", borderRadius: 100, letterSpacing: "0.05em" }}>PALING POPULER</div>
            <h3 className={`heading-md ${spaceGrotesk.className}`} style={{ marginBottom: 8 }}>Pro</h3>
            <p style={{ color: "var(--text-muted)", marginBottom: 24 }}>Akses penuh untuk jobseeker serius.</p>
            <div style={{ marginBottom: 32 }}>
              <span className={spaceGrotesk.className} style={{ fontSize: 48, fontWeight: 700, color: "#fff" }}>Rp 49rb</span>
              <span style={{ color: "var(--text-muted)" }}>/bulan</span>
            </div>
            <ul style={{ listStyle: "none", padding: 0, margin: "0 0 40px 0", display: "flex", flexDirection: "column", gap: 16 }}>
              {["Analisis CV Tak Terbatas", "Skor ATS Mendetail + Keyword", "Revisi Otomatis AI", "Rekomendasi Tak Terbatas", "5x Simulasi Interview AI / bulan"].map(text => (
                <li key={text} style={{ display: "flex", alignItems: "center", gap: 12 }}>
                  <svg viewBox="0 0 24 24" style={{ width: 18, height: 18, stroke: "var(--neon-blue)", fill: "none", strokeWidth: 2 }}><path d="M20 6L9 17l-5-5" /></svg>
                  <span style={{ color: "var(--text-main)" }}>{text}</span>
                </li>
              ))}
            </ul>
            <Link href="/login" className="btn-primary" style={{ width: "100%", marginTop: "auto" }}>Langganan Pro</Link>
          </div>

        </div>
      </div>
    </section>
  );
}

function CTASection() {
  return (
    <section style={{ padding: "100px 24px" }}>
      <div className="glass-panel" style={{ maxWidth: 1000, margin: "0 auto", padding: "80px 40px", textAlign: "center", position: "relative", overflow: "hidden", border: "1px solid var(--border-glow)", background: "linear-gradient(180deg, rgba(0,210,255,0.05), transparent)" }}>
         <div style={{ position: "absolute", inset: 0, background: "radial-gradient(circle at center, rgba(0, 210, 255, 0.1), transparent 60%)", pointerEvents: "none" }} />
         
         <h2 className={`heading-lg ${spaceGrotesk.className}`} style={{ marginBottom: 24 }}>Siap Membuka <span style={{ color: "var(--neon-blue)" }}>Potensi Penuhmu?</span></h2>
         <p className="text-body" style={{ margin: "0 auto 40px auto" }}>Bergabunglah dengan ribuan pencari kerja lainnya yang telah menemukan karir impian mereka bersama Nexus AI.</p>
         
         <Link href="/login" className="btn-primary" style={{ fontSize: 18, padding: "20px 48px", boxShadow: "0 0 40px rgba(0, 210, 255, 0.4)" }}>
            Buat Akun Gratis Sekarang
         </Link>
      </div>
    </section>
  );
}

function Footer() {
  return (
    <footer id="tentang" style={{ borderTop: "1px solid var(--border-glass)", background: "rgba(0,0,0,0.5)", padding: "64px 24px 32px 24px" }}>
      <div style={{ maxWidth: 1200, margin: "0 auto", display: "flex", flexWrap: "wrap", justifyContent: "space-between", gap: 48, marginBottom: 64 }}>
        
        <div style={{ maxWidth: 300 }}>
          <div style={{ display: "flex", alignItems: "center", gap: 12, marginBottom: 20 }}>
            <div style={{ width: 28, height: 28, borderRadius: 6, background: "linear-gradient(135deg, var(--neon-blue), var(--deep-blue))", display: "flex", alignItems: "center", justifyContent: "center" }}>
              <svg viewBox="0 0 24 24" style={{ width: 16, height: 16, stroke: "#fff", strokeWidth: 2, fill: "none" }}><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5" /></svg>
            </div>
            <span className={spaceGrotesk.className} style={{ fontSize: 18, fontWeight: 700, color: "#fff", letterSpacing: "-0.02em" }}>NEXUS<span style={{ color: "var(--neon-blue)" }}>.AI</span></span>
          </div>
          <p style={{ color: "var(--text-muted)", lineHeight: 1.6, fontSize: 15 }}>Memanfaatkan AI generatif untuk merevolusi proses rekrutmen dan pencarian kerja di Indonesia.</p>
        </div>

        <div style={{ display: "flex", gap: 64, flexWrap: "wrap" }}>
          <div>
            <h4 style={{ color: "#fff", fontWeight: 600, marginBottom: 20, fontSize: 16 }}>Platform</h4>
            <div style={{ display: "flex", flexDirection: "column", gap: 12 }}>
              {["Analisis CV", "Simulasi Interview", "ATS Checker", "Harga"].map(t => (
                <a key={t} href="#" style={{ color: "var(--text-muted)", textDecoration: "none", fontSize: 14, transition: "color 0.2s" }} onMouseEnter={e=>(e.currentTarget.style.color="#fff")} onMouseLeave={e=>(e.currentTarget.style.color="var(--text-muted)")}>{t}</a>
              ))}
            </div>
          </div>
          <div>
            <h4 style={{ color: "#fff", fontWeight: 600, marginBottom: 20, fontSize: 16 }}>Perusahaan</h4>
            <div style={{ display: "flex", flexDirection: "column", gap: 12 }}>
              {["Tentang Kami", "Hubungi Kami", "Kebijakan Privasi", "Syarat & Ketentuan"].map(t => (
                <a key={t} href="#" style={{ color: "var(--text-muted)", textDecoration: "none", fontSize: 14, transition: "color 0.2s" }} onMouseEnter={e=>(e.currentTarget.style.color="#fff")} onMouseLeave={e=>(e.currentTarget.style.color="var(--text-muted)")}>{t}</a>
              ))}
            </div>
          </div>
        </div>

      </div>
      
      <div style={{ maxWidth: 1200, margin: "0 auto", borderTop: "1px solid var(--border-glass)", paddingTop: 32, display: "flex", justifyContent: "space-between", alignItems: "center", flexWrap: "wrap", gap: 16 }}>
        <p style={{ color: "var(--text-muted)", fontSize: 14, margin: 0 }}>&copy; 2026 Nexus AI. All rights reserved.</p>
        <div style={{ display: "flex", gap: 16 }}>
          {["Twitter", "LinkedIn", "Instagram"].map(t => (
            <a key={t} href="#" style={{ color: "var(--text-muted)", fontSize: 14, textDecoration: "none" }} onMouseEnter={e=>(e.currentTarget.style.color="#fff")} onMouseLeave={e=>(e.currentTarget.style.color="var(--text-muted)")}>{t}</a>
          ))}
        </div>
      </div>
    </footer>
  );
}

/* --- MAIN PAGE --- */

export default function LandingPage() {
  return (
    <div className={inter.className} style={{ position: "relative", minHeight: "100vh" }}>
      <GlobalStyles />
      <div className="ambient-bg" />
      <Navbar />
      <HeroSection />
      <FeaturesSection />
      <HowItWorksSection />
      <PricingSection />
      <CTASection />
      <Footer />
    </div>
  );
}