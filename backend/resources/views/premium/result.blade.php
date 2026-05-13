<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Premium CV Analysis</title>

<style>
body {
    margin: 0;
    background: radial-gradient(circle at top, #0b1b2b, #050b16);
    color: white;
    font-family: Inter, sans-serif;
    padding: 40px;
}

.container {
    max-width: 1200px;
    margin: auto;
}

/* GRID LAYOUT */
.grid {
    display: grid;
    grid-template-columns: 380px 1fr;
    gap: 24px;
}

/* CARD */
.card {
    background: rgba(255,255,255,0.05);
    border: 1px solid rgba(0,210,255,0.25);
    border-radius: 18px;
    padding: 24px;
    backdrop-filter: blur(10px);
}

h1 { margin-bottom: 10px; }
h2 { color: #00d2ff; margin-bottom: 12px; }
h3 { margin-bottom: 10px; }

/* SCORE */
.score {
    font-size: 72px;
    font-weight: 800;
    color: #00d2ff;
}

/* LIST */
ul {
    padding-left: 18px;
    line-height: 1.6;
}
li { margin-bottom: 6px; }

/* BUTTON */
.btn {
    display: inline-block;
    padding: 12px 20px;
    border-radius: 10px;
    background: linear-gradient(135deg, #00d2ff, #007bff);
    color: white;
    text-decoration: none;
    font-weight: 600;
    margin-top: 12px;
    transition: 0.2s;
}

.btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(0,210,255,0.3);
}

/* SECTION */
.section {
    margin-bottom: 20px;
}

/* GRID ANALYSIS */
.analysis-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 20px;
}

/* BOX */
.box {
    background: rgba(0,210,255,0.06);
    padding: 16px;
    border-radius: 12px;
    border: 1px solid rgba(0,210,255,0.2);
}

.empty {
    opacity: 0.6;
    font-style: italic;
}

/* RESPONSIVE */
@media(max-width: 900px){
    .grid {
        grid-template-columns: 1fr;
    }

    .analysis-grid {
        grid-template-columns: 1fr;
    }
}
</style>
</head>

<body>

@php
$analysis = $data['analysis'] ?? [];

$strengths = $analysis['strengths'] ?? $analysis['kelebihan'] ?? [];
$weaknesses = $analysis['weaknesses'] ?? $analysis['kekurangan'] ?? [];
$suggestions = $analysis['suggestions'] ?? $analysis['saran'] ?? [];
@endphp

<div class="container">

    <h1>Premium CV Analysis</h1>

    <div class="grid">

        <!-- KIRI -->
        <div>

            <div class="card">
                <h2>ATS Score</h2>
                <div class="score">{{ $data['score'] ?? 0 }}%</div>
            </div>

            <div class="card">
                <h2>Rekomendasi Pekerjaan</h2>

                <h3>{{ $data['job']['title'] ?? '-' }}</h3>
                <p>{{ $data['job']['company'] ?? '-' }}</p>

                <a href="{{ $data['job']['link'] ?? '#' }}" target="_blank" class="btn">
                    Lihat Lowongan
                </a>
            </div>

        </div>

        <!-- KANAN -->
        <div class="card">

            <h2>Analisis AI</h2>

            <div class="analysis-grid">

                <!-- KELEBIHAN -->
                <div class="box">
                    <h3>✅ Kelebihan</h3>
                    @if(!empty($strengths))
                        <ul>
                            @foreach($strengths as $item)
                                <li>{{ $item }}</li>
                            @endforeach
                        </ul>
                    @else
                        <div class="empty">Tidak tersedia</div>
                    @endif
                </div>

                <!-- KEKURANGAN -->
                <div class="box">
                    <h3>⚠️ Kekurangan</h3>
                    @if(!empty($weaknesses))
                        <ul>
                            @foreach($weaknesses as $item)
                                <li>{{ $item }}</li>
                            @endforeach
                        </ul>
                    @else
                        <div class="empty">Tidak tersedia</div>
                    @endif
                </div>

                <!-- SARAN -->
                <div class="box">
                    <h3>💡 Saran</h3>
                    @if(!empty($suggestions))
                        <ul>
                            @foreach($suggestions as $item)
                                <li>{{ $item }}</li>
                            @endforeach
                        </ul>
                    @else
                        <div class="empty">Tidak tersedia</div>
                    @endif
                </div>

            </div>

        </div>

    </div>

</div>

</body>
</html>