from fastapi import FastAPI, BackgroundTasks, HTTPException
from pydantic import BaseModel
import torch
from transformers import BertTokenizer, BertForSequenceClassification
import fitz
import joblib
import re
import os
import pytesseract
from pdf2image import convert_from_path
import mysql.connector
import json
import requests
import traceback
import google.generativeai as genai
from fastapi.middleware.cors import CORSMiddleware

app = FastAPI(title="Nexus AI CV Engine")
app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)
# =====================================================
# LOAD MODEL
# =====================================================
MODEL_DIR = "/app/model_bert_cv"

model = BertForSequenceClassification.from_pretrained(MODEL_DIR)
tokenizer = BertTokenizer.from_pretrained(MODEL_DIR)
label_encoder = joblib.load(f"{MODEL_DIR}/label_encoder.pkl")
model.eval()

# =====================================================
# DATABASE CONFIG
# =====================================================
db_config = {
    "host": os.getenv("DB_HOST", "db"),
    "user": os.getenv("DB_USERNAME", "root"),
    "password": os.getenv("DB_PASSWORD", "password"),
    "database": os.getenv("DB_DATABASE", "laravel")
}

# =====================================================
# RAPID API CONFIG
# =====================================================
RAPIDAPI_KEY = "b25b1c209bmshf7eadd4554dde75p105d85jsn903fd0ca2145"
RAPIDAPI_HOST = "jsearch.p.rapidapi.com"

# =====================================================
# GEMINI CONFIG
# =====================================================

GEMINI_API_KEY = "AIzaSyDChBZySgWJaZ5L7nmDw1i4AOwCUuXNKII"

genai.configure(api_key=GEMINI_API_KEY)

gemini_model = genai.GenerativeModel("gemini-2.5-flash")
# =====================================================
# BASE PATH STORAGE LARAVEL
# Sesuaikan dengan path storage Laravel kamu
# Contoh: /var/www/html/namaproject/storage/app/public
# =====================================================
LARAVEL_STORAGE_BASE = "/var/www/html/public/storage"


# =====================================================
# REQUEST MODEL
# =====================================================
class CVRequest(BaseModel):
    cv_id: int
    file_path: str

class InterviewRequest(BaseModel):
    user_id: int
    cv_id: int
    job_tema: str

class EvaluateAnswerRequest(BaseModel):
    interview_id: int
    answer: str

# =====================================================
# CLEAN TEXT
# =====================================================
def normalize_text(text):
    text = text.lower()
    text = re.sub(r"\s+", " ", text)
    text = re.sub(r"[^a-zA-Z0-9@+.\-\s]", " ", text)
    return text.strip()


# =====================================================
# RESOLVE FILE PATH
# Otomatis handle path relative dari Laravel
# =====================================================
def resolve_file_path(file_path: str) -> str:
    # Jika sudah absolute path dan file exist, langsung pakai
    if os.path.isabs(file_path) and os.path.exists(file_path):
        return file_path

    # Coba gabungkan dengan LARAVEL_STORAGE_BASE
    joined = os.path.join(LARAVEL_STORAGE_BASE, file_path)
    if os.path.exists(joined):
        return joined

    # Coba tanpa prefix "cv_uploads/" jika sudah ada di base
    filename = os.path.basename(file_path)
    fallback = os.path.join(LARAVEL_STORAGE_BASE, "cv_uploads", filename)
    if os.path.exists(fallback):
        return fallback

    # Return original untuk error message yang jelas
    return file_path


# =====================================================
# EXTRACT TEXT PDF
# =====================================================
def extract_text_comprehensive(file_path):
    text = ""

    try:
        doc = fitz.open(file_path)
        for page in doc:
            page_text = page.get_text()
            if page_text:
                text += page_text
        doc.close()
        print(f"PyMuPDF extracted {len(text)} chars")
    except Exception as e:
        print(f"PyMuPDF Error: {str(e)}")

    if len(text.strip()) < 50:
        print("Teks terlalu sedikit, mencoba OCR...")
        try:
            images = convert_from_path(file_path, dpi=200)
            for i, img in enumerate(images):
                ocr_text = pytesseract.image_to_string(img, lang="eng+ind")
                text += ocr_text
                print(f"OCR page {i+1}: {len(ocr_text)} chars")
        except Exception as e:
            print(f"OCR Error: {str(e)}")

    normalized = normalize_text(text)
    print(f"Total normalized text length: {len(normalized)}")
    return normalized


