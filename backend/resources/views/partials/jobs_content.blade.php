<div class="rounded-2xl overflow-hidden backdrop-blur-xl"
     style="background: linear-gradient(145deg, rgba(11,23,40,0.95) 0%, rgba(2,6,15,0.98) 100%);
            border: 1px solid rgba(255,255,255,0.12);
            box-shadow: 0 20px 60px rgba(0,0,0,0.5);">

    <!-- HEADER -->
    <div class="px-6 py-5 border-b border-white/10 flex items-center justify-between">
        <div>
            <h3 class="text-base font-bold text-white tracking-wide">
                Rekomendasi Lowongan
            </h3>
            <p class="text-sm text-slate-400 mt-1">
   {{ $jobData->count() }} peluang cocok untukmu
@if($jobData->isNotEmpty() && !empty($cvAnalysis['job_tema']))
    • <span class="text-cyan-400 font-medium">
        {{ $cvAnalysis['job_tema'] }}
    </span>
@elseif($jobData->isNotEmpty())
    {{-- Fallback ke job_title pertama --}}
    @php
        $firstJobTitle = $jobData->first()['job_title'] ?? null;
    @endphp
    @if($firstJobTitle)
        • <span class="text-cyan-400 font-medium">
            {{ $firstJobTitle }}
        </span>
    @endif
@endif
</p>
        </div>
    </div>

    <!-- FILTER -->
    @if($cvs->count() > 0)
    <div class="px-6 py-3 flex gap-2 overflow-x-auto">
        @foreach($cvs as $cv)
            <a href="{{ route('dashboard', ['view' => 'jobs', 'cv_id' => $cv->id]) }}"
               class="px-4 py-2 text-sm rounded-full whitespace-nowrap transition-all duration-200
               {{ $selectedCvId == $cv->id ? 'bg-cyan-500 text-white' : 'bg-white/10 text-slate-400 hover:bg-white/20' }}">
                CV {{ $loop->iteration }}
            </a>
        @endforeach
    </div>
    @endif

    <!-- GRID -->
    <div class="px-6 py-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">

            @forelse($jobData as $job)
            @php 
                $score = $job['match_score'];

                // 🔥 SEMUA pakai tema biru
                $mainColor = '#00d4ff';
                $bgSoft = 'rgba(0,212,255,0.15)';
                $glowStrong = 'rgba(0,212,255,0.35)';
            @endphp

            <div class="group rounded-2xl p-5 text-white flex flex-col justify-between relative overflow-hidden transition-all duration-300 hover:scale-[1.04]"
                 style="border: 1px solid rgba(255,255,255,0.08);
                        background: rgba(255,255,255,0.04);
                        min-height: 200px;">

                <!-- 🔥 BLUE GLOW -->
                <div class="absolute inset-0 opacity-0 group-hover:opacity-100 transition duration-300"
                     style="background: radial-gradient(circle at top, {{ $glowStrong }}, transparent 70%);">
                </div>

                <!-- BORDER GLOW -->
                <div class="absolute inset-0 rounded-2xl opacity-0 group-hover:opacity-100 transition duration-300"
                     style="box-shadow: 0 0 25px {{ $glowStrong }};">
                </div>

                <div class="relative z-10">
                    <!-- TITLE -->
                    <div class="text-base font-semibold leading-snug line-clamp-2 group-hover:text-cyan-300 transition">
                        {{ $job['job_title'] }}
                    </div>

                    <!-- COMPANY -->
                    <div class="text-sm text-slate-400 mt-2">
                        {{ $job['company'] }}
                    </div>

                    <!-- TAGS -->
                    @if(!empty($job['tags']))
                    <div class="flex flex-wrap gap-2 mt-3">
                        @foreach($job['tags'] as $tag)
                            <span class="text-[10px] px-2.5 py-1 rounded-full border border-white/10 bg-white/5">
                                {{ $tag }}
                            </span>
                        @endforeach
                    </div>
                    @endif
                </div>

                <!-- FOOTER -->
                <div class="relative z-10 flex items-center justify-between mt-6">

                    <!-- SCORE -->
                    <div class="flex items-center gap-2">
                        <div class="text-lg font-bold"
                             style="color: {{ $mainColor }}">
                            {{ $score }}%
                        </div>

                        <div class="w-16 h-1.5 rounded-full bg-white/10">
                            <div class="h-full rounded-full"
                                 style="width: {{ $score }}%; background: {{ $mainColor }}">
                            </div>
                        </div>
                    </div>

                    <!-- APPLY -->
                    @if($job['link'])
                        <a href="{{ $job['link'] }}" target="_blank"
                           class="text-xs font-semibold text-cyan-400 group-hover:text-cyan-300 transition-all duration-200 group-hover:translate-x-1">
                           Apply →
                        </a>
                    @endif
                </div>

            </div>

            @empty
            <div class="col-span-full text-center py-20">
                <div class="text-6xl mb-4 opacity-70">💼</div>
                <p class="text-lg text-slate-300">
                    Belum ada rekomendasi job
                </p>
                <p class="text-sm text-slate-500 mt-2">
                    Upload & analisis CV untuk mulai
                </p>
            </div>
            @endforelse

        </div>
    </div>
</div>