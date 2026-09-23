<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="user-id" content="{{ auth()->id() }}">
    <title>Dashboard - CareerSense</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script
        src="https://app.sandbox.midtrans.com/snap/snap.js"
        data-client-key="{{ config('midtrans.client_key') }}">
    </script>
    <!-- Di dalam <head> atau sebelum closing body -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
    
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700;800&display=swap');
        
        * {
            font-family: 'Space Grotesk', sans-serif;
        }
        
        /* FIX: Pastikan semua teks di jobsView terlihat */
        #jobsView,
        #jobsView * {
            color: white !important;
        }
        
        #jobsView .text-slate-400 {
            color: #94a3b8 !important;
        }
        
        #jobsView .bg-white\/10 {
            background-color: rgba(255, 255, 255, 0.1) !important;
        }
        
        #jobsView .border-white\/10 {
            border-color: rgba(255, 255, 255, 0.1) !important;
        }
        
        ::-webkit-scrollbar {
            width: 4px;
        }
        
        ::-webkit-scrollbar-track {
            background: transparent;
        }
        
        ::-webkit-scrollbar-thumb {
            background: rgba(0,212,255,0.15);
            border-radius: 4px;
        }

        :root {
            --cv-preview-height: 880px;
        }

        .cv-preview-grid {
            align-items: start;
        }

        .cv-pdf-panel,
        .cv-analysis-panel {
            height: var(--cv-preview-height);
        }

        .cv-pdf-panel {
            display: flex;
            flex-direction: column;
        }

        .cv-pdf-canvas {
            flex: 1 1 auto;
            min-height: 0;
        }

        .cv-analysis-panel {
            overflow-y: auto;
            padding-right: 6px;
            scrollbar-width: thin;
            scrollbar-color: rgba(0, 212, 255, 0.28) transparent;
        }

        .cv-analysis-panel::-webkit-scrollbar {
            width: 5px;
        }

        .cv-analysis-panel::-webkit-scrollbar-thumb {
            background: rgba(0, 212, 255, 0.28);
            border-radius: 999px;
        }

        @media (max-width: 1023px) {
            .cv-pdf-panel,
            .cv-analysis-panel {
                height: auto;
                max-height: none;
            }

            .cv-analysis-panel {
                overflow: visible;
                padding-right: 0;
            }
        }

        .app-sidebar {
            position: fixed;
            left: 18px;
            top: 18px;
            z-index: 50;
            width: 82px;
            height: calc(100vh - 36px);
            overflow: hidden;
            border: 1px solid rgba(0,212,255,0.18);
            border-radius: 27px;
            background: linear-gradient(180deg, rgba(5,16,30,0.96) 0%, rgba(2,8,15,0.98) 100%);
            box-shadow: 0 18px 55px rgba(0,212,255,0.12), inset 0 1px 0 rgba(255,255,255,0.05);
            backdrop-filter: blur(20px);
            transition: width .45s ease;
            padding: 11px;
        }

        .app-sidebar:hover {
            width: 275px;
        }

        .sidebar-logo,
        .nav-btn {
            display: flex;
            align-items: center;
            width: 100%;
            border-radius: 17px;
            text-align: left;
            transition: .25s;
        }

        .sidebar-logo {
            margin-bottom: 26px;
            text-decoration: none;
        }

        .sidebar-logo-mark,
        .nav-icon {
            display: grid;
            min-width: 58px;
            height: 58px;
            place-items: center;
        }

        .sidebar-logo-mark {
            border-radius: 17px;
            background: linear-gradient(135deg, #00d4ff, #3b82f6);
            color: #fff;
            font-size: 25px;
            font-weight: 900;
            box-shadow: 0 0 22px rgba(0,212,255,0.33), 0 8px 20px rgba(59,130,246,0.18);
        }

        .sidebar-label {
            overflow: hidden;
            margin-left: 11px;
            white-space: nowrap;
            opacity: 0;
            transition: opacity .25s ease;
        }

        .app-sidebar:hover .sidebar-label {
            opacity: 1;
        }

        .sidebar-brand {
            color: #e6fbff;
            font-size: 21px;
            font-weight: 900;
            letter-spacing: -1px;
            line-height: 1;
        }

        .sidebar-brand b {
            color: #00d4ff;
        }

        .sidebar-brand small {
            display: block;
            margin-top: 4px;
            color: #00d4ff;
            font-size: 7px;
            letter-spacing: 2px;
        }

        .sidebar-section-label {
            overflow: hidden;
            height: 18px;
            margin: 0 0 7px 69px;
            color: rgba(148,163,184,0.7);
            font-size: 9px;
            font-weight: 900;
            letter-spacing: .14em;
            text-transform: uppercase;
            white-space: nowrap;
            opacity: 0;
            transition: opacity .25s ease;
        }

        .app-sidebar:hover .sidebar-section-label {
            opacity: 1;
        }

        .nav-btn {
            height: 54px;
            gap: 0;
            padding: 0;
            border: 0 !important;
            background: transparent;
            color: rgba(255,255,255,0.48);
            font-size: 13px;
            font-weight: 800;
        }

        .nav-btn:hover {
            background: rgba(0,212,255,0.09) !important;
            color: #00d4ff !important;
            box-shadow: inset 0 0 0 1px rgba(0,212,255,0.13);
        }

        .nav-btn.active {
            background: linear-gradient(135deg, rgba(0,212,255,0.18), rgba(59,130,246,0.13)) !important;
            color: #fff !important;
            box-shadow: 0 0 18px rgba(0,212,255,0.16), inset 0 0 0 1px rgba(0,212,255,0.32);
        }

        .nav-icon {
            height: 54px;
        }

        .dashboard-shell {
            width: calc(100% - 156px);
            margin-left: 118px;
            margin-right: 38px;
        }

        .sidebar-user-card {
            overflow: hidden;
            background: rgba(255,255,255,0.04);
            border: 1px solid rgba(0,212,255,0.14);
            color: #e6fbff;
        }

        .cyber-panel {
            background: linear-gradient(145deg, #07111f 0%, #030810 100%);
            border: 1px solid rgba(0,212,255,0.12);
            border-radius: 18px;
            box-shadow: 0 20px 55px rgba(0,0,0,0.2);
        }

        .cyber-input {
            width: 100%;
            border: 1px solid rgba(0,212,255,0.16);
            border-radius: 12px;
            background: rgba(2,8,16,0.82);
            color: #fff;
            padding: 12px 14px;
            outline: none;
        }

        .cyber-modal-backdrop {
            position: fixed;
            inset: 0;
            z-index: 90;
            display: none;
            place-items: center;
            padding: 22px;
            background: rgba(2,8,16,0.72);
            backdrop-filter: blur(10px);
        }

        .cyber-modal-backdrop.show {
            display: grid;
        }

        .plan-card {
            background: rgba(255,255,255,0.035);
            border: 1px solid rgba(0,212,255,0.14);
            border-radius: 16px;
            padding: 18px;
        }

        .admin-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
        }

        .admin-table th {
            color: rgba(148,163,184,0.85);
            font-size: 10px;
            letter-spacing: .12em;
            text-align: left;
            text-transform: uppercase;
            padding: 12px;
        }

        .admin-table td {
            border-top: 1px solid rgba(255,255,255,0.06);
            padding: 12px;
            color: rgba(255,255,255,0.78);
        }

        .admin-field {
            width: 100%;
            min-width: 118px;
            border: 1px solid rgba(0,212,255,0.16);
            border-radius: 10px;
            background: rgba(2,8,16,0.78);
            color: #e6fbff;
            padding: 9px 10px;
            outline: none;
        }

        .admin-save {
            border-radius: 10px;
            background: linear-gradient(135deg,#00d4ff,#2563eb);
            color: #020810;
            font-size: 11px;
            font-weight: 900;
            padding: 10px 12px;
            white-space: nowrap;
            box-shadow: 0 0 18px rgba(0,212,255,0.2);
        }

        .admin-tabbar {
            display: inline-flex;
            gap: 6px;
            padding: 6px;
            border: 1px solid rgba(0,212,255,0.14);
            border-radius: 14px;
            background: rgba(2,8,16,0.65);
        }

        .admin-tab-btn {
            border-radius: 10px;
            color: #94a3b8;
            font-size: 12px;
            font-weight: 900;
            padding: 10px 16px;
            transition: .2s;
        }

        .admin-tab-btn.active {
            background: linear-gradient(135deg, rgba(0,212,255,0.18), rgba(59,130,246,0.16));
            color: #e6fbff;
            box-shadow: inset 0 0 0 1px rgba(0,212,255,0.32), 0 0 18px rgba(0,212,255,0.12);
        }

        .admin-tab-panel {
            display: none;
        }

        .admin-tab-panel.active {
            display: block;
        }

        .line-chart-wrap {
            height: 260px;
            border: 1px solid rgba(0,212,255,0.1);
            border-radius: 16px;
            background: radial-gradient(circle at 18% 14%, rgba(0,212,255,0.1), transparent 30%), rgba(2,8,16,0.45);
            padding: 14px;
        }

        .line-chart {
            width: 100%;
            height: 100%;
            overflow: visible;
        }

        .premium-badge {
            display: inline-flex;
            align-items: center;
            border-radius: 999px;
            background: rgba(0,212,255,0.12);
            color: #67e8f9;
            font-size: 9px;
            font-weight: 900;
            letter-spacing: .12em;
            padding: 3px 7px;
            text-transform: uppercase;
        }

        .chart-legend {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            color: #94a3b8;
            font-size: 11px;
            font-weight: 800;
        }

        .legend-dot {
            display: inline-block;
            width: 9px;
            height: 9px;
            margin-right: 6px;
            border-radius: 999px;
            box-shadow: 0 0 12px currentColor;
        }

        @media (max-width: 640px) {
            .app-sidebar {
                left: 8px;
                top: 8px;
                width: 68px;
                height: calc(100vh - 16px);
                border-radius: 22px;
                padding: 7px;
            }

            .app-sidebar:hover {
                width: 252px;
            }

            .sidebar-logo-mark,
            .nav-icon {
                min-width: 52px;
                height: 52px;
            }

            .nav-btn {
                height: 52px;
            }

            .dashboard-shell {
                width: calc(100% - 104px);
                margin-left: 84px;
                margin-right: 20px;
            }
        }
    </style>
</head>
<body class="min-h-screen" style="background: #03080f; color: #ffffff;">

<!-- Background Effects -->
<div class="fixed inset-0 pointer-events-none z-0" style="
    background: radial-gradient(ellipse 700px 500px at 20% -5%, rgba(0,212,255,0.055) 0%, transparent 60%),
                radial-gradient(ellipse 500px 500px at 95% 85%, rgba(59,130,246,0.045) 0%, transparent 60%),
                radial-gradient(ellipse 350px 350px at 65% 55%, rgba(129,140,248,0.025) 0%, transparent 60%);
"></div>

<!-- Main Container -->
<div class="flex min-h-screen relative z-10">

    <!-- Sidebar -->
    <aside id="sidebar" class="app-sidebar flex flex-col">
        @php
            $user = Auth::user();
            $plan = $user->role === 'admin' ? 'admin' : $user->currentPlan();
        @endphp
        <!-- Logo -->
        <a class="sidebar-logo" href="/dashboard">
            <span class="sidebar-logo-mark">C</span>
            <span class="sidebar-label sidebar-brand">Career<b>Sense</b><small>WORKSPACE</small></span>
        </a>

        <!-- Menu Utama -->
        <p class="sidebar-section-label">Menu Utama</p>
        <div class="space-y-0.5">
            @php
                $mainNav = [
                    ['id' => 'dashboard', 'icon' => 'M3 12h18M12 3v18', 'label' => 'Dashboard'],
                    ['id' => 'cv', 'icon' => 'M4 4h16v16H4zM8 8h8M8 12h6M8 16h4', 'label' => 'Analisis CV'],
                    ['id' => 'jobs', 'icon' => 'M20 7h-4.5L15 4H9L8.5 7H4v13h16V7zM12 17a3 3 0 100-6 3 3 0 000 6z', 'label' => 'Lowongan', 'badge' => 'Plus'],
                    ['id' => 'interview', 'icon' => 'M12 2a3 3 0 00-3 3v7a3 3 0 006 0V5a3 3 0 00-3-3zM19 10v3a7 7 0 01-14 0v-3M12 19v3', 'label' => 'Simulasi Interview', 'badge' => 'Pro'],
                ];
            @endphp
            @foreach($mainNav as $nav)
            <button onclick="changeTab('{{ $nav['id'] }}')" data-nav="{{ $nav['id'] }}" class="nav-btn relative">
                <span class="nav-icon">
                    <svg width="21" height="21" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="{{ $nav['icon'] }}"/>
                    </svg>
                </span>
                <span class="sidebar-label flex-1">{{ $nav['label'] }}</span>
                @if(!empty($nav['badge']))
                    <span class="sidebar-label premium-badge mr-3">{{ $nav['badge'] }}</span>
                @endif
            </button>
            @endforeach
        </div>

        <div class="my-4" style="height: 1px; background: rgba(0,212,255,0.12);"></div>

        <!-- Pengembangan -->
        <p class="sidebar-section-label">Pengembangan</p>
        <div class="space-y-0.5">
            @php
                $devNav = [
                    ['id' => 'certs', 'icon' => 'M12 2l3 4.5 5 .5-3.5 3.5 1 5-5.5-2-5.5 2 1-5L4 7l5-.5L12 2z', 'label' => 'Sertifikasi'],
                    ['id' => 'network', 'icon' => 'M3 12h3l3-9 3 18 3-9h3', 'label' => 'Jaringan'],
                ];
                if (($user->role ?? 'user') === 'admin') {
                    $devNav[] = ['id' => 'admin', 'icon' => 'M12 2l8 4v6c0 5-3.5 9-8 10-4.5-1-8-5-8-10V6l8-4zM9 12l2 2 4-5', 'label' => 'Admin'];
                }
            @endphp
            @foreach($devNav as $nav)
            <button onclick="changeTab('{{ $nav['id'] }}')" data-nav="{{ $nav['id'] }}" class="nav-btn relative">
                <span class="nav-icon">
                    <svg width="21" height="21" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="{{ $nav['icon'] }}"/>
                    </svg>
                </span>
                <span class="sidebar-label flex-1">{{ $nav['label'] }}</span>
            </button>
            @endforeach
        </div>

        <!-- Bottom -->
        <div class="mt-auto space-y-0.5">
            @php
                $sysNav = [
                    ['id' => 'settings', 'icon' => 'M12 15a3 3 0 100-6 3 3 0 000 6zM19.4 15a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H5.78a1.65 1.65 0 0 0-1.51 1 1.65 1.65 0 0 0 .33 1.82l.03.03A10 10 0 0 0 12 17.66a10 10 0 0 0 6.37-2.63zM12 2v4', 'label' => 'Pengaturan'],
                ];
            @endphp
            @foreach($sysNav as $nav)
            <button onclick="changeTab('{{ $nav['id'] }}')" data-nav="{{ $nav['id'] }}" class="nav-btn relative">
                <span class="nav-icon">
                    <svg width="21" height="21" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="{{ $nav['icon'] }}"/>
                    </svg>
                </span>
                <span class="sidebar-label flex-1">{{ $nav['label'] }}</span>
            </button>
            @endforeach

            <div class="mt-3 pt-3" style="border-top: 1px solid rgba(0,212,255,0.12);">
                <button type="button" onclick="openPlanModal()" class="sidebar-user-card flex items-center rounded-xl w-full text-left">
<div class="nav-icon text-[12px] font-bold text-white flex-shrink-0">
    <span class="w-9 h-9 rounded-xl flex items-center justify-center" style="background: linear-gradient(135deg, #00d4ff, #3b82f6); box-shadow: 0 0 14px rgba(0,212,255,0.25);">
    {{ strtoupper(substr($user->name ?? 'User', 0, 2)) }}
    </span>
</div>

<div class="sidebar-label min-w-0">
    <p class="text-[12px] font-bold leading-none truncate" style="color: #e6fbff;">
        {{ $user->name ?? 'User' }}
    </p>

    <span class="inline-block mt-1 text-[9px] px-1.5 py-px rounded font-bold"
          style="background: rgba(0,212,255,0.1); color: #00d4ff; border: 1px solid rgba(0,212,255,0.2);">
        {{ strtoupper($plan) }}
    </span>
</div>
                </button>
            </div>
        </div>
    </aside>

    <!-- Main Content -->
    <div class="dashboard-shell flex flex-col min-w-0">
        <!-- Topbar -->
        <header id="topbar" class="flex items-center justify-between px-6 h-[56px] sticky top-0 z-10 gap-4" style="
            background: rgba(3,8,15,0.9);
            border-bottom: 1px solid rgba(255,255,255,0.06);
            backdrop-filter: blur(20px);
        ">
            <div class="flex items-center gap-3">
                <div class="w-[3px] h-5 rounded-full flex-shrink-0" style="background: linear-gradient(to bottom, #00d4ff, #3b82f6);"></div>
                <h1 id="pageTitle" class="text-[14px] font-bold text-white" style="font-family: 'Space Grotesk', sans-serif; letter-spacing: -0.3px;">Dashboard</h1>
            </div>

            <div class="flex items-center gap-3">

    <!-- Kembali ke Home -->
    <a href="/" 
       class="btn-secondary flex items-center gap-2 text-sm px-4 py-2">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M15 18l-6-6 6-6"/>
        </svg>
        <span>Home</span>
    </a>

    <!-- Logout -->
    <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button type="submit"
            class="flex items-center gap-2 text-sm px-4 py-2 rounded-lg transition-all duration-200"
            style="
                background: rgba(255, 80, 80, 0.08);
                border: 1px solid rgba(255, 80, 80, 0.25);
                color: #ff6b6b;
            "
            onmouseenter="this.style.background='rgba(255,80,80,0.18)';this.style.boxShadow='0 0 12px rgba(255,80,80,0.25)'"
            onmouseleave="this.style.background='rgba(255,80,80,0.08)';this.style.boxShadow='none'"
        >
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                <path d="M16 17l5-5-5-5"/>
                <path d="M21 12H9"/>
            </svg>
            <span>Logout</span>
        </button>
    </form>

</div>
        </header>

        <!-- Main Content Area -->
        <main id="mainContent" class="flex-1 p-5 overflow-y-auto">
            @php
                $firstValidationError = isset($errors) && $errors->any() ? $errors->first() : null;
            @endphp
            @if(session('success'))
                <div class="mb-4 rounded-xl border border-emerald-500/30 bg-emerald-500/10 p-4 text-sm text-emerald-100">{{ session('success') }}</div>
            @endif
            @if(session('error') || $firstValidationError)
                <div class="mb-4 rounded-xl border border-red-500/30 bg-red-500/10 p-4 text-sm text-red-100">{{ session('error') ?? $firstValidationError }}</div>
            @endif
            <!-- Dashboard Content -->
            <div id="dashboardView" class="space-y-4">
                @include('partials.dashboard_content')
            </div>
            <div id="jobsView" style="display: none;">@include('partials.jobs_content')</div>
   
        
            <div id="interviewView" style="display: none;">@include('partials.interview_content')</div>
            <div id="certsView" style="display: none;">@include('partials.placeholder', ['icon' => '🏆', 'message' => 'Fitur sertifikasi segera hadir'])</div>
            <div id="networkView" style="display: none;">@include('partials.placeholder', ['icon' => '🌐', 'message' => 'Fitur jaringan segera hadir'])</div>
            <div id="settingsView" style="display: none;">
                <div class="grid lg:grid-cols-2 gap-5">
                    <section class="cyber-panel p-6">
                        <span class="text-[10px] font-bold uppercase tracking-[0.18em]" style="color:#00d4ff;">Pengaturan Akun</span>
                        <h2 class="text-2xl font-black mt-2">Profil CareerSense</h2>
                        <p class="text-sm text-slate-400 mt-1">Edit identitas akun yang dipakai di workspace Anda.</p>
                        <form method="POST" action="{{ route('profile.update') }}" class="grid gap-4 mt-6">
                            @csrf
                            @method('PATCH')
                            <label class="text-xs font-bold text-slate-400 uppercase tracking-widest">Nama</label>
                            <input class="cyber-input" name="name" value="{{ old('name', $user->name) }}" required>
                            <label class="text-xs font-bold text-slate-400 uppercase tracking-widest">Email</label>
                            <input class="cyber-input" type="email" name="email" value="{{ old('email', $user->email) }}" required>
                            <label class="text-xs font-bold text-slate-400 uppercase tracking-widest">Role</label>
                            <input class="cyber-input" value="{{ strtoupper($user->role) }}" readonly>
                            <button class="mt-2 rounded-xl py-3 text-sm font-black" style="background: linear-gradient(135deg,#00d4ff,#3b82f6); color:#020810;">
                                Simpan Perubahan Profil
                            </button>
                        </form>
                        <form method="POST" action="{{ route('password.email') }}" class="mt-4">
                            @csrf
                            <input type="hidden" name="email" value="{{ $user->email }}">
                            <button type="submit" class="w-full rounded-xl py-3 text-sm font-black" style="background: rgba(0,212,255,0.1); border:1px solid rgba(0,212,255,0.24); color:#67e8f9;">
                                Kirim Link Reset Password ke Email
                            </button>
                            <p class="mt-2 text-xs text-slate-500">Link reset memakai token Laravel dan dikirim lewat mailer. Untuk production, gunakan SMTP/Resend/Postmark di `.env`.</p>
                        </form>
                    </section>
                    <section class="cyber-panel p-6">
                        <span class="text-[10px] font-bold uppercase tracking-[0.18em]" style="color:#00d4ff;">Subscription</span>
                        <h2 class="text-2xl font-black mt-2">{{ strtoupper($plan) }} Plan</h2>
                        <p class="text-sm text-slate-400 mt-1">Plan aktif menentukan akses dashboard, analisis CV, lowongan, dan simulasi interview.</p>
                        <button type="button" onclick="openPlanModal()" class="mt-6 w-full rounded-xl py-3 text-sm font-black" style="background: linear-gradient(135deg,#00d4ff,#3b82f6); color:#020810;">
                            Lihat Semua Plan
                        </button>
                    </section>
                </div>
            </div>
            @if(($user->role ?? 'user') === 'admin')
            <div id="adminView" style="display: none;">
                <div class="space-y-5">
                    <div class="grid md:grid-cols-4 gap-4">
                        @foreach([
                            ['label' => 'Total User', 'value' => $adminStats['users'] ?? 0],
                            ['label' => 'Pembayaran', 'value' => $adminStats['payments'] ?? 0],
                            ['label' => 'Paid', 'value' => $adminStats['paid'] ?? 0],
                            ['label' => 'Subscription Aktif', 'value' => $adminStats['active_subscriptions'] ?? 0],
                        ] as $stat)
                        <div class="cyber-panel p-5">
                            <p class="text-[10px] uppercase tracking-[0.18em] text-slate-500 font-bold">{{ $stat['label'] }}</p>
                            <strong class="block mt-2 text-3xl font-black" style="color:#00d4ff;">{{ $stat['value'] }}</strong>
                        </div>
                        @endforeach
                    </div>
                    <div class="grid xl:grid-cols-2 gap-5">
                        <section class="cyber-panel p-6">
                            <div class="flex items-start justify-between gap-4 mb-5">
                                <div>
                                    <span class="text-[10px] font-bold uppercase tracking-[0.18em]" style="color:#00d4ff;">Grafik 12 Bulan</span>
                                    <h2 class="text-xl font-black mt-2">Status Transaksi Plan</h2>
                                </div>
                                <div class="chart-legend">
                                    <span><i class="legend-dot" style="color:#94a3b8; background:#94a3b8;"></i>Free</span>
                                    <span><i class="legend-dot" style="color:#00d4ff; background:#00d4ff;"></i>Plus</span>
                                    <span><i class="legend-dot" style="color:#8b5cf6; background:#8b5cf6;"></i>Pro</span>
                                </div>
                            </div>
                            <div class="line-chart-wrap">
                                <svg id="planLineChart" class="line-chart" viewBox="0 0 520 240" role="img" aria-label="Grafik transaksi plan tahunan"></svg>
                            </div>
                        </section>
                        <section class="cyber-panel p-6">
                            <div class="flex items-start justify-between gap-4 mb-5">
                                <div>
                                    <span class="text-[10px] font-bold uppercase tracking-[0.18em]" style="color:#00d4ff;">Grafik 12 Bulan</span>
                                    <h2 class="text-xl font-black mt-2">User Login</h2>
                                </div>
                                <div class="chart-legend">
                                    <span><i class="legend-dot" style="color:#22d3ee; background:#22d3ee;"></i>Login</span>
                                </div>
                            </div>
                            <div class="line-chart-wrap">
                                <svg id="loginLineChart" class="line-chart" viewBox="0 0 520 240" role="img" aria-label="Grafik user login tahunan"></svg>
                            </div>
                        </section>
                    </div>
                    <section class="cyber-panel p-6 overflow-auto">
                        <div class="mb-5 flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
                            <div>
                                <span class="text-[10px] font-bold uppercase tracking-[0.18em]" style="color:#00d4ff;">Editable</span>
                                <h2 class="text-xl font-black mt-2">Info Admin</h2>
                            </div>
                            <div class="admin-tabbar" role="tablist" aria-label="Info admin">
                                <button type="button" class="admin-tab-btn active" data-admin-tab="users" onclick="switchAdminInfoTab('users')">User</button>
                                <button type="button" class="admin-tab-btn" data-admin-tab="payments" onclick="switchAdminInfoTab('payments')">Pembayaran</button>
                            </div>
                        </div>

                        <div id="adminUsersPanel" class="admin-tab-panel active">
                            <table class="admin-table">
                                <thead><tr><th>Nama</th><th>Email</th><th>Role</th><th>Plan</th><th>Aksi</th></tr></thead>
                                <tbody>
                                @foreach($adminUsers as $item)
                                    <form id="userForm{{ $item->id }}" method="POST" action="{{ route('admin.users.update', $item) }}">
                                        @csrf
                                        @method('PATCH')
                                    </form>
                                    <tr>
                                        <td><input form="userForm{{ $item->id }}" class="admin-field" name="name" value="{{ $item->name }}" required></td>
                                        <td><input form="userForm{{ $item->id }}" class="admin-field" name="email" type="email" value="{{ $item->email }}" required></td>
                                        <td>
                                            <select form="userForm{{ $item->id }}" class="admin-field" name="role">
                                                <option value="user" @selected($item->role === 'user')>USER</option>
                                                <option value="admin" @selected($item->role === 'admin')>ADMIN</option>
                                            </select>
                                        </td>
                                        <td>
                                            @php
                                                $userPlan = optional($item->activeSubscription)->plan ?? 'free';
                                            @endphp
                                            <select form="userForm{{ $item->id }}" class="admin-field" name="plan">
                                                <option value="free" @selected($userPlan === 'free')>FREE</option>
                                                <option value="plus" @selected($userPlan === 'plus')>PLUS</option>
                                                <option value="pro" @selected($userPlan === 'pro')>PRO</option>
                                            </select>
                                        </td>
                                        <td><button form="userForm{{ $item->id }}" class="admin-save" type="submit">Simpan</button></td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div id="adminPaymentsPanel" class="admin-tab-panel">
                            <table class="admin-table">
                                <thead><tr><th>Order</th><th>User</th><th>Plan</th><th>Status</th><th>Aksi</th></tr></thead>
                                <tbody>
                                @foreach($adminPayments as $payment)
                                    <form id="paymentForm{{ $payment->id }}" method="POST" action="{{ route('admin.payments.update', $payment) }}">
                                        @csrf
                                        @method('PATCH')
                                    </form>
                                    <tr>
                                        <td>
                                            <input form="paymentForm{{ $payment->id }}" class="admin-field" name="order_id" value="{{ $payment->order_id }}" required>
                                            <small class="mt-1 block text-slate-500">{{ strtoupper($payment->type) }}</small>
                                        </td>
                                        <td>{{ optional($payment->user)->email ?? '-' }}</td>
                                        <td>
                                            <select form="paymentForm{{ $payment->id }}" class="admin-field" name="plan">
                                                @php
                                                    $paymentPlan = $payment->plan ?? 'plus';
                                                @endphp
                                                <option value="free" @selected($paymentPlan === 'free')>FREE</option>
                                                <option value="plus" @selected($paymentPlan === 'plus')>PLUS</option>
                                                <option value="pro" @selected($paymentPlan === 'pro')>PRO</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select form="paymentForm{{ $payment->id }}" class="admin-field" name="status">
                                                @foreach(['pending', 'paid', 'failed', 'expired', 'cancelled'] as $status)
                                                <option value="{{ $status }}" @selected($payment->status === $status)>{{ strtoupper($status) }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td><button form="paymentForm{{ $payment->id }}" class="admin-save" type="submit">Simpan</button></td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </section>
                </div>
            </div>
            @endif

            <div id="cvView" style="display: none;">@include('partials.cv_content')</div>
        </main>
    </div>
</div>
<div id="planModal" class="cyber-modal-backdrop" onclick="closePlanModal()">
    <section class="cyber-panel w-full max-w-4xl p-6" onclick="event.stopPropagation()">
        <div class="flex items-start justify-between gap-4">
            <div>
                <span class="text-[10px] font-bold uppercase tracking-[0.18em]" style="color:#00d4ff;">CareerSense Plans</span>
                <h2 class="text-2xl font-black mt-2">Pilih akses yang sesuai</h2>
            </div>
            <button type="button" onclick="closePlanModal()" class="w-10 h-10 rounded-full" style="background:rgba(255,255,255,0.06); color:#fff;">x</button>
        </div>
        <div class="grid md:grid-cols-3 gap-4 mt-6">
            @foreach([
                ['key' => 'free', 'name' => 'Free', 'price' => 'Rp0', 'features' => ['Dashboard dasar', 'Upload dan analisis CV dasar', 'Upgrade kapan saja']],
                ['key' => 'plus', 'name' => 'Plus', 'price' => 'Rp2.000', 'features' => ['Semua fitur Free', 'Rekomendasi lowongan', 'Insight job match']],
                ['key' => 'pro', 'name' => 'Pro', 'price' => 'Rp5.000', 'features' => ['Semua fitur Plus', 'Simulasi interview AI', 'Akses prioritas fitur baru']],
            ] as $item)
            <article class="plan-card">
                <h3 class="text-xl font-black">{{ $item['name'] }}</h3>
                <strong class="block mt-2 text-2xl" style="color:#00d4ff;">{{ $item['price'] }}</strong>
                <div class="grid gap-2 mt-4">
                    @foreach($item['features'] as $feature)
                    <span class="text-sm text-slate-300">✓ {{ $feature }}</span>
                    @endforeach
                </div>
                @if($item['key'] === 'free')
                    <button type="button" class="mt-5 w-full rounded-xl py-3 text-sm font-black" style="background:rgba(255,255,255,0.06); color:#94a3b8;" disabled>
                        Plan Dasar
                    </button>
                @else
                    <button type="button" onclick="subscribePlanFromDashboard('{{ $item['key'] }}')" class="mt-5 w-full rounded-xl py-3 text-sm font-black" style="background:linear-gradient(135deg,#00d4ff,#3b82f6); color:#020810;">
                        Upgrade {{ $item['name'] }}
                    </button>
                @endif
            </article>
            @endforeach
        </div>
    </section>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>

<script>
    // ========================================
    // GLOBAL VARIABLES
    // ========================================
    let currentView = '{{ $selectedView }}';
    const currentPlan = @json(strtolower($plan));
    const planRank = { free: 0, plus: 1, pro: 2 };
    
    // ========================================
    // NAVIGATION FUNCTIONS
    // ========================================
    function navigateTo(view) {
        console.log('Navigating to:', view);
        if (!canAccessView(view)) {
            promptUpgradeForView(view);
            view = 'dashboard';
        }

        currentView = view;
        
        // Update active nav
        document.querySelectorAll('.nav-btn').forEach(btn => {
            btn.classList.remove('active');
        });
        
        const activeBtn = document.querySelector(`[data-nav="${view}"]`);
        if (activeBtn) {
            activeBtn.classList.add('active');
        }
        
        // Hide all views
        ['dashboardView', 'cvView', 'jobsView', 'interviewView', 'certsView', 'networkView', 'settingsView', 'adminView'].forEach(id => {
            const el = document.getElementById(id);
            if (el) el.style.display = 'none';
        });
        
        // Show selected view
        const viewMap = {
            'dashboard': 'dashboardView',
            'cv': 'cvView',
            'jobs': 'jobsView',
            'interview': 'interviewView',
            'certs': 'certsView',
            'network': 'networkView',
            'settings': 'settingsView',
            'admin': 'adminView'
        };
        
        const viewId = viewMap[view];
        if (viewId && document.getElementById(viewId)) {
            document.getElementById(viewId).style.display = 'block';
            console.log('Showing:', viewId);
        } else {
            document.getElementById('dashboardView').style.display = 'block';
            view = 'dashboard';
        }
        
        // Update page title
        const titles = {
            'dashboard': 'Dashboard',
            'cv': 'Analisis CV',
            'jobs': 'Rekomendasi Lowongan',
            'interview': 'Simulasi Interview AI',
            'certs': 'Sertifikasi & Skill',
            'network': 'Jaringan',
            'settings': 'Pengaturan',
            'admin': 'Admin'
        };
        
        document.getElementById('pageTitle').textContent = titles[view] || 'Dashboard';
        
        // === INITIALIZE VIEW-SPECIFIC FEATURES ===
        if (view === 'cv') {
            initializeCVFeatures();
        } else if (view === 'jobs') {
            console.log('Jobs view active - no CV scripts will run');
        } else if (view === 'admin') {
            renderAdminCharts();
        }
    }
    
    function changeTab(view) {
        window.location.href = '/dashboard?view=' + view;
    }

    function openPlanModal() {
        document.getElementById('planModal')?.classList.add('show');
    }

    function closePlanModal() {
        document.getElementById('planModal')?.classList.remove('show');
    }

    function canAccessView(view) {
        if (currentPlan === 'admin') return true;
        if (view === 'jobs') return (planRank[currentPlan] ?? 0) >= planRank.plus;
        if (view === 'interview') return (planRank[currentPlan] ?? 0) >= planRank.pro;
        return true;
    }

    function promptUpgradeForView(view) {
        const requiredPlan = view === 'interview' ? 'Pro' : 'Plus';
        Swal.fire({
            title: `Fitur ${requiredPlan} Terkunci`,
            text: `Upgrade ke ${requiredPlan} untuk membuka fitur ini.`,
            icon: 'info',
            showCancelButton: true,
            confirmButtonText: `Upgrade ${requiredPlan}`,
            cancelButtonText: 'Nanti',
            background: '#07111f',
            color: '#fff',
            confirmButtonColor: '#00d4ff'
        }).then(result => {
            if (result.isConfirmed) {
                openPlanModal();
            }
        });
    }

    async function subscribePlanFromDashboard(plan) {
        closePlanModal();

        Swal.fire({
            title: 'Memproses...',
            text: 'Menyiapkan pembayaran upgrade plan',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading(),
            background: '#07111f',
            color: '#fff'
        });

        try {
            const response = await fetch('/subscribe', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ plan })
            });

            const data = await response.json();
            Swal.close();

            if (!response.ok || !data.snap_token || !data.order_id) {
                throw new Error(data.message || 'Snap token tidak tersedia');
            }

            if (typeof snap === 'undefined') {
                throw new Error('Midtrans Snap belum termuat');
            }

            snap.pay(data.snap_token, {
                onSuccess: async function() {
                    try {
                        await confirmDashboardSubscription(data.order_id);
                    } catch (err) {
                        Swal.fire({
                            title: 'Plan Belum Aktif',
                            text: err.message,
                            icon: 'warning',
                            background: '#07111f',
                            color: '#fff'
                        });
                    }
                },
                onPending: function() {
                    Swal.fire({
                        title: 'Pembayaran Pending',
                        text: 'Selesaikan pembayaran agar plan aktif.',
                        icon: 'info',
                        background: '#07111f',
                        color: '#fff'
                    });
                },
                onError: function() {
                    Swal.fire({
                        title: 'Pembayaran Gagal',
                        text: 'Silakan coba lagi.',
                        icon: 'error',
                        background: '#07111f',
                        color: '#fff'
                    });
                },
                onClose: function() {
                    Swal.fire({
                        title: 'Pembayaran Dibatalkan',
                        text: 'Upgrade belum diproses.',
                        icon: 'info',
                        background: '#07111f',
                        color: '#fff'
                    });
                }
            });
        } catch (err) {
            Swal.fire({
                title: 'Gagal Membuat Transaksi',
                text: err.message,
                icon: 'error',
                background: '#07111f',
                color: '#fff'
            });
        }
    }

    async function confirmDashboardSubscription(orderId) {
        Swal.fire({
            title: 'Mengaktifkan Plan...',
            text: 'Mengonfirmasi pembayaran ke Midtrans',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading(),
            background: '#07111f',
            color: '#fff'
        });

        const response = await fetch('/subscription/confirm', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ order_id: orderId })
        });

        const data = await response.json();

        if (!response.ok || data.status !== 'success') {
            throw new Error(data.message || 'Pembayaran belum terkonfirmasi');
        }

        Swal.fire({
            title: 'Plan Aktif!',
            text: `Plan ${data.plan?.toUpperCase() || ''} berhasil diaktifkan.`,
            icon: 'success',
            background: '#07111f',
            color: '#fff'
        }).then(() => {
            window.location.href = '/dashboard';
        });
    }

    function switchAdminInfoTab(tab) {
        const usersPanel = document.getElementById('adminUsersPanel');
        const paymentsPanel = document.getElementById('adminPaymentsPanel');

        document.querySelectorAll('[data-admin-tab]').forEach(button => {
            button.classList.toggle('active', button.dataset.adminTab === tab);
        });

        usersPanel?.classList.toggle('active', tab === 'users');
        paymentsPanel?.classList.toggle('active', tab === 'payments');
    }

    const adminPlanChart = @json($planChart);
    const adminLoginChart = @json($loginChart);

    function renderAdminCharts() {
        if (!adminPlanChart || !document.getElementById('planLineChart')) return;
        renderLineChart('planLineChart', adminPlanChart);
        renderLineChart('loginLineChart', adminLoginChart);
    }

    function renderLineChart(svgId, chartData) {
        const svg = document.getElementById(svgId);
        if (!svg || !chartData) return;

        const width = 520;
        const height = 240;
        const pad = { left: 38, right: 18, top: 18, bottom: 42 };
        const chartWidth = width - pad.left - pad.right;
        const chartHeight = height - pad.top - pad.bottom;
        const allValues = chartData.series.flatMap(item => item.values);
        const maxValue = Math.max(1, ...allValues);
        const labels = chartData.labels || [];
        const ns = 'http://www.w3.org/2000/svg';

        svg.innerHTML = '';

        const make = (tag, attrs = {}) => {
            const node = document.createElementNS(ns, tag);
            Object.entries(attrs).forEach(([key, value]) => node.setAttribute(key, value));
            return node;
        };

        [0, 0.25, 0.5, 0.75, 1].forEach(step => {
            const y = pad.top + chartHeight - (chartHeight * step);
            svg.appendChild(make('line', {
                x1: pad.left, x2: width - pad.right, y1: y, y2: y,
                stroke: 'rgba(148,163,184,0.13)', 'stroke-width': '1'
            }));
            svg.appendChild(make('text', {
                x: 8, y: y + 4, fill: 'rgba(148,163,184,0.72)', 'font-size': '10'
            })).textContent = Math.round(maxValue * step);
        });

        labels.forEach((label, index) => {
            const x = pad.left + (labels.length === 1 ? 0 : (chartWidth / (labels.length - 1)) * index);
            svg.appendChild(make('line', {
                x1: x, x2: x, y1: pad.top, y2: pad.top + chartHeight,
                stroke: index % 2 === 0 ? 'rgba(0,212,255,0.08)' : 'transparent',
                'stroke-width': '1'
            }));
            svg.appendChild(make('text', {
                x, y: height - 14, fill: 'rgba(226,251,255,0.64)', 'font-size': '9',
                'text-anchor': 'middle'
            })).textContent = label;
        });

        chartData.series.forEach(series => {
            const points = series.values.map((value, index) => {
                const x = pad.left + (series.values.length === 1 ? 0 : (chartWidth / (series.values.length - 1)) * index);
                const y = pad.top + chartHeight - ((value / maxValue) * chartHeight);
                return { x, y, value };
            });

            const polyline = make('polyline', {
                points: points.map(point => `${point.x},${point.y}`).join(' '),
                fill: 'none',
                stroke: series.color,
                'stroke-width': '3',
                'stroke-linecap': 'round',
                'stroke-linejoin': 'round',
                filter: 'drop-shadow(0 0 7px rgba(0,212,255,0.45))'
            });
            svg.appendChild(polyline);

            points.forEach(point => {
                svg.appendChild(make('circle', {
                    cx: point.x, cy: point.y, r: '4',
                    fill: '#020810',
                    stroke: series.color,
                    'stroke-width': '2'
                }));
            });
        });
    }
    
    // ========================================
    // CV FEATURES (ONLY INITIALIZED WHEN ON CV PAGE)
    // ========================================
    let pdfDoc = null;
    let currentPageNum = 1;
    let pdfRenderTask = null;
    let pdfFallbackUrl = null;
    
    function initializeCVFeatures() {
        console.log('Initializing CV features...');
        
        // Set PDF.js worker
        if (window.pdfjsLib) {
            pdfjsLib.GlobalWorkerOptions.workerSrc = "https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js";
        }
        
        // Reset variables
        pdfDoc = null;
        currentPageNum = 1;
        pdfFallbackUrl = null;
        
        @php
            $displayCvForViewer = $selectedCv ?? $latestCv ?? null;
            $latestCvDebug = isset($displayCvForViewer)
                ? [
                    'id' => $displayCvForViewer->id,
                    'score' => $displayCvForViewer->score ?? null,
                    'file_path' => $displayCvForViewer->file_path ?? null,
                ]
                : null;
        @endphp
        
        const url = @json(isset($displayCvForViewer) && $displayCvForViewer->file_path
            ? asset('storage/' . $displayCvForViewer->file_path)
            : null);
        
        console.log('CV Features Init:', { url, hasCanvas: !!document.getElementById('pdfCanvas') });
        
        if (url && document.getElementById('pdfCanvas') && window.pdfjsLib) {
            loadPdfDocument(url)
                .then(pdf => {
                    pdfDoc = pdf;
                    currentPageNum = 1;
                    const pageCountEl = document.getElementById('pageCount');
                    if (pageCountEl) pageCountEl.textContent = pdf.numPages;
                    hidePdfFallback();
                    renderCVPage(1);
                })
                .catch(err => {
                    console.error('PDF render error', err);
                    showPdfFallback(url, true);
                });
        } else {
            console.log('No PDF to render or canvas missing');
            if (url) showPdfFallback(url, true);
        }
    }

    async function loadPdfDocument(url) {
        try {
            return await pdfjsLib.getDocument({ url }).promise;
        } catch (err) {
            console.warn('PDF.js worker render failed, retrying without worker', err);
            return pdfjsLib.getDocument({
                url,
                disableWorker: true,
                disableStream: true,
                disableAutoFetch: true
            }).promise;
        }
    }
    
    function renderCVPage(num) {
        if (!pdfDoc) return;
        if (num < 1 || num > pdfDoc.numPages) return;
        
        pdfDoc.getPage(num).then(page => {
            const canvas = document.getElementById('pdfCanvas');
            if (!canvas) return;
            const ctx = canvas.getContext('2d');
            const panel = canvas.closest('.cv-pdf-panel');
            const controlsHeight = 94;
            const availableWidth = Math.max((panel?.clientWidth || canvas.parentElement?.clientWidth || 640) - 24, 280);
            const availableHeight = Math.max((panel?.clientHeight || 880) - controlsHeight, 360);
            const baseViewport = page.getViewport({ scale: 1 });
            const scale = Math.min(
                availableWidth / baseViewport.width,
                availableHeight / baseViewport.height,
                1.7
            );
            const viewport = page.getViewport({ scale });

            if (pdfRenderTask) {
                pdfRenderTask.cancel();
                pdfRenderTask = null;
            }

            canvas.height = viewport.height;
            canvas.width = viewport.width;
            canvas.style.width = `${viewport.width}px`;
            canvas.style.height = `${viewport.height}px`;
            ctx.clearRect(0, 0, canvas.width, canvas.height);

            pdfRenderTask = page.render({ canvasContext: ctx, viewport: viewport });
            pdfRenderTask.promise.catch(err => {
                if (err?.name !== 'RenderingCancelledException') {
                    console.error('PDF page render error', err);
                    showPdfFallback(document.getElementById('openPdfLink')?.href || '', true);
                }
            }).finally(() => {
                pdfRenderTask = null;
            });
            const pageNumEl = document.getElementById('pageNum');
            if (pageNumEl) pageNumEl.textContent = num;
            currentPageNum = num;
            hidePdfFallback();
        });
    }

    function showPdfFallback(url, showError = false) {
        const canvas = document.getElementById('pdfCanvas');
        const frame = document.getElementById('pdfFallbackFrame');
        const error = document.getElementById('pdfPreviewError');
        const link = document.getElementById('openPdfLink');
        const cleanUrl = (url || link?.href || '').split('#')[0];

        if (canvas) canvas.style.display = 'none';
        if (frame) {
            frame.src = `${cleanUrl}#page=${currentPageNum}&toolbar=0&navpanes=0&scrollbar=0&view=FitH`;
            frame.style.display = cleanUrl ? 'block' : 'none';
        }
        if (error) error.classList.toggle('hidden', !showError || !cleanUrl);
        if (link && cleanUrl) link.href = cleanUrl;
        pdfFallbackUrl = cleanUrl || null;
    }

    function hidePdfFallback() {
        const canvas = document.getElementById('pdfCanvas');
        const frame = document.getElementById('pdfFallbackFrame');
        const error = document.getElementById('pdfPreviewError');

        if (canvas) canvas.style.display = 'block';
        if (frame) frame.style.display = 'none';
        if (error) error.classList.add('hidden');
        pdfFallbackUrl = null;
    }
    
    // Make CV functions available globally (but only called when on CV page)
    window.nextPage = function() {
        if (currentView !== 'cv') return;
        if (!pdfDoc && !pdfFallbackUrl) return;
        if (pdfDoc && currentPageNum >= pdfDoc.numPages) return;
        currentPageNum++;
        if (pdfDoc) {
            renderCVPage(currentPageNum);
        } else {
            showPdfFallback(pdfFallbackUrl, false);
            const pageNumEl = document.getElementById('pageNum');
            if (pageNumEl) pageNumEl.textContent = currentPageNum;
        }
    };
    
    window.prevPage = function() {
        if (currentView !== 'cv') return;
        if (currentPageNum <= 1) return;
        currentPageNum--;
        if (pdfDoc) {
            renderCVPage(currentPageNum);
        } else if (pdfFallbackUrl) {
            showPdfFallback(pdfFallbackUrl, false);
            const pageNumEl = document.getElementById('pageNum');
            if (pageNumEl) pageNumEl.textContent = currentPageNum;
        }
    };
    
    window.scrollCv = function(amount) {
        if (currentView !== 'cv') return;
        const slider = document.getElementById('cvSlider');
        if (slider) slider.scrollLeft += amount * 250;
    };
    
    window.selectCv = function(data) {
        if (currentView !== 'cv') return;
        if (!data) return;
        
        // Load PDF
        const link = document.getElementById('openPdfLink');
        if (link) link.href = data.url;
        pdfFallbackUrl = null;

        if (!window.pdfjsLib) {
            showPdfFallback(data.url, true);
        } else {
            loadPdfDocument(data.url).then(pdf => {
            pdfDoc = pdf;
            currentPageNum = 1;
            const pageCountEl = document.getElementById('pageCount');
            if (pageCountEl) pageCountEl.textContent = pdf.numPages;
            hidePdfFallback();
            renderCVPage(currentPageNum);
            }).catch(err => {
                console.error('PDF render error', err);
                showPdfFallback(data.url, true);
            });
        }
        
        // Update score
        const scoreEl = document.getElementById('cvScore');
        if (scoreEl) scoreEl.textContent = data.score ?? 0;
        
        // Parse analysis
        let analysis = data.analysis;
        if (typeof analysis === "string") {
            try { analysis = JSON.parse(analysis); } catch { analysis = {}; }
        }
        
        renderCVList("strengthList", analysis.strengths, "text-green-400");
        renderCVList("weaknessList", analysis.weaknesses, "text-red-400");
        renderCVList("suggestionList", analysis.suggestions, "text-yellow-400");
        renderCvInsightDetails(analysis);
    };
    
    function renderCVList(elementId, items, colorClass) {
        const el = document.getElementById(elementId);
        if (!el) return;
        el.innerHTML = "";
        
        if (!items || items.length === 0) {
            el.innerHTML = `<div class="text-[11px] text-white/30">Tidak ada data</div>`;
            return;
        }
        
        items.forEach(item => {
            el.innerHTML += `
                <div class="text-[12px] text-white/80 mb-1 flex gap-2">
                    <span class="${colorClass}">•</span>
                    <span>${escapeHtml(item)}</span>
                </div>
            `;
        });
    }

    function renderCvInsightDetails(analysis) {
        const skillGap = analysis?.skill_gap || {};
        const semantic = analysis?.semantic_similarity || {};
        const recommendation = analysis?.recommendation_engine || {};

        setText("cvMatchScore", analysis?.match_score ?? "-");
        setText("cvReadiness", formatLabel(analysis?.readiness));
        setText("cvSemanticScore", semantic?.score ?? "-");
        setText("cvSemanticLabel", formatLabel(semantic?.label));
        setText("skillGapRate", `Match ${skillGap?.match_rate ?? "-"}%`);
        setText("recommendationSummary", recommendation?.summary || "Belum ada ringkasan rekomendasi.");

        renderSkillChips(
            "matchedSkillList",
            skillGap?.matched_skills || analysis?.matched_skills || [],
            "bg-green-500/10 text-green-300 border-green-500/20"
        );
        renderSkillChips(
            "missingSkillList",
            skillGap?.missing_skills || analysis?.missing_skills || [],
            "bg-red-500/10 text-red-300 border-red-500/20"
        );
        renderAtsBreakdown(analysis?.ats_breakdown || {});
        renderPriorityActions(recommendation?.priority_actions || []);
    }

    function setText(elementId, value) {
        const el = document.getElementById(elementId);
        if (!el) return;
        el.textContent = value ?? "-";
    }

    function formatLabel(value) {
        if (!value) return "-";
        return String(value)
            .replaceAll("_", " ")
            .replace(/\b\w/g, char => char.toUpperCase());
    }

    function renderSkillChips(elementId, items, colorClass) {
        const el = document.getElementById(elementId);
        if (!el) return;
        el.innerHTML = "";

        if (!items || items.length === 0) {
            el.innerHTML = `<span class="text-[11px] text-white/30">Belum ada data</span>`;
            return;
        }

        items.forEach(item => {
            const label = typeof item === "string"
                ? item
                : (item.skill || item.name || "");
            if (!label) return;
            el.innerHTML += `
                <span class="text-[10px] px-2 py-1 rounded border ${colorClass}">
                    ${escapeHtml(String(label).replaceAll("_", " "))}
                </span>
            `;
        });
    }

    function renderAtsBreakdown(items) {
        const el = document.getElementById("atsBreakdownList");
        if (!el) return;
        el.innerHTML = "";

        const entries = Object.entries(items || {});
        if (entries.length === 0) {
            el.innerHTML = `<div class="text-[11px] text-white/30">Belum ada data</div>`;
            return;
        }

        entries.forEach(([name, item]) => {
            const score = Number(item?.score || 0);
            const maxScore = Math.max(Number(item?.max_score || 1), 1);
            const percent = Math.min(100, Math.round((score / maxScore) * 100));
            el.innerHTML += `
                <div>
                    <div class="flex justify-between text-[11px] mb-1">
                        <span class="text-white/70">${escapeHtml(formatLabel(name))}</span>
                        <span class="text-cyan-300">${score}/${maxScore}</span>
                    </div>
                    <div class="h-1.5 rounded-full bg-white/10 overflow-hidden">
                        <div class="h-full bg-cyan-400" style="width: ${percent}%;"></div>
                    </div>
                </div>
            `;
        });
    }

    function renderPriorityActions(items) {
        const el = document.getElementById("priorityActionList");
        if (!el) return;
        el.innerHTML = "";

        if (!items || items.length === 0) {
            el.innerHTML = `<div class="text-[11px] text-white/30">Belum ada prioritas</div>`;
            return;
        }

        items.forEach(item => {
            el.innerHTML += `
                <div class="text-[12px] text-white/80 flex gap-2">
                    <span class="text-orange-300">•</span>
                    <span>${escapeHtml(item)}</span>
                </div>
            `;
        });
    }
    
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
    
    // ========================================
    // UPLOAD CV HANDLER
    // ========================================
    async function handleUploadCV() {
        const { value: file } = await Swal.fire({
            title: 'Upload CV',
            text: 'Pilih file CV Anda (PDF, max 2MB)',
            icon: 'info',
            input: 'file',
            inputAttributes: { accept: 'application/pdf' },
            showCancelButton: true,
            confirmButtonText: 'Upload',
            background: '#07111f',
            color: '#fff',
            inputValidator: (file) => {
                if (!file) return 'Pilih file dulu';
                if (file.type !== 'application/pdf') return 'Harus PDF';
                if (file.size > 2 * 1024 * 1024) return 'Max 2MB';
            }
        });
        
        if (!file) return;
        
        Swal.fire({
            title: 'Uploading...',
            text: 'AI sedang memproses CV kamu',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading(),
            background: '#07111f',
            color: '#fff'
        });
        
        const formData = new FormData();
        formData.append('cv_file', file);
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);
        
        try {
            const res = await fetch('/upload-cv', { method: 'POST', body: formData });
            const data = await res.json();
            if (!res.ok) throw new Error(data.message || 'Upload gagal');
            Swal.fire({
                title: 'Berhasil!',
                text: 'CV sedang dianalisis AI...',
                icon: 'success',
                timer: 1500,
                showConfirmButton: false,
                background: '#07111f',
                color: '#fff'
            }).then(() => {
                navigateTo('cv');
                setTimeout(() => location.reload(), 500);
            });
        } catch (err) {
            Swal.fire({
                title: 'Error',
                text: err.message,
                icon: 'error',
                background: '#07111f',
                color: '#fff'
            });
        }
    }
    
    // ========================================
    // INITIALIZATION
    // ========================================
    const urlParams = new URLSearchParams(window.location.search);
    const initialView = urlParams.get('view') || 'dashboard';
    navigateTo(initialView);
    
    // Handle browser back/forward
    window.addEventListener('popstate', function() {
        const params = new URLSearchParams(window.location.search);
        navigateTo(params.get('view') || 'dashboard');
    });
    
    console.log('Dashboard loaded, current view:', initialView);
    console.log('Job count:', {{ $jobData->count() }});





