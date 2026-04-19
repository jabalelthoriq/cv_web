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

    <!-- Hasil Analisis CV -->
    @if($cvAnalysis['score'] > 0)
    <div class="rounded-2xl overflow-hidden" style="background: linear-gradient(145deg, #07111f 0%, #030810 100%); border: 1px solid rgba(255,255,255,0.07);">
        <div class="px-5 py-4" style="border-bottom: 1px solid rgba(255,255,255,0.05);">
            <h3 class="text-sm font-bold">Hasil Analisis CV Terbaru</h3>
        </div>
        <div class="px-5 py-4 space-y-4">
            <div class="text-center">
                <div class="text-5xl font-black" style="color: #00d4ff;">{{ $cvAnalysis['score'] }}</div>
                <p class="text-sm text-slate-500 mt-1">Skor ATS</p>
            </div>
            
            @if(count($cvAnalysis['strengths']) > 0)
            <div>
                <p class="text-xs font-semibold text-green-500 mb-2">✅ Kelebihan</p>
                <ul class="space-y-1">
                    @foreach($cvAnalysis['strengths'] as $strength)
                    <li class="text-xs text-slate-400">• {{ $strength }}</li>
                    @endforeach
                </ul>
            </div>
            @endif
            
            @if(count($cvAnalysis['weaknesses']) > 0)
            <div>
                <p class="text-xs font-semibold text-red-500 mb-2">⚠️ Kekurangan</p>
                <ul class="space-y-1">
                    @foreach($cvAnalysis['weaknesses'] as $weakness)
                    <li class="text-xs text-slate-400">• {{ $weakness }}</li>
                    @endforeach
                </ul>
            </div>
            @endif
            
            @if(count($cvAnalysis['suggestions']) > 0)
            <div>
                <p class="text-xs font-semibold text-blue-500 mb-2">💡 Saran</p>
                <ul class="space-y-1">
                    @foreach($cvAnalysis['suggestions'] as $suggestion)
                    <li class="text-xs text-slate-400">• {{ $suggestion }}</li>
                    @endforeach
                </ul>
            </div>
            @endif
        </div>
    </div>
    @endif
</div>