# =====================================================
# STRUCTURE DETECTION
# =====================================================
def detect_structure(text):
    structure = {}

    structure["Email"] = bool(re.search(
        r"[a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9-.]+",
        text
    ))

    structure["Phone"] = bool(re.search(
        r"(\+62|62|0)8[0-9]{8,15}",
        text
    ))

    structure["Skills"] = any(x in text for x in [
        "skills", "technical skills", "soft skills",
        "keahlian", "kompetensi"
    ])

    structure["Experience"] = any(x in text for x in [
        "experience", "work experience",
        "employment history",
        "pengalaman kerja", "pengalaman"
    ])

    structure["Education"] = any(x in text for x in [
        "education", "pendidikan",
        "riwayat pendidikan",
        "academic background"
    ])

    structure["Summary"] = any(x in text for x in [
        "summary", "professional summary",
        "about me", "profile",
        "career objective",
        "ringkasan", "tentang saya"
    ])

    structure["Location"] = any(x in text for x in [
        "jakarta", "surabaya", "bandung",
        "indonesia", "alamat", "address"
    ])

    first_words = text.split()[:20]
    structure["Name"] = len(first_words) >= 2

    weights = {
        "Name": 10,
        "Email": 15,
        "Phone": 15,
        "Skills": 15,
        "Experience": 20,
        "Education": 15,
        "Summary": 5,
        "Location": 5
    }

    final_score = 0
    for key, value in structure.items():
        if value:
            final_score += weights[key]

    return final_score, structure


# =====================================================
# JOB API
# Mencari lowongan yang benar-benar relevan dengan
# job_title hasil prediksi BERT, lokasi Indonesia.
# Strategi:
#   1. Query: "{job_title} Indonesia"
#   2. Dari semua hasil (maks 10), pilih job yang
#      title-nya paling banyak mengandung keyword
#      dari job_title prediksi BERT
#   3. Bonus +2 jika job punya apply link
#   4. Fallback ke job_google_link jika apply_link kosong
#   5. company & job_link selalu dari job yang sama
#      sehingga ketiganya (job_title, company, job_link)
#      pasti saling nyambung
# =====================================================

def fetch_job_recommendations(job_title, limit=12):
    try:
        url = "https://jsearch.p.rapidapi.com/search"

        headers = {
            "X-RapidAPI-Key": "b25b1c209bmshf7eadd4554dde75p105d85jsn903fd0ca2145",
            "X-RapidAPI-Host": "jsearch.p.rapidapi.com"
        }

        params = {
            "query": job_title,
            "page": "1",
            "num_pages": "2",
            "language": "en"
        }

        response = requests.get(url, headers=headers, params=params, timeout=15)
        response.raise_for_status()
        data = response.json()

        jobs = data.get("data", [])
        if not jobs:
            return []

        job_title_lower = job_title.lower()
        keywords = re.findall(r"[a-zA-Z]+", job_title_lower)

        scored_jobs = []

        for job in jobs:
            api_title = job.get("job_title", "").lower()
            apply_link = job.get("job_apply_link", "")

            match_count = sum(1 for kw in keywords if kw in api_title)

            max_possible = len(keywords)

            match_percent = int((match_count / max_possible) * 100) if max_possible > 0 else 0

            if apply_link:
                match_percent += 10

            match_percent = min(match_percent, 100)
            job_link = apply_link if apply_link else job.get("job_google_link", "")

            scored_jobs.append({
                "job_title": job.get("job_title", "Unknown Job"),
                "company": job.get("employer_name", "Unknown Company"),
                "job_link": job_link,
                "score": match_percent
            })

        # 🔥 SORT + ambil TOP N
        scored_jobs = sorted(scored_jobs, key=lambda x: x["score"], reverse=True)

        return scored_jobs[:limit]

    except Exception as e:
        print(f"Job API Error: {str(e)}")
        return []