let currentQuestions = [];
let currentQuestionIndex = 0;
let userAnswers = [];
let interviewTimer = null;
let recognition;
let isRecording = false;
let mediaRecorder = null;
let recordedAudioChunks = [];
let recordingQuestionIndex = null;
let currentFinalTranscript = '';
let currentInterimTranscript = '';
let voices = [];

if ('speechSynthesis' in window) {
    voices = window.speechSynthesis.getVoices();
    window.speechSynthesis.onvoiceschanged = () => {
        voices = window.speechSynthesis.getVoices();
    };
}
    
async function startInterview() {
    const cvSelect = document.getElementById('cvSelector');
    
    if (!cvSelect.value) {
        Swal.fire({
            title: 'Pilih CV',
            text: 'Silakan pilih CV terlebih dahulu',
            icon: 'warning',
            background: '#07111f',
            color: '#fff'
        });
        return;
    }
    
    const cvId = parseInt(cvSelect.value);
    const selectedOption = cvSelect.options[cvSelect.selectedIndex];
    const jobTema = selectedOption.dataset.jobtema || 'Umum';
    
    // Tampilkan modal loading
    document.getElementById("jobTemaText").innerHTML = `
        <span class="text-cyan-400">🎯 Interview untuk:</span> ${escapeHtml(jobTema)}
    `;
    
    document.getElementById("questionBox").innerHTML = `
        <div class="flex items-center justify-center py-8">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-cyan-400"></div>
            <span class="ml-3">AI sedang menyusun pertanyaan untuk posisi ${escapeHtml(jobTema)}...</span>
        </div>
    `;
    
    document.getElementById("interviewModal").classList.remove("hidden");
    document.getElementById("interviewModal").classList.add("flex");
    
    // Reset state
    currentQuestions = [];
    currentQuestionIndex = 0;
    userAnswers = [];
    resetSpeechState();
    document.getElementById("answerInput").value = '';
    document.getElementById("answerInput").disabled = true;
    document.getElementById("progressContainer").classList.remove("hidden");
    
    try {
        const response = await fetch("/generate-interview", {
    method: "POST",
    headers: {
        "Content-Type": "application/json",
        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
    },
    body: JSON.stringify({
        cv_id: cvId,
        job_tema: jobTema
    })
        });
        
        if (!response.ok) {
            const errorData = await response.json();
            throw new Error(errorData.detail || `HTTP ${response.status}`);
        }
        
        const result = await response.json();
        console.log("API Response:", result);
        
        if (result.status === "success" && result.questions && result.questions.length > 0) {
            currentQuestions = result.questions;
            currentInterviewIds = result.interview_ids || [];
            
            showCurrentQuestion();
            startTimer(180);
            document.getElementById("answerInput").disabled = false;
            document.getElementById("answerInput").focus();
        } else {
            throw new Error(result.detail || "Gagal generate pertanyaan");
        }
        
    } catch (error) {
        console.error("Error:", error);
        document.getElementById("questionBox").innerHTML = `
            <div class="text-center py-8">
                <div class="text-red-400 text-lg mb-2">⚠️ Error: ${error.message}</div>
                <div class="text-white/50 text-xs mt-4">Pastikan server FastAPI berjalan di port 8004</div>
            </div>
        `;
        document.getElementById("progressContainer").classList.add("hidden");
    }
}

