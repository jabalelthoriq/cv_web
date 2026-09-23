import re
from collections import Counter
from dataclasses import dataclass

from app.job_recommender import recommend_jobs
from app.normalizer import normalize_bilingual_cv
from app.recommendation import build_recommendations
from app.semantic import semantic_similarity
from app.skill_gap import analyze_skill_gap
from app.softskill_gap import analyze_softskill_gap
from app.text_cleaning import word_count


SECTION_PATTERNS = {
    "profile": r"\b(profile|profil|summary|ringkasan|about|tentang|objective)\b",
    "education": r"\b(education|pendidikan|universitas|university|kampus|sekolah|sma|smk|sarjana|diploma|s1|s2|s3)\b",
    "experience": r"\b(experience|pengalaman|work|kerja|magang|internship|freelance|project|proyek|employment)\b",
    "skills": r"\b(skill|skills|keahlian|kemampuan|tools|technology|teknologi|kompetensi)\b",
    "contact": r"[\w.+-]+@[\w-]+\.[\w.-]+|\+?\d[\d\s().-]{7,}",
}

SKILL_ALIASES = {
    "python": {"python", "fastapi", "django", "flask"},
    "php": {"php", "laravel", "codeigniter"},
    "javascript": {"javascript", "typescript", "node", "nodejs", "react", "vue", "next", "nextjs"},
    "database": {"mysql", "postgres", "postgresql", "mongodb", "sql", "database", "redis"},
    "design": {"figma", "ui", "ux", "photoshop", "canva"},
    "data": {"excel", "tableau", "power bi", "pandas", "analytics", "data analysis"},
    "cloud": {"aws", "gcp", "azure", "docker", "kubernetes", "linux", "nginx"},
    "mobile": {"android", "kotlin", "flutter", "react native", "ios", "swift"},
    "writing": {"copywriting", "content", "seo", "artikel", "blog"},
    "marketing": {"marketing", "social media", "ads", "meta ads", "google ads", "campaign"},
}

ACTION_VERBS = {
    "built",
    "created",
    "developed",
    "designed",
    "managed",
    "improved",
    "optimized",
    "implemented",
    "launched",
    "membangun",
    "membuat",
    "mengembangkan",
    "mengelola",
    "meningkatkan",
    "mengoptimalkan",
    "merancang",
}


@dataclass(frozen=True)
class ComponentScore:
    score: int
    max_score: int
    notes: list[str]


