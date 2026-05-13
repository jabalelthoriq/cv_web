
<!-- Welcome Banner -->
<div class="relative rounded-2xl overflow-hidden mb-4" style="background: linear-gradient(135deg, #07182e 0%, #030c18 100%); border: 1px solid rgba(0,212,255,0.12);">
    <div class="relative p-6">
        <div class="flex items-start justify-between gap-4 flex-wrap">
            <div>
                <div class="flex items-center gap-2 mb-2">
                    <div class="w-1.5 h-1.5 rounded-full animate-pulse" style="background: #10b981; box-shadow: 0 0 8px #10b981;"></div>
                    <span class="text-[10px] font-bold uppercase tracking-[0.2em]" style="color: rgba(0,212,255,0.6);">Selamat Datang Kembali</span>
                </div>
                <h1 class="text-2xl font-black text-white leading-tight mb-1" style="font-family: 'Space Grotesk', sans-serif; letter-spacing: -0.6px;">
                    {{ $userData['name'] }}
                    <span class="ml-2 text-2xl">👋</span>
                </h1>
                @php
                    $cv = auth()->user()->cvs()->latest()->first();
                    $completion = 0;

                    if ($cv && $cv->analysis) {
                        $analysis = is_array($cv->analysis) ? $cv->analysis : json_decode($cv->analysis, true);
                        $strengths = $analysis['strengths'] ?? [];
                        $completion = round((count($strengths) / 8) * 100);
                    }
                @endphp

                <p class="text-[13px] text-slate-400 mt-1.5">
                    Cv kamu <span class="font-semibold" style="color: #00d4ff;">{{ $completion }}% lengkap</span> — tingkatkan untuk match yang lebih baik
                </p>
            </div>
            <button onclick="changeTab('cv')" class="flex-shrink-0 flex items-center gap-2 px-5 py-2.5 rounded-xl text-[12px] font-bold transition-all duration-200 hover:scale-105 hover:brightness-110" style="background: linear-gradient(135deg, #00d4ff, #3b82f6); color: #020810;">
                <span>Analisis Cv</span>
                <span>→</span>
            </button>
        </div>
        
        <!-- Progress -->
        <div class="mt-5">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[10px] text-slate-600 uppercase tracking-[0.12em] font-semibold">Kelengkapan Cv</span>
                <span class="text-[10px] font-bold" style="color: #00d4ff;">{{ $completion }}%</span>
            </div>
            <div class="h-1.5 rounded-full overflow-hidden" style="background: rgba(255,255,255,0.05);">
                <div class="h-full rounded-full relative overflow-hidden" style="width: {{ $completion }}%; background: linear-gradient(to right, #00d4ff, #3b82f6);"></div>
            </div>
        </div>
    </div>
</div>