function showCurrentQuestion() {
    if (!currentQuestions || currentQuestions.length === 0) return;

    const question = currentQuestions[currentQuestionIndex];
    const total = currentQuestions.length;
    const progress = ((currentQuestionIndex + 1) / total) * 100;

    document.getElementById("questionCounter").innerHTML =
        `Pertanyaan ${currentQuestionIndex + 1} dari ${total}`;

    document.getElementById("progressPercent").innerHTML =
        `${Math.round(progress)}%`;

    document.getElementById("progressBar").style.width = `${progress}%`;

    document.getElementById("questionBox").innerHTML =
        escapeHtml(question);

    // 🔥 STOP MIC saat pindah soal
    if (recognition && isRecording) {
        isRecording = false;
        recognition.stop();
        stopAudioCapture();
        updateMicUI(false);
    }

    // LOAD JAWABAN
    const savedAnswer = normalizeTranscript(userAnswers[currentQuestionIndex] || '');
    currentFinalTranscript = savedAnswer;
    currentInterimTranscript = '';
    document.getElementById("answerInput").value = savedAnswer;

    // 🔥 DELAY biar smooth
    setTimeout(() => {
        speakQuestionNative(question);
    }, 300);

    updateButtons();
}
function isModalOpen() {
    const modal = document.getElementById("interviewModal");
    return modal && modal.classList.contains("flex");
}

