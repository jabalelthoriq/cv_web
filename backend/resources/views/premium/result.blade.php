<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Premium CV Analyze - CareerSense</title>

<style>
:root {
    --bg: #07111f;
    --panel: rgba(15, 23, 42, 0.82);
    --panel-strong: rgba(15, 23, 42, 0.96);
    --line: rgba(148, 163, 184, 0.22);
    --text: #f8fafc;
    --muted: #94a3b8;
    --cyan: #22d3ee;
    --blue: #3b82f6;
    --green: #34d399;
    --amber: #fbbf24;
    --red: #fb7185;
}

* { box-sizing: border-box; }

body {
    margin: 0;
    min-height: 100vh;
    color: var(--text);
    font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
    background:
        radial-gradient(circle at 15% 0%, rgba(34, 211, 238, 0.20), transparent 28rem),
        radial-gradient(circle at 90% 10%, rgba(59, 130, 246, 0.18), transparent 28rem),
        linear-gradient(135deg, #07111f 0%, #0f172a 46%, #111827 100%);
}

a { color: inherit; }

.page {
    width: min(1180px, calc(100% - 32px));
    margin: 0 auto;
    padding: 28px 0 40px;
}

.topbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    margin-bottom: 22px;
}

.brand {
    display: flex;
    align-items: center;
    gap: 12px;
    min-width: 0;
}

.brand-mark {
    width: 38px;
    height: 38px;
    border-radius: 10px;
    display: grid;
    place-items: center;
    color: #06111f;
    font-weight: 900;
    background: linear-gradient(135deg, var(--cyan), var(--green));
    box-shadow: 0 12px 30px rgba(34, 211, 238, 0.20);
}

.eyebrow {
    margin: 0 0 3px;
    color: var(--cyan);
    font-size: 11px;
    font-weight: 800;
    letter-spacing: 0.10em;
    text-transform: uppercase;
}

.brand h1 {
    margin: 0;
    font-size: clamp(22px, 3vw, 34px);
    line-height: 1.08;
    letter-spacing: 0;
}

.meta {
    color: var(--muted);
    font-size: 13px;
    text-align: right;
    overflow-wrap: anywhere;
}

.hero {
    display: grid;
    grid-template-columns: minmax(260px, 0.78fr) minmax(320px, 1.22fr);
    gap: 18px;
    align-items: stretch;
    margin-bottom: 18px;
}

.panel {
    border: 1px solid var(--line);
    border-radius: 8px;
    background: var(--panel);
    box-shadow: 0 24px 70px rgba(2, 6, 23, 0.28);
    backdrop-filter: blur(14px);
}

.score-panel {
    padding: 24px;
    position: relative;
    overflow: hidden;
}

.score-panel::after {
    content: "";
    position: absolute;
    inset: auto -60px -80px auto;
    width: 210px;
    height: 210px;
    border-radius: 999px;
    background: rgba(34, 211, 238, 0.12);
    filter: blur(4px);
}

.score-label {
    color: var(--muted);
    font-size: 13px;
    margin: 0 0 12px;
}

.score-row {
    display: flex;
    align-items: flex-end;
    gap: 10px;
    margin-bottom: 18px;
}

.score {
    font-size: clamp(64px, 10vw, 92px);
    line-height: 0.9;
    font-weight: 900;
    letter-spacing: 0;
    color: var(--cyan);
}

.score-row span {
    color: var(--muted);
    font-size: 18px;
    font-weight: 800;
    padding-bottom: 6px;
}

.meter {
    height: 10px;
    border-radius: 999px;
    background: rgba(148, 163, 184, 0.18);
    overflow: hidden;
}

.meter-fill {
    height: 100%;
    border-radius: inherit;
    background: linear-gradient(90deg, var(--blue), var(--cyan), var(--green));
}

.quick-meta {
    display: grid;
    gap: 10px;
    margin-top: 20px;
}

.meta-row {
    display: flex;
    justify-content: space-between;
    gap: 14px;
    padding: 10px 0;
    border-top: 1px solid rgba(148, 163, 184, 0.14);
    font-size: 13px;
}