def analyze_cv(text: str, job_description: str | None = None) -> dict:
    normalized_cv = normalize_bilingual_cv(text)
    analysis_text = normalized_cv.analysis_text
    normalized_job_description = (
        normalize_bilingual_cv(job_description).analysis_text if job_description else None
    )

    lowered = analysis_text.lower()
    lines = [line.strip() for line in text.splitlines() if line.strip()]
    words = word_count(text)
    sections = _section_hits(lowered)
    if not sections.get("profile") and _has_profile_intro(text):
        sections["profile"] = True
    matched_skills = _matched_skills(lowered) | set(normalized_cv.canonical_skills)
    semantic_target = normalized_job_description or _infer_semantic_target(normalized_cv, matched_skills)
    skill_gap = analyze_skill_gap(analysis_text, normalized_job_description)
    semantic = semantic_similarity(analysis_text, semantic_target)
    if not normalized_job_description and semantic.get("enabled"):
        semantic["method"] = "inferred_profile_" + str(semantic.get("method", "semantic"))
        semantic.setdefault("notes", [])
        semantic["notes"].insert(
            0,
            "Job description belum dipilih; semantic score memakai target profil otomatis dari skill CV.",
        )
    missing_skills = set(skill_gap["missing_skills"])
    matched_skills = (set(skill_gap["matched_skills"]) | matched_skills) or matched_skills

    # Analisis dataset pekerjaan IT & Softskill IT secara semantik
    job_recommendations = recommend_jobs(analysis_text, top_n=5)
    softskill_gap = analyze_softskill_gap(analysis_text)

    components = {
        "identity": _score_identity(text, lowered, lines),
        "education": _score_education(lowered),
        "experience": _score_experience(analysis_text, lowered),
        "skills": _score_skills(lowered, matched_skills),
        "structure": _score_structure(text, lowered, sections, words),
    }

    score = min(100, sum(component.score for component in components.values()))
    match_score = _match_score(score, skill_gap, semantic)
    strengths = _strengths(words, sections, matched_skills, components)
    weaknesses = _weaknesses(words, sections, matched_skills, missing_skills, components)
    suggestions = _suggestions(words, sections, missing_skills, components)
    ats_breakdown = {
        name: {
            "score": component.score,
            "max_score": component.max_score,
            "notes": component.notes,
        }
        for name, component in components.items()
    }
    recommendation_engine = build_recommendations(
        text=analysis_text,
        sections=sections,
        ats_breakdown=ats_breakdown,
        skill_gap=skill_gap,
        semantic=semantic,
        match_score=match_score,
    )

    return {
        "status": "completed",
        "phase": "recommendation_engine",
        "message": "Analisis CV lengkap selesai.",
        "language": normalized_cv.language,
        "bilingual": {
            "enabled": True,
            "strategy": "local_hybrid_normalization",
            "translation_provider": "none",
            "original_text_preserved": True,
            "canonical_sections": normalized_cv.canonical_sections,
            "canonical_skills": normalized_cv.canonical_skills,
            "canonical_roles": normalized_cv.canonical_roles,
            "entity_counts": normalized_cv.entity_counts,
            "notes": [
                "CV Inggris/Indonesia dibaca dengan normalisasi bilingual lokal.",
                "Teks asli tetap dipertahankan; sistem tidak memakai API translasi eksternal.",
            ],
        },
        "score": score,
        "match_score": match_score,
        "strengths": strengths,
        "weaknesses": weaknesses,
        "suggestions": suggestions,
        "matched_skills": sorted(matched_skills),
        "missing_skills": sorted(missing_skills),
        "skill_gap": skill_gap,
        "softskill_gap": softskill_gap,
        "job_recommendations": job_recommendations,
        "semantic_similarity": semantic,
        "recommendation_engine": recommendation_engine,
        "sections": sections,
        "word_count": words,
        "ats_breakdown": ats_breakdown,
        "ats_weights": {
            "identity": 18,
            "education": 16,
            "experience": 24,
            "skills": 22,
            "structure": 20,
        },
        "readiness": _readiness_label(score),
    }


def _section_hits(text: str) -> dict[str, bool]:
    return {
        section: bool(re.search(pattern, text, flags=re.IGNORECASE))
        for section, pattern in SECTION_PATTERNS.items()
    }


def _has_profile_intro(text: str) -> bool:
    first_block = " ".join(line.strip() for line in text.splitlines()[:8]).lower()
    if len(first_block.split()) < 25:
        return False
    return bool(
        re.search(
            r"\b(i am|i'm|saya|undergraduate|student|mahasiswa|passion|professional|developer|engineer|designer)\b",
            first_block,
        )
    )


def _matched_skills(text: str) -> set[str]:
    found: set[str] = set()
    for canonical, aliases in SKILL_ALIASES.items():
        if any(re.search(rf"\b{re.escape(alias)}\b", text) for alias in aliases):
            found.add(canonical)
    return found


def _infer_semantic_target(normalized_cv, matched_skills: set[str]) -> str:
    skills = {skill.lower() for skill in matched_skills}
    role_parts = [role.replace("_", " ") for role in normalized_cv.canonical_roles]

    if {"flutter", "mobile_development", "mobile", "android"} & skills:
        role_parts.append("mobile developer android flutter application development")
    if {"laravel", "php", "database", "api", "nodejs"} & skills:
        role_parts.append("backend developer laravel php mysql api database")
    if {"react", "javascript", "ui_ux"} & skills:
        role_parts.append("frontend developer react javascript ui ux interface")
    if {"python", "machine_learning", "data_analysis", "data"} & skills:
        role_parts.append("data analyst ai machine learning python analytics")
    if not role_parts:
        role_parts.append("entry level professional with communication teamwork project experience")

    readable_skills = " ".join(skill.replace("_", " ") for skill in sorted(skills))
    return (
        "Target pekerjaan otomatis berdasarkan CV: "
        + ". ".join(role_parts)
        + ". Skill relevan: "
        + readable_skills
    )