# =====================================================
# UPDATE DB — SET STATUS FAILED
# =====================================================
def update_db_failed(cv_id, error_message):
    conn = None
    try:
        conn = mysql.connector.connect(**db_config)
        cursor = conn.cursor()

        failed_data = {
            "status": "Failed",
            "error": error_message
        }

        cursor.execute(
            """
            UPDATE cvs
            SET score = 0,
                analysis = %s,
                job_tema = NULL,
                updated_at = NOW()
            WHERE id = %s
            """,
            (json.dumps(failed_data), cv_id)
        )
        conn.commit()
        print(f"DB updated as Failed for cv_id={cv_id}")
    except Exception as e:
        print(f"update_db_failed error: {str(e)}")
    finally:
        if conn and conn.is_connected():
            conn.close()


# =====================================================
# MAIN PROCESS
# =====================================================
def process_and_update_db(cv_id, file_path):
    conn = None

    try:
        print("=" * 50)
        print("START PROCESS")
        print(f"CV ID: {cv_id}")

        # =========================
        # STEP 1: LOAD FILE
        # =========================
        resolved_path = resolve_file_path(file_path)

        if not os.path.exists(resolved_path):
            raise FileNotFoundError(f"File tidak ditemukan: {resolved_path}")

        text = extract_text_comprehensive(resolved_path)

        if not text.strip():
            raise ValueError("Teks kosong")

        # =========================
        # STEP 2: SCORE
        # =========================
        final_score, detail = detect_structure(text)
        print(f"FINAL SCORE: {final_score}")

        # =========================
        # STEP 3: BERT
        # =========================
        inputs = tokenizer(
            text,
            return_tensors="pt",
            truncation=True,
            padding=True,
            max_length=512
        )

        with torch.no_grad():
            outputs = model(**inputs)

        pred = torch.argmax(outputs.logits, dim=1).item()
        job_label = label_encoder.inverse_transform([pred])[0]

        print(f"JOB TEMA: {job_label}")

        # =========================
        # STEP 4: JOB API
        # =========================
        job_list = fetch_job_recommendations(job_label, limit=12)

        # =========================
        # STEP 5: ANALYSIS
        # =========================
        strengths = [k for k, v in detail.items() if v]
        weaknesses = [k for k, v in detail.items() if not v]

        analysis_data = {
            "status": "done",
            "strengths": strengths,
            "weaknesses": weaknesses,
            "suggestions": [f"Lengkapi bagian {k}" for k in weaknesses]
        }

        # =========================
        # STEP 6: DB CONNECT
        # =========================
        conn = mysql.connector.connect(**db_config)
        cursor = conn.cursor()

        # =========================
        # STEP 7: UPDATE CVS (WAJIB SUKSES)
        # =========================
        cursor.execute(
            """
            UPDATE cvs
            SET score = %s,
                analysis = %s,
                job_tema = %s,
                updated_at = NOW()
            WHERE id = %s
            """,
            (final_score, json.dumps(analysis_data), job_label, cv_id)
        )

        print("CV UPDATED SUCCESS")

        # =========================
        # STEP 8: INSERT JOBS (NO USER_ID)
        # =========================
        cursor.execute(
            "DELETE FROM job_recommendations WHERE cv_id = %s",
            (cv_id,)
        )

        for job in job_list:
            cursor.execute(
                """
                INSERT INTO job_recommendations
                (
                    cv_id,
                    job_title,
                    company,
                    match_score,
                    job_link,
                    created_at,
                    updated_at
                )
                VALUES (%s, %s, %s, %s, %s, NOW(), NOW())
                """,
                (
                    cv_id,
                    job["job_title"],
                    job["company"],
                    job["score"],
                    job["job_link"]
                )
            )

        print(f"{len(job_list)} JOBS INSERTED")

        # =========================
        # STEP 9: COMMIT
        # =========================
        conn.commit()

        print("COMMIT SUCCESS ✅")

    except Exception as e:
        print("ERROR:", traceback.format_exc())

        if conn and conn.is_connected():
            conn.rollback()

        update_db_failed(cv_id, str(e))

    finally:
        if conn and conn.is_connected():
            conn.close()

# =====================================================
# GENERATE PERTANYAAN
# =====================================================

