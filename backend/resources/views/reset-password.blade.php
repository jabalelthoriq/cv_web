<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Reset Password - CareerSense</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;600;700;800&display=swap');
        * { font-family:'Space Grotesk',sans-serif; }
        body { background:#020810; color:#fff; }
        .auth-page { min-height:100vh; display:grid; place-items:center; padding:28px; background:radial-gradient(circle at 80% 20%, rgba(0,212,255,.16), transparent 32%), #020810; }
        .auth-card { width:min(460px,100%); padding:30px; border:1px solid rgba(0,212,255,.16); border-radius:24px; background:rgba(255,255,255,.045); box-shadow:0 28px 80px rgba(0,212,255,.1); backdrop-filter:blur(18px); }
        .input { width:100%; padding:13px 14px; border:1px solid rgba(0,212,255,.18); border-radius:12px; background:rgba(2,8,16,.82); color:#fff; outline:0; }
        .input:focus { border-color:#00d4ff; box-shadow:0 0 0 3px rgba(0,212,255,.12); }
        .button { display:flex; align-items:center; justify-content:center; width:100%; padding:13px 15px; border-radius:12px; font-weight:900; background:linear-gradient(135deg,#00d4ff,#3b82f6); color:#020810; }
    </style>
</head>
<body>
<main class="auth-page">
    <section class="auth-card">
        <div class="mb-7 text-center">
            <span class="mx-auto grid h-11 w-11 place-items-center rounded-2xl font-black" style="background:linear-gradient(135deg,#00d4ff,#3b82f6);">C</span>
            <h1 class="mt-4 text-3xl font-black">Reset Password</h1>
            <p class="mt-2 text-sm text-slate-400">Buat password baru untuk akun CareerSense Anda.</p>
        </div>
        @if(session('error'))<div class="mb-4 rounded-xl border border-red-500/40 bg-red-500/10 p-3 text-sm text-red-200">{{ session('error') }}</div>@endif
        @if($errors->any())<div class="mb-4 rounded-xl border border-red-500/40 bg-red-500/10 p-3 text-sm text-red-200">{{ $errors->first() }}</div>@endif
        <form action="{{ route('password.update') }}" method="POST" class="grid gap-4">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            <input class="input" type="email" name="email" placeholder="Email akun" value="{{ old('email', $email) }}" required>
            <input class="input" type="password" name="password" placeholder="Password baru" required>
            <input class="input" type="password" name="password_confirmation" placeholder="Konfirmasi password baru" required>
            <button class="button" type="submit">Reset Password</button>
        </form>
    </section>
</main>
</body>
</html>
