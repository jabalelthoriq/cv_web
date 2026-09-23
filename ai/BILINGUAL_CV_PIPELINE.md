# CareerSense Bilingual CV Pipeline

Dokumen ini menjelaskan rancangan fitur agar CareerSense dapat membaca CV berbahasa Inggris, Indonesia, atau campuran, lalu menghasilkan analisis dalam Bahasa Indonesia secara presisi tanpa memakai API berbayar.

## Pilihan Yang Dipakai

Pendekatan paling cocok untuk kondisi saat ini adalah **hybrid bilingual lokal**.

Artinya:

- CV tidak diterjemahkan penuh dari Inggris ke Indonesia.
- Skill, role, section, dan keyword penting dinormalisasi ke bentuk standar.
- Kalimat naratif Inggris tetap dipahami sebagai konteks asli.
- Output analisis tetap diberikan dalam Bahasa Indonesia.
- Semua proses berjalan lokal di AI service FastAPI.

Pendekatan ini dipilih karena gratis, lebih aman untuk data CV, dan lebih presisi untuk CV orang Indonesia yang sering memakai campuran istilah Inggris seperti `software engineer`, `internship`, `machine learning`, `data analysis`, `leadership`, `Laravel`, `React`, dan `project management`.

## Kenapa Tidak Full Translate

Full translate terlihat mudah, tetapi berisiko untuk CV:

- Nama perusahaan, universitas, sertifikasi, dan tech stack bisa ikut berubah.
- Istilah teknis bisa diterjemahkan terlalu literal.
- Achievement seperti `reduced processing time by 30%` bisa kehilangan makna jika diterjemahkan asal.
- Translation API paling bagus biasanya berbayar.
- Jika memakai API eksternal, data CV user keluar dari server.

Karena itu, sistem lebih baik membaca CV secara bilingual, bukan mengganti semua teks menjadi Indonesia.

## Target Sistem

Sistem harus mampu:

1. Mendeteksi bahasa CV per dokumen dan per paragraf.
2. Mengenali section CV dalam dua bahasa.
3. Mengenali skill Inggris dan Indonesia sebagai skill yang sama.
4. Menjaga entity penting agar tidak rusak.
5. Menghasilkan analisis dan saran dalam Bahasa Indonesia.
6. Tetap bekerja tanpa internet dan tanpa API berbayar.
7. Tetap kompatibel dengan alur `/analyze` yang sudah ada.

## Alur Sistem

```text
Upload CV
   |
   v
Extract Text
   |
   v
Clean Text
   |
   v
Detect Language
   |
   v
Protect Entities
   |
   v
Normalize Bilingual Terms
   |
   v
Analyze CV
   |
   v
Skill Gap + Semantic Similarity
   |
   v
Recommendation Engine
   |
   v
Indonesian Output
```

## Detail Fungsi

### 1. `extract_text`

Lokasi saat ini:

- `app/main.py`
- `app/extractor.py`

Fungsi:

- Membaca file PDF/DOCX/TXT.
- Mengubah file menjadi plain text.
- Mengembalikan metadata ekstraksi seperti `file_type`, `word_count`, dan `warnings`.

Output penting:

```json
{
  "text": "raw extracted CV text",
  "file_type": "pdf",
  "character_count": 12000,
  "word_count": 900
}
```

### 2. `clean_text`

Lokasi saat ini:

- `app/text_cleaning.py`

Fungsi:

- Membersihkan whitespace.
- Menghapus noise OCR ringan.
- Menyiapkan teks agar mudah dianalisis.

Catatan:

Cleaning tidak boleh menghapus simbol penting untuk skill seperti `C++`, `C#`, `.NET`, `Node.js`, `Next.js`, `CI/CD`, atau `UI/UX`.

### 3. `detect_language`

Lokasi usulan:

- `app/language.py`

Fungsi:

- Mendeteksi bahasa utama CV.
- Mendeteksi bahasa per paragraf.
- Memberi label:
  - `id`
  - `en`
  - `mixed`
  - `unknown`

Implementasi gratis tahap awal:

- Rule-based scoring dari stopword Indonesia dan Inggris.
- Tidak perlu dependency besar.
- Cukup presisi untuk membedakan CV Indonesia, Inggris, dan campuran.

Contoh logika:

```text
Jika kata seperti "experience", "education", "skills", "summary" dominan -> en
Jika kata seperti "pengalaman", "pendidikan", "keahlian", "ringkasan" dominan -> id
Jika keduanya kuat -> mixed
```

Output:

```json
{
  "document_language": "mixed",
  "confidence": 0.82,
  "paragraphs": [
    { "index": 0, "language": "en" },
    { "index": 1, "language": "id" }
  ]
}
```

