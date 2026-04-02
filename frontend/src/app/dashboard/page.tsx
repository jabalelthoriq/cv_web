"use client";

import { useState } from "react";
import Sidebar from "@/components/Sidebar";
import CVUploadZone from "@/components/CVUploadZone";
import CVCompletenessWidget from "@/components/CVCompletenessWidget";
import JobRecommendations from "@/components/JobRecommendations";
import InterviewSimulator from "@/components/InterviewSimulator";

// ──────────────────────────────────────────────
//  Stat card
// ──────────────────────────────────────────────
function StatCard({ label, value, delta, icon, accent }: {
  label: string; value: string; delta?: string; icon: string; accent: string;
}) {
  return (
    <div className={`bg-[#050d1a] border border-white/10 rounded-2xl p-4 relative overflow-hidden`}>
      <div className={`absolute inset-0 bg-gradient-to-br ${accent} pointer-events-none`} />
      <div className="relative">
        <div className="flex items-center justify-between mb-3">
          <span className="text-lg">{icon}</span>
          {delta && <span className="text-xs text-emerald-400 bg-emerald-400/10 px-2 py-0.5 rounded-full">{delta}</span>}
        </div>
        <p className="text-2xl font-black text-white mb-0.5">{value}</p>
        <p className="text-xs text-slate-500">{label}</p>
      </div>
    </div>
  );
}

// ──────────────────────────────────────────────
//  Activity feed
// ──────────────────────────────────────────────
function ActivityFeed() {
  const activities = [
    { icon: "📄", text: "CV kamu diperbarui", sub: "Skor naik 12 poin", time: "5m lalu", color: "text-cyan-400" },
    { icon: "💼", text: "Match baru ditemukan", sub: "Senior Frontend — Tokopedia", time: "1j lalu", color: "text-blue-400" },
    { icon: "🎙️", text: "Sesi interview selesai", sub: "Skor 87/100", time: "3j lalu", color: "text-violet-400" },
    { icon: "✉️", text: "Undangan interview", sub: "Gojek — Product Designer", time: "Kemarin", color: "text-emerald-400" },
    { icon: "📊", text: "Laporan ATS tersedia", sub: "CV lolos 8 dari 10 filter", time: "2h lalu", color: "text-amber-400" },
  ];
  return (
    <div className="bg-[#050d1a] border border-white/10 rounded-2xl p-5">
      <p className="text-xs text-cyan-400 tracking-widest uppercase mb-4">// Aktivitas Terbaru</p>
      <div className="space-y-3">
        {activities.map((a, i) => (
          <div key={i} className="flex items-start gap-3">
            <div className="w-8 h-8 rounded-lg bg-white/5 flex items-center justify-center text-sm flex-shrink-0">{a.icon}</div>
            <div className="flex-1 min-w-0">
              <p className="text-xs text-white font-medium">{a.text}</p>
              <p className={`text-xs ${a.color}`}>{a.sub}</p>
            </div>
            <span className="text-xs text-slate-600 flex-shrink-0">{a.time}</span>
          </div>
        ))}
      </div>
    </div>
  );
}

// ──────────────────────────────────────────────
//  Dashboard content views
// ──────────────────────────────────────────────
function DashboardHome() {
  return (
    <div className="space-y-6">
      {/* Stats row */}
      <div className="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <StatCard icon="📊" label="Skor CV" value="73" delta="+12" accent="from-cyan-400/5 to-transparent" />
        <StatCard icon="💼" label="Job Match" value="5" delta="Baru" accent="from-blue-400/5 to-transparent" />
        <StatCard icon="🎙️" label="Sesi Interview" value="3" accent="from-violet-400/5 to-transparent" />
        <StatCard icon="✉️" label="Lamaran Aktif" value="2" accent="from-emerald-400/5 to-transparent" />
      </div>

      {/* Main grid */}
      <div className="grid lg:grid-cols-2 gap-6">
        {/* Upload & completeness */}
        <div className="space-y-5">
          <CVUploadZone />
          <CVCompletenessWidget />
        </div>
        {/* Activity + quick actions */}
        <div className="space-y-5">
          <ActivityFeed />
          {/* Quick actions */}
          <div className="grid grid-cols-2 gap-3">
            {[
              { icon: "🎙️", label: "Mulai Interview", color: "border-cyan-400/30 hover:bg-cyan-400/5" },
              { icon: "📝", label: "Edit CV", color: "border-blue-400/30 hover:bg-blue-400/5" },
              { icon: "🔍", label: "Cari Lowongan", color: "border-violet-400/30 hover:bg-violet-400/5" },
              { icon: "📊", label: "Cek ATS", color: "border-amber-400/30 hover:bg-amber-400/5" },
            ].map(({ icon, label, color }) => (
              <button key={label} className={`bg-[#050d1a] border ${color} rounded-xl p-4 flex flex-col items-center gap-2 transition-all text-center group`}>
                <span className="text-2xl">{icon}</span>
                <span className="text-xs text-slate-400 group-hover:text-white transition-colors">{label}</span>
              </button>
            ))}
          </div>
        </div>
      </div>
    </div>
  );
}