def _missing_skills(matched: set[str], job_description: str | None) -> set[str]:
    if not job_description:
        return set()

    job_text = job_description.lower()
    required = {
        canonical
        for canonical, aliases in SKILL_ALIASES.items()
        if any(re.search(rf"\b{re.escape(alias)}\b", job_text) for alias in aliases)
    }
    return required - matched


def _score_identity(text: str, lowered: str, lines: list[str]) -> ComponentScore:
    score = 0
    notes: list[str] = []

    if re.search(r"[\w.+-]+@[\w-]+\.[\w.-]+", text):
        score += 5
        notes.append("Email terdeteksi.")
    if re.search(r"\+?\d[\d\s().-]{7,}", text):
        score += 4
        notes.append("Nomor telepon terdeteksi.")
    if re.search(r"\b(linkedin|github|portfolio|behance|dribbble|gitlab)\b", lowered):
        score += 4
        notes.append("Link profesional/portofolio terdeteksi.")
    if lines and 2 <= len(lines[0].split()) <= 5 and not re.search(r"@|\d{4,}", lines[0]):
        score += 3
        notes.append("Nama kandidat kemungkinan terdeteksi di bagian atas.")
    if re.search(r"\b(jakarta|bandung|surabaya|yogyakarta|semarang|indonesia|remote)\b", lowered):
        score += 2
        notes.append("Lokasi atau preferensi kerja terdeteksi.")

    return ComponentScore(min(score, 18), 18, notes or ["Identitas kandidat masih minim."])


def _score_education(text: str) -> ComponentScore:
    score = 0
    notes: list[str] = []

    if re.search(SECTION_PATTERNS["education"], text):
        score += 5
        notes.append("Bagian pendidikan tersedia.")
    if re.search(r"\b(s1|s2|s3|d3|d4|sarjana|diploma|bachelor|master|degree)\b", text):
        score += 4
        notes.append("Level pendidikan terdeteksi.")
    if re.search(r"\b(universitas|university|institut|politeknik|kampus|sma|smk)\b", text):
        score += 4
        notes.append("Institusi pendidikan terdeteksi.")
    if re.search(r"\b(20\d{2}|19\d{2})\b", text):
        score += 3
        notes.append("Tahun pendidikan/kelulusan terdeteksi.")

    return ComponentScore(min(score, 16), 16, notes or ["Bagian pendidikan belum kuat."])


def _score_experience(text: str, lowered: str) -> ComponentScore:
    score = 0
    notes: list[str] = []

    if re.search(SECTION_PATTERNS["experience"], lowered):
        score += 6
        notes.append("Bagian pengalaman/proyek tersedia.")
    if re.search(r"\b(intern|magang|staff|developer|engineer|designer|manager|analyst|freelance|founder|owner)\b", lowered):
        score += 5
        notes.append("Role pekerjaan/proyek terdeteksi.")
    if re.search(r"\b(20\d{2}|19\d{2}|present|sekarang|jan|feb|mar|apr|mei|jun|jul|agu|sep|okt|nov|des)\b", lowered):
        score += 4
        notes.append("Timeline pengalaman terdeteksi.")
    if any(re.search(rf"\b{re.escape(verb)}\b", lowered) for verb in ACTION_VERBS):
        score += 4
        notes.append("Action verb terdeteksi pada pengalaman.")
    if re.search(r"\b\d+[%x]?|\brp\s?\d+|\b(user|client|customer|tim|team|project)\b", lowered):
        score += 5
        notes.append("Impact, angka, atau konteks hasil terdeteksi.")

    return ComponentScore(min(score, 24), 24, notes or ["Pengalaman/proyek belum terlihat kuat."])


