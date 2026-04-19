"use client";

import { useState, useEffect } from "react";
import { useRouter, useSearchParams } from "next/navigation";

// ─────────────────────────────────────────────────────────────
//  TYPES
// ─────────────────────────────────────────────────────────────
interface NavItemDef {
  id: string;
  icon: string;
  label: string;
  badge?: string;
}

// ─────────────────────────────────────────────────────────────
//  TOKENS
// ─────────────────────────────────────────────────────────────
const T = {
  cyan:    "#00d4ff",
  blue:    "#3b82f6",
  violet:  "#818cf8",
  emerald: "#10b981",
  amber:   "#f59e0b",
  rose:    "#f43f5e",
};

// ─────────────────────────────────────────────────────────────
//  STAT CARD
// ─────────────────────────────────────────────────────────────
interface StatCardProps {
  icon: string;
  label: string;
  value: string;
  sub: string;
  color: string;
  sparkline?: number[];
}

function StatCard({ icon, label, value, sub, color, sparkline }: StatCardProps) {
  const max = sparkline ? Math.max(...sparkline) : 1;
  const pts = sparkline
    ? sparkline
        .map((v, i) => `${(i / (sparkline.length - 1)) * 72},${20 - (v / max) * 16}`)
        .join(" ")
    : "";

  return (
    <div
      className="relative rounded-2xl p-5 overflow-hidden group cursor-default transition-all duration-300 hover:-translate-y-1"
      style={{
        background: "linear-gradient(145deg, #07111f 0%, #030810 100%)",
        border: "1px solid rgba(255,255,255,0.07)",
      }}
      onMouseEnter={(e) => {
        (e.currentTarget as HTMLDivElement).style.borderColor = `${color}33`;
        (e.currentTarget as HTMLDivElement).style.boxShadow = `0 8px 32px ${color}0d`;
      }}
      onMouseLeave={(e) => {
        (e.currentTarget as HTMLDivElement).style.borderColor = "rgba(255,255,255,0.07)";
        (e.currentTarget as HTMLDivElement).style.boxShadow = "none";
      }}
    >
      {/* Corner glow */}
      <div
        className="absolute -top-8 -right-8 w-28 h-28 rounded-full opacity-[0.12] blur-3xl pointer-events-none"
        style={{ background: color }}
      />

      {/* Top row */}
      <div className="flex items-start justify-between mb-4">
        <div
          className="w-10 h-10 rounded-xl flex items-center justify-center text-[18px]"
          style={{
            background: `${color}14`,
            border: `1px solid ${color}28`,
          }}
        >
          {icon}
        </div>
        {sparkline && (
          <svg width="72" height="20" viewBox="0 0 72 20" fill="none">
            <polyline
              points={pts}
              stroke={color}
              strokeWidth="1.5"
              fill="none"
              strokeLinecap="round"
              strokeLinejoin="round"
              opacity="0.6"
            />
          </svg>
        )}
      </div>

      {/* Value */}
      <div
        className="text-[32px] font-black leading-none mb-1.5 tabular-nums"
        style={{ fontFamily: "'Space Grotesk', sans-serif", letterSpacing: "-1.5px" }}
      >
        {value}
      </div>
      <div className="text-[11px] font-semibold text-slate-500 uppercase tracking-[0.12em]">
        {label}
      </div>

      {/* Sub badge */}
      <div className="mt-3 flex items-center gap-1.5">
        <div
          className="text-[10px] font-bold px-2 py-0.5 rounded-full"
          style={{
            background: `${color}15`,
            color: color,
            border: `1px solid ${color}25`,
          }}
        >
          {sub}
        </div>
      </div>

      {/* Hover accent bottom */}
      <div
        className="absolute bottom-0 left-0 h-[2px] w-0 group-hover:w-full transition-all duration-700 pointer-events-none"
        style={{ background: `linear-gradient(to right, transparent, ${color}66, transparent)` }}
      />
    </div>
  );
}