function speakQuestionNative(text) {
    if (!('speechSynthesis' in window)) return;
    if (!isModalOpen()) return;

    const utterance = new SpeechSynthesisUtterance(text);
    voices = window.speechSynthesis.getVoices();

    const indoVoice = voices.find(v => v.lang === "id-ID")
        || voices.find(v => v.lang?.toLowerCase().startsWith("id"))
        || voices.find(v => /indonesia/i.test(v.name));

    if (indoVoice) {
        utterance.voice = indoVoice;
    }

    utterance.lang = "id-ID";
    utterance.rate = 0.9;
    utterance.pitch = 1;

    window.speechSynthesis.cancel();
    window.speechSynthesis.speak(utterance);
}
function initSpeechRecognition() {
    const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;

    if (!SpeechRecognition) {
        setSttStatus("Live STT browser tidak tersedia. Rekaman tetap dikirim ke Whisper lokal setelah Stop.");
        return;
    }

    recognition = new SpeechRecognition();
    recognition.lang = "id-ID";
    recognition.continuous = true;
    recognition.interimResults = true;

    recognition.onresult = function (event) {
        let interimTranscript = '';

        for (let i = event.resultIndex; i < event.results.length; i++) {
            const transcript = event.results[i][0].transcript.trim();
            if (!transcript) continue;

            if (event.results[i].isFinal) {
                currentFinalTranscript = normalizeTranscript(`${currentFinalTranscript} ${transcript}`);
            } else {
                interimTranscript = normalizeTranscript(`${interimTranscript} ${transcript}`);
            }
        }

        currentInterimTranscript = interimTranscript;
        renderSpeechTranscript();
    };

    // 🔥 FIX UTAMA (tidak override lagi)
    recognition.onend = function () {
        if (isRecording) {
            try {
                recognition.start(); // auto restart
            } catch (e) {
                console.log("Restart blocked:", e);
            }
        } else {
            updateMicUI(false);
        }
    };

    recognition.onerror = function (event) {
        console.log("Speech error:", event.error);

        if (event.error === "not-allowed") {
            setSttStatus("Akses microphone ditolak. Izinkan microphone di browser.");
            isRecording = false;
            updateMicUI(false);
        }
    };
}
function safeStartRecognition() {
    try {
        recognition.start();
    } catch (e) {
        console.log("Recognition already running");
    }
}
async function toggleRecording() {
    if (!recognition) initSpeechRecognition();

    if (!isRecording) {
        if ('speechSynthesis' in window) {
            window.speechSynthesis.cancel();
        }

        const micReady = await startAudioCapture();
        if (!micReady) return;

        if (recognition) safeStartRecognition();
        isRecording = true;
        updateMicUI(true);
        setSttStatus("Mendengarkan... hasil final akan dipresisikan dengan Whisper setelah Stop.");
    } else {
        isRecording = false;
        if (recognition) recognition.stop();
        stopAudioCapture();
        updateMicUI(false);
    }
}

