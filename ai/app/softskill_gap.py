"""
Softskill Gap Analysis Module
=============================
Mencocokkan teks CV secara semantik dengan dataset softskill IT Indonesia
menggunakan IndoSBERT / Semantic Similarity, untuk mengukur kecocokan dan menampilkan gap.
"""

from app.dataset_loader import load_softskill_dataset
from app.semantic import _load_model, _token_cosine_similarity, USE_TRANSFORMER


def analyze_softskill_gap(cv_text: str) -> dict:
    """Mencocokkan CV dengan seluruh entri softskill IT di dataset secara semantik."""
    softskills = load_softskill_dataset()
    if not softskills or not cv_text.strip():
        return {
            "matched_softskills": [],
            "missing_softskills": [],
            "overall_softskill_score": 0,
            "details": [],
        }

    scored_skills = []

    if USE_TRANSFORMER:
        try:
            model = _load_model()
            skill_texts = [
                f"{s['nama_softskill']}. {s['deskripsi']}. Contoh: {s['contoh_penerapan']}"
                for s in softskills
            ]
            all_embeddings = model.encode([cv_text] + skill_texts, normalize_embeddings=True)
            cv_emb = all_embeddings[0]
            skill_embs = all_embeddings[1:]

            for idx, item in enumerate(softskills):
                score = round(float(sum(a * b for a, b in zip(cv_emb, skill_embs[idx]))) * 100)
                score = max(0, min(100, score))
                scored_skills.append(_format_softskill_item(item, score))
        except Exception:
            scored_skills = _softskill_fallback(cv_text, softskills)
    else:
        scored_skills = _softskill_fallback(cv_text, softskills)

    # Memisahkan softskill yang cocok vs yang gap/missing berdasarkan ambang batas (threshold 45)
    matched = [s for s in scored_skills if s["similarity_score"] >= 45]
    missing = [s for s in scored_skills if s["similarity_score"] < 45]

    matched.sort(key=lambda x: x["similarity_score"], reverse=True)
    missing.sort(key=lambda x: x["similarity_score"])  # terendah dulu

    avg_score = round(sum(s["similarity_score"] for s in scored_skills) / len(scored_skills)) if scored_skills else 0

    return {
        "overall_softskill_score": avg_score,
        "matched_softskills_count": len(matched),
        "missing_softskills_count": len(missing),
        "matched_softskills": matched,
        "missing_softskills": missing,
        "all_details": scored_skills,
    }


def _softskill_fallback(cv_text: str, softskills: list[dict]) -> list[dict]:
    scored = []
    for item in softskills:
        text = f"{item['nama_softskill']} {item['deskripsi']} {item['contoh_penerapan']}"
        score = _token_cosine_similarity(cv_text, text)
        scored.append(_format_softskill_item(item, score))
    return scored


def _format_softskill_item(item: dict, score: int) -> dict:
    return {
        "no": item.get("no"),
        "kategori": item.get("kategori_softskill"),
        "nama_softskill": item.get("nama_softskill"),
        "deskripsi": item.get("deskripsi"),
        "contoh_penerapan": item.get("contoh_penerapan"),
        "tingkat_kepentingan": item.get("tingkat_kepentingan"),
        "similarity_score": score,
        "status": "matched" if score >= 45 else "gap",
    }
