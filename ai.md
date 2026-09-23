# AI_STATUS.md

# CareerSense AI Service

Status Proyek:
PLANNING

Belum ada modul yang dianggap selesai.

---

# Tujuan Sistem

Membangun AI Service untuk menganalisis kesesuaian CV terhadap posisi pekerjaan menggunakan:

* OCR
* Text Extraction
* ATS Scoring
* Skill Gap Analysis
* Semantic Similarity
* Recommendation Engine

---

# Stack

Framework:

* FastAPI

Machine Learning:

* Sentence Transformers
* IndoSBERT

OCR:

* PyMuPDF
* PyPDF
* Tesseract OCR

Deployment:

* Docker

Komunikasi:

* REST API

---

# Arsitektur

Laravel
↓
Queue
↓
FastAPI
↓
JSON Result
↓
Laravel Save Database

Rule:

* FastAPI tidak mengakses MySQL.
* FastAPI hanya menerima request dan mengembalikan hasil analisis.
* Laravel menyimpan seluruh hasil ke database.

---

# Roadmap Pengembangan

## Phase 1 - Text Extraction Foundation

Status:
NOT STARTED

Target:

* Membaca PDF digital
* Membaca DOCX
* OCR untuk PDF scan
* OCR untuk gambar
* Membersihkan hasil ekstraksi

Output:

Clean Text

---

## Phase 2 - ATS Scoring

Status:
NOT STARTED

Target:

* Analisis identitas
* Analisis pendidikan
* Analisis pengalaman
* Analisis skill
* Analisis struktur CV

Output:

ATS Score

---

## Phase 3 - Skill Gap Analysis

Status:
NOT STARTED

Target:

* Matching skill
* Alias skill
* Missing skill detection

Output:

Matched Skills
Missing Skills

---

## Phase 4 - Semantic Similarity

Status:
NOT STARTED

Target:

* Embedding CV
* Embedding Job Description
* Cosine Similarity

Output:

Match Score

---

## Phase 5 - Recommendation Engine

Status:
NOT STARTED

Target:

* ATS Recommendation
* Skill Recommendation
* CV Improvement Suggestion

Output:

Recommendations

---

# API Planned

## POST /extract-text

Status:
NOT IMPLEMENTED

---

## POST /analyze

Status:
NOT IMPLEMENTED

---

# Current Sprint

Belum dimulai.

Fokus pertama:

1. Menentukan arsitektur OCR.
2. Menentukan library ekstraksi PDF/DOCX.
3. Menentukan strategi text cleaning.
4. Membuat endpoint /extract-text.

---

# Success Criteria

Phase 1 dianggap selesai jika:

* PDF digital dapat dibaca.
* DOCX dapat dibaca.
* PDF scan dapat dibaca OCR.
* Hasil teks bersih dan konsisten.
* Error handling tersedia.

---

# Resume Instruction

Jika AI Agent dihentikan:

1. Baca AI_STATUS.md.
2. Lanjutkan dari Current Sprint.
3. Jangan melompat ke ATS atau IndoSBERT sebelum Text Extraction selesai.
4. Fokus pada kualitas ekstraksi teks terlebih dahulu.