async function startAudioCapture() {
    if (!navigator.mediaDevices?.getUserMedia || !window.MediaRecorder) {
        setSttStatus("Browser tidak mendukung rekaman audio. Gunakan Chrome/Edge terbaru.");
        return false;
    }

    try {
        const stream = await navigator.mediaDevices.getUserMedia({
            audio: {
                echoCancellation: true,
                noiseSuppression: true,
                autoGainControl: true
            }
        });

        recordedAudioChunks = [];
        recordingQuestionIndex = currentQuestionIndex;
        const mimeType = MediaRecorder.isTypeSupported('audio/webm;codecs=opus')
            ? 'audio/webm;codecs=opus'
            : 'audio/webm';

        mediaRecorder = new MediaRecorder(stream, { mimeType });
        mediaRecorder.ondataavailable = event => {
            if (event.data && event.data.size > 0) {
                recordedAudioChunks.push(event.data);
            }
        };
        mediaRecorder.onstop = () => {
            stream.getTracks().forEach(track => track.stop());
            transcribeRecordedAudio(mimeType, recordingQuestionIndex);
        };
        mediaRecorder.start();
        return true;
    } catch (error) {
        console.error("Mic capture error:", error);
        setSttStatus("Microphone belum bisa dipakai. Cek permission browser.");
        return false;
    }
}