<!-- Stats Cards -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
    <div class="relative rounded-2xl p-5 overflow-hidden transition-all duration-300" style="background: linear-gradient(145deg, #07111f 0%, #030810 100%); border: 1px solid rgba(255,255,255,0.07);">
        <div class="flex items-start justify-between mb-4">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center text-[18px]" style="background: #00d4ff14; border: 1px solid #00d4ff28;">📊</div>
        </div>
        <div class="text-[32px] font-black leading-none mb-1.5 tabular-nums" style="font-family: 'Space Grotesk', sans-serif; letter-spacing: -1.5px;">{{ $cvAnalysis['score'] }}</div>
        <div class="text-[11px] font-semibold text-slate-500 uppercase tracking-[0.12em]">Skor CV</div>
        <div class="mt-3 flex items-center gap-1.5">
           <div class="text-[10px] font-bold px-2 py-0.5 rounded-full"
                style="background: #00d4ff15; color: #00d4ff; border: 1px solid #00d4ff25;">
                {{ $cvAnalysis['total_cv'] }} Total CV
            </div>
        </div>
    </div>

    <div class="relative rounded-2xl p-5 overflow-hidden transition-all duration-300" style="background: linear-gradient(145deg, #07111f 0%, #030810 100%); border: 1px solid rgba(255,255,255,0.07);">
        <div class="flex items-start justify-between mb-4">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center text-[18px]" style="background: #3b82f614; border: 1px solid #3b82f628;">💼</div>
        </div>
        <div class="text-[32px] font-black leading-none mb-1.5 tabular-nums" style="font-family: 'Space Grotesk', sans-serif; letter-spacing: -1.5px;">{{ $jobData->count() }}</div>
        <div class="text-[11px] font-semibold text-slate-500 uppercase tracking-[0.12em]">Job Match</div>
        <div class="mt-3 flex items-center gap-1.5">
            <div class="text-[10px] font-bold px-2 py-0.5 rounded-full" style="background: #3b82f615; color: #3b82f6; border: 1px solid #3b82f625;">Rekomendasi</div>
        </div>
    </div>

    <div class="relative rounded-2xl p-5 overflow-hidden transition-all duration-300" style="background: linear-gradient(145deg, #07111f 0%, #030810 100%); border: 1px solid rgba(255,255,255,0.07);">
        <div class="flex items-start justify-between mb-4">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center text-[18px]" style="background: #818cf814; border: 1px solid #818cf828;">🎙️</div>
        </div>
        <div class="text-[32px] font-black leading-none mb-1.5 tabular-nums" style="font-family: 'Space Grotesk', sans-serif; letter-spacing: -1.5px;">{{ $interviewData['total_sessions'] }}</div>
        <div class="text-[11px] font-semibold text-slate-500 uppercase tracking-[0.12em]">Sesi Interview</div>
        <div class="mt-3 flex items-center gap-1.5">
            <div class="text-[10px] font-bold px-2 py-0.5 rounded-full" style="background: #818cf815; color: #818cf8; border: 1px solid #818cf825;">Avg {{ $interviewData['average_score'] }}</div>
        </div>
    </div>

    <div class="relative rounded-2xl p-5 overflow-hidden transition-all duration-300" style="background: linear-gradient(145deg, #07111f 0%, #030810 100%); border: 1px solid rgba(255,255,255,0.07);">
        <div class="flex items-start justify-between mb-4">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center text-[18px]" style="background: #10b98114; border: 1px solid #10b98128;">📝</div>
        </div>
        <div class="text-[32px] font-black leading-none mb-1.5 tabular-nums" style="font-family: 'Space Grotesk', sans-serif; letter-spacing: -1.5px;">{{ $cvAnalysis['ats_passed'] }}</div>
        <div class="text-[11px] font-semibold text-slate-500 uppercase tracking-[0.12em]">ATS Passed</div>
        <div class="mt-3 flex items-center gap-1.5">
            <div class="text-[10px] font-bold px-2 py-0.5 rounded-full" style="background: #10b98115; color: #10b981; border: 1px solid #10b98125;">Strengths</div>
        </div>
    </div>
</div>

