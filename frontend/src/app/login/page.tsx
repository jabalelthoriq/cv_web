"use client";

import Link from "next/link";
import { useState } from "react";
import { useRouter } from "next/navigation";

export default function LoginPage() {
  const router = useRouter();

  const [email, setEmail] = useState("");
  const [password, setPassword] = useState("");
  const [loading, setLoading] = useState(false);

  const handleLogin = async (e: any) => {
    e.preventDefault(); // 🔥 penting biar tidak reload

    setLoading(true);

    try {
      const res = await fetch("http://127.0.0.1:8000/api/login", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify({
          email,
          password,
        }),
      });

      const data = await res.json();

      if (!res.ok) {
        alert(data.message || "Login gagal");
        return;
      }

      // 🔐 simpan token
      localStorage.setItem("token", data.token);

      // 🚀 redirect dashboard
      router.push("/dashboard");

    } catch (error) {
      console.error(error);
      alert("Server error");
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className="min-h-screen bg-[#020810] flex items-center justify-center px-6 text-white">
      
      <div className="absolute w-[500px] h-[500px] bg-cyan-500/10 blur-[120px] rounded-full" />

      <div className="relative z-10 w-full max-w-md">
        
        <div className="bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl p-8 shadow-xl">
          
          {/* Logo */}
          <div className="flex items-center gap-2 mb-6 justify-center">
            <div className="w-8 h-8 bg-gradient-to-br from-cyan-400 to-blue-600 rounded-md flex items-center justify-center">
              <span className="text-xs font-black text-white">CV</span>
            </div>
            <span className="font-bold tracking-widest text-sm">
              NEXUS<span className="text-cyan-400">.AI</span>
            </span>
          </div>

          <h2 className="text-2xl font-black text-center mb-6">
            Masuk ke Akun
          </h2>

          {/* FORM */}
          <form onSubmit={handleLogin} className="space-y-4">
            
            <input
              type="email"
              placeholder="Email"
              value={email}
              onChange={(e) => setEmail(e.target.value)}
              className="w-full bg-[#020810] border border-white/10 rounded-lg px-4 py-3 text-sm focus:outline-none focus:border-cyan-400"
            />

            <input
              type="password"
              placeholder="Password"
              value={password}
              onChange={(e) => setPassword(e.target.value)}
              className="w-full bg-[#020810] border border-white/10 rounded-lg px-4 py-3 text-sm focus:outline-none focus:border-cyan-400"
            />

            <button
              type="submit"
              disabled={loading}
              className="w-full bg-cyan-400 text-[#020810] font-bold py-3 rounded-lg text-sm tracking-widest hover:bg-cyan-300 transition disabled:opacity-50"
            >
              {loading ? "Loading..." : "LOGIN"}
            </button>
          </form>

          {/* Divider */}
          <div className="my-6 text-center text-xs text-slate-500">
            ATAU
          </div>

          {/* Google (belum aktif) */}
          <button className="w-full border border-white/10 py-3 rounded-lg text-sm hover:bg-white/5 transition">
            Login dengan Google
          </button>

          {/* Footer */}
          <p className="text-xs text-center text-slate-500 mt-6">
            Belum punya akun?{" "}
            <Link href="/register" className="text-cyan-400 hover:underline">
              Daftar
            </Link>
          </p>
        </div>
      </div>
    </div>
  );
}