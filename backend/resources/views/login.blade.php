<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - NEXUS.AI</title>
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <style>
        body {
            background-color: #020810;
        }
    </style>
</head>
<body class="min-h-screen bg-[#020810] flex items-center justify-center px-6 text-white">

<div class="absolute w-[500px] h-[500px] bg-cyan-500/10 blur-[120px] rounded-full"></div>

<div class="relative z-10 w-full max-w-md">
    
    <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl p-8 shadow-xl">
        
        <!-- Logo -->
        <div class="flex items-center gap-2 mb-6 justify-center">
            <div class="w-8 h-8 bg-gradient-to-br from-cyan-400 to-blue-600 rounded-md flex items-center justify-center">
                <span class="text-xs font-black text-white">CV</span>
            </div>
            <span class="font-bold tracking-widest text-sm">
                NEXUS<span class="text-cyan-400">.AI</span>
            </span>
        </div>

        <h2 class="text-2xl font-black text-center mb-6">
            Masuk ke Akun
        </h2>

        <!-- Alert untuk error dari server -->
        @if(session('error'))
        <div class="mb-4 p-3 bg-red-500/20 border border-red-500/50 rounded-lg text-sm text-red-300">
            {{ session('error') }}
        </div>
        @endif

        @if(session('success'))
        <div class="mb-4 p-3 bg-green-500/20 border border-green-500/50 rounded-lg text-sm text-green-300">
            {{ session('success') }}
        </div>
        @endif

        <!-- FORM - TIDAK DIUBAH -->
        <form action="{{ route('login.submit') }}" method="POST" class="space-y-4" id="loginForm">
            @csrf
            
            <input 
                type="email" 
                name="email"
                placeholder="Email" 
                value="{{ old('email') }}"
                class="w-full bg-[#020810] border border-white/10 rounded-lg px-4 py-3 text-sm focus:outline-none focus:border-cyan-400 @error('email') border-red-500 @enderror"
                required
            />
            @error('email')
            <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
            @enderror

            <input 
                type="password" 
                name="password"
                placeholder="Password" 
                class="w-full bg-[#020810] border border-white/10 rounded-lg px-4 py-3 text-sm focus:outline-none focus:border-cyan-400 @error('password') border-red-500 @enderror"
                required
            />
            @error('password')
            <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
            @enderror

            <button 
                type="submit"
                class="w-full bg-cyan-400 text-[#020810] font-bold py-3 rounded-lg text-sm tracking-widest hover:bg-cyan-300 transition"
            >
                LOGIN
            </button>
        </form>

        <!-- Divider -->
        <div class="my-6 text-center text-xs text-slate-500">
            ATAU
        </div>

        <!-- Google -->
        <button class="w-full border border-white/10 py-3 rounded-lg text-sm hover:bg-white/5 transition">
            Login dengan Google
        </button>

        <!-- Footer -->
        <p class="text-xs text-center text-slate-500 mt-6">
            Belum punya akun? 
            <a href="{{ route('register') }}" class="text-cyan-400 hover:underline">
                Daftar
            </a>
        </p>
    </div>
</div>

<script>
    // Tambahan script untuk SweetAlert dari session
    @if(session('success'))
    Swal.fire({
        icon: 'success',
        title: 'Berhasil!',
        text: '{{ session('success') }}',
        background: '#0a0e17',
        color: '#fff',
        confirmButtonColor: '#00d2ff',
        timer: 3000,
        timerProgressBar: true
    });
    @endif

    @if(session('error'))
    Swal.fire({
        icon: 'error',
        title: 'Gagal!',
        text: '{{ session('error') }}',
        background: '#0a0e17',
        color: '#fff',
        confirmButtonColor: '#00d2ff'
    });
    @endif

    @if($errors->any())
    Swal.fire({
        icon: 'error',
        title: 'Validasi Gagal',
        text: '{{ $errors->first() }}',
        background: '#0a0e17',
        color: '#fff',
        confirmButtonColor: '#00d2ff'
    });
    @endif
</script>

</body>
</html>