function stopAudioCapture() {
    if (mediaRecorder && mediaRecorder.state !== 'inactive') {
        setSttStatus("Memproses audio dengan Whisper lokal...");
        mediaRecorder.stop();
    }
}

async function transcribeRecordedAudio(mimeType, questionIndex) {
    if (!recordedAudioChunks.length) {
        setSttStatus("Tidak ada audio yang terekam.");
        return;
    }

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
    if (!csrfToken) {
        setSttStatus("CSRF token tidak ditemukan.");
        return;
    }

    const audioBlob = new Blob(recordedAudioChunks, { type: mimeType });
    const formData = new FormData();
    formData.append('audio', audioBlob, 'interview-answer.webm');

    try {
        const response = await fetch('/interview/transcribe', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken
            },
            body: formData
        });

        const result = await response.json();
        if (!response.ok || result.status !== 'success') {
            throw new Error(result.detail || result.message || 'Transkripsi gagal');
        }

        const preciseText = normalizeTranscript(result.text || '');
        if (preciseText) {
            if (Number.isInteger(questionIndex)) {
                userAnswers[questionIndex] = preciseText;
            }

            if (questionIndex === currentQuestionIndex) {
                document.getElementById("answerInput").value = preciseText;
                currentFinalTranscript = preciseText;
                currentInterimTranscript = '';
            }

            setSttStatus("Transkripsi final selesai dari Whisper lokal.");
        } else {
            renderSpeechTranscript();
            setSttStatus("Audio selesai, tapi tidak ada suara yang terbaca jelas.");
        }
    } catch (error) {
        console.error("Whisper STT error:", error);
        renderSpeechTranscript();
        setSttStatus("Whisper lokal gagal, transcript live browser tetap dipakai.");
    } finally {
        recordedAudioChunks = [];
        mediaRecorder = null;
        recordingQuestionIndex = null;
    }
}