// ─────────────────────────────────────────────────────────────
//  WELCOME BANNER
// ─────────────────────────────────────────────────────────────
function WelcomeBanner({ data }: any) {
  return (
    <div
      className="relative rounded-2xl overflow-hidden mb-4"
      style={{
        background: "linear-gradient(135deg, #07182e 0%, #030c18 100%)",
        border: "1px solid rgba(0,212,255,0.12)",
      }}
    >
      {/* Background decoration */}
      <div className="absolute inset-0 pointer-events-none overflow-hidden">
        <div
          className="absolute -top-16 -right-16 w-64 h-64 rounded-full blur-3xl"
          style={{ background: "radial-gradient(circle, rgba(0,212,255,0.09) 0%, transparent 70%)" }}
        />
        <div
          className="absolute -bottom-12 left-1/3 w-48 h-48 rounded-full blur-3xl"
          style={{ background: "radial-gradient(circle, rgba(59,130,246,0.07) 0%, transparent 70%)" }}
        />
        {/* Grid lines */}
        <svg className="absolute right-0 top-0 h-full w-64 opacity-[0.04]" viewBox="0 0 256 120" preserveAspectRatio="xMaxYMid slice">
          {[0,32,64,96,128,160,192,224,256].map(x => (
            <line key={x} x1={x} y1="0" x2={x} y2="120" stroke="#00d4ff" strokeWidth="0.5"/>
          ))}
          {[0,24,48,72,96,120].map(y => (
            <line key={y} x1="0" y1={y} x2="256" y2={y} stroke="#00d4ff" strokeWidth="0.5"/>
          ))}
        </svg>
      </div>

      <div className="relative p-6">
        <div className="flex items-start justify-between gap-4 flex-wrap">
          <div>
            <div className="flex items-center gap-2 mb-2">
              <div
                className="w-1.5 h-1.5 rounded-full animate-pulse"
                style={{ background: T.emerald, boxShadow: `0 0 8px ${T.emerald}` }}
              />
              <span
                className="text-[10px] font-bold uppercase tracking-[0.2em]"
                style={{ color: "rgba(0,212,255,0.6)" }}
              >
                Selamat Datang Kembali
              </span>
            </div>

            <h1
              className="text-2xl font-black text-white leading-tight mb-1"
              style={{ fontFamily: "'Space Grotesk', sans-serif", letterSpacing: "-0.6px" }}
            >
              {data?.user?.name ?? "User"}
              <span className="ml-2 text-2xl">👋</span>
            </h1>

            <p className="text-[13px] text-slate-400 mt-1.5">
              Profil kamu{" "}
              <span
                className="font-semibold"
                style={{ color: T.cyan }}
              >
                73% lengkap
              </span>{" "}
              — 3 hal lagi untuk match yang lebih baik
            </p>
          </div>

          <button
            className="flex-shrink-0 flex items-center gap-2 px-5 py-2.5 rounded-xl text-[12px] font-bold transition-all duration-200 hover:scale-105 hover:brightness-110"
            style={{
              background: "linear-gradient(135deg, #00d4ff, #3b82f6)",
              color: "#020810",
              letterSpacing: "0.02em",
            }}
          >
            <span>Lengkapi Profil</span>
            <span>→</span>
          </button>
        </div>

        {/* Progress */}
        <div className="mt-5">
          <div className="flex items-center justify-between mb-2">
            <span className="text-[10px] text-slate-600 uppercase tracking-[0.12em] font-semibold">
              Kelengkapan Profil
            </span>
            <span className="text-[10px] font-bold" style={{ color: T.cyan }}>73%</span>
          </div>
          <div
            className="h-1.5 rounded-full overflow-hidden"
            style={{ background: "rgba(255,255,255,0.05)" }}
          >
            <div
              className="h-full rounded-full relative overflow-hidden"
              style={{
                width: "73%",
                background: "linear-gradient(to right, #00d4ff, #3b82f6)",
              }}
            >
              <div
                className="absolute inset-0 animate-pulse"
                style={{ background: "linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent)" }}
              />
            </div>
          </div>

          {/* Steps */}
          <div className="flex gap-3 mt-3">
            {[
              { label: "Foto profil", done: false, color: T.amber },
              { label: "Ringkasan diri", done: false, color: T.cyan },
              { label: "Portofolio link", done: false, color: T.violet },
            ].map((s) => (
              <div
                key={s.label}
                className="flex items-center gap-1.5 text-[10px] font-medium"
                style={{ color: s.color + "99" }}
              >
                <span
                  className="w-3.5 h-3.5 rounded-full border flex items-center justify-center text-[8px]"
                  style={{ borderColor: s.color + "44", background: s.color + "10" }}
                >
                  !
                </span>
                {s.label}
              </div>
            ))}
          </div>
        </div>
      </div>
    </div>
  );
}

// ─────────────────────────────────────────────────────────────
//  JOB MATCH CARD (mini)
// ─────────────────────────────────────────────────────────────
function JobCard({ company, role, match, tags, logo }: {
  company: string; role: string; match: number; tags: string[]; logo: string;
}) {
  return (
    <div
      className="flex items-center gap-3 px-4 py-3 rounded-xl cursor-pointer transition-all duration-150 group"
      style={{ border: "1px solid rgba(255,255,255,0.05)", background: "rgba(255,255,255,0.02)" }}
      onMouseEnter={(e) => {
        (e.currentTarget as HTMLDivElement).style.background = "rgba(0,212,255,0.03)";
        (e.currentTarget as HTMLDivElement).style.borderColor = "rgba(0,212,255,0.12)";
      }}
      onMouseLeave={(e) => {
        (e.currentTarget as HTMLDivElement).style.background = "rgba(255,255,255,0.02)";
        (e.currentTarget as HTMLDivElement).style.borderColor = "rgba(255,255,255,0.05)";
      }}
    >
      <div
        className="w-9 h-9 rounded-lg flex items-center justify-center text-lg flex-shrink-0"
        style={{ background: "rgba(255,255,255,0.06)", border: "1px solid rgba(255,255,255,0.08)" }}
      >
        {logo}
      </div>
      <div className="flex-1 min-w-0">
        <div className="flex items-center justify-between gap-2">
          <span className="text-[13px] font-semibold text-white/85 truncate group-hover:text-white transition-colors">
            {role}
          </span>
          <span
            className="text-[10px] font-bold flex-shrink-0 px-2 py-0.5 rounded-full"
            style={{
              background: match >= 90 ? `${T.emerald}18` : `${T.cyan}15`,
              color: match >= 90 ? T.emerald : T.cyan,
              border: `1px solid ${match >= 90 ? T.emerald : T.cyan}25`,
            }}
          >
            {match}% match
          </span>
        </div>
        <div className="flex items-center gap-2 mt-0.5">
          <span className="text-[11px] text-slate-500">{company}</span>
          <span className="text-slate-700">·</span>
          <div className="flex gap-1">
            {tags.map((t) => (
              <span
                key={t}
                className="text-[9px] px-1.5 py-px rounded font-medium"
                style={{ background: "rgba(255,255,255,0.05)", color: "rgba(255,255,255,0.35)" }}
              >
                {t}
              </span>
            ))}
          </div>
        </div>
      </div>
    </div>
  );
}

