<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="user-id" content="{{ auth()->id() }}">
    <title>Dashboard - NEXUS.AI</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
            <button onclick="changeTab('{{ $nav['id'] }}')" data-nav="{{ $nav['id'] }}" class="nav-btn relative flex items-center gap-2.5 rounded-xl transition-all duration-150 w-full px-2 py-2" style="
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
            <button onclick="changeTab('{{ $nav['id'] }}')" data-nav="{{ $nav['id'] }}" class="nav-btn relative flex items-center gap-2.5 rounded-xl transition-all duration-150 w-full px-2 py-2" style="
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
            <button onclick="changeTab('{{ $nav['id'] }}')" data-nav="{{ $nav['id'] }}" class="nav-btn relative flex items-center gap-2.5 rounded-xl transition-all duration-150 w-full px-2 py-2" style="
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
                   @php
    $user = Auth::user();
    $plan = optional($user->activeSubscription)->plan ?? 'free';
@endphp

<div class="w-8 h-8 rounded-full flex items-center justify-center text-[11px] font-bold text-white flex-shrink-0" 
     style="background: linear-gradient(135deg, #818cf8, #3b82f6);">
    {{ strtoupper(substr($user->name ?? 'User', 0, 2)) }}
</div>

<div class="min-w-0">
    <p class="text-[11px] font-semibold text-white/85 leading-none truncate">
        {{ $user->name ?? 'User' }}
    </p>

    <span class="inline-block mt-1 text-[9px] px-1.5 py-px rounded font-bold"
          style="background: rgba(0,212,255,0.1); color: #00d4ff; border: 1px solid rgba(0,212,255,0.18);">
        {{ strtoupper($plan) }}
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
            <!-- Dashboard Content -->
            <div id="dashboardView" class="space-y-4">
                @include('partials.dashboard_content')
            </div>
            <div id="jobsView" style="display: none;">@include('partials.jobs_content')</div>
   
        
            <div id="interviewView" style="display: none;">@include('partials.interview_content')</div>
            <div id="certsView" style="display: none;">@include('partials.placeholder', ['icon' => '🏆', 'message' => 'Fitur sertifikasi segera hadir'])</div>
            <div id="networkView" style="display: none;">@include('partials.placeholder', ['icon' => '🌐', 'message' => 'Fitur jaringan segera hadir'])</div>
            <div id="settingsView" style="display: none;">@include('partials.placeholder', ['icon' => '⚙️', 'message' => 'Halaman pengaturan'])</div>
            <div id="helpView" style="display: none;">@include('partials.placeholder', ['icon' => '❓', 'message' => 'Pusat bantuan'])</div>

            <div id="cvView" style="display: none;">@include('partials.cv_content')</div>
        </main>
    </div>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>

