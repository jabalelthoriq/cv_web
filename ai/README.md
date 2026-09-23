# CareerSense AI Service

FastAPI service untuk fondasi AI CareerSense sesuai `ai.md`.

## Scope

- FastAPI tidak mengakses MySQL.
- FastAPI menerima file atau path file dari Laravel.
- FastAPI mengembalikan JSON hasil ekstraksi/analisis.
- Laravel menyimpan hasil ke database.

## Endpoints

- `GET /health`
- `POST /extract-text`
  - Multipart: field `file`
  - JSON: `{ "file_path": "cv_uploads/file.pdf" }`
- `POST /analyze`
  - JSON: `{ "cv_id": 1, "file_path": "cv_uploads/file.pdf", "job_description": "optional" }`

## Phase Implemented

Phase 1 - Text Extraction Foundation:

- PDF digital extraction
- DOCX extraction
- OCR fallback untuk PDF scan
- OCR untuk gambar
- Text cleaning
- Error handling

Phase 2 - ATS Scoring:

- Identity/contact scoring
- Education scoring
- Experience/project scoring
- Skill scoring
- CV structure scoring
- `ats_breakdown` with component weights and notes
- Readiness label: `weak`, `needs_improvement`, `ready`, `strong`

Phase 3 - Skill Gap Analysis:

- Skill catalog across backend, frontend, devops, data, design, mobile, and marketing
- Skill aliases for common variants
- CV skill detection
- Job description required skill detection
- Matched skills
- Missing skills
- Extra skills
- Skill match rate
- Skill recommendations

Phase 4 - Semantic Similarity:

- Semantic similarity between extracted CV text and job description
- IndoSBERT SentenceTransformer model for CV/job semantic matching
- Cosine similarity fallback using text vectors if the model cannot load
- `semantic_similarity` response block
- `match_score` combining ATS, skill gap, and semantic similarity
- Active SentenceTransformer configuration:
  - `USE_SENTENCE_TRANSFORMER=true`
  - `SEMANTIC_MODEL=/app/models/indoSBERT-large`
  - Local model files: `ai/models/indoSBERT-large`

Phase 5 - Recommendation Engine:

- ATS recommendations from section and component score gaps
- Skill recommendations from missing job requirements
- CV improvement suggestions from semantic score, match score, metrics, and action verbs
- Priority actions for dashboard UI
- Human-readable recommendation summary

Roadmap dasar `ai.md` sudah selesai sampai Phase 5. Semantic similarity sudah aktif memakai IndoSBERT melalui SentenceTransformer. Tahap berikutnya adalah training/evaluasi model dan tuning bobot scoring.
