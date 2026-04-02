"use client";

import { useState } from "react";

interface Section {
  label: string;
  score: number;
  issues: string[];
  color: string;
}

const sections: Section[] = [
  { label: "Foto Profil",     score: 100, issues: [],                                          color: "#00e5ff" },
  { label: "Ringkasan",       score: 70,  issues: ["Terlalu singkat (< 50 kata)"],             color: "#3b82f6" },
  { label: "Pengalaman",      score: 90,  issues: ["Tidak ada angka/metrik di 2 posisi"],      color: "#8b5cf6" },
  { label: "Pendidikan",      score: 100, issues: [],                                          color: "#10b981" },
  { label: "Skill",           score: 55,  issues: ["Soft skill kosong", "Tambah tools spesifik"], color: "#f59e0b" },
  { label: "Portofolio",      score: 30,  issues: ["Link GitHub tidak ada", "Tidak ada proyek"],  color: "#ef4444" },
  { label: "Sertifikasi",     score: 80,  issues: ["Tambah tanggal expired"],                  color: "#06b6d4" },
  { label: "Bahasa",          score: 60,  issues: ["Level bahasa tidak spesifik"],             color: "#a78bfa" },
];

function RadarChart({ sections }: { sections: Section[] }) {
  const size = 220;
  const cx = size / 2;
  const cy = size / 2;
  const r = 80;
  const n = sections.length;
  const angle = (i: number) => (i * 2 * Math.PI) / n - Math.PI / 2;
  const pt = (i: number, radius: number) => ({
    x: cx + radius * Math.cos(angle(i)),
    y: cy + radius * Math.sin(angle(i)),
  });

  const gridLevels = [0.25, 0.5, 0.75, 1];

  return (
    <svg viewBox={`0 0 ${size} ${size}`} className="w-full max-w-[220px]">
      {/* Grid */}
      {gridLevels.map((lvl) => {
        const pts = sections.map((_, i) => pt(i, r * lvl));
        return (
          <polygon
            key={lvl}
            points={pts.map((p) => `${p.x},${p.y}`).join(" ")}
            fill="none"
            stroke="rgba(255,255,255,0.06)"
            strokeWidth="1"
          />
        );
      })}
      {/* Spokes */}
      {sections.map((_, i) => {
        const end = pt(i, r);
        return <line key={i} x1={cx} y1={cy} x2={end.x} y2={end.y} stroke="rgba(255,255,255,0.06)" strokeWidth="1" />;
      })}
      {/* Data area */}
      <polygon
        points={sections.map((s, i) => { const p = pt(i, r * (s.score / 100)); return `${p.x},${p.y}`; }).join(" ")}
        fill="rgba(0,229,255,0.12)"
        stroke="#00e5ff"
        strokeWidth="1.5"
      />
      {/* Data dots */}
      {sections.map((s, i) => {
        const p = pt(i, r * (s.score / 100));
        return <circle key={i} cx={p.x} cy={p.y} r="3" fill="#00e5ff" />;
      })}
      {/* Labels */}
      {sections.map((s, i) => {
        const p = pt(i, r + 14);
        return (
          <text key={i} x={p.x} y={p.y} textAnchor="middle" dominantBaseline="middle"
            fill="rgba(255,255,255,0.5)" fontSize="7" fontFamily="monospace">
            {s.label.split(" ")[0]}
          </text>
        );
      })}
    </svg>
  );
}

function ScoreRing({ score }: { score: number }) {
  const r = 52;
  const circ = 2 * Math.PI * r;
  const dash = (score / 100) * circ;
  return (
    <div className="relative w-32 h-32 flex items-center justify-center">
      <svg className="absolute inset-0 -rotate-90" viewBox="0 0 120 120">
        <circle cx="60" cy="60" r={r} fill="none" stroke="rgba(255,255,255,0.05)" strokeWidth="8" />
        <circle cx="60" cy="60" r={r} fill="none" stroke="#00e5ff" strokeWidth="8"
          strokeDasharray={`${dash} ${circ}`} strokeLinecap="round" />
      </svg>
      <div className="text-center">
        <p className="text-2xl font-black text-cyan-400">{score}</p>
        <p className="text-xs text-slate-500">/ 100</p>
      </div>
    </div>
  );
}

export default function CVCompletenessWidget() {
  const [active, setActive] = useState<Section | null>(null);
  const overallScore = Math.round(sections.reduce((s, x) => s + x.score, 0) / sections.length);

  return (
    <div className="bg-[#050d1a] border border-white/10 rounded-2xl p-6 space-y-6">
      {/* Header */}
      <div className="flex items-center justify-between">
        <div>
          <p className="text-xs text-cyan-400 tracking-widest uppercase mb-1">// Analisis CV</p>
          <h2 className="text-white font-bold text-lg">Kelengkapan CV</h2>
        </div>
        <ScoreRing score={overallScore} />
      </div>

      {/* Radar + bars layout */}
      <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div className="flex items-center justify-center">
          <RadarChart sections={sections} />
        </div>

        <div className="space-y-3">
          {sections.map((s) => (
            <button
              key={s.label}
              onClick={() => setActive(active?.label === s.label ? null : s)}
              className="w-full text-left group"
            >
              <div className="flex items-center gap-2 mb-1">
                <span className="text-xs text-slate-400 w-24 truncate">{s.label}</span>
                <span className="text-xs font-mono ml-auto" style={{ color: s.color }}>{s.score}%</span>
              </div>
              <div className="h-1.5 rounded-full bg-white/5 overflow-hidden">
                <div
                  className="h-full rounded-full transition-all duration-700"
                  style={{ width: `${s.score}%`, background: s.color }}
                />
              </div>
              {/* Expanded issues */}
              {active?.label === s.label && s.issues.length > 0 && (
                <ul className="mt-2 space-y-1">
                  {s.issues.map((iss) => (
                    <li key={iss} className="flex items-center gap-2 text-xs text-amber-400">
                      <span>⚠</span> {iss}
                    </li>
                  ))}
                </ul>
              )}
              {active?.label === s.label && s.issues.length === 0 && (
                <p className="mt-1 text-xs text-emerald-400">✓ Bagian ini sudah lengkap</p>
              )}
            </button>
          ))}
        </div>
      </div>

      <button className="w-full bg-cyan-400/10 border border-cyan-400/30 text-cyan-400 rounded-xl py-2.5 text-xs font-bold tracking-widest hover:bg-cyan-400/20 transition-colors uppercase">
        Perbaiki CV dengan AI →
      </button>
    </div>
  );
}