def generate_interview_questions(job_tema):
    try:
        prompt = f"""
Anda adalah HRD senior yang berpengalaman di Indonesia.

Buatlah tepat 5 pertanyaan interview profesional dalam BAHASA INDONESIA untuk posisi ini:

{job_tema}

Aturan:
- Pertanyaan harus relevan dengan posisi tersebut
- Gunakan Bahasa Indonesia yang baik dan benar
- Campurkan pertanyaan technical + behavioral + problem solving
- Kembalikan hanya dalam format JSON

Format yang diharapkan:

{{
  "questions": [
    "Pertanyaan 1",
    "Pertanyaan 2",
    "Pertanyaan 3",
    "Pertanyaan 4",
    "Pertanyaan 5"
  ]
}}
"""

        response = gemini_model.generate_content(prompt)

        raw_text = response.text.strip()

        # Bersihkan markdown jika Gemini return ```json
        raw_text = raw_text.replace("```json", "").replace("```", "").strip()

        parsed = json.loads(raw_text)

        return parsed.get("questions", [])
        print(f"Generated {len(questions)} questions in Bahasa Indonesia")
        for i, q in enumerate(questions, 1):
            print(f"{i}. {q}")
        
        return questions

    except Exception as e:
        print(f"Gemini Generate Question Error: {str(e)}")
        return []



# =====================================================
# EVALUATE JAWABAN
# =====================================================

def evaluate_interview_answer(question, answer):
    try:
        prompt = f"""
Anda adalah HRD senior yang berpengalaman di Indonesia.
WAJIB gunakan Bahasa Indonesia.
DILARANG menggunakan Bahasa Inggris.

Evaluasi jawaban interview berikut secara profesional dalam BAHASA INDONESIA.

Pertanyaan:
{question}

Jawaban Kandidat:
{answer}

Aturan penilaian:
- Berikan skor dari 0 hingga 100
- Nilai kejelasan jawaban (clarity)
- Nilai kepercayaan diri (confidence)
- Nilai relevansi dengan pertanyaan (relevance)
- Nilai kedalaman teknis (technical depth)
- Berikan feedback yang membangun dalam Bahasa Indonesia

Kembalikan hanya dalam format JSON:

{{
   "score": 85,
  "feedback": "Jawaban bagus dengan penjelasan yang jelas, tetapi bisa lebih kuat dengan contoh nyata dari pengalaman kerja."
}}

Pastikan:
- Feedback FULL Bahasa Indonesia
- Tidak ada kata Bahasa Inggris sama sekali
"""

        response = gemini_model.generate_content(prompt)

        raw_text = response.text.strip()
        raw_text = raw_text.replace("```json", "").replace("```", "").strip()

        parsed = json.loads(raw_text)

        score = parsed.get("score", 0)
        feedback = parsed.get("feedback", "No feedback generated")

        return score, feedback

    except Exception as e:
        print(f"Gemini Evaluate Error: {str(e)}")
        return 0, "Evaluation failed"


# =====================================================
# ENDPOINT
# =====================================================
@app.post("/analyze")
async def analyze_cv(
    request: CVRequest,
    background_tasks: BackgroundTasks
):
    # Resolve path dulu sebelum add task
    resolved = resolve_file_path(request.file_path)

    if not os.path.exists(resolved):
        raise HTTPException(
            status_code=404,
            detail=(
                f"File PDF tidak ditemukan: {request.file_path} "
                f"(resolved: {resolved})"
            )
        )

    background_tasks.add_task(
        process_and_update_db,
        request.cv_id,
        request.file_path
    )

    return {
        "status": "Processing",
        "message": "AI Engine sedang menganalisis file Anda...",
        "cv_id": request.cv_id,
        "file_path": request.file_path
    }


@app.get("/health")
async def health_check():
    return {
        "status": "ok",
        "model_loaded": model is not None,
        "label_classes": list(label_encoder.classes_)
    }