function renderSpeechTranscript() {
    const text = normalizeTranscript(`${currentFinalTranscript} ${currentInterimTranscript}`);
    document.getElementById("answerInput").value = text;
}

function normalizeTranscript(text) {
    return (text || '').replace(/\s+/g, ' ').trim();
}

function setSttStatus(message) {
    const status = document.getElementById("sttStatus");
    if (status) status.innerText = message;
}

function resetSpeechState() {
    currentFinalTranscript = '';
    currentInterimTranscript = '';
    recordedAudioChunks = [];
}
function updateMicUI(active) {
    const btn = document.getElementById("micBtn");

    if (!btn) return;

    if (active) {
        btn.innerText = "⏹ Stop";
        btn.classList.add("bg-red-600");
    } else {
        btn.innerText = "🎤 Mulai Bicara";
        btn.classList.remove("bg-red-600");
    }
}

function updateButtons() {
    const isLast = currentQuestionIndex === currentQuestions.length - 1;
    const nextBtn = document.getElementById("nextBtn");
    const submitBtn = document.getElementById("submitBtn");
    
    if (isLast) {
        nextBtn.style.display = "none";
        submitBtn.style.display = "block";
    } else {
        nextBtn.style.display = "block";
        submitBtn.style.display = "none";
    }
}