// ──────────────────────────────────────────────
//  Topbar
// ──────────────────────────────────────────────
function Topbar({ title }: { title: string }) {
  return (
    <header className="flex items-center justify-between px-6 py-3 border-b border-white/8 bg-[#020810]/80 backdrop-blur sticky top-0 z-30">
      <h1 className="font-bold text-white text-sm tracking-wide">{title}</h1>
      <div className="flex items-center gap-3">
        <button className="w-8 h-8 rounded-lg bg-white/5 border border-white/10 flex items-center justify-center text-slate-400 hover:text-white text-sm transition-colors">🔔</button>
        <button className="w-8 h-8 rounded-lg bg-white/5 border border-white/10 flex items-center justify-center text-slate-400 hover:text-white text-sm transition-colors">🔍</button>
        <div className="w-8 h-8 rounded-full bg-gradient-to-br from-violet-400 to-blue-500 flex items-center justify-center text-xs font-bold text-white">AR</div>
      </div>
    </header>
  );
}

// ──────────────────────────────────────────────
//  MAIN DASHBOARD PAGE
// ──────────────────────────────────────────────
export default function DashboardPage() {
  const [active, setActive] = useState("dashboard");

  const viewMap: Record<string, { title: string; content: React.ReactNode }> = {
    dashboard: { title: "Dashboard", content: <DashboardHome /> },
    cv:        { title: "Analisis CV", content: <div className="space-y-6"><CVUploadZone /><CVCompletenessWidget /></div> },
    jobs:      { title: "Rekomendasi Lowongan", content: <JobRecommendations /> },
    interview: { title: "Simulasi Interview AI", content: <InterviewSimulator /> },
    tracking:  { title: "Tracking Lamaran", content: (
      <div className="bg-[#050d1a] border border-white/10 rounded-2xl p-8 text-center text-slate-500">
        <p className="text-3xl mb-3">📈</p>
        <p className="text-sm">Tracking lamaran akan tersedia segera</p>
      </div>
    )},
    certs:     { title: "Sertifikasi & Skill", content: (
      <div className="bg-[#050d1a] border border-white/10 rounded-2xl p-8 text-center text-slate-500">
        <p className="text-3xl mb-3">🏆</p>
        <p className="text-sm">Fitur sertifikasi akan tersedia segera</p>
      </div>
    )},
    settings:  { title: "Pengaturan", content: (
      <div className="bg-[#050d1a] border border-white/10 rounded-2xl p-8 text-center text-slate-500">
        <p className="text-3xl mb-3">⚙️</p>
        <p className="text-sm">Halaman pengaturan</p>
      </div>
    )},
    help:      { title: "Bantuan", content: (
      <div className="bg-[#050d1a] border border-white/10 rounded-2xl p-8 text-center text-slate-500">
        <p className="text-3xl mb-3">❓</p>
        <p className="text-sm">Pusat bantuan</p>
      </div>
    )},
  };

  const current = viewMap[active] ?? viewMap.dashboard;

  return (
    <div className="min-h-screen bg-[#020810] text-white flex relative overflow-hidden">
      {/* Ambient glow */}
      <div className="fixed top-0 left-52 w-[500px] h-[200px] bg-cyan-500/5 blur-[100px] pointer-events-none" />

      {/* Sidebar */}
      <div className="relative">
        <Sidebar active={active} onNavigate={setActive} />
      </div>

      {/* Main content */}
      <div className="flex-1 flex flex-col min-w-0">
        <Topbar title={current.title} />
        <main className="flex-1 p-6 overflow-y-auto">
          {current.content}
        </main>
      </div>

      <style>{`
        @import url('https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;700;900&display=swap');
        * { font-family: 'Space Grotesk', sans-serif; }
        ::-webkit-scrollbar { width: 4px; height: 4px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 2px; }
      `}</style>
    </div>
  );
}