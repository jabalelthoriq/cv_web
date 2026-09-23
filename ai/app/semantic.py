import math
import os
import re
from collections import Counter
from functools import lru_cache
from typing import Iterable


DEFAULT_MODEL = os.getenv("SEMANTIC_MODEL", "denaya/indoSBERT-large")
USE_TRANSFORMER = os.getenv("USE_SENTENCE_TRANSFORMER", "false").lower() in {"1", "true", "yes"}

STOPWORDS = {
    "a",
    "an",
    "and",
    "atau",
    "dan",
    "dengan",
    "di",
    "for",
    "from",
    "in",
    "ini",
    "is",
    "ke",
    "of",
    "on",
    "or",
    "the",
    "to",
    "untuk",
    "with",
    "yang",
}


def semantic_similarity(cv_text: str, job_description: str | None = None) -> dict:
    if not job_description or not job_description.strip():
        return {
            "enabled": False,
            "method": "none",
            "score": None,
            "label": "not_available",
            "notes": ["Job description belum diberikan, semantic similarity dilewati."],
        }

    transformer_result = _try_transformer_similarity(cv_text, job_description)
    if transformer_result is not None:
        return transformer_result

    score = _token_cosine_similarity(cv_text, job_description)
    return {
        "enabled": True,
        "method": "token_cosine_fallback",
        "score": score,
        "label": _label(score),
        "notes": [
            "Menggunakan fallback cosine berbasis token.",
            "Aktifkan USE_SENTENCE_TRANSFORMER=true dan SEMANTIC_MODEL untuk memakai model embedding.",
        ],
    }


def _try_transformer_similarity(cv_text: str, job_description: str) -> dict | None:
    if not USE_TRANSFORMER:
        return None

    try:
        model = _load_model()
        embeddings = model.encode([cv_text, job_description], normalize_embeddings=True)
        score = round(float(sum(a * b for a, b in zip(embeddings[0], embeddings[1]))) * 100)
        return {
            "enabled": True,
            "method": "sentence_transformer",
            "model": DEFAULT_MODEL,
            "score": max(0, min(100, score)),
            "label": _label(score),
            "notes": ["Semantic similarity dihitung memakai SentenceTransformer."],
        }
    except Exception as exc:
        return {
            "enabled": True,
            "method": "token_cosine_fallback",
            "score": _token_cosine_similarity(cv_text, job_description),
            "label": "fallback",
            "notes": [
                "SentenceTransformer gagal dimuat, fallback token cosine digunakan.",
                str(exc),
            ],
        }


@lru_cache(maxsize=1)
def _load_model():
    from sentence_transformers import SentenceTransformer

    return SentenceTransformer(DEFAULT_MODEL)


def _token_cosine_similarity(left: str, right: str) -> int:
    left_vector = _vectorize(_tokens(left))
    right_vector = _vectorize(_tokens(right))
    if not left_vector or not right_vector:
        return 0

    common = set(left_vector) & set(right_vector)
    dot = sum(left_vector[token] * right_vector[token] for token in common)
    left_norm = math.sqrt(sum(value * value for value in left_vector.values()))
    right_norm = math.sqrt(sum(value * value for value in right_vector.values()))
    if left_norm == 0 or right_norm == 0:
        return 0

    return round((dot / (left_norm * right_norm)) * 100)


def _tokens(text: str) -> list[str]:
    tokens = re.findall(r"\b[a-zA-Z][a-zA-Z0-9+#./-]{1,}\b", text.lower())
    return [token for token in tokens if token not in STOPWORDS]


def _vectorize(tokens: Iterable[str]) -> Counter:
    return Counter(tokens)


def _label(score: int | float | None) -> str:
    if score is None:
        return "not_available"
    if score >= 80:
        return "high"
    if score >= 55:
        return "medium"
    return "low"
