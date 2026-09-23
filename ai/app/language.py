import re


INDONESIAN_SIGNALS = {
    "adalah",
    "atau",
    "dan",
    "dengan",
    "di",
    "ke",
    "kemampuan",
    "kerja",
    "membuat",
    "mengembangkan",
    "mengelola",
    "meningkatkan",
    "pengalaman",
    "pendidikan",
    "profil",
    "proyek",
    "saya",
    "sebagai",
    "sertifikasi",
    "untuk",
    "yang",
}

ENGLISH_SIGNALS = {
    "about",
    "and",
    "built",
    "created",
    "developed",
    "education",
    "experience",
    "for",
    "from",
    "implemented",
    "improved",
    "in",
    "managed",
    "of",
    "profile",
    "project",
    "skills",
    "summary",
    "the",
    "to",
    "with",
}


def detect_language(text: str) -> dict:
    paragraphs = detect_paragraph_languages(text)
    id_score = sum(item["scores"]["id"] for item in paragraphs)
    en_score = sum(item["scores"]["en"] for item in paragraphs)
    total = id_score + en_score

    if total == 0:
        language = "unknown"
        confidence = 0.0
    elif id_score and en_score and min(id_score, en_score) / max(id_score, en_score) >= 0.35:
        language = "mixed"
        confidence = round(max(id_score, en_score) / total, 2)
    elif en_score > id_score:
        language = "en"
        confidence = round(en_score / total, 2)
    else:
        language = "id"
        confidence = round(id_score / total, 2)

    return {
        "document_language": language,
        "confidence": confidence,
        "scores": {"id": id_score, "en": en_score},
        "paragraphs": paragraphs,
    }


def detect_paragraph_languages(text: str) -> list[dict]:
    chunks = [chunk.strip() for chunk in re.split(r"\n\s*\n|\r\n\s*\r\n", text) if chunk.strip()]
    if not chunks and text.strip():
        chunks = [text.strip()]

    return [_score_chunk(index, chunk) for index, chunk in enumerate(chunks)]


def _score_chunk(index: int, chunk: str) -> dict:
    tokens = re.findall(r"\b[a-zA-Z][a-zA-Z'-]*\b", chunk.lower())
    id_score = sum(1 for token in tokens if token in INDONESIAN_SIGNALS)
    en_score = sum(1 for token in tokens if token in ENGLISH_SIGNALS)
    total = id_score + en_score

    if total == 0:
        language = "unknown"
        confidence = 0.0
    elif id_score and en_score and min(id_score, en_score) / max(id_score, en_score) >= 0.4:
        language = "mixed"
        confidence = round(max(id_score, en_score) / total, 2)
    elif en_score > id_score:
        language = "en"
        confidence = round(en_score / total, 2)
    else:
        language = "id"
        confidence = round(id_score / total, 2)

    return {
        "index": index,
        "language": language,
        "confidence": confidence,
        "scores": {"id": id_score, "en": en_score},
    }
