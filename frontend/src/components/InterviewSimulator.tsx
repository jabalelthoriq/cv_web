"use client";

import { useState, useEffect, useRef } from "react";

type Phase = "idle" | "thinking" | "speaking" | "listening" | "feedback";

interface Message {
  role: "ai" | "user";
  text: string;
  feedback?: string;
}

const questions = [
  "Ceritakan tentang dirimu dan perjalanan karirmu hingga saat ini.",
  "Apa pencapaian terbesar yang kamu raih di pekerjaan sebelumnya? Berikan contoh spesifik dengan angka.",
  "Bagaimana cara kamu menangani konflik dengan rekan kerja?",
  "Di mana kamu melihat dirimu dalam 5 tahun ke depan?",
  "Mengapa kamu tertarik bergabung dengan perusahaan kami?",
];

const feedbacks = [
  "Jawaban solid! Struktur STAR sudah bagus. Coba tambahkan metrik yang lebih spesifik.",
  "Bagus! Tapi hindari kata-kata negatif soal perusahaan lama. Fokus pada pembelajaran.",
  "Excellent! Sangat profesional dan to-the-point. Keep it up!",
];

function Waveform({ active }: { active: boolean }) {
  const bars = 20;
  return (
    <div className="flex items-center gap-0.5 h-8">
      {Array.from({ length: bars }).map((_, i) => (
        <div
          key={i}
          className={`w-1 rounded-full transition-all ${active ? "bg-cyan-400" : "bg-white/20"}`}
          style={{
            height: active ? `${20 + Math.random() * 60}%` : "20%",
            animationName: active ? "wave" : "none",
            animationDuration: `${0.4 + Math.random() * 0.4}s`,
            animationTimingFunction: "ease-in-out",
            animationIterationCount: "infinite",
            animationDirection: "alternate",
            animationDelay: `${i * 0.04}s`,
          }}
        />
      ))}
      <style>{`
        @keyframes wave {
          from { height: 20%; }
          to { height: 80%; }
        }
      `}</style>
    </div>
  );
}

function AvatarAI({ phase }: { phase: Phase }) {
  const glowColors: Record<Phase, string> = {
    idle: "shadow-[0_0_0px_transparent]",
    thinking: "shadow-[0_0_30px_rgba(139,92,246,0.5)]",
    speaking: "shadow-[0_0_40px_rgba(0,229,255,0.6)]",
    listening: "shadow-[0_0_30px_rgba(16,185,129,0.5)]",
    feedback: "shadow-[0_0_25px_rgba(245,158,11,0.4)]",
  };
  const borderColors: Record<Phase, string> = {
    idle: "border-white/20",
    thinking: "border-violet-400/60",
    speaking: "border-cyan-400/80",
    listening: "border-emerald-400/60",
    feedback: "border-amber-400/60",
  };
  return (
    <div className={`relative w-16 h-16 rounded-2xl border-2 ${borderColors[phase]} ${glowColors[phase]} transition-all duration-500 flex items-center justify-center bg-gradient-to-br from-slate-800 to-slate-900 overflow-hidden flex-shrink-0`}>
      <span className="text-2xl">🤖</span>
      {phase === "thinking" && (
        <div className="absolute inset-0 bg-violet-400/10 animate-pulse" />
      )}
      {phase === "speaking" && (
        <div className="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r from-transparent via-cyan-400 to-transparent animate-pulse" />
      )}
    </div>
  );
}