<script>
    // ========================================
    // GLOBAL VARIABLES
    // ========================================
    let currentView = '{{ $selectedView }}';
    
    // ========================================
    // NAVIGATION FUNCTIONS
    // ========================================
    function navigateTo(view) {
        console.log('Navigating to:', view);
        currentView = view;
        
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
        
        // Hide all views
        document.getElementById('dashboardView').style.display = 'none';
        document.getElementById('cvView').style.display = 'none';
        document.getElementById('jobsView').style.display = 'none';
        document.getElementById('interviewView').style.display = 'none';
        document.getElementById('certsView').style.display = 'none';
        document.getElementById('networkView').style.display = 'none';
        document.getElementById('settingsView').style.display = 'none';
        document.getElementById('helpView').style.display = 'none';
        
        // Show selected view
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
            console.log('Showing:', viewId);
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
        
        // === INITIALIZE VIEW-SPECIFIC FEATURES ===
        if (view === 'cv') {
            initializeCVFeatures();
        } else if (view === 'jobs') {
            console.log('Jobs view active - no CV scripts will run');
        }
    }
    
    function changeTab(view) {
        window.location.href = '/dashboard?view=' + view;
    }
    
    // ========================================
    // CV FEATURES (ONLY INITIALIZED WHEN ON CV PAGE)
    // ========================================
    let pdfDoc = null;
    let currentPageNum = 1;
    
    function initializeCVFeatures() {
        console.log('Initializing CV features...');
        
        // Set PDF.js worker
        pdfjsLib.GlobalWorkerOptions.workerSrc = "https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js";
        
        // Reset variables
        pdfDoc = null;
        currentPageNum = 1;
        
        @php
            $latestCvDebug = isset($latestCv)
                ? [
                    'id' => $latestCv->id,
                    'score' => $latestCv->score ?? null,
                    'file_path' => $latestCv->file_path ?? null,
                ]
                : null;
        @endphp
        
        const url = @json(isset($latestCv) && $latestCv->file_path
            ? asset('storage/' . $latestCv->file_path)
            : null);
        
        console.log('CV Features Init:', { url, hasCanvas: !!document.getElementById('pdfCanvas') });
        
        if (url && document.getElementById('pdfCanvas')) {
            pdfjsLib.getDocument(url).promise
                .then(pdf => {
                    pdfDoc = pdf;
                    currentPageNum = 1;
                    const pageCountEl = document.getElementById('pageCount');
                    if (pageCountEl) pageCountEl.textContent = pdf.numPages;
                    renderCVPage(1);
                })
                .catch(err => console.error('PDF render error', err));
        } else {
            console.log('No PDF to render or canvas missing');
        }
    }
    
    function renderCVPage(num) {
        if (!pdfDoc) return;
        if (num < 1 || num > pdfDoc.numPages) return;
        
        pdfDoc.getPage(num).then(page => {
            const canvas = document.getElementById('pdfCanvas');
            if (!canvas) return;
            const ctx = canvas.getContext('2d');
            const viewport = page.getViewport({ scale: 1.4 });
            canvas.height = viewport.height;
            canvas.width = viewport.width;
            page.render({ canvasContext: ctx, viewport: viewport });
            const pageNumEl = document.getElementById('pageNum');
            if (pageNumEl) pageNumEl.textContent = num;
            currentPageNum = num;
        });
    }
    
    // Make CV functions available globally (but only called when on CV page)
    window.nextPage = function() {
        if (currentView !== 'cv') return;
        if (!pdfDoc) return;
        if (currentPageNum >= pdfDoc.numPages) return;
        currentPageNum++;
        renderCVPage(currentPageNum);
    };
    
    window.prevPage = function() {
        if (currentView !== 'cv') return;
        if (!pdfDoc) return;
        if (currentPageNum <= 1) return;
        currentPageNum--;
        renderCVPage(currentPageNum);
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
        pdfjsLib.getDocument(data.url).promise.then(pdf => {
            pdfDoc = pdf;
            currentPageNum = 1;
            const pageCountEl = document.getElementById('pageCount');
            if (pageCountEl) pageCountEl.textContent = pdf.numPages;
            renderCVPage(currentPageNum);
        });
        
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
    // SIDEBAR TOGGLE
    // ========================================
    let sidebarExpanded = true;
    const toggleBtn = document.getElementById('toggleSidebar');
    if (toggleBtn) {
        toggleBtn.addEventListener('click', function() {
            const sidebar = document.getElementById('sidebar');
            sidebarExpanded = !sidebarExpanded;
            sidebar.style.width = sidebarExpanded ? '220px' : '64px';
        });
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
let voices = [];

speechSynthesis.onvoiceschanged = () => {
    voices = speechSynthesis.getVoices();
};
    
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
    }

    // LOAD JAWABAN
    if (userAnswers[currentQuestionIndex]) {
        document.getElementById("answerInput").value =
            userAnswers[currentQuestionIndex];
    } else {
        document.getElementById("answerInput").value = '';
    }

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

    const indoVoice = voices.find(v => v.lang === "id-ID");

    if (indoVoice) {
        utterance.voice = indoVoice;
    }

    utterance.rate = 0.9;
    utterance.pitch = 1;

    window.speechSynthesis.cancel();
    window.speechSynthesis.speak(utterance);
}
function initSpeechRecognition() {
    const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;

    if (!SpeechRecognition) {
        alert("Browser tidak mendukung Speech Recognition (gunakan Chrome)");
        return;
    }

    recognition = new SpeechRecognition();
    recognition.lang = "id-ID";
    recognition.continuous = true;
    recognition.interimResults = true;

    recognition.onresult = function (event) {
        let transcript = '';

        for (let i = event.resultIndex; i < event.results.length; i++) {
            transcript += event.results[i][0].transcript;
        }

        document.getElementById("answerInput").value = transcript;
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
            alert("Izinkan akses microphone dulu");
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
function toggleRecording() {
    if (!recognition) initSpeechRecognition();

    if (!isRecording) {
        // 🔥 STOP TTS dulu
        window.speechSynthesis.cancel();

        safeStartRecognition();
        isRecording = true;
        updateMicUI(true);
    } else {
        isRecording = false;
        recognition.stop();
        updateMicUI(false);
    }
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

    // 🔥 STOP TTS
    window.speechSynthesis.cancel();

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