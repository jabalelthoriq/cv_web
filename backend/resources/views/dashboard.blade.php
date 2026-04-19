<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dashboard - NEXUS.AI</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700;800&display=swap');
        
        * {
            font-family: 'Space Grotesk', sans-serif;
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
        
        ::-webkit-scrollbar-thumb:hover {
            background: rgba(0,212,255,0.3);
        }
        
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.4; }
        }
        
        .animate-pulse {
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
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
<div class="fixed inset-0 pointer-events-none z-0" style="
    background-image: linear-gradient(rgba(0,212,255,0.012) 1px, transparent 1px),
                      linear-gradient(90deg, rgba(0,212,255,0.012) 1px, transparent 1px);
    background-size: 48px 48px;
"></div>

<!-- Main Container -->
<div class="flex min-h-screen relative z-10">

    <!-- Sidebar -->
    <aside id="sidebar" class="flex-shrink-0 flex flex-col h-screen sticky top-0 transition-all duration-300 z-20" style="
        width: 220px;
        background: linear-gradient(180deg, #05101e 0%, #02080f 100%);
        border-right: 1px solid rgba(255,255,255,0.07);
        padding: 16px 12px;
    ">
        <!-- Logo -->
        <div class="flex items-center gap-2.5 mb-6 justify-start">
            <button id="toggleSidebar" class="w-9 h-9 rounded-xl flex items-center justify-center text-lg flex-shrink-0 transition-all duration-200 hover:brightness-110" style="
                background: linear-gradient(135deg, #00d4ff, #3b82f6);
                box-shadow: 0 0 18px rgba(0,212,255,0.25);
                border: none;
            ">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
                    <path d="M3 9h18M3 15h18M9 3v18M15 3v18"/>
                </svg>
            </button>
            <span class="text-base font-black" style="
                background: linear-gradient(135deg, #00d4ff, #3b82f6);
                -webkit-background-clip: text;
                background-clip: text;
                color: transparent;
                letter-spacing: -0.4px;
            ">
                NEXUS<span style="color: #00d4ff; background: none; -webkit-text-fill-color: #00d4ff;">.AI</span>
            </span>
        </div>

        <!-- Menu Utama -->
        <p class="text-[9px] text-slate-700 uppercase tracking-[0.14em] font-bold px-2.5 mb-2">Menu Utama</p>
        <div class="space-y-0.5">
            @php
                $mainNav = [
                    ['id' => 'dashboard', 'icon' => 'M3 12h18M12 3v18', 'label' => 'Dashboard'],
                    ['id' => 'cv', 'icon' => 'M4 4h16v16H4zM8 8h8M8 12h6M8 16h4', 'label' => 'Analisis CV'],
                    ['id' => 'jobs', 'icon' => 'M20 7h-4.5L15 4H9L8.5 7H4v13h16V7zM12 17a3 3 0 100-6 3 3 0 000 6z', 'label' => 'Lowongan'],
                    ['id' => 'interview', 'icon' => 'M12 2a3 3 0 00-3 3v7a3 3 0 006 0V5a3 3 0 00-3-3zM19 10v3a7 7 0 01-14 0v-3M12 19v3', 'label' => 'Simulasi Interview'],
                ];
            @endphp
            @foreach($mainNav as $nav)
            <button onclick="navigateTo('{{ $nav['id'] }}')" data-nav="{{ $nav['id'] }}" class="nav-btn relative flex items-center gap-2.5 rounded-xl transition-all duration-150 w-full px-2 py-2" style="
                background: transparent;
                border: 1px solid transparent;
                color: rgba(255,255,255,0.4);
            ">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="{{ $nav['icon'] }}"/>
                </svg>
                <span class="text-[12px] font-medium flex-1 text-left">{{ $nav['label'] }}</span>
            </button>
            @endforeach
        </div>

        <div class="my-4" style="height: 1px; background: rgba(255,255,255,0.06);"></div>

        <!-- Pengembangan -->
        <p class="text-[9px] text-slate-700 uppercase tracking-[0.14em] font-bold px-2.5 mb-2">Pengembangan</p>
        <div class="space-y-0.5">
            @php
                $devNav = [
                    ['id' => 'certs', 'icon' => 'M12 2l3 4.5 5 .5-3.5 3.5 1 5-5.5-2-5.5 2 1-5L4 7l5-.5L12 2z', 'label' => 'Sertifikasi'],
                    ['id' => 'network', 'icon' => 'M3 12h3l3-9 3 18 3-9h3', 'label' => 'Jaringan'],
                ];
            @endphp
            @foreach($devNav as $nav)
            <button onclick="navigateTo('{{ $nav['id'] }}')" data-nav="{{ $nav['id'] }}" class="nav-btn relative flex items-center gap-2.5 rounded-xl transition-all duration-150 w-full px-2 py-2" style="
                background: transparent;
                border: 1px solid transparent;
                color: rgba(255,255,255,0.4);
            ">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="{{ $nav['icon'] }}"/>
                </svg>
                <span class="text-[12px] font-medium flex-1 text-left">{{ $nav['label'] }}</span>
            </button>
            @endforeach
        </div>

        <!-- Bottom -->
        <div class="mt-auto space-y-0.5">
            @php
                $sysNav = [
                    ['id' => 'settings', 'icon' => 'M12 15a3 3 0 100-6 3 3 0 000 6zM19.4 15a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H5.78a1.65 1.65 0 0 0-1.51 1 1.65 1.65 0 0 0 .33 1.82l.03.03A10 10 0 0 0 12 17.66a10 10 0 0 0 6.37-2.63zM12 2v4', 'label' => 'Pengaturan'],
                    ['id' => 'help', 'icon' => 'M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10zM12 8v4M12 16h.01', 'label' => 'Bantuan'],
                ];
            @endphp
            @foreach($sysNav as $nav)
            <button onclick="navigateTo('{{ $nav['id'] }}')" data-nav="{{ $nav['id'] }}" class="nav-btn relative flex items-center gap-2.5 rounded-xl transition-all duration-150 w-full px-2 py-2" style="
                background: transparent;
                border: 1px solid transparent;
                color: rgba(255,255,255,0.4);
            ">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="{{ $nav['icon'] }}"/>
                </svg>
                <span class="text-[12px] font-medium flex-1 text-left">{{ $nav['label'] }}</span>
            </button>
            @endforeach

            <div class="mt-3 pt-3" style="border-top: 1px solid rgba(255,255,255,0.06);">
                <div class="flex items-center gap-2.5 rounded-xl px-2 py-1.5" style="
                    background: rgba(255,255,255,0.03);
                    border: 1px solid rgba(255,255,255,0.06);
                ">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-[11px] font-bold text-white flex-shrink-0" style="background: linear-gradient(135deg, #818cf8, #3b82f6);">
                        {{ strtoupper(substr(Auth::user()->name ?? 'User', 0, 2)) }}
                    </div>
                    <div class="min-w-0">
                        <p class="text-[11px] font-semibold text-white/85 leading-none truncate">{{ Auth::user()->name ?? 'User' }}</p>
                        <span class="inline-block mt-1 text-[9px] px-1.5 py-px rounded font-bold" style="background: rgba(0,212,255,0.1); color: #00d4ff; border: 1px solid rgba(0,212,255,0.18);">
                            {{ $userData['plan'] }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </aside>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col min-w-0">
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

            <div class="flex items-center gap-2">
                <form action="{{ route('logout') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="flex items-center gap-2.5 h-8 px-3 rounded-lg transition-all duration-150" style="background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.07); color: rgba(255,255,255,0.6); font-size: 12px;">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4M16 17l5-5-5-5M21 12H9"/>
                        </svg>
                        <span>Logout</span>
                    </button>
                </form>
            </div>
        </header>

        <!-- Main Content Area -->
        <main id="mainContent" class="flex-1 p-5 overflow-y-auto">
            <!-- Dashboard Content -->
            <div id="dashboardView" class="space-y-4">
                @include('partials.dashboard_content')
            </div>
            <div id="cvView" style="display: none;">@include('partials.cv_content')</div>
            <div id="jobsView" style="display: none;">@include('partials.jobs_content')</div>
            <div id="interviewView" style="display: none;">@include('partials.interview_content')</div>
            <div id="certsView" style="display: none;">@include('partials.placeholder', ['icon' => '🏆', 'message' => 'Fitur sertifikasi segera hadir'])</div>
            <div id="networkView" style="display: none;">@include('partials.placeholder', ['icon' => '🌐', 'message' => 'Fitur jaringan segera hadir'])</div>
            <div id="settingsView" style="display: none;">@include('partials.placeholder', ['icon' => '⚙️', 'message' => 'Halaman pengaturan'])</div>
            <div id="helpView" style="display: none;">@include('partials.placeholder', ['icon' => '❓', 'message' => 'Pusat bantuan'])</div>
        </main>
    </div>
</div>

<script>
    function navigateTo(view) {
        // Update URL tanpa reload
        const url = new URL(window.location.href);
        url.searchParams.set('view', view);
        window.history.pushState({}, '', url);
        
        // Update active nav
        document.querySelectorAll('.nav-btn').forEach(btn => {
            btn.style.background = 'transparent';
            btn.style.borderColor = 'transparent';
            btn.style.color = 'rgba(255,255,255,0.4)';
        });
        
        const activeBtn = document.querySelector(`[data-nav="${view}"]`);
        if (activeBtn) {
            activeBtn.style.background = 'linear-gradient(135deg, rgba(0,212,255,0.1), rgba(59,130,246,0.07))';
            activeBtn.style.borderColor = 'rgba(0,212,255,0.2)';
            activeBtn.style.color = '#00d4ff';
        }
        
        // Sembunyikan semua view
        document.getElementById('dashboardView').style.display = 'none';
        document.getElementById('cvView').style.display = 'none';
        document.getElementById('jobsView').style.display = 'none';
        document.getElementById('interviewView').style.display = 'none';
        document.getElementById('certsView').style.display = 'none';
        document.getElementById('networkView').style.display = 'none';
        document.getElementById('settingsView').style.display = 'none';
        document.getElementById('helpView').style.display = 'none';
        
        // Tampilkan view yang dipilih
        const viewMap = {
            'dashboard': 'dashboardView',
            'cv': 'cvView',
            'jobs': 'jobsView',
            'interview': 'interviewView',
            'certs': 'certsView',
            'network': 'networkView',
            'settings': 'settingsView',
            'help': 'helpView'
        };
        
        const viewId = viewMap[view];
        if (viewId) {
            document.getElementById(viewId).style.display = 'block';
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
            'help': 'Bantuan'
        };
        
        document.getElementById('pageTitle').textContent = titles[view] || 'Dashboard';
    }
    
    // Initialize
    const urlParams = new URLSearchParams(window.location.search);
    const initialView = urlParams.get('view') || 'dashboard';
    navigateTo(initialView);
    
    // Handle browser back/forward
    window.addEventListener('popstate', function() {
        const params = new URLSearchParams(window.location.search);
        const view = params.get('view') || 'dashboard';
        navigateTo(view);
    });
    
    // Sidebar toggle
    let sidebarExpanded = true;
    document.getElementById('toggleSidebar')?.addEventListener('click', function() {
        const sidebar = document.getElementById('sidebar');
        sidebarExpanded = !sidebarExpanded;
        sidebar.style.width = sidebarExpanded ? '220px' : '64px';
    });
    
    // Upload CV handler
    function handleUploadCV() {
        Swal.fire({
            title: 'Upload CV',
            text: 'Fitur upload CV akan segera tersedia',
            icon: 'info',
            background: '#07111f',
            color: '#fff'
        });
    }
</script>

</body>
</html>