<div class="space-y-4">

    <!-- ========================= -->
    <!-- INTERVIEW HEADER -->
    <!-- ========================= -->
    <div class="rounded-2xl overflow-hidden"
         style="background: linear-gradient(145deg, #07111f 0%, #030810 100%);
                border: 1px solid rgba(255,255,255,0.07);">

        <div class="px-5 py-4 border-b border-white/5">
            <h3 class="text-sm font-bold text-white">
                AI Interview Simulator
            </h3>
            <p class="text-xs text-slate-500 mt-1">
                Simulasi interview berdasarkan CV Anda
            </p>
        </div>

        <div class="p-5">

            <!-- ========================= -->
            <!-- CV SELECTOR (SLIDER) -->
            <!-- ========================= -->
            <div class="mb-5">
                <label class="text-xs text-slate-400 block mb-3">
                    Pilih CV untuk Interview
                </label>

                <div class="flex gap-3 overflow-x-auto pb-2"
                     id="cvSliderInterview"
                     style="scrollbar-width: none;">

                    @foreach($allCvs as $cv)
                        <div onclick="chooseCv(this)"
                             data-id="{{ $cv->id }}"
                             data-jobtema="{{ $cv->job_tema ?? 'Umum' }}"
                             class="cv-card min-w-[220px] p-4 rounded-xl cursor-pointer border border-white/5 bg-white/5 hover:bg-white/10 transition">

                            <p class="text-xs font-semibold text-white truncate">
                                CV #{{ $cv->id }}
                            </p>

                            <p class="text-[10px] text-slate-400 mt-1">
                                {{ $cv->job_tema ?? 'Belum dianalisis' }}
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

                <!-- hidden select (logic tetap jalan) -->
                <select id="cvSelector" class="hidden">
                    <option value="">Pilih CV</option>
                    @foreach($allCvs as $cv)
                        <option value="{{ $cv->id }}" 
                                data-jobtema="{{ $cv->job_tema ?? 'Umum' }}">
                            CV {{ $cv->id }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- ========================= -->
            <!-- START BUTTON -->
            <!-- ========================= -->
            <button onclick="startInterview()"
                    class="w-full rounded-xl py-4 font-bold text-sm transition hover:scale-[1.02]"
                    style="background: linear-gradient(135deg, #00d4ff, #2563eb); color: white;">
                Mulai Interview AI
            </button>

        </div>
    </div>

    <!-- ========================= -->
    <!-- HISTORY INTERVIEW -->
    <!-- ========================= -->
    <div class="rounded-2xl overflow-hidden"
         style="background: linear-gradient(145deg, #07111f 0%, #030810 100%);
                border: 1px solid rgba(255,255,255,0.07);">

        <div class="px-5 py-4 border-b border-white/5 flex justify-between items-center">
            <h3 class="text-sm font-bold text-white">
                Riwayat Interview
            </h3>

            <span class="text-[10px] text-slate-500">
                Berdasarkan CV terpilih
            </span>
        </div>

        <!-- 🔥 AJAX CONTAINER -->
        <div id="interviewHistoryContainer"
             class="p-5 grid grid-cols-1 md:grid-cols-2 gap-4">

            <div class="col-span-full text-center py-10 text-slate-500">
                Pilih CV terlebih dahulu
            </div>

        </div>

    </div>
</div>

<!-- ========================= -->
<!-- INTERVIEW MODAL -->
<!-- ========================= -->
<div id="interviewModal"
     class="hidden fixed inset-0 z-50 items-center justify-center"
     style="background: rgba(0,0,0,0.8); backdrop-filter: blur(8px);">

    <div class="relative w-full max-w-2xl mx-4 rounded-2xl overflow-hidden"
         style="background: linear-gradient(135deg, #0a1622, #03080f);
                border: 1px solid rgba(0,212,255,0.2);">

        <!-- HEADER -->
        <div class="flex items-center justify-between p-5 border-b border-white/10">
            <div>
                <h3 class="text-lg font-bold text-cyan-400">
                    Simulasi Interview AI
                </h3>
                <p class="text-xs text-white/50 mt-1" id="jobTemaText">
                    Loading...
                </p>
            </div>

            <button onclick="closeInterviewModal()"
                    class="text-white/50 hover:text-white/80 transition">
                ✕
            </button>
        </div>

        <div class="p-6">

            <!-- TIMER -->
            <div class="flex justify-end mb-4">
                <div class="px-4 py-2 rounded-lg bg-cyan-500/10 border border-cyan-500/20">
                    <span class="text-xs text-white/70">Waktu:</span>
                    <span id="timerText" class="text-lg font-mono font-bold text-cyan-400">
                        03:00
                    </span>
                </div>
            </div>

            <!-- PROGRESS -->
            <div id="progressContainer" class="mb-4 hidden">
                <div class="flex justify-between text-xs mb-1">
                    <span id="questionCounter" class="text-cyan-400"></span>
                    <span id="progressPercent" class="text-white/40"></span>
                </div>

                <div class="w-full bg-white/10 rounded-full h-1">
                    <div id="progressBar"
                         class="bg-cyan-400 h-1 rounded-full transition-all"></div>
                </div>
            </div>

            <!-- QUESTION -->
            <div class="p-6 rounded-xl mb-6 bg-white/5 border border-white/10">
                <p class="text-sm text-white/60 mb-2">Pertanyaan:</p>
                <p id="questionBox" class="text-white text-lg leading-relaxed">
                    Loading...
                </p>
            </div>

            <!-- ANSWER -->
            <div class="space-y-3">
                <textarea id="answerInput"
                          rows="4"
                          class="w-full rounded-xl px-4 py-3 text-white text-sm bg-white/5 border border-white/10"
                          placeholder="Bisa ketik atau tekan 🎤 untuk menjawab dengan suara..."></textarea>
                <div class="flex gap-2">
                    <button onclick="toggleRecording()"
                            id="micBtn"
                            class="px-4 py-2 rounded-lg bg-red-500 text-white text-sm">
                        🎤 Mulai Bicara
                    </button>
                </div>

                <div class="flex justify-between gap-3">

                    <button onclick="previousQuestion()"
                            class="px-4 py-2 rounded-lg bg-white/5 border border-white/10 text-white/70">
                        ◀
                    </button>

                    <button id="nextBtn"
                            onclick="nextQuestion()"
                            class="px-5 py-2 rounded-lg bg-cyan-500 text-white">
                        Next ▶
                    </button>

                    <button id="submitBtn"
                            onclick="submitInterview()"
                            class="px-5 py-2 rounded-lg bg-green-500 text-white hidden">
                        Submit
                    </button>

                </div>
            </div>

        </div>

        <div class="p-4 border-t border-white/10 text-right">
            <button onclick="closeInterviewModal()" class="text-sm text-white/60">
                Tutup
            </button>
        </div>
    </div>
</div>