.meta-row span:first-child { color: var(--muted); }
.meta-row span:last-child { font-weight: 750; text-align: right; overflow-wrap: anywhere; }

.job-panel {
    padding: 24px;
    display: flex;
    flex-direction: column;
    gap: 18px;
}

.section-title {
    margin: 0;
    font-size: 14px;
    color: var(--muted);
    font-weight: 800;
    letter-spacing: 0.08em;
    text-transform: uppercase;
}

.job-title {
    margin: 8px 0 8px;
    font-size: clamp(26px, 4vw, 46px);
    line-height: 1.04;
    letter-spacing: 0;
}

.company {
    color: var(--muted);
    font-size: 15px;
    margin: 0;
}

.job-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 16px;
    margin-top: auto;
}

.match-badge {
    min-width: 112px;
    border: 1px solid rgba(52, 211, 153, 0.35);
    border-radius: 8px;
    padding: 11px 14px;
    background: rgba(52, 211, 153, 0.10);
}

.match-badge b {
    display: block;
    font-size: 26px;
    line-height: 1;
    color: var(--green);
}

.match-badge span {
    color: var(--muted);
    font-size: 12px;
}

.btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-height: 44px;
    padding: 0 18px;
    border-radius: 8px;
    background: linear-gradient(135deg, var(--cyan), var(--blue));
    color: #06111f;
    text-decoration: none;
    font-size: 14px;
    font-weight: 850;
    box-shadow: 0 14px 30px rgba(34, 211, 238, 0.20);
    white-space: nowrap;
}

.btn.disabled {
    pointer-events: none;
    color: var(--muted);
    background: rgba(148, 163, 184, 0.14);
    box-shadow: none;
}

.analysis-grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 18px;
}

.analysis-card {
    padding: 20px;
    min-height: 280px;
}

.analysis-card h2 {
    margin: 0 0 14px;
    font-size: 18px;
}

.analysis-card.strength h2 { color: var(--green); }
.analysis-card.weakness h2 { color: var(--red); }
.analysis-card.suggestion h2 { color: var(--amber); }

.analysis-list {
    list-style: none;
    padding: 0;
    margin: 0;
    display: grid;
    gap: 10px;
}

.analysis-list li {
    position: relative;
    padding: 12px 12px 12px 34px;
    border: 1px solid rgba(148, 163, 184, 0.14);
    border-radius: 8px;
    background: rgba(15, 23, 42, 0.56);
    color: #dbeafe;
    font-size: 14px;
    line-height: 1.48;
}

.analysis-list li::before {
    content: "";
    position: absolute;
    left: 14px;
    top: 18px;
    width: 7px;
    height: 7px;
    border-radius: 999px;
    background: var(--cyan);
}

.empty {
    margin: 0;
    color: var(--muted);
    font-size: 14px;
    line-height: 1.5;
}

@media (max-width: 900px) {
    .topbar,
    .job-footer {
        align-items: flex-start;
        flex-direction: column;
    }

    .meta {
        text-align: left;
    }

    .hero,
    .analysis-grid {
        grid-template-columns: 1fr;
    }

    .analysis-card {
        min-height: auto;
    }
}

@media (max-width: 520px) {
    .page {
        width: min(100% - 20px, 1180px);
        padding-top: 18px;
    }

    .score-panel,
    .job-panel,
    .analysis-card {
        padding: 18px;
    }

    .btn {
        width: 100%;
    }
}
</style>
</head>