function nextQuestion() {
    const currentAnswer = document.getElementById("answerInput").value.trim();
    
    if (!currentAnswer) {
        Swal.fire({
            title: 'Jawaban Kosong',
            text: 'Silakan isi jawaban Anda terlebih dahulu',
            icon: 'warning',
            background: '#07111f',
            color: '#fff',
            confirmButtonColor: '#00d4ff'
        });
        return;
    }
    
    // Simpan jawaban
    userAnswers[currentQuestionIndex] = currentAnswer;
    
    // Pindah ke pertanyaan berikutnya
    if (currentQuestionIndex < currentQuestions.length - 1) {
        currentQuestionIndex++;
        showCurrentQuestion();
        document.getElementById("answerInput").focus();
    }
}

function previousQuestion() {
    // Simpan jawaban saat ini
    const currentAnswer = document.getElementById("answerInput").value;
    userAnswers[currentQuestionIndex] = currentAnswer;
    
    // Pindah ke pertanyaan sebelumnya
    if (currentQuestionIndex > 0) {
        currentQuestionIndex--;
        showCurrentQuestion();
        document.getElementById("answerInput").focus();
    }
}

function startTimer(seconds) {
    if (interviewTimer) clearInterval(interviewTimer);
    
    let remaining = seconds;
    const timerElement = document.getElementById("timerText");
    
    const updateTimer = () => {
        const minutes = Math.floor(remaining / 60);
        const secs = remaining % 60;
        timerElement.innerHTML = `${String(minutes).padStart(2, '0')}:${String(secs).padStart(2, '0')}`;
        
        if (remaining <= 30) {
            timerElement.classList.add('text-red-400');
        }
        
        if (remaining <= 0) {
            clearInterval(interviewTimer);
            Swal.fire({
                title: '⏰ Waktu Habis!',
                text: 'Interview akan disubmit secara otomatis',
                icon: 'warning',
                background: '#07111f',
                color: '#fff',
                confirmButtonColor: '#00d4ff'
            }).then(() => submitInterview());
        }
        
        remaining--;
    };
    
    updateTimer();
    interviewTimer = setInterval(updateTimer, 1000);
}

function closeInterviewModal() {
    document.getElementById("interviewModal").classList.add("hidden");
    document.getElementById("interviewModal").classList.remove("flex");

    // 🔥 STOP MIC
    if (recognition && isRecording) {
        isRecording = false;
        recognition.stop();
    }

    if (mediaRecorder && mediaRecorder.state !== 'inactive') {
        stopAudioCapture();
    }

    // 🔥 STOP TTS
    if ('speechSynthesis' in window) {
        window.speechSynthesis.cancel();
    }

    if (interviewTimer) {
        clearInterval(interviewTimer);
        interviewTimer = null;
    }
}

async function submitInterview() {
    // =========================
    // AMBIL DATA CV
    // =========================
    const cvSelect = document.getElementById('cvSelector');

    if (!cvSelect || !cvSelect.value) {
        Swal.fire({
            title: 'Error',
            text: 'CV tidak valid',
            icon: 'error',
            background: '#07111f',
            color: '#fff'
        });
        return;
    }

    const selectedOption = cvSelect.options[cvSelect.selectedIndex];

    const cvId = parseInt(cvSelect.value);
    const jobTema = selectedOption?.dataset?.jobtema || 'Umum';

    // =========================
    // SIMPAN JAWABAN TERAKHIR
    // =========================
    const lastAnswer = document.getElementById("answerInput").value;
    userAnswers[currentQuestionIndex] = lastAnswer;

    // =========================
    // VALIDASI JAWABAN KOSONG
    // =========================
    const emptyIndex = userAnswers.findIndex(a => !a || !a.trim());

    if (emptyIndex !== -1) {
        const result = await Swal.fire({
            title: 'Peringatan',
            text: `Pertanyaan ${emptyIndex + 1} belum dijawab. Tetap submit?`,
            icon: 'question',
            background: '#07111f',
            color: '#fff',
            showCancelButton: true,
            confirmButtonText: 'Ya, Submit',
            cancelButtonText: 'Kembali'
        });

        if (!result.isConfirmed) {
            currentQuestionIndex = emptyIndex;
            showCurrentQuestion();
            return;
        }
    }

    // =========================
    // STOP TIMER
    // =========================
    if (interviewTimer) {
        clearInterval(interviewTimer);
        interviewTimer = null;
    }

    // =========================
    // LOADING
    // =========================
    Swal.fire({
        title: 'Mengirim Jawaban...',
        allowOutsideClick: false,
        didOpen: () => Swal.showLoading(),
        background: '#07111f',
        color: '#fff'
    });

    try {
        // =========================
        // CSRF TOKEN
        // =========================
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

        if (!csrfToken) {
            throw new Error("CSRF token tidak ditemukan");
        }

        // =========================
        // REQUEST KE LARAVEL
        // =========================
        const response = await fetch('/submit-interview', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({
                cv_id: cvId,
                job_tema: jobTema,
                questions: currentQuestions,
                answers: userAnswers,
                interview_ids: currentInterviewIds
            })
        });

        // =========================
        // HANDLE ERROR RESPONSE (PENTING!)
        // =========================
        if (!response.ok) {
            const text = await response.text();
            console.error("SERVER RESPONSE:", text);
            throw new Error("Server error: " + text.substring(0, 200));
        }

        // =========================
        // PARSE JSON
        // =========================
        const result = await response.json();

        // =========================
        // SUCCESS UI
        // =========================
        Swal.fire({
            title: '🎉 Interview Selesai!',
            html: `
                <div class="text-center">
                    <div class="text-4xl font-bold text-cyan-400 mb-2">
                        ${result.score ?? 0}
                    </div>
                    <div class="text-sm text-white/60 mb-3">
                        Skor Akhir
                    </div>
                    <div class="p-3 rounded-lg bg-white/5 text-sm text-white/80">
                        ${escapeHtml(result.feedback ?? 'Terima kasih!')}
                    </div>
                </div>
            `,
            icon: 'success',
            background: '#07111f',
            color: '#fff'
        });

        closeInterviewModal();

        setTimeout(() => location.reload(), 1500);

    } catch (error) {
        console.error("ERROR SUBMIT:", error);

        Swal.fire({
            title: 'Error',
            text: error.message,
            icon: 'error',
            background: '#07111f',
            color: '#fff'
        });
    }
}

function chooseCv(el) {
    // reset active
    document.querySelectorAll('.cv-card').forEach(c => {
        c.classList.remove('ring-2', 'ring-cyan-400');
    });

    // active
    el.classList.add('ring-2', 'ring-cyan-400');

    // set ke select (logic lama tetap jalan)
    const id = el.dataset.id;
    const jobTema = el.dataset.jobtema;

    const select = document.getElementById('cvSelector');
    select.value = id;

    // trigger change kalau kamu pakai listener
    select.dispatchEvent(new Event('change'));

    // 🔥 load interview history sesuai CV
    loadInterviewHistory(id);
}
function loadInterviewHistory(cvId) {
    fetch(`/interviews/by-cv/${cvId}`)
        .then(res => res.json())
        .then(data => {
            renderInterviewHistory(data);
        });
} 
function renderInterviewHistory(data) {
    const container = document.getElementById('interviewHistoryContainer');

    if (!data.length) {
        container.innerHTML = `
            <div class="text-center py-10 text-slate-500">
                Belum ada interview
            </div>
        `;
        return;
    }

    container.innerHTML = data.map(item => `
        <div class="p-4 rounded-xl border border-white/5 bg-gradient-to-br from-white/5 to-transparent hover:scale-[1.01] transition">

            <!-- HEADER -->
            <div class="flex justify-between items-center mb-3">
                <span class="text-[10px] text-cyan-400 uppercase tracking-wider">
                    Question
                </span>
                <span class="px-2 py-1 rounded-md text-xs font-bold bg-cyan-500/10 text-cyan-400">
                    ${item.score}
                </span>
            </div>

            <!-- QUESTION -->
            <p class="text-sm text-white font-semibold line-clamp-2 mb-2">
                ${item.question}
            </p>

            <!-- ANSWER -->
            <p class="text-xs text-slate-400 line-clamp-2 mb-3">
                ${item.answer ?? '-'}
            </p>

            <!-- FEEDBACK -->
            <div class="p-3 rounded-lg bg-yellow-500/5 border border-yellow-500/20">
                <p class="text-[11px] text-yellow-300 line-clamp-3">
                    💡 ${item.feedback ?? 'Belum ada feedback'}
                </p>
            </div>

        </div>
    `).join('');
}
function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}
</script>

</body>
</html>