// ─────────────────────────────────────────────────────────────
//  ACTIVITY ITEM
// ─────────────────────────────────────────────────────────────
function ActivityItem({ icon, title, sub, time, color }: {
  icon: string; title: string; sub: string; time: string; color: string;
}) {
  return (
    <div
      className="flex items-center gap-3 px-3 py-2.5 rounded-xl cursor-pointer group transition-all duration-150"
      onMouseEnter={(e) => { (e.currentTarget as HTMLDivElement).style.background = "rgba(255,255,255,0.025)"; }}
      onMouseLeave={(e) => { (e.currentTarget as HTMLDivElement).style.background = "transparent"; }}
    >
      <div className="flex flex-col items-center gap-0.5 flex-shrink-0">
        <div
          className="w-1.5 h-1.5 rounded-full"
          style={{ background: color, boxShadow: `0 0 5px ${color}` }}
        />
      </div>
      <div
        className="w-8 h-8 rounded-lg flex items-center justify-center text-[13px] flex-shrink-0"
        style={{ background: `${color}14`, border: `1px solid ${color}22` }}
      >
        {icon}
      </div>
      <div className="flex-1 min-w-0">
        <p className="text-[12px] font-medium text-white/75 truncate group-hover:text-white/90 transition-colors">
          {title}
        </p>
        <p className="text-[10px] font-medium" style={{ color: color + "bb" }}>{sub}</p>
      </div>
      <span className="text-[10px] text-slate-600 flex-shrink-0">{time}</span>
    </div>
  );
}

// ─────────────────────────────────────────────────────────────
//  UPLOAD ZONE
// ─────────────────────────────────────────────────────────────
function UploadZone() {
  const [dragging, setDragging] = useState(false);

  return (
    <div
      className="rounded-xl p-6 text-center transition-all duration-200 cursor-pointer relative overflow-hidden"
      style={{
        border: `1.5px dashed ${dragging ? "rgba(0,212,255,0.5)" : "rgba(0,212,255,0.18)"}`,
        background: dragging ? "rgba(0,212,255,0.05)" : "rgba(0,212,255,0.02)",
      }}
      onDragOver={(e) => { e.preventDefault(); setDragging(true); }}
      onDragLeave={() => setDragging(false)}
      onDrop={() => setDragging(false)}
    >
      <div className="absolute inset-0 pointer-events-none">
        <div
          className="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-32 h-32 rounded-full blur-2xl"
          style={{ background: "radial-gradient(circle, rgba(0,212,255,0.07) 0%, transparent 70%)" }}
        />
      </div>

      <div
        className="w-14 h-14 rounded-2xl mx-auto mb-3 flex items-center justify-center text-2xl relative"
        style={{
          background: "linear-gradient(135deg, rgba(0,212,255,0.15), rgba(59,130,246,0.15))",
          border: "1px solid rgba(0,212,255,0.2)",
        }}
      >
        📄
        <div
          className="absolute -top-1 -right-1 w-4 h-4 rounded-full flex items-center justify-center text-[9px]"
          style={{ background: T.emerald, color: "#020810", fontWeight: 800 }}
        >
          +
        </div>
      </div>

      <p className="text-sm font-semibold text-white/80 mb-1">Drag & drop CV kamu di sini</p>
      <p className="text-[11px] text-slate-500">
        atau{" "}
        <span className="font-semibold" style={{ color: T.cyan }}>
          pilih file
        </span>{" "}
        dari perangkat kamu
      </p>

      <div className="flex items-center justify-center gap-2 mt-4">
        {["PDF", "DOCX", "DOC"].map((f) => (
          <span
            key={f}
            className="text-[9px] font-bold px-2 py-1 rounded-md uppercase tracking-wide"
            style={{
              background: "rgba(0,212,255,0.08)",
              border: "1px solid rgba(0,212,255,0.15)",
              color: "rgba(0,212,255,0.7)",
            }}
          >
            {f}
          </span>
        ))}
        <span className="text-[10px] text-slate-600">• Maks. 5 MB</span>
      </div>
    </div>
  );
}

// ─────────────────────────────────────────────────────────────
//  COMPLETENESS
// ─────────────────────────────────────────────────────────────
function Completeness() {
  const items = [
    { icon: "👤", label: "Informasi Pribadi", pct: 100, color: T.cyan },
    { icon: "💼", label: "Pengalaman Kerja",  pct: 85,  color: T.blue },
    { icon: "🎓", label: "Pendidikan",         pct: 90,  color: T.violet },
    { icon: "⚡", label: "Keahlian",           pct: 60,  color: T.amber },
    { icon: "📎", label: "Portofolio",         pct: 40,  color: T.rose },
  ];

  return (
    <div className="space-y-1.5">
      {items.map(({ icon, label, pct, color }) => (
        <div key={label} className="flex items-center gap-3 group">
          <div
            className="w-7 h-7 rounded-lg flex items-center justify-center text-[12px] flex-shrink-0"
            style={{ background: `${color}14`, border: `1px solid ${color}22` }}
          >
            {icon}
          </div>
          <span className="text-[12px] text-white/65 flex-1">{label}</span>
          <span className="text-[11px] font-bold w-8 text-right flex-shrink-0" style={{ color }}>
            {pct}%
          </span>
          <div
            className="w-20 h-1.5 rounded-full overflow-hidden flex-shrink-0"
            style={{ background: "rgba(255,255,255,0.06)" }}
          >
            <div
              className="h-full rounded-full transition-all duration-700"
              style={{ width: `${pct}%`, background: `linear-gradient(to right, ${color}cc, ${color})` }}
            />
          </div>
        </div>
      ))}
    </div>
  );
}