<!-- Main Grid -->
<div class="grid lg:grid-cols-2 gap-4">
    <!-- Left Column -->
    <div class="space-y-4">
        <!-- Upload Zone -->
        <div class="rounded-2xl overflow-hidden" style="background: linear-gradient(145deg, #07111f 0%, #030810 100%); border: 1px solid rgba(255,255,255,0.07);">
            <div class="flex items-center justify-between px-5 py-4" style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                <div class="flex items-center gap-2">
                    <div class="w-1.5 h-1.5 rounded-full" style="background: #00d4ff;"></div>
                    <span class="text-[10px] font-bold uppercase tracking-[0.18em]" style="color: #00d4ff;">Upload CV</span>
                </div>
            </div>
            <div class="px-5 py-4">
                <div onclick="handleUploadCV()" class="rounded-xl p-6 text-center transition-all duration-200 cursor-pointer relative overflow-hidden" style="border: 1.5px dashed rgba(0,212,255,0.18); background: rgba(0,212,255,0.02);">
                    <div class="w-14 h-14 rounded-2xl mx-auto mb-3 flex items-center justify-center text-2xl relative" style="background: linear-gradient(135deg, rgba(0,212,255,0.15), rgba(59,130,246,0.15)); border: 1px solid rgba(0,212,255,0.2);">📄</div>
                    <p class="text-sm font-semibold text-white/80 mb-1">Drag & drop CV kamu di sini</p>
                    <p class="text-[11px] text-slate-500">atau <span class="font-semibold" style="color: #00d4ff;">pilih file</span> dari perangkat kamu</p>
                    <div class="flex items-center justify-center gap-2 mt-4">
                        <span class="text-[9px] font-bold px-2 py-1 rounded-md uppercase" style="background: rgba(0,212,255,0.08); border: 1px solid rgba(0,212,255,0.15); color: rgba(0,212,255,0.7);">PDF</span>
                        <span class="text-[9px] font-bold px-2 py-1 rounded-md uppercase" style="background: rgba(0,212,255,0.08); border: 1px solid rgba(0,212,255,0.15); color: rgba(0,212,255,0.7);">DOCX</span>
                        <span class="text-[9px] font-bold px-2 py-1 rounded-md uppercase" style="background: rgba(0,212,255,0.08); border: 1px solid rgba(0,212,255,0.15); color: rgba(0,212,255,0.7);">DOC</span>
                        <span class="text-[10px] text-slate-600">• Maks. 5 MB</span>
                    </div>
                </div>
            </div>
        </div>

      <!-- Analisis CV (Horizontal) -->
<div class="rounded-2xl overflow-hidden"
     style="background: linear-gradient(145deg, #07111f 0%, #030810 100%); border: 1px solid rgba(255,255,255,0.07);">

    <!-- Header -->
    <div class="flex items-center justify-between px-5 py-4"
         style="border-bottom: 1px solid rgba(255,255,255,0.05);">
        
        <div class="flex items-center gap-2">
            <div class="w-1.5 h-1.5 rounded-full bg-green-400"></div>
            <span class="text-[10px] font-bold uppercase tracking-[0.18em] text-green-400">
                Analisis CV AI
            </span>
        </div>

        <button onclick="changeTab('cv')"
                class="text-[10px] font-semibold uppercase tracking-widest text-white/30 hover:text-white/60">
            Detail →
        </button>
    </div>

    <!-- Content 3 Kolom -->
    <div class="grid grid-cols-3 gap-4 px-5 py-4">

        <!-- STRENGTHS -->
        <div>
            <div class="text-[11px] font-semibold text-green-400 mb-2">
                ✅ Kelebihan ({{ count($cvAnalysis['strengths']) }})
            </div>

            @forelse($cvAnalysis['strengths'] as $item)
                <div class="text-[12px] text-white/70 mb-1 pl-3 relative">
                    <span class="absolute left-0 top-1 w-1 h-1 rounded-full bg-green-400"></span>
                    {{ $item }}
                </div>
            @empty
                <div class="text-[11px] text-white/30">-</div>
            @endforelse
        </div>

        <!-- WEAKNESSES -->
        <div>
            <div class="text-[11px] font-semibold text-red-400 mb-2">
                ⚠️ Kekurangan ({{ count($cvAnalysis['weaknesses']) }})
            </div>

            @forelse($cvAnalysis['weaknesses'] as $item)
                <div class="text-[12px] text-white/70 mb-1 pl-3 relative">
                    <span class="absolute left-0 top-1 w-1 h-1 rounded-full bg-red-400"></span>
                    {{ $item }}
                </div>
            @empty
                <div class="text-[11px] text-white/30">-</div>
            @endforelse
        </div>

        <!-- SUGGESTIONS -->
        <div>
            <div class="text-[11px] font-semibold text-yellow-400 mb-2">
                💡 Saran ({{ count($cvAnalysis['suggestions']) }})
            </div>

            @forelse($cvAnalysis['suggestions'] as $item)
                <div class="text-[12px] text-white/70 mb-1 pl-3 relative">
                    <span class="absolute left-0 top-1 w-1 h-1 rounded-full bg-yellow-400"></span>
                    {{ $item }}
                </div>
            @empty
                <div class="text-[11px] text-white/30">-</div>
            @endforelse
        </div>

    </div>
