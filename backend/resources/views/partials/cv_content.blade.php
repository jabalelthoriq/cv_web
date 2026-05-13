
<div class="space-y-4">

    <!-- ========================= -->
    <!-- UPLOAD CV -->
    <!-- ========================= -->
    <div class="rounded-2xl overflow-hidden"
         style="background: linear-gradient(145deg, #07111f 0%, #030810 100%); border: 1px solid rgba(255,255,255,0.07);">

        <div class="flex items-center justify-between px-5 py-4"
             style="border-bottom: 1px solid rgba(255,255,255,0.05);">
            
            <div class="flex items-center gap-2">
                <div class="w-1.5 h-1.5 rounded-full bg-cyan-400"></div>
                <span class="text-[10px] font-bold uppercase tracking-[0.18em] text-cyan-400">
                    Upload CV
                </span>
            </div>

            <span class="text-[9px] text-slate-500 italic">
                PDF • Max 2MB
            </span>
        </div>

        <div class="px-5 py-4">
            <div onclick="handleUploadCV()"
                 class="group rounded-xl p-6 text-center cursor-pointer transition hover:scale-[1.02]"
                 style="border: 1.5px dashed rgba(0,212,255,0.18); background: rgba(0,212,255,0.02);">

                <div class="w-14 h-14 mx-auto mb-3 flex items-center justify-center rounded-xl text-2xl"
                     style="background: rgba(0,212,255,0.1);">
                    📄
                </div>

                <p class="text-sm text-white/80 font-semibold">
                    Klik untuk upload CV
                </p>

                <p class="text-[11px] text-slate-500">
                    Diproses dengan AI & OCR Engine
                </p>
            </div>
        </div>
    </div>

<!-- ========================= -->
<!-- HISTORY CV (HORIZONTAL) -->
<!-- ========================= -->
<div class="rounded-2xl overflow-hidden mt-4"
     style="background: linear-gradient(145deg, #07111f 0%, #030810 100%); border: 1px solid rgba(255,255,255,0.07);">

    <div class="px-5 py-4 border-b border-white/5 flex justify-between items-center">
        <h3 class="text-sm font-bold">Riwayat CV</h3>

        <div class="flex gap-2">
            <button onclick="scrollCv(-1)" class="px-2 py-1 bg-white/5 rounded">⬅</button>
            <button onclick="scrollCv(1)" class="px-2 py-1 bg-white/5 rounded">➡</button>
        </div>
    </div>

    <div id="cvSlider"
         class="flex gap-3 overflow-x-auto px-4 py-4 scroll-smooth"
         style="scrollbar-width: none;">

        @foreach($cvs as $cv)
            <div onclick='selectCv(@json([
                    "url" => asset("storage/".$cv->file_path),
                    "score" => $cv->score,
                    "analysis" => $cv->analysis
                ]))'
                 
                 class="min-w-[220px] p-4 rounded-xl cursor-pointer border border-white/5 hover:bg-white/5 transition">

                <p class="text-xs font-semibold text-white/80 truncate">
                    {{ basename($cv->file_path) }}
                </p>

                <p class="text-[10px] text-slate-400 mt-1">
                    {{ $cv->created_at->diffForHumans() }}
                </p>

                <div class="mt-3 flex justify-between items-center">
                    <span class="text-[10px] text-slate-500">Score</span>
                    <span class="text-cyan-400 font-bold text-sm">
                        {{ $cv->score ?? 0 }}
                    </span>
                </div>

            </div>
        @endforeach

    </div>
</div>

    <!-- ========================= -->