// ─────────────────────────────────────────────────────────────
//  QUICK ACTIONS
// ─────────────────────────────────────────────────────────────
function QuickActions({ onNavigate }: { onNavigate: (id: string) => void }) {
  const actions = [
    { icon: "🎙️", label: "Mulai Interview",  sub: "AI-powered",   color: T.cyan,    size: "lg", id: "interview" },
    { icon: "📝",  label: "Edit CV",          sub: "Update profil", color: T.blue,    size: "sm", id: "cv" },
    { icon: "🔍",  label: "Cari Lowongan",    sub: "5 match baru",  color: T.violet,  size: "sm", id: "jobs" },
    { icon: "📊",  label: "Analisis ATS",     sub: "Cek skor",      color: T.amber,   size: "sm", id: "cv" },
  ];

  return (
    <div className="grid grid-cols-2 gap-2.5">
      {actions.map(({ icon, label, sub, color, size, id }) => (
        <button
          key={label}
          onClick={() => onNavigate(id)}
          className="flex items-center gap-3 rounded-xl text-left transition-all duration-200 hover:-translate-y-0.5 group"
          style={{
            padding: size === "lg" ? "14px" : "12px",
            background: `${color}08`,
            border: `1px solid ${color}18`,
          }}
          onMouseEnter={(e) => {
            (e.currentTarget as HTMLButtonElement).style.background = `${color}0f`;
            (e.currentTarget as HTMLButtonElement).style.borderColor = `${color}35`;
          }}
          onMouseLeave={(e) => {
            (e.currentTarget as HTMLButtonElement).style.background = `${color}08`;
            (e.currentTarget as HTMLButtonElement).style.borderColor = `${color}18`;
          }}
        >
          <div
            className="rounded-xl flex items-center justify-center text-base flex-shrink-0"
            style={{
              width: size === "lg" ? "38px" : "32px",
              height: size === "lg" ? "38px" : "32px",
              background: `${color}15`,
              border: `1px solid ${color}28`,
            }}
          >
            {icon}
          </div>
          <div className="min-w-0">
            <div
              className="font-semibold truncate group-hover:text-white transition-colors"
              style={{ fontSize: size === "lg" ? "12px" : "11px", color: "rgba(255,255,255,0.8)" }}
            >
              {label}
            </div>
            <div className="text-[10px] truncate mt-0.5" style={{ color: `${color}88` }}>
              {sub}
            </div>
          </div>
        </button>
      ))}
    </div>
  );
}

// ─────────────────────────────────────────────────────────────
//  PANEL WRAPPER
// ─────────────────────────────────────────────────────────────
function Panel({
  title, action, children, accent, onActionClick,
}: {
  title: string; action?: string; children: React.ReactNode; accent?: string; onActionClick?: () => void;
}) {
  return (
    <div
      className="rounded-2xl overflow-hidden transition-all duration-200"
      style={{
        background: "linear-gradient(145deg, #07111f 0%, #030810 100%)",
        border: "1px solid rgba(255,255,255,0.07)",
      }}
    >
      {/* Panel header */}
      <div
        className="flex items-center justify-between px-5 py-4"
        style={{ borderBottom: "1px solid rgba(255,255,255,0.05)" }}
      >
        <div className="flex items-center gap-2">
          {accent && (
            <div
              className="w-1.5 h-1.5 rounded-full"
              style={{ background: accent, boxShadow: `0 0 6px ${accent}` }}
            />
          )}
          <span
            className="text-[10px] font-bold uppercase tracking-[0.18em]"
            style={{ color: accent || T.cyan }}
          >
            {title}
          </span>
        </div>
        {action && (
          <button
            onClick={onActionClick}
            className="text-[10px] font-semibold uppercase tracking-widest transition-colors"
            style={{ color: "rgba(255,255,255,0.25)", background: "none", border: "none" }}
            onMouseEnter={(e) => { (e.currentTarget as HTMLButtonElement).style.color = T.cyan; }}
            onMouseLeave={(e) => { (e.currentTarget as HTMLButtonElement).style.color = "rgba(255,255,255,0.25)"; }}
          >
            {action} →
          </button>
        )}
      </div>
      <div className="px-5 py-4">{children}</div>
    </div>
  );
}

