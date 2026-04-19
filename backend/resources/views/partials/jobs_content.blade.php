<div class="rounded-2xl overflow-hidden" style="background: linear-gradient(145deg, #07111f 0%, #030810 100%); border: 1px solid rgba(255,255,255,0.07);">
    <div class="px-5 py-4" style="border-bottom: 1px solid rgba(255,255,255,0.05);">
        <h3 class="text-sm font-bold">Semua Rekomendasi Lowongan</h3>
        <p class="text-xs text-slate-500 mt-1">{{ $jobData->count() }} lowongan yang cocok untukmu</p>
    </div>
    <div class="px-5 py-4">
        <div class="space-y-2">
            @forelse($jobData as $job)
            @php $matchClass = $job['match_score'] >= 90 ? '#10b981' : '#00d4ff'; @endphp
            <div class="flex items-center gap-3 px-4 py-4 rounded-xl" style="border: 1px solid rgba(255,255,255,0.05); background: rgba(255,255,255,0.02);">
                <div class="w-10 h-10 rounded-lg flex items-center justify-center text-lg flex-shrink-0" style="background: rgba(255,255,255,0.06);">💼</div>
                <div class="flex-1">
                    <div class="flex items-center justify-between gap-2 flex-wrap">
                        <span class="text-[14px] font-semibold text-white/85">{{ $job['job_title'] }}</span>
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full" style="background: {{ $matchClass }}18; color: {{ $matchClass }};">{{ $job['match_score'] }}% Match</span>
                    </div>
                    <div class="flex items-center gap-2 mt-1">
                        <span class="text-[12px] text-slate-500">{{ $job['company'] }}</span>
                        <span class="text-slate-700">•</span>
                        <div class="flex gap-1">
                            @foreach($job['tags'] as $tag)
                            <span class="text-[10px] px-2 py-0.5 rounded" style="background: rgba(255,255,255,0.05);">{{ $tag }}</span>
                            @endforeach
                        </div>
                    </div>
                    @if($job['link'])
                    <a href="{{ $job['link'] }}" target="_blank" class="inline-block mt-2 text-[10px] font-semibold" style="color: #00d4ff;">Lihat Detail →</a>
                    @endif
                </div>
            </div>
            @empty
            <div class="text-center py-12">
                <div class="text-5xl mb-3">💼</div>
                <p class="text-sm text-slate-500">Belum ada rekomendasi lowongan</p>
            </div>
            @endforelse
        </div>
    </div>
</div>