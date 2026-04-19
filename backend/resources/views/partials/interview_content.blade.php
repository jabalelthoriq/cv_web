<div class="space-y-4">
    <div class="rounded-2xl overflow-hidden" style="background: linear-gradient(145deg, #07111f 0%, #030810 100%); border: 1px solid rgba(255,255,255,0.07);">
        <div class="px-5 py-4" style="border-bottom: 1px solid rgba(255,255,255,0.05);">
            <h3 class="text-sm font-bold">Riwayat Interview</h3>
            <p class="text-xs text-slate-500 mt-1">Total {{ $interviewData['total_sessions'] }} sesi interview</p>
        </div>
        <div class="px-5 py-4">
            @forelse($interviewData['recent_interviews'] as $interview)
            <div class="mb-4 p-4 rounded-xl" style="border: 1px solid rgba(255,255,255,0.05); background: rgba(255,255,255,0.02);">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-[13px] font-semibold text-white/85">{{ $interview['question'] }}</span>
                    <span class="text-[11px] font-bold px-2 py-0.5 rounded-full" style="background: #00d4ff18; color: #00d4ff;">Score: {{ $interview['score'] }}</span>
                </div>
                @if($interview['answer'])
                <p class="text-[12px] text-slate-400 mb-2">"{{ $interview['answer'] }}"</p>
                @endif
                @if($interview['feedback'])
                <p class="text-[11px] text-slate-500 mt-2">💡 {{ $interview['feedback'] }}</p>
                @endif
            </div>
            @empty
            <div class="text-center py-12">
                <div class="text-5xl mb-3">🎙️</div>
                <p class="text-sm text-slate-500">Belum ada sesi interview. Mulai interview AI sekarang!</p>
                <button onclick="navigateTo('dashboard')" class="mt-4 px-4 py-2 rounded-lg text-xs font-bold" style="background: #00d4ff20; color: #00d4ff; border: 1px solid #00d4ff30;">
                    Mulai Interview →
                </button>
            </div>
            @endforelse
        </div>
    </div>
</div>