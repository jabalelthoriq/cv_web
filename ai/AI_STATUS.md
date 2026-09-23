# CareerSense AI Service

Status Proyek:
PHASE 5 IMPLEMENTED

## Phase 1 - Text Extraction Foundation

Status:
DONE

Implemented:

- Membaca PDF digital.
- Membaca DOCX.
- OCR fallback untuk PDF scan.
- OCR untuk gambar.
- Membersihkan hasil ekstraksi.
- Error handling untuk file tidak ditemukan, format tidak didukung, dan teks kosong.

Endpoints:

- `GET /health`
- `POST /extract-text`
- `POST /analyze`

Notes:

- FastAPI tidak mengakses MySQL.
- Laravel bertanggung jawab menyimpan hasil ke database.
- ATS scoring sudah memakai breakdown identitas, pendidikan, pengalaman, skill, dan struktur CV.
- Skill gap analysis sudah mendeteksi alias skill, matched skill, missing skill, extra skill, dan rekomendasi skill.
- Semantic similarity sudah aktif memakai IndoSBERT via SentenceTransformer, dengan fallback token cosine jika model gagal dimuat.
- Recommendation engine sudah menghasilkan rekomendasi ATS, skill, perbaikan CV, dan priority actions.

## Phase 2 - ATS Scoring

Status:
DONE

Implemented:

- Analisis identitas dan kontak.
- Analisis pendidikan.
- Analisis pengalaman/proyek.
- Analisis skill.
- Analisis struktur CV.
- Breakdown skor dengan bobot:
  - identity: 18
  - education: 16
  - experience: 24
  - skills: 22
  - structure: 20
- Label readiness: weak, needs_improvement, ready, strong.

## Phase 3 - Skill Gap Analysis

Status:
DONE

Implemented:

- Skill catalog lintas kategori backend, frontend, devops, data, design, mobile, dan marketing.
- Alias skill, misalnya `node`, `nodejs`, `next`, `docker compose`, `ci/cd`.
- Deteksi skill CV.
- Deteksi required skill dari job description.
- Matched skill.
- Missing skill.
- Extra skill.
- Skill match rate.
- Recommendation berbasis skill gap.

## Phase 4 - Semantic Similarity

Status:
DONE

Implemented:

- IndoSBERT SentenceTransformer untuk semantic matching CV dan job description.
- Fallback cosine berbasis token jika model tidak tersedia.
- Active SentenceTransformer configuration:
  - `USE_SENTENCE_TRANSFORMER=true`
  - `SEMANTIC_MODEL=/app/models/indoSBERT-large`
  - Local model files: `ai/models/indoSBERT-large`
- Response `semantic_similarity`.
- Response `match_score` gabungan ATS score, skill match rate, dan semantic score.

## Phase 5 - Recommendation Engine

Status:
DONE

Implemented:

- ATS recommendation dari section dan breakdown skor.
- Skill recommendation dari missing skill dan job required skill.
- CV improvement suggestion dari semantic score, match score, metrik, dan action verb.
- Priority actions untuk frontend/dashboard.
- Summary rekomendasi singkat.

Current Sprint:

- Backend AI roadmap dasar selesai sampai Phase 5.
- IndoSBERT sudah aktif dan tervalidasi pada endpoint `/analyze`.
- Next: training/evaluasi model dan tuning bobot scoring.