// ─────────────────────────────────────────────────────────────
//  INTERVIEW SCORE RING
// ─────────────────────────────────────────────────────────────
function ScoreRing({ score, label, color }: { score: number; label: string; color: string }) {
  const r = 28;
  const circ = 2 * Math.PI * r;
  const dash = (score / 100) * circ;

  return (
    <div className="flex flex-col items-center gap-1">
      <div className="relative w-16 h-16">
        <svg width="64" height="64" viewBox="0 0 64 64">
          <circle cx="32" cy="32" r={r} fill="none" stroke="rgba(255,255,255,0.06)" strokeWidth="4"/>
          <circle
            cx="32" cy="32" r={r}
            fill="none"
            stroke={color}
            strokeWidth="4"
            strokeDasharray={`${dash} ${circ}`}
            strokeLinecap="round"
            transform="rotate(-90 32 32)"
            opacity="0.85"
          />
        </svg>
        <div className="absolute inset-0 flex items-center justify-center">
          <span
            className="text-[13px] font-black"
            style={{ color, fontFamily: "'Space Grotesk', sans-serif" }}
          >
            {score}
          </span>
        </div>
      </div>
      <span className="text-[10px] text-slate-500 text-center">{label}</span>
    </div>
  );
}

// ─────────────────────────────────────────────────────────────
//  DASHBOARD HOME
// ─────────────────────────────────────────────────────────────
function DashboardHome({ onNavigate, data }: any) {
  const stats: StatCardProps[] = [
  {
    icon: "📊",
    label: "Skor CV",
    value: String(data.cv_analysis.score),
    sub: `Δ ${data.cv_analysis.delta}`,
    color: T.cyan,
  },
  {
    icon: "💼",
    label: "Job Match",
    value: String(data.jobs.length),
    sub: "Rekomendasi",
    color: T.blue,
  },
  {
    icon: "🎙️",
    label: "Sesi Interview",
    value: String(data.interview.total_sessions),
    sub: `Avg ${data.interview.average_score}`,
    color: T.violet,
  },
  {
    icon: "✉️",
    label: "Lamaran",
    value: String(data.applications.total_active),
    sub: "Aktif",
    color: T.emerald,
  },
];

  return (
    <div className="space-y-4">
      <WelcomeBanner data={data} />

      {/* Stats */}
      <div className="grid grid-cols-2 lg:grid-cols-4 gap-3">
        {stats.map((s) => <StatCard key={s.label} {...s} />)}
      </div>

      {/* Main 2-col grid */}
      <div className="grid lg:grid-cols-2 gap-4">

        {/* LEFT */}
        <div className="space-y-4">
          {/* Upload */}
          <Panel title="Upload CV" accent={T.cyan}>
            <UploadZone />
          </Panel>

          {/* Completeness */}
          <Panel 
            title="Kelengkapan CV" 
            action="Detail" 
            accent={T.blue}
            onActionClick={() => onNavigate("cv")}
          >
            <Completeness />
          </Panel>

          {/* Interview scores */}
          <Panel title="Skor Interview Terakhir" accent={T.violet}>
           <div className="flex items-center justify-around py-1">
  <ScoreRing 
    score={data?.interview?.breakdown?.confidence ?? 0} 
    label="Kepercayaan Diri" 
    color={T.violet} 
  />
  <ScoreRing 
    score={data?.interview?.breakdown?.technical ?? 0} 
    label="Teknis" 
    color={T.cyan} 
  />
  <ScoreRing 
    score={data?.interview?.breakdown?.communication ?? 0} 
    label="Komunikasi" 
    color={T.amber} 
  />
  <ScoreRing 
    score={data?.interview?.breakdown?.problem_solving ?? 0} 
    label="Problem Solving" 
    color={T.emerald} 
  />
</div>
          </Panel>
        </div>

        {/* RIGHT */}
        <div className="space-y-4">
          {/* Job Recommendations */}
          <Panel 
            title="Rekomendasi Lowongan" 
            action="Lihat Semua" 
            accent={T.blue}
            onActionClick={() => onNavigate("jobs")}
          >
            <div className="space-y-2">
              {data.jobs.map((j: any) => (
  <JobCard
    key={j.job_title}
    company={j.company}
    role={j.job_title}
    match={j.match_score}
    tags={j.tags}
    logo="💼"
  />
))}
            </div>
          </Panel>

          {/* Activity */}
          <Panel 
            title="Aktivitas Terbaru" 
            action="Lihat Semua" 
            accent={T.cyan}
          >
            <div className="-mx-1">
  {data?.activities?.map((a: any) => (
    <ActivityItem
      key={a.message}
      icon="📌"
      title={a.message}
      sub={a.type}
      time={new Date(a.time).toLocaleTimeString()}
      color={T.cyan}
    />
  ))}
</div>
          </Panel>

          {/* Quick Actions */}
          <Panel title="Aksi Cepat" accent={T.amber}>
            <QuickActions onNavigate={onNavigate} />
          </Panel>
        </div>
      </div>
    </div>
  );
}


