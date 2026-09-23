"""
Job Recommender Module
======================
Mencocokkan teks CV yang diekstrak dengan pekerjaan di dataset IT
menggunakan IndoSBERT / Semantic Similarity, lalu menghasilkan rekomendasi pekerjaan
terbaik beserta data yang siap dikirimkan ke API lowongan pekerjaan.
"""

from app.dataset_loader import load_job_dataset
from app.semantic import _load_model, _token_cosine_similarity, USE_TRANSFORMER


def recommend_jobs(cv_text: str, top_n: int = 5) -> list[dict]:
    """Mencocokkan CV dengan semua pekerjaan di dataset pekerjaan IT.

    Menghasilkan list rekomendasi pekerjaan terbaik diurutkan berdasarkan skor kemiripan semantik.
    """
    jobs = load_job_dataset()
    if not jobs or not cv_text.strip():
        return []

    scored_jobs = []

    if USE_TRANSFORMER:
        try:
            model = _load_model()
            # Gabungkan deskripsi dan skill teknis untuk konteks pekerjaan yang komprehensif
            job_texts = [
                f"{j['nama_pekerjaan']}. {j['deskripsi_pekerjaan']}. Skill Teknis: {j['skill_teknis']}"
                for j in jobs
            ]
            
            # Encode CV dan semua pekerjaan
            all_embeddings = model.encode([cv_text] + job_texts, normalize_embeddings=True)
            cv_embedding = all_embeddings[0]
            job_embeddings = all_embeddings[1:]

            for idx, job in enumerate(jobs):
                score = round(float(sum(a * b for a, b in zip(cv_embedding, job_embeddings[idx]))) * 100)
                score = max(0, min(100, score))
                scored_jobs.append(_format_job_recommendation(job, score))
        except Exception:
            # Fallback ke token cosine jika model transformer gagal
            scored_jobs = _recommend_fallback(cv_text, jobs)
    else:
        scored_jobs = _recommend_fallback(cv_text, jobs)

    # Urutkan berdasarkan similarity_score tertinggi
    scored_jobs.sort(key=lambda x: x["similarity_score"], reverse=True)
    return scored_jobs[:top_n]


def _recommend_fallback(cv_text: str, jobs: list[dict]) -> list[dict]:
    scored_jobs = []
    for job in jobs:
        job_text = f"{job['nama_pekerjaan']} {job['deskripsi_pekerjaan']} {job['skill_teknis']}"
        score = _token_cosine_similarity(cv_text, job_text)
        scored_jobs.append(_format_job_recommendation(job, score))
    return scored_jobs


def _format_job_recommendation(job: dict, score: int) -> dict:
    return {
        "job_id": job.get("no"),
        "kategori": job.get("kategori"),
        "nama_pekerjaan": job.get("nama_pekerjaan"),
        "deskripsi": job.get("deskripsi_pekerjaan"),
        "skill_teknis_required": job.get("skill_teknis"),
        "skill_non_teknis_required": job.get("skill_non_teknis"),
        "pendidikan_minimal": job.get("pendidikan_minimal"),
        "similarity_score": score,
        "api_payload": {
            "query": job.get("nama_pekerjaan"),
            "category": job.get("kategori"),
            "skills": [s.strip() for s in job.get("skill_teknis", "").split(",") if s.strip()],
        },
    }
