<?php
$avgCompletion = collect($profileCompletion)->avg();
?>

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
                <p class="text-[13px] text-slate-400 mt-1.5">
                    Profil kamu <span class="font-semibold" style="color: #00d4ff;">{{ round($avgCompletion) }}% lengkap</span> — tingkatkan untuk match yang lebih baik
                </p>
            </div>
            <button onclick="navigateTo('cv')" class="flex-shrink-0 flex items-center gap-2 px-5 py-2.5 rounded-xl text-[12px] font-bold transition-all duration-200 hover:scale-105 hover:brightness-110" style="background: linear-gradient(135deg, #00d4ff, #3b82f6); color: #020810;">
                <span>Lengkapi Profil</span>
                <span>→</span>
            </button>
        </div>
        
        <!-- Progress -->
        <div class="mt-5">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[10px] text-slate-600 uppercase tracking-[0.12em] font-semibold">Kelengkapan Profil</span>
                <span class="text-[10px] font-bold" style="color: #00d4ff;">{{ round($avgCompletion) }}%</span>
            </div>
            <div class="h-1.5 rounded-full overflow-hidden" style="background: rgba(255,255,255,0.05);">
                <div class="h-full rounded-full relative overflow-hidden" style="width: {{ $avgCompletion }}%; background: linear-gradient(to right, #00d4ff, #3b82f6);"></div>
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
            <div class="text-[10px] font-bold px-2 py-0.5 rounded-full" style="background: #00d4ff15; color: #00d4ff; border: 1px solid #00d4ff25;">
                {{ $cvAnalysis['delta'] > 0 ? '+' . $cvAnalysis['delta'] : $cvAnalysis['delta'] }}
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

        <!-- Kelengkapan CV -->
        <div class="rounded-2xl overflow-hidden" style="background: linear-gradient(145deg, #07111f 0%, #030810 100%); border: 1px solid rgba(255,255,255,0.07);">
            <div class="flex items-center justify-between px-5 py-4" style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                <div class="flex items-center gap-2">
                    <div class="w-1.5 h-1.5 rounded-full" style="background: #3b82f6;"></div>
                    <span class="text-[10px] font-bold uppercase tracking-[0.18em]" style="color: #3b82f6;">Kelengkapan CV</span>
                </div>
                <button onclick="navigateTo('cv')" class="text-[10px] font-semibold uppercase tracking-widest transition-colors" style="color: rgba(255,255,255,0.25); background: none; border: none;">Detail →</button>
            </div>
            <div class="px-5 py-4">
                <div class="space-y-1.5">
                    @foreach(['personal_info' => '👤', 'experience' => '💼', 'education' => '🎓', 'skills' => '⚡', 'portfolio' => '📎'] as $key => $icon)
                    @php
                        $colors = ['personal_info' => '#00d4ff', 'experience' => '#3b82f6', 'education' => '#818cf8', 'skills' => '#f59e0b', 'portfolio' => '#f43f5e'];
                        $labels = ['personal_info' => 'Informasi Pribadi', 'experience' => 'Pengalaman Kerja', 'education' => 'Pendidikan', 'skills' => 'Keahlian', 'portfolio' => 'Portofolio'];
                        $color = $colors[$key];
                        $value = $profileCompletion[$key] ?? 0;
                    @endphp
                    <div class="flex items-center gap-3">
                        <div class="w-7 h-7 rounded-lg flex items-center justify-center text-[12px] flex-shrink-0" style="background: {{ $color }}14; border: 1px solid {{ $color }}22;">{{ $icon }}</div>
                        <span class="text-[12px] text-white/65 flex-1">{{ $labels[$key] }}</span>
                        <span class="text-[11px] font-bold w-8 text-right flex-shrink-0" style="color: {{ $color }};">{{ $value }}%</span>
                        <div class="w-20 h-1.5 rounded-full overflow-hidden flex-shrink-0" style="background: rgba(255,255,255,0.06);">
                            <div class="h-full rounded-full transition-all duration-700" style="width: {{ $value }}%; background: linear-gradient(to right, {{ $color }}cc, {{ $color }});"></div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Skor Interview -->
        <div class="rounded-2xl overflow-hidden" style="background: linear-gradient(145deg, #07111f 0%, #030810 100%); border: 1px solid rgba(255,255,255,0.07);">
            <div class="flex items-center justify-between px-5 py-4" style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                <div class="flex items-center gap-2">
                    <div class="w-1.5 h-1.5 rounded-full" style="background: #818cf8;"></div>
                    <span class="text-[10px] font-bold uppercase tracking-[0.18em]" style="color: #818cf8;">Skor Interview</span>
                </div>
            </div>
            <div class="px-5 py-4">
                <div class="flex items-center justify-around py-1">
                    @foreach(['confidence' => 'Kepercayaan Diri', 'technical' => 'Teknis', 'communication' => 'Komunikasi', 'problem_solving' => 'Problem Solving'] as $key => $label)
                    @php
                        $colors = ['confidence' => '#818cf8', 'technical' => '#00d4ff', 'communication' => '#f59e0b', 'problem_solving' => '#10b981'];
                        $score = $interviewData['breakdown'][$key] ?? 0;
                        $color = $colors[$key];
                        $r = 28;
                        $circ = 2 * M_PI * $r;
                        $dash = ($score / 100) * $circ;
                    @endphp
                    <div class="flex flex-col items-center gap-1">
                        <div class="relative w-16 h-16">
                            <svg width="64" height="64" viewBox="0 0 64 64">
                                <circle cx="32" cy="32" r="{{ $r }}" fill="none" stroke="rgba(255,255,255,0.06)" stroke-width="4"/>
                                <circle cx="32" cy="32" r="{{ $r }}" fill="none" stroke="{{ $color }}" stroke-width="4" stroke-dasharray="{{ $dash }} {{ $circ }}" stroke-linecap="round" transform="rotate(-90 32 32)" opacity="0.85"/>
                            </svg>
                            <div class="absolute inset-0 flex items-center justify-center">
                                <span class="text-[13px] font-black" style="color: {{ $color }}; font-family: 'Space Grotesk', sans-serif;">{{ $score }}</span>
                            </div>
                        </div>
                        <span class="text-[10px] text-slate-500 text-center">{{ $label }}</span>
                    </div>
                    @endforeach
                </div>
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
                <button onclick="navigateTo('jobs')" class="text-[10px] font-semibold uppercase tracking-widest transition-colors" style="color: rgba(255,255,255,0.25); background: none; border: none;">Lihat Semua →</button>
            </div>
            <div class="px-5 py-4">
                <div class="space-y-2">
                    @forelse($jobData->take(3) as $job)
                    @php $matchClass = $job['match_score'] >= 90 ? '#10b981' : '#00d4ff'; @endphp
                    <div class="flex items-center gap-3 px-4 py-3 rounded-xl" style="border: 1px solid rgba(255,255,255,0.05); background: rgba(255,255,255,0.02);">
                        <div class="w-9 h-9 rounded-lg flex items-center justify-center text-lg flex-shrink-0" style="background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.08);">💼</div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between gap-2">
                                <span class="text-[13px] font-semibold text-white/85 truncate">{{ $job['job_title'] }}</span>
                                <span class="text-[10px] font-bold flex-shrink-0 px-2 py-0.5 rounded-full" style="background: {{ $matchClass }}18; color: {{ $matchClass }}; border: 1px solid {{ $matchClass }}25;">{{ $job['match_score'] }}% match</span>
                            </div>
                            <div class="flex items-center gap-2 mt-0.5">
                                <span class="text-[11px] text-slate-500">{{ $job['company'] }}</span>
                                <span class="text-slate-700">·</span>
                                <div class="flex gap-1">
                                    @foreach($job['tags'] as $tag)
                                    <span class="text-[9px] px-1.5 py-px rounded font-medium" style="background: rgba(255,255,255,0.05); color: rgba(255,255,255,0.35);">{{ $tag }}</span>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-6 text-slate-500">Belum ada rekomendasi lowongan</div>
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

        <!-- Aksi Cepat -->
        <div class="rounded-2xl overflow-hidden" style="background: linear-gradient(145deg, #07111f 0%, #030810 100%); border: 1px solid rgba(255,255,255,0.07);">
            <div class="flex items-center justify-between px-5 py-4" style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                <div class="flex items-center gap-2">
                    <div class="w-1.5 h-1.5 rounded-full" style="background: #f59e0b;"></div>
                    <span class="text-[10px] font-bold uppercase tracking-[0.18em]" style="color: #f59e0b;">Aksi Cepat</span>
                </div>
            </div>
            <div class="px-5 py-4">
                <div class="grid grid-cols-2 gap-2.5">
                    @php
                        $actions = [
                            ['icon' => '🎙️', 'label' => 'Mulai Interview', 'sub' => 'AI-powered', 'color' => '#00d4ff', 'id' => 'interview'],
                            ['icon' => '📝', 'label' => 'Analisis CV', 'sub' => 'Cek skor ATS', 'color' => '#3b82f6', 'id' => 'cv'],
                            ['icon' => '🔍', 'label' => 'Cari Lowongan', 'sub' => 'Match terbaru', 'color' => '#818cf8', 'id' => 'jobs'],
                            ['icon' => '📊', 'label' => 'Lihat Statistik', 'sub' => 'Progress kamu', 'color' => '#f59e0b', 'id' => 'dashboard'],
                        ];
                    @endphp
                    @foreach($actions as $action)
                    <button onclick="navigateTo('{{ $action['id'] }}')" class="flex items-center gap-3 rounded-xl text-left transition-all duration-200 hover:-translate-y-0.5 group" style="padding: 12px; background: {{ $action['color'] }}08; border: 1px solid {{ $action['color'] }}18;">
                        <div class="rounded-xl flex items-center justify-center text-base flex-shrink-0" style="width: 32px; height: 32px; background: {{ $action['color'] }}15; border: 1px solid {{ $action['color'] }}28;">{{ $action['icon'] }}</div>
                        <div class="min-w-0">
                            <div class="font-semibold truncate group-hover:text-white transition-colors" style="font-size: 11px; color: rgba(255,255,255,0.8);">{{ $action['label'] }}</div>
                            <div class="text-[10px] truncate mt-0.5" style="color: {{ $action['color'] }}88;">{{ $action['sub'] }}</div>
                        </div>
                    </button>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>