def _score_skills(text: str, matched_skills: set[str]) -> ComponentScore:
    score = 0
    notes: list[str] = []

    if re.search(SECTION_PATTERNS["skills"], text):
        score += 6
        notes.append("Bagian skill tersedia.")
    if matched_skills:
        score += min(len(matched_skills) * 3, 10)
        notes.append(f"Skill kategori terdeteksi: {', '.join(sorted(matched_skills))}.")
    if len(matched_skills) >= 3:
        score += 3
        notes.append("Variasi skill cukup baik.")
    if re.search(r"\b(beginner|intermediate|advanced|expert|dasar|menengah|mahir|sertifikat|certified)\b", text):
        score += 3
        notes.append("Level skill atau sertifikasi terdeteksi.")

    return ComponentScore(min(score, 22), 22, notes or ["Skill belum terstruktur atau belum banyak terdeteksi."])


def _score_structure(text: str, lowered: str, sections: dict[str, bool], words: int) -> ComponentScore:
    score = 0
    notes: list[str] = []

    section_count = sum(1 for present in sections.values() if present)
    score += min(section_count * 3, 12)
    if section_count:
        notes.append(f"{section_count} bagian utama CV terdeteksi.")

    if 250 <= words <= 900:
        score += 4
        notes.append("Panjang CV ideal untuk analisis ATS.")
    elif 150 <= words < 250 or 900 < words <= 1200:
        score += 2
        notes.append("Panjang CV cukup, tetapi masih bisa dirapikan.")

    if re.search(r"(^|\n)\s*[-*•]", text) or re.search(r"\b(responsibilities|achievements|pencapaian|tanggung jawab)\b", lowered):
        score += 2
        notes.append("Format bullet/pencapaian terdeteksi.")
    if not re.search(r"\b(lorem ipsum|your name|nama anda|template)\b", lowered):
        score += 2
        notes.append("Tidak ada placeholder template yang jelas.")

    return ComponentScore(min(score, 20), 20, notes or ["Struktur CV belum cukup terbaca ATS."])


def _strengths(
    words: int,
    sections: dict[str, bool],
    matched_skills: set[str],
    components: dict[str, ComponentScore],
) -> list[str]:
    strengths: list[str] = []
    if components["identity"].score >= 10:
        strengths.append("Identitas dan kontak kandidat cukup jelas.")
    if components["experience"].score >= 14:
        strengths.append("Pengalaman atau proyek cukup kuat untuk dibaca ATS.")
    if sections.get("education"):
        strengths.append("Bagian pendidikan tersedia.")
    if matched_skills:
        strengths.append(f"Skill utama terdeteksi: {', '.join(sorted(matched_skills))}.")
    if words >= 250:
        strengths.append("Isi CV cukup lengkap untuk dianalisis.")
    return strengths or ["Teks CV berhasil diekstrak dan dapat dianalisis."]


def _weaknesses(
    words: int,
    sections: dict[str, bool],
    matched_skills: set[str],
    missing_skills: set[str],
    components: dict[str, ComponentScore],
) -> list[str]:
    weaknesses: list[str] = []
    weaknesses.extend(_ats_component_weaknesses(components, sections, words, matched_skills))

    if components["identity"].score < 10:
        weaknesses.append("Identitas/kontak belum lengkap.")
    if not sections.get("skills"):
        weaknesses.append("Bagian skill belum terstruktur jelas.")
    if components["experience"].score < 12:
        weaknesses.append("Pengalaman kerja/proyek belum cukup kuat.")
    if words < 180:
        weaknesses.append("Konten CV masih terlalu singkat.")
    if len(matched_skills) < 2:
        weaknesses.append("Skill yang terdeteksi masih sedikit.")
    if missing_skills:
        weaknesses.append("Ada skill target pekerjaan yang belum muncul di CV.")
    unique_weaknesses = list(dict.fromkeys(weaknesses))
    return unique_weaknesses or ["Tidak ada kelemahan besar dari struktur dasar CV."]


