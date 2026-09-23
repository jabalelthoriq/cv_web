<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Masuk - CareerSense</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;600;700;800&display=swap');
        * { font-family: 'Space Grotesk', sans-serif; }
        body { background:#020810; color:#fff; }
        .auth-shell { min-height:100vh; display:grid; grid-template-columns:1.08fr .92fr; }
        .auth-brand { display:flex; flex-direction:column; justify-content:space-between; padding:46px 8vw 46px 7vw; background:radial-gradient(circle at 78% 18%, rgba(0,212,255,.24), transparent 26%), linear-gradient(140deg,#03101f,#061d36 58%,#020810); }
        .auth-brand h1 { font-size:52px; line-height:1.08; letter-spacing:-2px; margin:28px 0 14px; }
        .auth-card { display:grid; place-items:center; padding:34px; background:radial-gradient(circle at 30% 20%, rgba(0,212,255,.11), transparent 30%), #020810; }
        .auth-form { width:min(440px,100%); padding:30px; border:1px solid rgba(0,212,255,.16); border-radius:24px; background:rgba(255,255,255,.045); box-shadow:0 28px 80px rgba(0,212,255,.09); backdrop-filter:blur(18px); }
        .input { width:100%; padding:13px 14px; border:1px solid rgba(0,212,255,.18); border-radius:12px; background:rgba(2,8,16,.82); color:#fff; outline:0; }
        .input:focus { border-color:#00d4ff; box-shadow:0 0 0 3px rgba(0,212,255,.12); }
        .button { display:flex; align-items:center; justify-content:center; gap:10px; width:100%; padding:13px 15px; border-radius:12px; font-weight:900; transition:.2s; }
        .primary { background:linear-gradient(135deg,#00d4ff,#3b82f6); color:#020810; }
        .google { border:1px solid rgba(255,255,255,.12); background:rgba(255,255,255,.04); color:#fff; }
        .back-link { display:inline-flex; align-items:center; gap:8px; margin-bottom:18px; color:#7dd3fc; font-size:13px; font-weight:800; text-decoration:none; }
        .back-link:hover { color:#fff; }
        .google-logo { width:20px; height:20px; flex:0 0 auto; }
        @media(max-width:860px){ .auth-shell{grid-template-columns:1fr}.auth-brand{display:none}.auth-card{min-height:100vh} }
    </style>
</head>
<body>
<main class="auth-shell">
    <section class="auth-brand">
        <a href="/" class="flex items-center gap-3 text-white no-underline">
            <span class="grid h-12 w-12 place-items-center rounded-2xl font-black" style="background:linear-gradient(135deg,#00d4ff,#3b82f6); box-shadow:0 0 24px rgba(0,212,255,.35);">C</span>
            <span class="text-2xl font-black">Career<span style="color:#00d4ff;">Sense</span><small class="block text-[8px] tracking-[.28em] text-cyan-300">WORKSPACE</small></span>
        </a>
        <div>
            <span class="text-[11px] font-black uppercase tracking-[.22em]" style="color:#00d4ff;">AI Career Intelligence</span>
            <h1>Masuk ke dashboard karier yang lebih cerdas.</h1>
            <p class="max-w-xl text-slate-300 leading-7">Analisis CV, rekomendasi lowongan, dan simulasi interview dalam satu workspace gelap neon cyan.</p>
        </div>
        <div class="grid gap-3 text-sm text-cyan-100">
            <span>✓ Login Google siap untuk akun terverifikasi</span>
            <span>✓ Free tetap masuk dashboard, fitur premium terkunci rapi</span>
        </div>
    </section>
    <section class="auth-card">
        <div class="auth-form">
            <a href="{{ route('home') }}" class="back-link">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M19 12H5"/>
                    <path d="M12 19l-7-7 7-7"/>
                </svg>
                Kembali ke landing page
            </a>
            <div class="mb-7 text-center">
                <span class="mx-auto grid h-11 w-11 place-items-center rounded-2xl font-black" style="background:linear-gradient(135deg,#00d4ff,#3b82f6);">C</span>
                <h2 class="mt-4 text-3xl font-black">Masuk</h2>
                <p class="mt-1 text-sm text-slate-400">Gunakan email atau akun Google.</p>
            </div>
            @if(session('error'))<div class="mb-4 rounded-xl border border-red-500/40 bg-red-500/10 p-3 text-sm text-red-200">{{ session('error') }}</div>@endif
            @if(session('success'))<div class="mb-4 rounded-xl border border-emerald-500/40 bg-emerald-500/10 p-3 text-sm text-emerald-200">{{ session('success') }}</div>@endif
            <form action="{{ route('login.submit') }}" method="POST" class="grid gap-4">
                @csrf
                <input class="input @error('email') border-red-500 @enderror" type="email" name="email" placeholder="Email" value="{{ old('email') }}" required>
                <input class="input @error('password') border-red-500 @enderror" type="password" name="password" placeholder="Password" required>
                <div class="text-right">
                    <a href="{{ route('password.request') }}" class="text-xs font-bold text-cyan-400 hover:text-white">Lupa password?</a>
                </div>
                <button class="button primary" type="submit">MASUK</button>
            </form>
            <div class="my-5 text-center text-xs text-slate-500">ATAU</div>
            <a href="{{ route('auth.google.redirect') }}" class="button google">
                <svg class="google-logo" viewBox="0 0 24 24" aria-hidden="true">
                    <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                    <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                    <path fill="#FBBC05" d="M5.84 14.1c-.22-.66-.35-1.36-.35-2.1s.13-1.44.35-2.1V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l3.66-2.84z"/>
                    <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06L5.84 9.9C6.71 7.31 9.14 5.38 12 5.38z"/>
                </svg>
                Lanjutkan dengan Google
            </a>
            <p class="mt-6 text-center text-sm text-slate-500">Belum punya akun? <a href="{{ route('register') }}" class="font-bold text-cyan-400">Daftar</a></p>
        </div>
    </section>
</main>
@if($errors->any())
<script>Swal.fire({icon:'error',title:'Validasi Gagal',text:@json($errors->first()),background:'#07111f',color:'#fff',confirmButtonColor:'#00d4ff'});</script>
@endif
</body>
</html>