### 4. `protect_entities`

Lokasi usulan:

- `app/normalizer.py`

Fungsi:

- Menandai bagian yang tidak boleh diterjemahkan atau diubah.
- Menjaga data penting tetap sama.

Entity yang harus dilindungi:

- Nama orang.
- Email.
- Nomor telepon.
- URL.
- LinkedIn/GitHub/portfolio.
- Nama perusahaan.
- Nama universitas.
- Tanggal dan tahun.
- Tech stack.
- Sertifikasi.
- Angka achievement.

Contoh:

```text
Original:
Reduced API response time by 35% using Redis and Laravel Queue.

Entity protected:
Reduced API response time by <PERCENT_1> using <SKILL_REDIS> and <SKILL_LARAVEL_QUEUE>.
```

Pada pendekatan hybrid lokal, entity protection tetap berguna walaupun tidak ada full translation, karena membantu analyzer membedakan skill, angka impact, dan data identitas.

### 5. `normalize_bilingual_terms`

Lokasi usulan:

- `app/normalizer.py`

Fungsi:

- Mengubah variasi istilah Inggris/Indonesia menjadi canonical skill atau canonical section.
- Menambahkan teks normalisasi tambahan tanpa menghapus teks asli.

Contoh mapping section:

```json
{
  "summary": "profile",
  "profile": "profile",
  "about me": "profile",
  "ringkasan": "profile",
  "education": "education",
  "pendidikan": "education",
  "experience": "experience",
  "pengalaman": "experience",
  "work history": "experience",
  "skills": "skills",
  "keahlian": "skills"
}
```

Contoh mapping skill:

```json
{
  "machine learning": "machine_learning",
  "pembelajaran mesin": "machine_learning",
  "data analysis": "data_analysis",
  "analisis data": "data_analysis",
  "leadership": "leadership",
  "kepemimpinan": "leadership",
  "communication": "communication",
  "komunikasi": "communication"
}
```

Output yang disarankan:

```json
{
  "original_text": "English or mixed CV text",
  "analysis_text": "original text + normalized bilingual signals",
  "canonical_skills": ["machine_learning", "data_analysis", "leadership"],
  "canonical_sections": {
    "profile": true,
    "education": true,
    "experience": true,
    "skills": true
  }
}
```

### 6. `analyze_cv`

Lokasi saat ini:

- `app/analyzer.py`

Fungsi saat ini:

- Memberi skor identitas.
- Memberi skor pendidikan.
- Memberi skor pengalaman.
- Memberi skor skill.
- Memberi skor struktur.
- Menghasilkan `strengths`, `weaknesses`, dan `suggestions`.

Perubahan yang disarankan:

```python
normalized = normalize_bilingual_cv(text)
analysis = analyze_cv(normalized.analysis_text, job_description)
analysis["language"] = normalized.language
analysis["canonical_skills"] = normalized.canonical_skills
analysis["original_text_preserved"] = True
```

Dengan cara ini analyzer lama tetap bisa dipakai, tetapi input-nya lebih kaya dan bilingual.

### 7. `analyze_skill_gap`

Lokasi saat ini:

- `app/skill_gap.py`

Fungsi:

- Membandingkan skill di CV dengan job description.
- Menghasilkan skill yang cocok dan skill yang hilang.

Perubahan yang disarankan:

- Gunakan canonical skill, bukan hanya string asli.
- Job description juga dinormalisasi bilingual.

Contoh:

```text
CV: "experienced in data analysis and dashboarding"
Job: "mampu melakukan analisis data dan membuat dashboard"

Tanpa bilingual mapping:
Match rendah.

Dengan bilingual mapping:
Match data_analysis dan dashboard naik.
```

### 8. `semantic_similarity`

Lokasi saat ini:

- `app/semantic.py`

Kondisi saat ini:

- Model menggunakan `SEMANTIC_MODEL=/app/models/indoSBERT-large`.
- Model ini kuat untuk Bahasa Indonesia, tetapi tidak ideal untuk CV full Inggris.

Strategi gratis yang disarankan:

- Tetap pakai IndoSBERT untuk teks Indonesia.
- Untuk CV Inggris/campuran, tambah sinyal token/canonical skill agar score tidak jatuh.
- Tahap lanjut: ganti atau tambah model multilingual lokal seperti:
  - `sentence-transformers/paraphrase-multilingual-MiniLM-L12-v2`
  - `sentence-transformers/distiluse-base-multilingual-cased-v2`

Catatan:

