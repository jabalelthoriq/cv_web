"use client";

import { useState, useCallback } from "react";

type UploadState = "idle" | "dragging" | "uploading" | "done";

export default function CVUploadZone({ onDone }: { onDone?: () => void }) {
  const [state, setState] = useState<UploadState>("idle");
  const [progress, setProgress] = useState(0);
  const [fileName, setFileName] = useState("");

  const simulateUpload = (name: string) => {
    setFileName(name);
    setState("uploading");
    setProgress(0);
    const steps = [15, 35, 55, 72, 88, 100];
    steps.forEach((p, i) => {
      setTimeout(() => {
        setProgress(p);
        if (p === 100) {
          setState("done");
          onDone?.();
        }
      }, i * 400 + 300);
    });
  };

  const handleDrop = useCallback((e: React.DragEvent) => {
    e.preventDefault();
    setState("idle");
    const file = e.dataTransfer.files[0];
    if (file) simulateUpload(file.name);
  }, []);

  const handleChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    const file = e.target.files?.[0];
    if (file) simulateUpload(file.name);
  };

  if (state === "done") {
    return (
      <div className="border border-emerald-400/30 bg-emerald-400/5 rounded-2xl p-8 text-center space-y-3">
        <div className="w-14 h-14 rounded-2xl bg-emerald-400/20 flex items-center justify-center mx-auto text-3xl">✓</div>
        <p className="text-emerald-400 font-bold">CV Berhasil Dianalisis!</p>
        <p className="text-xs text-slate-500 truncate max-w-[180px] mx-auto">{fileName}</p>
        <button onClick={() => { setState("idle"); setProgress(0); setFileName(""); }}
          className="text-xs text-slate-500 hover:text-white transition-colors underline">
          Upload CV lain
        </button>
      </div>
    );
  }

  if (state === "uploading") {
    return (
      <div className="border border-white/10 rounded-2xl p-8 text-center space-y-5 bg-[#050d1a]">
        <div className="text-3xl animate-spin">⚙️</div>
        <div className="space-y-2">
          <p className="text-sm text-white font-bold">Menganalisis CV...</p>
          <p className="text-xs text-slate-500 truncate">{fileName}</p>
        </div>
        <div className="space-y-1.5">
          <div className="h-1.5 bg-white/5 rounded-full overflow-hidden">
            <div
              className="h-full bg-gradient-to-r from-cyan-400 to-blue-500 rounded-full transition-all duration-500"
              style={{ width: `${progress}%` }}
            />
          </div>
          <div className="flex justify-between text-xs text-slate-600 font-mono">
            <span>{["Membaca teks...", "Deteksi bagian...", "Scoring...", "Matching jobs...", "Selesai!"][Math.floor(progress / 25)]}</span>
            <span>{progress}%</span>
          </div>
        </div>
      </div>
    );
  }

  return (
    <label
      className={`block border-2 border-dashed rounded-2xl p-8 text-center cursor-pointer transition-all duration-300 ${state === "dragging" ? "border-cyan-400 bg-cyan-400/5 scale-[1.01]" : "border-white/15 hover:border-cyan-400/40 hover:bg-white/3 bg-[#050d1a]"}`}
      onDragOver={(e) => { e.preventDefault(); setState("dragging"); }}
      onDragLeave={() => setState("idle")}
      onDrop={handleDrop}
    >
      <input type="file" accept=".pdf,.doc,.docx" className="sr-only" onChange={handleChange} />
      <div className="space-y-4">
        <div className={`w-14 h-14 rounded-2xl flex items-center justify-center mx-auto text-3xl border transition-all ${state === "dragging" ? "border-cyan-400/50 bg-cyan-400/10" : "border-white/10 bg-white/5"}`}>
          📄
        </div>
        <div>
          <p className="text-white font-bold mb-1">Drag & drop CV kamu</p>
          <p className="text-xs text-slate-500">atau klik untuk memilih file</p>
        </div>
        <div className="flex gap-2 justify-center flex-wrap">
          {["PDF", "DOC", "DOCX"].map((ext) => (
            <span key={ext} className="text-xs bg-white/5 text-slate-500 px-2.5 py-1 rounded-md border border-white/8">{ext}</span>
          ))}
        </div>
        <p className="text-xs text-slate-600">Maks. 10MB · Aman & terenkripsi</p>
      </div>
    </label>
  );
}