</div>

        <!-- Skor Interview -->
<div class="rounded-2xl overflow-hidden"
     style="background: linear-gradient(145deg, #07111f 0%, #030810 100%);
            border: 1px solid rgba(255,255,255,0.07);">

    <!-- HEADER -->
    <div class="flex items-center justify-between px-5 py-4"
         style="border-bottom: 1px solid rgba(255,255,255,0.05);">

        <div class="flex items-center gap-2">
            <div class="w-1.5 h-1.5 rounded-full bg-cyan-400"></div>
            <span class="text-[10px] font-bold uppercase tracking-[0.18em] text-cyan-400">
                Interview Score Breakdown
            </span>
        </div>
    </div>

    <!-- CONTENT -->
    <div class="px-5 py-5">

        @php
            $defaultBreakdown = [
                'confidence' => 0,
                'technical' => 0,
                'communication' => 0,
                'problem_solving' => 0,
                'relevance' => 0,
            ];

            $labels = [
                'confidence' => 'Confidence',
                'technical' => 'Technical',
                'communication' => 'Communication',
                'problem_solving' => 'Problem Solving',
                'relevance' => 'Relevance',
            ];

            $colors = [
                'confidence' => '#818cf8',
                'technical' => '#00d4ff',
                'communication' => '#f59e0b',
                'problem_solving' => '#10b981',
                'relevance' => '#ef4444',
            ];

            $breakdown = $interviewData['breakdown'] ?? $defaultBreakdown;
        @endphp

        <div class="grid grid-cols-3 md:grid-cols-5 gap-4">

            @foreach($breakdown as $key => $score)
                @php
                    $r = 26;
                    $circ = 2 * M_PI * $r;
                    $dash = ($score / 100) * $circ;
                @endphp

                <div class="flex flex-col items-center gap-2">

                    <!-- CIRCLE -->
                    <div class="relative w-16 h-16 group">
                        <svg width="64" height="64">
                            <circle cx="32" cy="32" r="{{ $r }}"
                                fill="none"
                                stroke="rgba(255,255,255,0.06)"
                                stroke-width="4"/>

                            <circle cx="32" cy="32" r="{{ $r }}"
                                fill="none"
                                stroke="{{ $colors[$key] }}"
                                stroke-width="4"
                                stroke-dasharray="{{ $dash }} {{ $circ }}"
                                stroke-linecap="round"
                                transform="rotate(-90 32 32)"
                                class="transition-all duration-500 group-hover:opacity-100"
                                style="opacity: 0.85"/>
                        </svg>

                        <!-- VALUE -->
                        <div class="absolute inset-0 flex items-center justify-center">
                            <span class="text-[13px] font-black"
                                  style="color: {{ $colors[$key] }}">
                                {{ $score }}
                            </span>
                        </div>
                    </div>

                    <!-- LABEL -->
                    <span class="text-[10px] text-slate-400 text-center leading-tight">
                        {{ $labels[$key] }}
                    </span>

                </div>
            @endforeach

        </div>

        <!-- EMPTY STATE -->
        @if(empty($interviewData['breakdown']))
            <div class="text-center mt-5 text-[11px] text-slate-500">
                Belum ada data detail penilaian (AI hanya mengembalikan skor total)
            </div>
        @endif

    </div>