Model multilingual bisa gratis, tetapi perlu download model tambahan dan ukuran image akan naik. Untuk tahap awal, bilingual normalization lebih ringan.

### 9. `build_recommendations`

Lokasi saat ini:

- `app/recommendation.py`

Fungsi:

- Membuat rekomendasi perbaikan CV.
- Menentukan prioritas saran.

Perubahan yang disarankan:

- Output tetap Bahasa Indonesia.
- Jika CV Inggris, beri saran yang menghormati konteks CV Inggris.

Contoh output:

```text
CV Anda sudah kuat pada pengalaman proyek, tetapi bagian achievement masih bisa dibuat lebih terukur. Tambahkan angka seperti jumlah user, persentase peningkatan, waktu proses yang dipercepat, atau ukuran tim.
```

## Struktur File Usulan

```text
cv_web/ai/app/
  language.py
  normalizer.py
  bilingual_terms.py
  analyzer.py
  skill_gap.py
  semantic.py
```

### `language.py`

Berisi:

- `detect_language(text: str) -> dict`
- `detect_paragraph_languages(text: str) -> list[dict]`

### `bilingual_terms.py`

Berisi:

- `SECTION_ALIASES`
- `SKILL_ALIASES_BILINGUAL`
- `ROLE_ALIASES_BILINGUAL`
- `SOFT_SKILL_ALIASES_BILINGUAL`
- `ACTION_VERBS_BILINGUAL`

### `normalizer.py`

Berisi:

- `protect_entities(text: str) -> dict`
- `normalize_bilingual_cv(text: str) -> BilingualCvNormalization`
- `append_normalized_signals(text: str, signals: list[str]) -> str`

## Format Response API Yang Disarankan

Tambahkan field baru pada response `/analyze`:

```json
{
  "status": "success",
  "score": 82,
  "analysis": {
    "language": {
      "document_language": "mixed",
      "confidence": 0.82
    },
    "bilingual": {
      "enabled": true,
      "strategy": "local_hybrid_normalization",
      "translation_provider": "none",
      "original_text_preserved": true,
      "canonical_skills": ["python", "laravel", "data_analysis"]
    }
  }
}
```

## Contoh Kasus

Input CV:

```text
Software Engineer Intern
Built a Laravel-based reporting dashboard and improved report generation time by 40%.
Skills: PHP, Laravel, MySQL, Redis, Communication
```

Normalisasi:

```text
canonical_role: software_engineer_intern
canonical_skills: php, laravel, mysql, redis, communication
canonical_impact: improved_metric
language: en
```

Output analisis:

```text
CV sudah kuat pada pengalaman teknis dan memiliki achievement terukur. Skill Laravel, MySQL, dan Redis terdeteksi jelas. Untuk meningkatkan skor, tambahkan konteks skala proyek seperti jumlah user, ukuran tim, atau tujuan bisnis dashboard.
```

## Tahapan Implementasi

### Phase 1 - Gratis, ringan, cepat

Target:

- Rule-based language detection.
- Bilingual section aliases.
- Bilingual skill aliases.
- Analyzer memakai `analysis_text` yang sudah ditambah canonical signals.
- Output analisis tetap Indonesia.

Estimasi:

- Cepat.
- Tidak perlu API.
- Tidak perlu model baru.
- Cocok untuk kondisi project sekarang.

### Phase 2 - Presisi lebih tinggi

Target:

- Tambah model multilingual SentenceTransformer lokal.
- Semantic similarity memilih model berdasarkan bahasa:
  - IndoSBERT untuk Indonesia.
  - Multilingual MiniLM untuk Inggris/campuran.

Konsekuensi:

- Download model tambahan.
- Docker image lebih besar.
- Waktu build lebih lama.

### Phase 3 - Optional translation preview

Target:

- Tampilkan versi ringkasan Indonesia, bukan full translation.
- Translasi tetap lokal atau optional provider.
- Tidak menjadi dasar utama scoring.

Konsekuensi:

- Butuh model lokal translation jika tetap ingin gratis.
- Tidak wajib untuk akurasi scoring.

## Rekomendasi Final

Untuk CareerSense sekarang, mulai dari **Phase 1**.

Alasannya:

- Gratis.
- Tidak mengirim data CV keluar server.
- Tidak menambah beban model besar.
- Cocok dengan analyzer yang sudah ada.
- Paling aman untuk CV campuran Inggris-Indonesia.
- Bisa langsung meningkatkan pembacaan CV Inggris tanpa merusak sistem lama.

Jika nanti butuh akurasi semantic yang lebih tinggi untuk job matching lintas bahasa, lanjut ke **Phase 2** dengan model multilingual lokal.