// ─────────────────────────────────────────────────────────────
//  SIDEBAR
// ─────────────────────────────────────────────────────────────
function Sidebar({
  active,
  onNavigate,
  expanded,
  setExpanded,
}: {
  active: string;
  onNavigate: (id: string) => void;
  expanded: boolean;
  setExpanded: (v: boolean) => void;
}) {
  const mainNav: NavItemDef[] = [
    { id: "dashboard", icon: "⊞",  label: "Dashboard" },
    { id: "cv",        icon: "📄",  label: "Analisis CV" },
    { id: "jobs",      icon: "💼",  label: "Lowongan",       badge: "5" },
    { id: "interview", icon: "🎙️", label: "Simulasi Interview" },
    { id: "tracking",  icon: "📈",  label: "Tracking Lamaran" },
  ];
  const devNav: NavItemDef[] = [
    { id: "certs",   icon: "🏆", label: "Sertifikasi" },
    { id: "network", icon: "🌐", label: "Jaringan" },
  ];
  const sysNav: NavItemDef[] = [
    { id: "settings", icon: "⚙️", label: "Pengaturan" },
    { id: "help",     icon: "❓", label: "Bantuan" },
  ];

  const NavBtn = ({ id, icon, label, badge }: NavItemDef) => {
    const isActive = active === id;
    return (
      <button
        onClick={() => onNavigate(id)}
        title={!expanded ? label : undefined}
        className="relative flex items-center gap-2.5 rounded-xl transition-all duration-150 w-full"
        style={{
          padding: expanded ? "8px 10px" : "0",
          width: expanded ? "100%" : "40px",
          height: "40px",
          justifyContent: expanded ? "flex-start" : "center",
          margin: "1px 0",
          background: isActive
            ? "linear-gradient(135deg, rgba(0,212,255,0.1), rgba(59,130,246,0.07))"
            : "transparent",
          border: isActive ? "1px solid rgba(0,212,255,0.2)" : "1px solid transparent",
          color: isActive ? T.cyan : "rgba(255,255,255,0.4)",
        }}
        onMouseEnter={(e) => {
          if (!isActive) {
            (e.currentTarget as HTMLButtonElement).style.background = "rgba(255,255,255,0.04)";
            (e.currentTarget as HTMLButtonElement).style.borderColor = "rgba(255,255,255,0.08)";
            (e.currentTarget as HTMLButtonElement).style.color = "rgba(255,255,255,0.8)";
          }
        }}
        onMouseLeave={(e) => {
          if (!isActive) {
            (e.currentTarget as HTMLButtonElement).style.background = "transparent";
            (e.currentTarget as HTMLButtonElement).style.borderColor = "transparent";
            (e.currentTarget as HTMLButtonElement).style.color = "rgba(255,255,255,0.4)";
          }
        }}
      >
        {/* Active pill */}
        {isActive && expanded && (
          <div
            className="absolute left-0 top-1/4 bottom-1/4 w-[3px] rounded-r-sm"
            style={{ background: "linear-gradient(to bottom, #00d4ff, #3b82f6)" }}
          />
        )}
        <span style={{ fontSize: "16px", flexShrink: 0 }}>{icon}</span>
        {expanded && (
          <>
            <span className="text-[12px] font-medium flex-1 text-left">{label}</span>
            {badge && (
              <span
                className="text-[9px] font-bold px-1.5 py-px rounded-full"
                style={{
                  background: "rgba(0,212,255,0.12)",
                  color: T.cyan,
                  border: "1px solid rgba(0,212,255,0.2)",
                }}
              >
                {badge}
              </span>
            )}
          </>
        )}
      </button>
    );
  };

  return (
    <aside
      className="flex-shrink-0 flex flex-col h-screen sticky top-0 z-20 transition-all duration-300"
      style={{
        width: expanded ? "220px" : "64px",
        background: "linear-gradient(180deg, #05101e 0%, #02080f 100%)",
        borderRight: "1px solid rgba(255,255,255,0.07)",
        padding: expanded ? "16px 12px" : "16px 12px",
      }}
    >
      {/* Logo row */}
      <div
        className="flex items-center gap-2.5 mb-6"
        style={{ justifyContent: expanded ? "flex-start" : "center" }}
      >
        <button
          onClick={() => setExpanded(!expanded)}
          className="w-9 h-9 rounded-xl flex items-center justify-center text-lg flex-shrink-0 transition-all duration-200 hover:brightness-110"
          style={{
            background: "linear-gradient(135deg, #00d4ff, #3b82f6)",
            boxShadow: "0 0 18px rgba(0,212,255,0.25)",
            border: "none",
          }}
        >
          🚀
        </button>
        {expanded && (
          <span
            className="text-[16px] font-black"
            style={{
              fontFamily: "'Space Grotesk', sans-serif",
              background: "linear-gradient(135deg, #00d4ff, #3b82f6)",
              WebkitBackgroundClip: "text",
              WebkitTextFillColor: "transparent",
              letterSpacing: "-0.4px",
            }}
          >
            KarirAI
          </span>
        )}
      </div>

      {/* Main Nav */}
      {expanded && (
        <p className="text-[9px] text-slate-700 uppercase tracking-[0.14em] font-bold px-2.5 mb-2">
          Menu Utama
        </p>
      )}
      <div className="space-y-0.5" style={{ alignItems: expanded ? "stretch" : "center", display: "flex", flexDirection: "column" }}>
        {mainNav.map((n) => <NavBtn key={n.id} {...n} />)}
      </div>

      <div
        className="my-4"
        style={{ height: "1px", background: "rgba(255,255,255,0.06)" }}
      />

      {expanded && (
        <p className="text-[9px] text-slate-700 uppercase tracking-[0.14em] font-bold px-2.5 mb-2">
          Pengembangan
        </p>
      )}
      <div className="space-y-0.5" style={{ alignItems: expanded ? "stretch" : "center", display: "flex", flexDirection: "column" }}>
        {devNav.map((n) => <NavBtn key={n.id} {...n} />)}
      </div>

      {/* Bottom */}
      <div className="mt-auto space-y-0.5">
        <div style={{ alignItems: expanded ? "stretch" : "center", display: "flex", flexDirection: "column" }}>
          {sysNav.map((n) => <NavBtn key={n.id} {...n} />)}
        </div>

        <div
          className="mt-3 pt-3"
          style={{ borderTop: "1px solid rgba(255,255,255,0.06)" }}
        >
          <div
            className="flex items-center gap-2.5 rounded-xl cursor-pointer transition-all duration-150 px-2 py-1.5"
            style={{
              background: "rgba(255,255,255,0.03)",
              border: "1px solid rgba(255,255,255,0.06)",
              justifyContent: expanded ? "flex-start" : "center",
            }}
            onMouseEnter={(e) => {
              (e.currentTarget as HTMLDivElement).style.borderColor = "rgba(0,212,255,0.2)";
            }}
            onMouseLeave={(e) => {
              (e.currentTarget as HTMLDivElement).style.borderColor = "rgba(255,255,255,0.06)";
            }}
          >
            <div
              className="w-8 h-8 rounded-full flex items-center justify-center text-[11px] font-black text-white flex-shrink-0"
              style={{ background: "linear-gradient(135deg, #818cf8, #3b82f6)" }}
            >
              AR
            </div>
            {expanded && (
              <div className="min-w-0">
                <p className="text-[11px] font-semibold text-white/85 leading-none truncate">
                  WELCOME
                </p>
                <span
                  className="inline-block mt-1 text-[9px] px-1.5 py-px rounded font-bold"
                  style={{
                    background: "rgba(0,212,255,0.1)",
                    color: T.cyan,
                    border: "1px solid rgba(0,212,255,0.18)",
                  }}
                >
                  Pro Plan
                </span>
              </div>
            )}
          </div>
        </div>
      </div>
    </aside>
  );
}