@app.post("/generate-interview")
async def generate_interview(request: InterviewRequest):
    conn = None
    
    print("=" * 50)
    print("RECEIVED REQUEST TO /generate-interview")
    print(f"Request: user_id={request.user_id}, cv_id={request.cv_id}, job_tema={request.job_tema}")
    
    try:
        # Validasi input
        if not request.user_id or request.user_id <= 0:
            raise HTTPException(status_code=400, detail="user_id tidak valid")
        
        if not request.cv_id or request.cv_id <= 0:
            raise HTTPException(status_code=400, detail="cv_id tidak valid")
        
        if not request.job_tema or request.job_tema.strip() == "":
            raise HTTPException(status_code=400, detail="job_tema tidak boleh kosong")
        
        job_tema = request.job_tema.strip()
        print(f"Job Tema: {job_tema}")
        
        # Generate pertanyaan dari Gemini berdasarkan job_tema
        questions = generate_interview_questions(job_tema)
        print(f"Generated questions: {len(questions)} questions")
        
        if not questions:
            raise HTTPException(
                status_code=500,
                detail="Gagal generate interview questions dari Gemini AI"
            )
        
        # Koneksi ke database
        conn = mysql.connector.connect(**db_config)
        cursor = conn.cursor()
        
        # Hapus interview lama untuk cv ini berdasarkan job_tema
        cursor.execute(
            """
            DELETE FROM interviews
            WHERE cv_id = %s AND job_tema = %s
            """,
            (request.cv_id, job_tema)
        )
        print(f"Deleted old interviews for cv_id={request.cv_id}, job_tema={job_tema}")
        
        # Insert pertanyaan ke database
        inserted_ids = []
        for index, question in enumerate(questions, start=1):
            cursor.execute(
                """
                INSERT INTO interviews
                (
                    user_id,
                    cv_id,
                    job_tema,
                    question,
                    answer,
                    score,
                    feedback,
                    question_order,
                    duration_seconds,
                    status,
                    created_at,
                    updated_at
                )
                VALUES
                (%s, %s, %s, %s, NULL, 0, NULL, %s, 180, 'pending', NOW(), NOW())
                """,
                (
                    request.user_id,
                    request.cv_id,
                    job_tema,
                    question,
                    index
                )
            )
            inserted_ids.append(cursor.lastrowid)
            print(f"Inserted question {index}: {question[:50]}...")
        
        conn.commit()
        print(f"Successfully inserted {len(questions)} questions")
        
        response_data = {
            "status": "success",
            "job_tema": job_tema,
            "total_questions": len(questions),
            "questions": questions,
            "interview_ids": inserted_ids
        }
        
        print(f"Response: {response_data}")
        return response_data
        
    except HTTPException:
        raise
    except Exception as e:
        error_detail = traceback.format_exc()
        print("=" * 50)
        print("ERROR TERJADI:")
        print(error_detail)
        
        if conn and conn.is_connected():
            try:
                conn.rollback()
                print("ROLLBACK berhasil")
            except Exception:
                pass
        
        raise HTTPException(
            status_code=500,
            detail=f"Internal server error: {str(e)}"
        )
        
    finally:
        if conn and conn.is_connected():
            conn.close()
            print("Koneksi DB ditutup")


@app.post("/evaluate-answer")
async def evaluate_answer(request: EvaluateAnswerRequest):
    conn = None

    try:
        conn = mysql.connector.connect(**db_config)
        cursor = conn.cursor(dictionary=True)

        # Ambil question dari DB
        cursor.execute(
            """
            SELECT id, question, user_id, cv_id, job_id, job_tema
            FROM interviews
            WHERE id = %s
            LIMIT 1
            """,
            (request.interview_id,)
        )

        interview = cursor.fetchone()

        if not interview:
            raise HTTPException(
                status_code=404,
                detail="Interview question tidak ditemukan"
            )

        question = interview["question"]

        # Gemini evaluate answer
        score, feedback = evaluate_interview_answer(
            question,
            request.answer
        )

        # Update DB
        cursor.execute(
            """
            UPDATE interviews
            SET
                answer = %s,
                score = %s,
                feedback = %s,
                status = 'evaluated',
                updated_at = NOW()
            WHERE id = %s
            """,
            (
                request.answer,
                score,
                feedback,
                request.interview_id
            )
        )

        conn.commit()

        return {
            "status": "success",
            "question": question,
            "answer": request.answer,
            "score": score,
            "feedback": feedback
        }

    except Exception as e:
        print(traceback.format_exc())

        if conn and conn.is_connected():
            conn.rollback()

        raise HTTPException(
            status_code=500,
            detail=str(e)
        )

    finally:
        if conn and conn.is_connected():
            conn.close()

# =====================================================
# RUN
# =====================================================
if __name__ == "__main__":
    import uvicorn

    uvicorn.run(
        app,
        host="0.0.0.0",
        port=8000
    )