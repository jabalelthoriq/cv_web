<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Register - NEXUS.AI</title>
    
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

<!-- Glow -->
<div class="absolute w-[500px] h-[500px] bg-blue-500/10 blur-[120px] rounded-full"></div>

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
            Buat Akun Baru
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
        <form action="{{ route('register.submit') }}" method="POST" class="space-y-4">
            @csrf

            <input 
                type="text" 
                name="name"
                placeholder="Nama Lengkap" 
                value="{{ old('name') }}"
                class="w-full bg-[#020810] border border-white/10 rounded-lg px-4 py-3 text-sm focus:outline-none focus:border-cyan-400 @error('name') border-red-500 @enderror"
                required
            />
            @error('name')
            <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
            @enderror

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

            <input 
                type="password" 
                name="password_confirmation"
                placeholder="Konfirmasi Password" 
                class="w-full bg-[#020810] border border-white/10 rounded-lg px-4 py-3 text-sm focus:outline-none focus:border-cyan-400"
                required
            />

            <button 
                type="submit"
                class="w-full bg-gradient-to-r from-cyan-400 to-blue-500 text-[#020810] font-bold py-3 rounded-lg text-sm tracking-widest hover:opacity-90 transition"
            >
                REGISTER
            </button>
        </form>

        <!-- Footer -->
        <p class="text-xs text-center text-slate-500 mt-6">
            Sudah punya akun? 
            <a href="{{ route('login') }}" class="text-cyan-400 hover:underline">
                Masuk
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