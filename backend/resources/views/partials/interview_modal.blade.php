<div id="interviewModal"
     class="fixed inset-0 z-50 hidden items-center justify-center bg-black/80 backdrop-blur-sm">

    <div class="w-full max-w-4xl rounded-3xl overflow-hidden"
         style="background: linear-gradient(145deg, #07111f 0%, #02060d 100%);
                border: 1px solid rgba(255,255,255,0.08);">

        <!-- HEADER -->
        <div class="px-6 py-5 border-b border-white/5 flex justify-between items-center">

            <div>
                <h2 class="text-lg font-bold text-white">
                    AI Interview Session
                </h2>
                <p id="jobTemaText" class="text-xs text-cyan-400">
                    Backend Developer
                </p>
            </div>

            <button onclick="closeInterviewModal()"
                    class="text-slate-400 hover:text-white text-xl">
                ✕
            </button>
        </div>

        <!-- BODY -->
        <div class="p-8 grid grid-cols-1 lg:grid-cols-2 gap-8">

            <!-- LEFT -->
            <div class="flex flex-col items-center justify-center">

                <!-- AI Avatar -->
                <div class="w-48 h-48 rounded-full flex items-center justify-center
                            border border-cyan-400/20
                            animate-pulse"
                     style="background: radial-gradient(circle,
                            rgba(0,212,255,0.15),
                            rgba(0,212,255,0.03));">

                    <div class="text-7xl">
                        🤖
                    </div>
                </div>

                <p class="text-sm text-cyan-400 mt-4 font-medium">
                    AI Interviewer is speaking...
                </p>

                <!-- TIMER -->
                <div class="mt-6 text-center">
                    <p class="text-xs text-slate-400">
                        Waktu Menjawab
                    </p>

                    <div id="timerText"
                         class="text-4xl font-black text-white mt-1">
                        03:00
                    </div>
                </div>
            </div>

            <!-- RIGHT -->
            <div>

                <p class="text-xs uppercase tracking-widest text-slate-500 mb-2">
                    Interview Question
                </p>

                <div id="questionBox"
                     class="p-5 rounded-2xl border border-white/5 bg-white/5 text-white leading-relaxed min-h-[180px]">
                    Loading question...
                </div>

                <textarea id="answerBox"
                          rows="6"
                          class="w-full mt-5 rounded-2xl bg-black/30 border border-white/10 text-white p-4"
                          placeholder="Jawaban user akan muncul di sini..."></textarea>

                <button
                    class="w-full mt-5 rounded-xl py-4 font-bold"
                    style="background: linear-gradient(135deg, #00d4ff, #2563eb);
                           color: white;">
                    Submit Jawaban
                </button>

            </div>

        </div>
    </div>
</div>