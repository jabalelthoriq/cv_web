import re
from dataclasses import dataclass


SKILL_CATALOG = {
    "backend": {
        "python": {"python", "py", "fastapi", "django", "flask"},
        "php": {"php", "laravel", "codeigniter", "symfony"},
        "javascript": {"javascript", "js", "typescript", "ts", "node", "nodejs", "express", "nest"},
        "database": {"mysql", "postgres", "postgresql", "mongodb", "sql", "database", "redis"},
        "api": {"api", "rest", "restful", "graphql", "webhook"},
        "testing": {"testing", "test", "unit test", "phpunit", "pytest", "jest"},
    },
    "frontend": {
        "react": {"react", "next", "nextjs", "vite"},
        "vue": {"vue", "nuxt", "nuxtjs"},
        "html_css": {"html", "css", "tailwind", "bootstrap", "sass"},
        "ui_state": {"redux", "pinia", "zustand", "state management"},
    },
    "devops": {
        "docker": {"docker", "container", "compose", "docker compose"},
        "cloud": {"aws", "gcp", "azure", "cloud", "ec2", "s3"},
        "linux": {"linux", "ubuntu", "nginx", "apache", "server"},
        "ci_cd": {"ci/cd", "cicd", "github actions", "gitlab ci", "pipeline"},
        "kubernetes": {"kubernetes", "k8s"},
    },
    "data": {
        "excel": {"excel", "spreadsheet", "google sheets"},
        "analytics": {"analytics", "data analysis", "analisis data", "tableau", "power bi"},
        "pandas": {"pandas", "numpy", "matplotlib", "scikit"},
    },
    "design": {
        "figma": {"figma"},
        "ui_ux": {"ui", "ux", "wireframe", "prototype", "user research"},
        "visual_design": {"photoshop", "illustrator", "canva", "design grafis"},
    },
    "mobile": {
        "android": {"android", "kotlin", "java android"},
        "ios": {"ios", "swift"},
        "flutter": {"flutter", "dart"},
        "react_native": {"react native", "expo"},
    },
    "marketing": {
        "seo": {"seo", "search engine optimization"},
        "copywriting": {"copywriting", "content writing", "artikel", "blog"},
        "ads": {"google ads", "meta ads", "facebook ads", "campaign", "ads"},
        "social_media": {"social media", "instagram", "tiktok", "linkedin content"},
    },
}


@dataclass(frozen=True)
class SkillHit:
    skill: str
    category: str
    aliases: list[str]


def analyze_skill_gap(cv_text: str, job_description: str | None = None) -> dict:
    cv_hits = _detect_skills(cv_text)
    job_hits = _detect_skills(job_description or "")
    matched = sorted(set(cv_hits) & set(job_hits)) if job_hits else sorted(cv_hits)
    missing = sorted(set(job_hits) - set(cv_hits)) if job_hits else []
    extra = sorted(set(cv_hits) - set(job_hits)) if job_hits else sorted(cv_hits)

    match_rate = 100
    if job_hits:
        match_rate = round((len(matched) / max(len(job_hits), 1)) * 100)

    # Deteksi skill teknis berbasis semantic dari dataset jika job_description mengandung skill teknis dataset
    semantic_technical_gap = _analyze_semantic_technical_skills(cv_text, job_description)

    return {
        "match_rate": match_rate,
        "matched_skills": matched,
        "missing_skills": missing,
        "extra_skills": extra,
        "cv_skills": _serialize_hits(cv_hits),
        "job_required_skills": _serialize_hits(job_hits),
        "semantic_technical_gap": semantic_technical_gap,
        "recommendations": _recommendations(missing),
    }


def _analyze_semantic_technical_skills(cv_text: str, job_description: str | None) -> dict:
    """Mencocokkan skill teknis dari CV terhadap deskripsi/persyaratan skill teknis target."""
    if not cv_text.strip():
        return {"score": 0, "matched_technical_terms": [], "missing_technical_terms": []}

    from app.semantic import _token_cosine_similarity
    if job_description:
        score = _token_cosine_similarity(cv_text, job_description)
    else:
        score = 100

    return {
        "similarity_score": score,
        "status": "evaluated"
    }



def _detect_skills(text: str) -> dict[str, SkillHit]:
    hits: dict[str, SkillHit] = {}
    normalized = _normalize(text)
    if not normalized:
        return hits

    for category, skills in SKILL_CATALOG.items():
        for skill, aliases in skills.items():
            matched_aliases = sorted(alias for alias in aliases if _contains_alias(normalized, alias))
            if matched_aliases:
                hits[skill] = SkillHit(skill=skill, category=category, aliases=matched_aliases)

    return hits


def _contains_alias(text: str, alias: str) -> bool:
    alias = _normalize(alias)
    if " " in alias or "/" in alias:
        return alias in text
    return bool(re.search(rf"(?<![a-z0-9+#.-]){re.escape(alias)}(?![a-z0-9+#.-])", text))


def _normalize(text: str) -> str:
    return re.sub(r"\s+", " ", text.lower()).strip()


def _serialize_hits(hits: dict[str, SkillHit]) -> list[dict]:
    return [
        {
            "skill": hit.skill,
            "category": hit.category,
            "aliases": hit.aliases,
        }
        for hit in sorted(hits.values(), key=lambda item: (item.category, item.skill))
    ]


def _recommendations(missing: list[str]) -> list[str]:
    if not missing:
        return ["Skill utama pada target pekerjaan sudah muncul di CV."]
    return [
        f"Tambahkan bukti pengalaman atau proyek terkait skill: {skill.replace('_', ' ')}."
        for skill in missing[:6]
    ]