</div>
    </div>

    <!-- Right Column -->
    <div class="space-y-4">
        <!-- Rekomendasi Lowongan -->
        <div class="rounded-2xl overflow-hidden" style="background: linear-gradient(145deg, #07111f 0%, #030810 100%); border: 1px solid rgba(255,255,255,0.07);">
            <div class="flex items-center justify-between px-5 py-4" style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                <div class="flex items-center gap-2">
                    <div class="w-1.5 h-1.5 rounded-full" style="background: #3b82f6;"></div>
                    <span class="text-[10px] font-bold uppercase tracking-[0.18em]" style="color: #3b82f6;">Rekomendasi Lowongan</span>
                </div>
                <button onclick="changeTab('jobs')" class="text-[10px] font-semibold uppercase tracking-widest transition-colors" style="color: rgba(255,255,255,0.25); background: none; border: none;">Lihat Semua →</button>
            </div>
            <div class="px-5 py-4">
                <div class="space-y-2">
                    @forelse($jobData->take(3) as $job)
                    @php 
                        $matchClass = $job['match_score'] >= 90 ? '#10b981' : '#00d4ff'; 
                    @endphp

                    <a href="{{ $job['link'] }}" target="_blank" class="block">
                        <div class="flex items-center gap-3 px-4 py-3 rounded-xl cursor-pointer transition hover:scale-[1.02]"
                            style="border: 1px solid rgba(255,255,255,0.05); background: rgba(255,255,255,0.02);">
                            
                            <div class="w-9 h-9 rounded-lg flex items-center justify-center text-lg flex-shrink-0"
                                style="background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.08);">
                                💼
                            </div>

                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between gap-2">
                                    <span class="text-[13px] font-semibold text-white/85 truncate">
                                        {{ $job['job_title'] }}
                                    </span>

                                    <span class="text-[10px] font-bold flex-shrink-0 px-2 py-0.5 rounded-full"
                                        style="background: {{ $matchClass }}18; color: {{ $matchClass }}; border: 1px solid {{ $matchClass }}25;">
                                        {{ $job['match_score'] }}% match
                                    </span>
                                </div>

                                <div class="flex items-center gap-2 mt-0.5">
                                    <span class="text-[11px] text-slate-500">
                                        {{ $job['company'] }}
                                    </span>

                                    <span class="text-slate-700">·</span>

                                    <div class="flex gap-1">
                                        @foreach($job['tags'] as $tag)
                                        <span class="text-[9px] px-1.5 py-px rounded font-medium"
                                            style="background: rgba(255,255,255,0.05); color: rgba(255,255,255,0.35);">
                                            {{ $tag }}
                                        </span>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                        </div>
                    </a>

                    @empty
                    <div class="text-center py-6 text-slate-500">
                        Belum ada rekomendasi lowongan
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Aktivitas Terbaru -->
        <div class="rounded-2xl overflow-hidden" style="background: linear-gradient(145deg, #07111f 0%, #030810 100%); border: 1px solid rgba(255,255,255,0.07);">
            <div class="flex items-center justify-between px-5 py-4" style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                <div class="flex items-center gap-2">
                    <div class="w-1.5 h-1.5 rounded-full" style="background: #00d4ff;"></div>
                    <span class="text-[10px] font-bold uppercase tracking-[0.18em]" style="color: #00d4ff;">Aktivitas Terbaru</span>
                </div>
            </div>
            <div class="px-5 py-4">
                <div class="-mx-1">
                    @forelse($activities->take(5) as $activity)
                    <div class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-150">
                        <div class="flex flex-col items-center gap-0.5 flex-shrink-0">
                            <div class="w-1.5 h-1.5 rounded-full" style="background: #00d4ff; box-shadow: 0 0 5px #00d4ff;"></div>
                        </div>
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center text-[13px] flex-shrink-0" style="background: #00d4ff14; border: 1px solid #00d4ff22;">
                            @if($activity['type'] == 'cv_upload') 📄
                            @elseif($activity['type'] == 'interview') 🎙️
                            @elseif($activity['type'] == 'job_match') 💼
                            @else 📌
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-[12px] font-medium text-white/75 truncate">{{ $activity['message'] }}</p>
                        </div>
                        <span class="text-[10px] text-slate-600 flex-shrink-0">{{ $activity['time']->diffForHumans() }}</span>
                    </div>
                    @empty
                    <div class="text-center py-6 text-slate-500">Belum ada aktivitas</div>
                    @endforelse
                </div>
            </div>
        </div>

        
    </div>
</div>