// ─────────────────────────────────────────────────────────────
//  TOPBAR
// ─────────────────────────────────────────────────────────────
function Topbar({ title }: { title: string }) {
  return (
    <header
      className="flex items-center justify-between px-6 h-[56px] sticky top-0 z-10 gap-4"
      style={{
        background: "rgba(3,8,15,0.9)",
        borderBottom: "1px solid rgba(255,255,255,0.06)",
        backdropFilter: "blur(20px)",
      }}
    >
      {/* Left */}
      <div className="flex items-center gap-3">
        <div
          className="w-[3px] h-5 rounded-full flex-shrink-0"
          style={{ background: "linear-gradient(to bottom, #00d4ff, #3b82f6)" }}
        />
        <h1
          className="text-[14px] font-bold text-white"
          style={{ fontFamily: "'Space Grotesk', sans-serif", letterSpacing: "-0.3px" }}
        >
          {title}
        </h1>
      </div>

      {/* Right */}
      <div className="flex items-center gap-2">
        {/* Search */}
        <button
          className="hidden sm:flex items-center gap-2.5 h-8 px-3 rounded-lg transition-all duration-150"
          style={{
            background: "rgba(255,255,255,0.04)",
            border: "1px solid rgba(255,255,255,0.07)",
            color: "rgba(255,255,255,0.35)",
            fontSize: "12px",
          }}
          onMouseEnter={(e) => {
            (e.currentTarget as HTMLButtonElement).style.borderColor = "rgba(0,212,255,0.2)";
            (e.currentTarget as HTMLButtonElement).style.color = "rgba(255,255,255,0.6)";
          }}
          onMouseLeave={(e) => {
            (e.currentTarget as HTMLButtonElement).style.borderColor = "rgba(255,255,255,0.07)";
            (e.currentTarget as HTMLButtonElement).style.color = "rgba(255,255,255,0.35)";
          }}
        >
          <span style={{ fontSize: "13px" }}>🔍</span>
          <span>Cari...</span>
          <kbd
            className="text-[9px] px-1 rounded"
            style={{ background: "rgba(255,255,255,0.07)", color: "rgba(255,255,255,0.2)" }}
          >
            ⌘K
          </kbd>
        </button>

        {/* Notif */}
        <button
          className="relative w-8 h-8 rounded-lg flex items-center justify-center text-[14px] transition-all duration-150"
          style={{
            background: "rgba(255,255,255,0.04)",
            border: "1px solid rgba(255,255,255,0.07)",
            color: "rgba(255,255,255,0.4)",
          }}
          onMouseEnter={(e) => {
            (e.currentTarget as HTMLButtonElement).style.borderColor = "rgba(0,212,255,0.2)";
            (e.currentTarget as HTMLButtonElement).style.color = "rgba(255,255,255,0.8)";
          }}
          onMouseLeave={(e) => {
            (e.currentTarget as HTMLButtonElement).style.borderColor = "rgba(255,255,255,0.07)";
            (e.currentTarget as HTMLButtonElement).style.color = "rgba(255,255,255,0.4)";
          }}
        >
          🔔
          <span
            className="absolute top-1.5 right-1.5 w-1.5 h-1.5 rounded-full"
            style={{ background: T.cyan, boxShadow: `0 0 5px ${T.cyan}` }}
          />
        </button>

        <div style={{ width: "1px", height: "20px", background: "rgba(255,255,255,0.08)" }} />

        {/* Avatar */}
        <div className="flex items-center gap-2 cursor-pointer group">
          <div
            className="w-8 h-8 rounded-full flex items-center justify-center text-[11px] font-bold text-white"
            style={{ background: "linear-gradient(135deg, #818cf8, #3b82f6)" }}
          >
            AR
          </div>
          <div className="hidden sm:block">
            <p className="text-[11px] font-semibold text-white/80 group-hover:text-white transition-colors leading-none">
              Andi R.
            </p>
            <p className="text-[9px] leading-none mt-0.5" style={{ color: T.cyan }}>
              Pro Plan
            </p>
          </div>
        </div>
      </div>
    </header>
  );
}