<!-- HASIL ANALISIS + PDF VIEW -->
<!-- ========================= -->
<div class="rounded-2xl overflow-hidden"
     style="background: linear-gradient(145deg, #07111f 0%, #030810 100%); border: 1px solid rgba(255,255,255,0.07);">

     
    <div class="px-5 py-4 border-b border-white/5 flex justify-between items-center">
        <h3 class="text-sm font-bold">Preview & Analisis CV</h3>

        @if(isset($latestCv))
            <span class="text-[10px] text-slate-500">
                {{ $latestCv->created_at->diffForHumans() }}
            </span>
        @endif
    </div>

    <div class="p-5">

        @if(empty($latestCv))
            <div class="text-center py-12 text-slate-500">
                Belum ada CV
            </div>

        @elseif($latestCv->score == 0)
            <div class="text-center py-12">
                <div class="animate-spin h-12 w-12 border-b-2 border-blue-500 mx-auto rounded-full"></div>
                <p class="mt-4 text-white/80">AI sedang menganalisis...</p>
            </div>

        @else

        <div class="grid grid-cols-1 lg:grid-cols-5 gap-5">

    <!-- ===================== -->
    <!-- PDF VIEW (KECIL) -->
    <!-- ===================== -->
    <div class="lg:col-span-3">
        <div class="bg-black/40 rounded-xl p-3 border border-white/5">

            <canvas id="pdfCanvas"
                class="w-full rounded-lg mx-auto"
                style="max-height: 800px; object-fit: contain;">
            </canvas>

            <!-- Controls -->
            <div class="flex justify-between items-center mt-2 text-[10px] text-slate-400">

                <button onclick="prevPage()" class="px-2 py-1 rounded bg-white/5 hover:bg-white/10">
                    ⬅
                </button>

                <span>
                    {{ __('Halaman') }}
                    <span id="pageNum">1</span> /
                    <span id="pageCount">1</span>
                </span>

                <button onclick="nextPage()" class="px-2 py-1 rounded bg-white/5 hover:bg-white/10">
                    ➡
                </button>

            </div>
        </div>
    </div>

   <!-- ===================== -->
<!-- AI ANALYSIS (VERTICAL) -->
<!-- ===================== -->
<div class="lg:col-span-2 space-y-4">

    <!-- SCORE -->
    <div class="p-5 rounded-2xl border border-cyan-500/20"
         style="background: linear-gradient(135deg, rgba(0,212,255,0.08), rgba(59,130,246,0.05));">

        <div class="flex items-center justify-between">

            <div>
                <p class="text-[10px] uppercase tracking-widest text-slate-400">
                    ATS Score
                </p>

                <div id="cvScore" class="text-5xl font-black text-cyan-400 leading-none">
                    {{ $cvAnalysis['score'] ?? 0 }}
                </div>
            </div>

            <div class="w-14 h-14 rounded-xl flex items-center justify-center text-2xl"
                 style="background: rgba(0,212,255,0.1); border: 1px solid rgba(0,212,255,0.2);">
                🚀
            </div>

        </div>
    </div>

    <!-- STRENGTH -->
    <div class="p-4 rounded-xl border border-green-500/20 bg-green-500/5">
        <p class="text-xs font-bold text-green-400 mb-3">
            ✅ Kelebihan
        </p>

        <div id="strengthList">
            @forelse($cvAnalysis['strengths'] ?? [] as $item)
                <div class="text-[12px] text-white/80 mb-1 flex gap-2">
                    <span class="text-green-400">•</span> 
                    <span>{{ $item }}</span>
                </div>
            @empty
                <div class="text-[11px] text-white/30">Belum ada data</div>
            @endforelse
        </div>
    </div>

    <!-- WEAKNESS -->
    <div class="p-4 rounded-xl border border-red-500/20 bg-red-500/5">
        <p class="text-xs font-bold text-red-400 mb-3">
            ⚠️ Kekurangan
        </p>

        <div id="weaknessList">
            @forelse($cvAnalysis['weaknesses'] ?? [] as $item)
                <div class="text-[12px] text-white/80 mb-1 flex gap-2">
                    <span class="text-red-400">•</span> 
                    <span>{{ $item }}</span>
                </div>
            @empty
                <div class="text-[11px] text-white/30">Belum ada data</div>
            @endforelse
        </div>
    </div>

    <!-- SUGGESTIONS -->
    <div class="p-4 rounded-xl border border-yellow-500/20 bg-yellow-500/5">
        <p class="text-xs font-bold text-yellow-400 mb-3">
            💡 Saran
        </p>

        <div id="suggestionList">
            @forelse($cvAnalysis['suggestions'] ?? [] as $item)
                <div class="text-[12px] text-white/80 mb-1 flex gap-2">
                    <span class="text-yellow-400">•</span> 
                    <span>{{ $item }}</span>
                </div>
            @empty
                <div class="text-[11px] text-white/30">Belum ada data</div>
            @endforelse
        </div>
    </div>

</div>

</div>
</div>
</div>
@endif