export default function InterviewSimulator() {
  const [phase, setPhase] = useState<Phase>("idle");
  const [qIdx, setQIdx] = useState(0);
  const [messages, setMessages] = useState<Message[]>([]);
  const [userInput, setUserInput] = useState("");
  const [started, setStarted] = useState(false);
  const [timer, setTimer] = useState(0);
  const scrollRef = useRef<HTMLDivElement>(null);

  useEffect(() => {
    if (scrollRef.current) scrollRef.current.scrollTop = scrollRef.current.scrollHeight;
  }, [messages]);

  useEffect(() => {
    let interval: NodeJS.Timeout;
    if (phase === "listening") {
      interval = setInterval(() => setTimer((t) => t + 1), 1000);
    } else {
      setTimer(0);
    }
    return () => clearInterval(interval);
  }, [phase]);

  const startInterview = () => {
    setStarted(true);
    setPhase("speaking");
    setMessages([{ role: "ai", text: questions[0] }]);
    setTimeout(() => setPhase("listening"), 2500);
  };

  const submitAnswer = () => {
    if (!userInput.trim()) return;
    const feedback = feedbacks[Math.floor(Math.random() * feedbacks.length)];
    setMessages((prev) => [...prev,
      { role: "user", text: userInput },
      { role: "ai", text: qIdx + 1 < questions.length ? questions[qIdx + 1] : "Terima kasih! Interview selesai. Skor kamu sangat baik.", feedback },
    ]);
    setUserInput("");
    setQIdx((i) => i + 1);
    setPhase("thinking");
    setTimeout(() => setPhase("speaking"), 1500);
    setTimeout(() => setPhase(qIdx + 1 < questions.length ? "listening" : "feedback"), 3500);
  };

  const phaseLabel: Record<Phase, string> = {
    idle: "Siap Mulai",
    thinking: "AI Berpikir...",
    speaking: "AI Berbicara",
    listening: "Mendengarkan kamu...",
    feedback: "Sesi Selesai",
  };

  const score = Math.min(95, 70 + qIdx * 5);

  return (
    <div className="bg-[#050d1a] border border-white/10 rounded-2xl p-6 space-y-5">
      {/* Header */}
      <div className="flex items-center justify-between">
        <div>
          <p className="text-xs text-cyan-400 tracking-widest uppercase mb-1">// AI Interviewer</p>
          <h2 className="text-white font-bold text-lg">Simulasi Interview</h2>
        </div>
        {started && (
          <div className="flex items-center gap-3">
            <div className="text-center">
              <p className="text-xs text-slate-500">Skor</p>
              <p className="text-lg font-black text-cyan-400">{score}</p>
            </div>
            <div className="text-center">
              <p className="text-xs text-slate-500">Pertanyaan</p>
              <p className="text-lg font-black text-white">{qIdx}/{questions.length}</p>
            </div>
          </div>
        )}
      </div>

      {!started ? (
        /* Start screen */
        <div className="text-center py-10 space-y-6">
          <div className="w-20 h-20 rounded-3xl bg-gradient-to-br from-cyan-400/20 to-blue-600/20 border border-cyan-400/30 flex items-center justify-center mx-auto text-4xl">
            🤖
          </div>
          <div>
            <p className="text-white font-bold mb-2">AI Interviewer Siap</p>
            <p className="text-sm text-slate-400 max-w-xs mx-auto leading-relaxed">
              Akan ada {questions.length} pertanyaan. Jawab dengan suara atau teks. AI akan memberikan feedback real-time.
            </p>
          </div>
          <div className="flex flex-wrap gap-2 justify-center">
            {["🎙️ TTS/STT", "🧠 LLM Claude", "📊 Analisis Real-time", "💬 Feedback Instan"].map((t) => (
              <span key={t} className="text-xs bg-white/5 text-slate-400 px-3 py-1.5 rounded-full">{t}</span>
            ))}
          </div>
          <button
            onClick={startInterview}
            className="bg-gradient-to-r from-cyan-400 to-blue-500 text-[#020810] font-black px-8 py-3 rounded-xl text-sm tracking-widest hover:opacity-90 transition-opacity uppercase"
          >
            Mulai Interview →
          </button>
        </div>
      ) : (
        <>
          {/* AI status bar */}
          <div className="flex items-center gap-3 bg-white/3 border border-white/8 rounded-xl p-3">
            <AvatarAI phase={phase} />
            <div className="flex-1">
              <p className="text-xs text-slate-500 mb-1">{phaseLabel[phase]}</p>
              <Waveform active={phase === "speaking" || phase === "listening"} />
            </div>
            {phase === "listening" && (
              <div className="text-xs font-mono text-emerald-400">
                {String(Math.floor(timer / 60)).padStart(2, "0")}:{String(timer % 60).padStart(2, "0")}
              </div>
            )}
          </div>

          {/* Chat */}
          <div ref={scrollRef} className="space-y-3 max-h-52 overflow-y-auto pr-1">
            {messages.map((m, i) => (
              <div key={i} className={`flex ${m.role === "user" ? "justify-end" : "justify-start"} gap-2`}>
                {m.role === "ai" && <span className="text-base flex-shrink-0 mt-0.5">🤖</span>}
                <div className="max-w-[85%] space-y-1.5">
                  <div className={`text-sm rounded-xl px-3 py-2.5 ${m.role === "ai" ? "bg-white/5 text-slate-200 border border-white/8" : "bg-cyan-400/10 text-white border border-cyan-400/20"}`}>
                    {m.text}
                  </div>
                  {m.feedback && (
                    <div className="text-xs bg-amber-400/10 border border-amber-400/20 text-amber-300 rounded-lg px-3 py-2">
                      💡 {m.feedback}
                    </div>
                  )}
                </div>
              </div>
            ))}
          </div>

          {/* Input area */}
          {phase !== "feedback" && (
            <div className="space-y-2">
              <div className="flex gap-2">
                {/* Mic button */}
                <button
                  className={`w-10 h-10 rounded-xl flex items-center justify-center border transition-all flex-shrink-0 ${phase === "listening" ? "bg-emerald-400/20 border-emerald-400/50 text-emerald-400 animate-pulse" : "bg-white/5 border-white/10 text-slate-500 hover:text-white hover:border-white/30"}`}
                  title="Tekan untuk bicara"
                >
                  🎙️
                </button>
                <input
                  value={userInput}
                  onChange={(e) => setUserInput(e.target.value)}
                  onKeyDown={(e) => e.key === "Enter" && submitAnswer()}
                  placeholder={phase === "listening" ? "Ketik jawaban atau klik 🎙️ untuk bicara..." : "Tunggu pertanyaan AI..."}
                  disabled={phase !== "listening"}
                  className="flex-1 bg-white/5 border border-white/10 text-white text-sm rounded-xl px-4 py-2.5 placeholder:text-slate-600 focus:outline-none focus:border-cyan-400/50 disabled:opacity-40"
                />
                <button
                  onClick={submitAnswer}
                  disabled={!userInput.trim() || phase !== "listening"}
                  className="px-4 py-2.5 bg-cyan-400 text-[#020810] font-bold text-xs rounded-xl disabled:opacity-30 hover:bg-cyan-300 transition-colors"
                >
                  Kirim
                </button>
              </div>
              <p className="text-xs text-slate-600 text-center">
                Pastikan mikrofon aktif untuk mode suara · Didukung Whisper STT + Claude TTS
              </p>
            </div>
          )}

          {phase === "feedback" && (
            <div className="bg-gradient-to-b from-cyan-400/10 to-transparent border border-cyan-400/20 rounded-xl p-4 text-center space-y-2">
              <p className="text-cyan-400 font-bold text-sm">🎉 Interview Selesai!</p>
              <p className="text-white text-2xl font-black">{score}<span className="text-slate-400 text-sm">/100</span></p>
              <p className="text-xs text-slate-400">Performa sangat baik! Lihat laporan lengkap →</p>
              <button onClick={() => { setStarted(false); setMessages([]); setQIdx(0); setPhase("idle"); }}
                className="text-xs text-cyan-400 underline hover:no-underline">
                Ulangi Sesi
              </button>
            </div>
          )}
        </>
      )}
    </div>
  );
}