<body>
@php
    $analysis = is_array($data['analysis'] ?? null) ? $data['analysis'] : [];

    $normalizeList = function ($value) {
        if (is_string($value) && trim($value) !== '') {
            return [trim($value)];
        }

        if (!is_array($value)) {
            return [];
        }

        return collect($value)
            ->flatten()
            ->filter(fn ($item) => is_scalar($item) && trim((string) $item) !== '')
            ->map(fn ($item) => trim((string) $item))
            ->values()
            ->all();
    };

    $strengths = $normalizeList($analysis['strengths'] ?? $analysis['kelebihan'] ?? []);
    $weaknesses = $normalizeList($analysis['weaknesses'] ?? $analysis['kekurangan'] ?? []);
    $suggestions = $normalizeList($analysis['suggestions'] ?? $analysis['saran'] ?? $analysis['recommendations'] ?? []);
    $score = max(0, min(100, (int) ($data['score'] ?? 0)));
    $job = $data['job'] ?? [];
    $hasJob = filled($job['title'] ?? null);
    $jobTitle = $job['title'] ?? 'Lowongan belum tersedia';
    $jobCompany = $job['company'] ?? 'CareerSense belum menemukan data perusahaan';
    $jobLink = $job['link'] ?? null;
    $matchScore = $job['match_score'] ?? null;
@endphp

<main class="page">
    <header class="topbar">
        <div class="brand">
            <div class="brand-mark">CS</div>
            <div>
                <p class="eyebrow">Premium CV Analyze</p>
                <h1>Hasil Analisis CV</h1>
            </div>
        </div>
        <div class="meta">
            {{ $data['file_name'] ?? 'CV' }}
        </div>
    </header>

    <section class="hero">
        <div class="panel score-panel">
            <p class="score-label">ATS readiness score</p>
            <div class="score-row">
                <div class="score">{{ $score }}</div>
                <span>/100</span>
            </div>
            <div class="meter" aria-label="ATS score {{ $score }} persen">
                <div class="meter-fill" style="width: {{ $score }}%;"></div>
            </div>

            <div class="quick-meta">
                <div class="meta-row">
                    <span>Fokus karier</span>
                    <span>{{ $data['job_tema'] ?? $job['job_tema'] ?? 'Belum terdeteksi' }}</span>
                </div>
                <div class="meta-row">
                    <span>Status akses</span>
                    <span>Premium aktif</span>
                </div>
            </div>
        </div>

        <div class="panel job-panel">
            <div>
                <p class="section-title">Rekomendasi job terbaik</p>
                <h2 class="job-title">{{ $jobTitle }}</h2>
                <p class="company">{{ $jobCompany }}</p>
                @unless($hasJob)
                    <p class="empty">Upload ulang CV atau buka dashboard lowongan agar CareerSense membuat rekomendasi berdasarkan CV ini.</p>
                @endunless
            </div>

            <div class="job-footer">
                <div class="match-badge">
                    <b>{{ $matchScore !== null ? $matchScore . '%' : '-' }}</b>
                    <span>match tertinggi</span>
                </div>

                @if($jobLink)
                    <a href="{{ $jobLink }}" target="_blank" rel="noopener noreferrer" class="btn">Buka Lowongan</a>
                @else
                    <span class="btn disabled">Link belum tersedia</span>
                @endif
            </div>
        </div>
    </section>

    <section class="analysis-grid">
        <article class="panel analysis-card strength">
            <h2>Kelebihan CV</h2>
            @if(count($strengths) > 0)
                <ul class="analysis-list">
                    @foreach($strengths as $item)
                        <li>{{ $item }}</li>
                    @endforeach
                </ul>
            @else
                <p class="empty">Belum ada data kelebihan yang bisa ditampilkan.</p>
            @endif
        </article>

        <article class="panel analysis-card weakness">
            <h2>Yang Perlu Diperbaiki</h2>
            @if(count($weaknesses) > 0)
                <ul class="analysis-list">
                    @foreach($weaknesses as $item)
                        <li>{{ $item }}</li>
                    @endforeach
                </ul>
            @else
                <p class="empty">Belum ada data kekurangan yang bisa ditampilkan.</p>
            @endif
        </article>

        <article class="panel analysis-card suggestion">
            <h2>Saran Prioritas</h2>
            @if(count($suggestions) > 0)
                <ul class="analysis-list">
                    @foreach($suggestions as $item)
                        <li>{{ $item }}</li>
                    @endforeach
                </ul>
            @else
                <p class="empty">Belum ada saran yang bisa ditampilkan.</p>
            @endif
        </article>
    </section>
</main>
</body>
</html>
