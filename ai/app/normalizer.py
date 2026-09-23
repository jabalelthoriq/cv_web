import re
from dataclasses import dataclass

from app.bilingual_terms import BILINGUAL_SKILL_ALIASES, ROLE_ALIASES, SECTION_ALIASES
from app.language import detect_language


@dataclass(frozen=True)
class BilingualCvNormalization:
    original_text: str
    analysis_text: str
    language: dict
    canonical_sections: dict[str, bool]
    canonical_skills: list[str]
    canonical_roles: list[str]
    entity_counts: dict[str, int]


def normalize_bilingual_cv(text: str) -> BilingualCvNormalization:
    language = detect_language(text)
    sections = _detect_aliases(text, SECTION_ALIASES)
    skills = _detect_aliases(text, BILINGUAL_SKILL_ALIASES)
    roles = _detect_aliases(text, ROLE_ALIASES)
    entity_counts = protect_entities(text)
    analysis_text = append_normalized_signals(text, sections, skills, roles)

    return BilingualCvNormalization(
        original_text=text,
        analysis_text=analysis_text,
        language=language,
        canonical_sections={section: section in sections for section in SECTION_ALIASES},
        canonical_skills=sorted(skills),
        canonical_roles=sorted(roles),
        entity_counts=entity_counts,
    )


def protect_entities(text: str) -> dict[str, int]:
    return {
        "emails": len(re.findall(r"[\w.+-]+@[\w-]+\.[\w.-]+", text)),
        "phones": len(re.findall(r"\+?\d[\d\s().-]{7,}", text)),
        "urls": len(re.findall(r"https?://\S+|(?:linkedin|github|gitlab)\.com/\S+", text, flags=re.IGNORECASE)),
        "years": len(re.findall(r"\b(?:19|20)\d{2}\b", text)),
        "percentages": len(re.findall(r"\b\d+(?:\.\d+)?\s?%", text)),
    }


def append_normalized_signals(
    text: str,
    sections: set[str],
    skills: set[str],
    roles: set[str],
) -> str:
    signals: list[str] = []

    if sections:
        signals.append("Normalized sections: " + ", ".join(sorted(sections)))
    if skills:
        readable_skills = [skill.replace("_", " ") for skill in sorted(skills)]
        signals.append("Normalized bilingual skills: " + ", ".join(readable_skills))
    if roles:
        readable_roles = [role.replace("_", " ") for role in sorted(roles)]
        signals.append("Normalized bilingual roles: " + ", ".join(readable_roles))

    if not signals:
        return text

    return text.rstrip() + "\n\n[BILINGUAL_NORMALIZATION]\n" + "\n".join(signals)


def _detect_aliases(text: str, catalog: dict[str, set[str]]) -> set[str]:
    normalized = _normalize(text)
    found: set[str] = set()

    for canonical, aliases in catalog.items():
        if any(_contains_alias(normalized, alias) for alias in aliases):
            found.add(canonical)

    return found


def _contains_alias(text: str, alias: str) -> bool:
    alias = _normalize(alias)
    if not alias:
        return False
    if re.search(r"[^a-z0-9 ]", alias) or " " in alias:
        return alias in text
    return bool(re.search(rf"(?<![a-z0-9+#./-]){re.escape(alias)}(?![a-z0-9+#./-])", text))


def _normalize(text: str) -> str:
    return re.sub(r"\s+", " ", text.lower()).strip()