// ─────────────────────────────────────────────────────────────
//  PAGE - MAIN COMPONENT
// ─────────────────────────────────────────────────────────────
export default function DashboardPage() {
  const router = useRouter();
  const searchParams = useSearchParams();
  const [sidebarExpanded, setSidebarExpanded] = useState(true);
  const [data, setData] = useState<any>(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    const fetchDashboard = async () => {
      try {
        const res = await fetch("http://127.0.0.1:8000/api/dashboard");
        const json = await res.json();
        setData(json);
      } catch (err) {
        console.error("Error fetch dashboard:", err);
      } finally {
        setLoading(false);
      }
    };

    fetchDashboard();
  }, []);

  if (loading) {
    return <div className="text-white p-10">Loading dashboard...</div>;
  }


  // ✅ Baca dari query parameter, default ke "dashboard"
  const view = searchParams.get("view") || "dashboard";

  // ✅ Navigasi dengan query parameter
  const handleNavigate = (id: string) => {
    router.push(`/dashboard?view=${id}`);
  };

  const placeholder = (emoji: string, text: string) => (
    <div
      className="rounded-2xl p-16 text-center"
      style={{
        background: "linear-gradient(145deg, #07111f 0%, #030810 100%)",
        border: "1px solid rgba(255,255,255,0.07)",
      }}
    >
      <div className="text-5xl mb-5">{emoji}</div>
      <p className="text-sm text-slate-500">{text}</p>
    </div>
  );

  const viewMap: Record<string, { title: string; content: React.ReactNode }> = {
    dashboard: { 
      title: "Dashboard", 
      content: <DashboardHome onNavigate={handleNavigate} data={data} />
    },
    cv: { 
      title: "Analisis CV", 
      content: (
        <div className="space-y-4">
          <Panel title="Upload CV" accent={T.cyan}>
            <UploadZone />
          </Panel>
          <Panel title="Kelengkapan CV" action="Detail" accent={T.blue}>
            <Completeness />
          </Panel>
        </div>
      )
    },
    jobs: { 
      title: "Rekomendasi Lowongan", 
      content: placeholder("💼", "Rekomendasi lowongan segera hadir") 
    },
    interview: { 
      title: "Simulasi Interview AI", 
      content: placeholder("🎙️", "Simulasi interview AI segera hadir") 
    },
    tracking: { 
      title: "Tracking Lamaran", 
      content: placeholder("📈", "Fitur tracking segera hadir") 
    },
    certs: { 
      title: "Sertifikasi & Skill", 
      content: placeholder("🏆", "Fitur sertifikasi segera hadir") 
    },
    network: { 
      title: "Jaringan", 
      content: placeholder("🌐", "Fitur jaringan segera hadir") 
    },
    settings: { 
      title: "Pengaturan", 
      content: placeholder("⚙️", "Halaman pengaturan") 
    },
    help: { 
      title: "Bantuan", 
      content: placeholder("❓", "Pusat bantuan") 
    },
  };

  const current = viewMap[view] ?? viewMap.dashboard;

  return (
    <div
      className="min-h-screen text-white flex"
      style={{ background: "#03080f", position: "relative", overflow: "hidden" }}
    >
      {/* Ambient mesh */}
      <div
        className="pointer-events-none"
        style={{
          position: "fixed", inset: 0, zIndex: 0,
          background: [
            "radial-gradient(ellipse 700px 500px at 20% -5%, rgba(0,212,255,0.055) 0%, transparent 60%)",
            "radial-gradient(ellipse 500px 500px at 95% 85%, rgba(59,130,246,0.045) 0%, transparent 60%)",
            "radial-gradient(ellipse 350px 350px at 65% 55%, rgba(129,140,248,0.025) 0%, transparent 60%)",
          ].join(", "),
        }}
      />
      {/* Grid */}
      <div
        className="pointer-events-none"
        style={{
          position: "fixed", inset: 0, zIndex: 0,
          backgroundImage: [
            "linear-gradient(rgba(0,212,255,0.012) 1px, transparent 1px)",
            "linear-gradient(90deg, rgba(0,212,255,0.012) 1px, transparent 1px)",
          ].join(", "),
          backgroundSize: "48px 48px",
        }}
      />

      {/* Sidebar */}
      <div style={{ position: "relative", zIndex: 10 }}>
        <Sidebar
          active={view}
          onNavigate={handleNavigate}
          expanded={sidebarExpanded}
          setExpanded={setSidebarExpanded}
        />
      </div>

      {/* Main */}
      <div className="flex-1 flex flex-col min-w-0" style={{ position: "relative", zIndex: 1 }}>
        <Topbar title={current.title} />
        <main className="flex-1 p-5 overflow-y-auto">{current.content}</main>
      </div>

      <style>{`
        @import url('https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&display=swap');
        * { font-family: 'Space Grotesk', sans-serif; }
        ::-webkit-scrollbar { width: 4px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: rgba(0,212,255,0.15); border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: rgba(0,212,255,0.3); }
        @keyframes pulse {
          0%,100%{opacity:1}50%{opacity:0.4}
        }
        .animate-pulse { animation: pulse 2s cubic-bezier(0.4,0,0.6,1) infinite; }
      `}</style>
    </div>
  );
}