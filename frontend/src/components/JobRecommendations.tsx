"use client";

import { useState } from "react";

interface Job {
  id: number;
  title: string;
  company: string;
  location: string;
  type: string;
  salary: string;
  match: number;
  tags: string[];
  logo: string;
  posted: string;
}

const jobs: Job[] = [
  { id: 1, title: "Senior Frontend Engineer", company: "Tokopedia", location: "Jakarta / Remote", type: "Full-time", salary: "Rp 20–35jt", match: 96, tags: ["React", "TypeScript", "Next.js"], logo: "TO", posted: "2j lalu" },
  { id: 2, title: "Product Designer", company: "Gojek", location: "Jakarta", type: "Full-time", salary: "Rp 18–28jt", match: 88, tags: ["Figma", "User Research", "Prototyping"], logo: "GO", posted: "5j lalu" },
  { id: 3, title: "Backend Engineer (Node)", company: "Traveloka", location: "Remote", type: "Full-time", salary: "Rp 22–40jt", match: 81, tags: ["Node.js", "PostgreSQL", "Microservices"], logo: "TV", posted: "1h lalu" },
  { id: 4, title: "AI/ML Engineer", company: "Bukalapak", location: "Bandung / Remote", type: "Full-time", salary: "Rp 25–45jt", match: 74, tags: ["Python", "LLM", "PyTorch"], logo: "BL", posted: "3h lalu" },
  { id: 5, title: "DevOps Engineer", company: "Shopee ID", location: "Jakarta", type: "Full-time", salary: "Rp 20–38jt", match: 69, tags: ["Kubernetes", "AWS", "CI/CD"], logo: "SP", posted: "Kemarin" },
];

function MatchBadge({ match }: { match: number }) {
  const color = match >= 90 ? "from-cyan-400 to-cyan-500" : match >= 75 ? "from-blue-400 to-blue-500" : match >= 60 ? "from-violet-400 to-violet-500" : "from-slate-500 to-slate-600";
  return (
    <div className={`flex items-center gap-1 bg-gradient-to-r ${color} px-2.5 py-1 rounded-full`}>
      <span className="text-xs font-black text-[#020810]">{match}%</span>
    </div>
  );
}

function LogoPlaceholder({ text, match }: { text: string; match: number }) {
  const bg = match >= 90 ? "bg-cyan-400/20 text-cyan-400" : match >= 75 ? "bg-blue-400/20 text-blue-400" : "bg-slate-700 text-slate-300";
  return (
    <div className={`w-10 h-10 rounded-xl flex items-center justify-center font-black text-xs flex-shrink-0 ${bg}`}>
      {text}
    </div>
  );
}

export default function JobRecommendations() {
  const [saved, setSaved] = useState<number[]>([]);
  const [applied, setApplied] = useState<number[]>([]);
  const [filter, setFilter] = useState("Semua");

  const filters = ["Semua", "90%+", "Remote", "Full-time"];

  const filtered = jobs.filter((j) => {
    if (filter === "90%+") return j.match >= 90;
    if (filter === "Remote") return j.location.includes("Remote");
    return true;
  });

  return (
    <div className="bg-[#050d1a] border border-white/10 rounded-2xl p-6 space-y-5">
      <div className="flex items-center justify-between">
        <div>
          <p className="text-xs text-cyan-400 tracking-widest uppercase mb-1">// AI Matching</p>
          <h2 className="text-white font-bold text-lg">Rekomendasi Pekerjaan</h2>
        </div>
        <span className="text-xs text-slate-500 bg-white/5 px-3 py-1.5 rounded-full">{jobs.length} posisi cocok</span>
      </div>

      {/* Filter chips */}
      <div className="flex gap-2 flex-wrap">
        {filters.map((f) => (
          <button
            key={f}
            onClick={() => setFilter(f)}
            className={`text-xs px-3 py-1.5 rounded-full transition-all ${filter === f ? "bg-cyan-400 text-[#020810] font-bold" : "bg-white/5 text-slate-400 hover:bg-white/10"}`}
          >
            {f}
          </button>
        ))}
      </div>

      {/* Job list */}
      <div className="space-y-3 max-h-[420px] overflow-y-auto pr-1 scrollbar-thin">
        {filtered.map((job) => (
          <div
            key={job.id}
            className={`group relative border rounded-xl p-4 transition-all duration-300 cursor-pointer ${applied.includes(job.id) ? "border-emerald-400/30 bg-emerald-400/5" : "border-white/8 bg-white/3 hover:border-cyan-400/30 hover:bg-white/5"}`}
          >
            <div className="flex items-start gap-3">
              <LogoPlaceholder text={job.logo} match={job.match} />
              <div className="flex-1 min-w-0">
                <div className="flex items-center justify-between gap-2 mb-0.5">
                  <p className="font-bold text-white text-sm truncate">{job.title}</p>
                  <MatchBadge match={job.match} />
                </div>
                <p className="text-xs text-slate-400 mb-2">
                  {job.company} · {job.location} · <span className="text-emerald-400">{job.salary}</span>
                </p>
                <div className="flex flex-wrap gap-1.5 mb-3">
                  {job.tags.map((t) => (
                    <span key={t} className="text-xs bg-white/5 text-slate-400 px-2 py-0.5 rounded-md">{t}</span>
                  ))}
                </div>
                <div className="flex items-center gap-2">
                  <button
                    onClick={() => setApplied((p) => p.includes(job.id) ? p.filter((x) => x !== job.id) : [...p, job.id])}
                    className={`text-xs px-3 py-1.5 rounded-lg font-bold transition-all ${applied.includes(job.id) ? "bg-emerald-400 text-[#020810]" : "bg-cyan-400/10 text-cyan-400 border border-cyan-400/30 hover:bg-cyan-400/20"}`}
                  >
                    {applied.includes(job.id) ? "✓ Dilamar" : "Lamar →"}
                  </button>
                  <button
                    onClick={() => setSaved((p) => p.includes(job.id) ? p.filter((x) => x !== job.id) : [...p, job.id])}
                    className="text-xs px-3 py-1.5 rounded-lg border border-white/10 text-slate-500 hover:text-white hover:border-white/30 transition-all"
                  >
                    {saved.includes(job.id) ? "♥" : "♡"}
                  </button>
                  <span className="text-xs text-slate-600 ml-auto">{job.posted}</span>
                </div>
              </div>
            </div>
          </div>
        ))}
      </div>
    </div>
  );
}