def _ats_component_weaknesses(
    components: dict[str, ComponentScore],
    sections: dict[str, bool],
    words: int,
    matched_skills: set[str],
) -> list[str]:
    weaknesses: list[str] = []

    if components["identity"].score < components["identity"].max_score:
        weaknesses.append(
            f"Identity ({components['identity'].score}/{components['identity'].max_score}): "
            "tambahkan atau pastikan nama, email, nomor telepon, lokasi, dan link profesional terbaca jelas."
        )

    if components["education"].score < components["education"].max_score:
        weaknesses.append(
            f"Education ({components['education'].score}/{components['education'].max_score}): "
            "lengkapi institusi, jurusan/level pendidikan, dan tahun masuk atau kelulusan."
        )

    if components["experience"].score < components["experience"].max_score:
        weaknesses.append(
            f"Experience ({components['experience'].score}/{components['experience'].max_score}): "
            "perjelas role, timeline, action verb, teknologi yang dipakai, serta impact terukur."
        )

    if components["skills"].score < components["skills"].max_score:
        skill_hint = "tambahkan level skill/sertifikasi" if len(matched_skills) >= 3 else "tambahkan skill teknis yang relevan"
        weaknesses.append(
            f"Skills ({components['skills'].score}/{components['skills'].max_score}): "
            f"buat bagian skill lebih terstruktur dan {skill_hint}."
        )

    if components["structure"].score < components["structure"].max_score:
        length_hint = "panjang CV belum ideal" if words < 250 or words > 900 else "struktur masih bisa dibuat lebih ATS-friendly"
        missing_sections = [name for name, present in sections.items() if not present]
        section_hint = (
            f" Bagian yang belum jelas: {', '.join(missing_sections)}."
            if missing_sections
            else ""
        )
        weaknesses.append(
            f"Structure ({components['structure'].score}/{components['structure'].max_score}): "
            f"{length_hint}; gunakan heading konsisten dan bullet achievement.{section_hint}"
        )

    return weaknesses


def _suggestions(
    words: int,
    sections: dict[str, bool],
    missing_skills: set[str],
    components: dict[str, ComponentScore],
) -> list[str]:
    suggestions: list[str] = []
    if not sections.get("profile"):
        suggestions.append("Tambahkan ringkasan profil singkat di bagian atas CV.")
    if components["identity"].score < 10:
        suggestions.append("Lengkapi email, nomor telepon, lokasi, dan link LinkedIn/GitHub/portofolio.")
    if not sections.get("skills"):
        suggestions.append("Buat bagian skill yang jelas dan mudah dibaca ATS.")
    if components["experience"].score < 16:
        suggestions.append("Tambahkan pengalaman, proyek, action verb, dan metrik hasil.")
    if words < 250:
        suggestions.append("Perkaya isi CV dengan detail tanggung jawab dan impact.")
    if missing_skills:
        suggestions.append(f"Tambahkan skill yang relevan dengan target kerja: {', '.join(sorted(missing_skills))}.")
    return suggestions or ["Pertahankan struktur CV dan sesuaikan kata kunci dengan posisi target."]


def _readiness_label(score: int) -> str:
    if score >= 85:
        return "strong"
    if score >= 70:
        return "ready"
    if score >= 50:
        return "needs_improvement"
    return "weak"


def _match_score(ats_score: int, skill_gap: dict, semantic: dict) -> int:
    skill_rate = skill_gap.get("match_rate")
    semantic_score = semantic.get("score")

    if semantic_score is None:
        return round((ats_score * 0.65) + (skill_rate * 0.35))

    return round((ats_score * 0.35) + (skill_rate * 0.30) + (semantic_score * 0.35))


def keywords(text: str, limit: int = 12) -> list[str]:
    tokens = re.findall(r"\b[a-zA-Z][a-zA-Z+#.-]{2,}\b", text.lower())
    ignored = {"dan", "yang", "with", "the", "untuk", "dengan", "from", "this", "that", "email"}
    counts = Counter(token for token in tokens if token not in ignored)
    return [word for word, _ in counts.most_common(limit)]
