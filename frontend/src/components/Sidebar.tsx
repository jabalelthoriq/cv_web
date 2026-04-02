"use client";

import { useState } from "react";

const navItems = [
  { icon: "⊞", label: "Dashboard",    id: "dashboard" },
  { icon: "📄", label: "Analisis CV",  id: "cv" },
  { icon: "💼", label: "Lowongan",     id: "jobs" },
  { icon: "🎙️", label: "Interview",    id: "interview" },
  { icon: "📈", label: "Lamaran",      id: "tracking" },
  { icon: "🏆", label: "Sertifikasi",  id: "certs" },
];

const bottomItems = [
  { icon: "⚙️", label: "Pengaturan", id: "settings" },
  { icon: "❓", label: "Bantuan",    id: "help" },
];

interface SidebarProps {
  active: string;
  onNavigate: (id: string) => void;
  collapsed?: boolean;
}

export default function Sidebar({ active, onNavigate, collapsed = false }: SidebarProps) {
  const [col, setCol] = useState(collapsed);

  const NavBtn = ({ icon, label, id }: { icon: string; label: string; id: string }) => {
    const isActive = active === id;
    return (
      <button
        onClick={() => onNavigate(id)}
        title={col ? label : undefined}
        className={`w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-left transition-all duration-200 group ${isActive ? "bg-cyan-400/10 border border-cyan-400/30 text-cyan-400" : "text-slate-500 hover:text-white hover:bg-white/5 border border-transparent"}`}
      >
        <span className="text-base flex-shrink-0">{icon}</span>
        {!col && (
          <span className="text-xs font-medium tracking-wide truncate">{label}</span>
        )}
        {isActive && !col && (
          <span className="ml-auto w-1.5 h-1.5 rounded-full bg-cyan-400" />
        )}
      </button>
    );
  };

  return (
    <aside className={`flex flex-col bg-[#050d1a] border-r border-white/8 transition-all duration-300 ${col ? "w-14" : "w-52"} flex-shrink-0`}>
      {/* Logo */}
      <div className={`flex items-center gap-2.5 px-3 py-4 border-b border-white/8 ${col ? "justify-center" : ""}`}>
        <div className="w-7 h-7 rounded-lg bg-gradient-to-br from-cyan-400 to-blue-600 flex items-center justify-center flex-shrink-0">
          <span className="text-xs font-black text-white">CV</span>
        </div>
        {!col && <span className="font-bold text-white text-sm tracking-widest">NEXUS<span className="text-cyan-400">.AI</span></span>}
      </div>

      {/* Collapse toggle */}
      <button
        onClick={() => setCol(!col)}
        className="absolute -right-3 top-8 w-6 h-6 rounded-full bg-[#050d1a] border border-white/15 text-slate-500 text-xs flex items-center justify-center hover:text-white hover:border-white/30 transition-all z-10"
      >
        {col ? "›" : "‹"}
      </button>

      {/* User profile mini */}
      <div className={`flex items-center gap-2.5 px-3 py-3 border-b border-white/8 ${col ? "justify-center" : ""}`}>
        <div className="w-7 h-7 rounded-full bg-gradient-to-br from-violet-400 to-blue-500 flex items-center justify-center text-xs font-bold text-white flex-shrink-0">
          AR
        </div>
        {!col && (
          <div className="min-w-0">
            <p className="text-xs font-medium text-white truncate">Andi Rahmat</p>
            <p className="text-xs text-slate-600 truncate">Pro Plan</p>
          </div>
        )}
      </div>

      {/* Nav items */}
      <nav className="flex-1 p-2 space-y-0.5">
        {navItems.map((item) => <NavBtn key={item.id} {...item} />)}
      </nav>

      {/* Bottom items */}
      <div className="p-2 space-y-0.5 border-t border-white/8">
        {bottomItems.map((item) => <NavBtn key={item.id} {...item} />)}
      </div>

      {/* Usage meter */}
      {!col && (
        <div className="p-3 border-t border-white/8">
          <p className="text-xs text-slate-600 mb-1.5 flex justify-between">
            <span>Analisis</span><span>2/3</span>
          </p>
          <div className="h-1 bg-white/5 rounded-full overflow-hidden">
            <div className="h-full w-2/3 bg-gradient-to-r from-cyan-400 to-blue-500 rounded-full" />
          </div>
          <button className="mt-2 w-full text-xs text-cyan-400 hover:text-cyan-300 transition-colors text-left">
            ↑ Upgrade ke Pro
          </button>
        </div>
      )}
    </aside>
  );
}