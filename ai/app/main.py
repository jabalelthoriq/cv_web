import os
import tempfile
from pathlib import Path

from fastapi import FastAPI, File, HTTPException, Request, UploadFile

os.environ.setdefault("HF_HUB_DISABLE_XET", "1")

from app.analyzer import analyze_cv, keywords
from app.extractor import ExtractionError, extract_from_path, extract_from_upload
from app.file_resolver import FileResolutionError, resolve_storage_path
from app.schemas import AnalyzeRequest, AnalyzeResponse, ExtractTextRequest, ExtractTextResponse
from app.semantic import DEFAULT_MODEL, USE_TRANSFORMER


STORAGE_ROOT = Path(os.getenv("STORAGE_ROOT", "/var/www/html/public/storage"))
WHISPER_MODEL = os.getenv("WHISPER_MODEL", "base")
WHISPER_CACHE_DIR = os.getenv("WHISPER_CACHE_DIR", "/app/models/whisper")
_whisper_model = None

app = FastAPI(
    title="CareerSense AI Service",
    description="FastAPI service for CV text extraction and analysis. Stateless: no MySQL access.",
    version="0.1.0",
)


@app.get("/health")
def health() -> dict:
    return {
        "status": "ok",
        "service": "careersense-ai",
        "phase": "recommendation-engine",
        "semantic": {
            "use_sentence_transformer": USE_TRANSFORMER,
            "model": DEFAULT_MODEL,
        },
        "storage_root": str(STORAGE_ROOT),
        "stt": {
            "engine": "faster-whisper",
            "model": WHISPER_MODEL,
        },
    }


def get_whisper_model():
    global _whisper_model
    if _whisper_model is None:
        from faster_whisper import WhisperModel

        _whisper_model = WhisperModel(
            WHISPER_MODEL,
            device="cpu",
            compute_type="int8",
            download_root=WHISPER_CACHE_DIR,
        )
    return _whisper_model


@app.post("/extract-text", response_model=ExtractTextResponse)
async def extract_text(request: Request):
    try:
        content_type = request.headers.get("content-type", "")

        if content_type.startswith("multipart/form-data"):
            form = await request.form()
            file = form.get("file")
            if not isinstance(file, UploadFile):
                raise HTTPException(status_code=422, detail="field multipart file wajib bernama 'file'")
            extraction = await extract_from_upload(file)

        else:
            payload = ExtractTextRequest.model_validate(await request.json())
            path = resolve_storage_path(payload.file_path, STORAGE_ROOT)
            extraction = extract_from_path(path)

        return ExtractTextResponse(status="success", **extraction)
    except (ExtractionError, FileResolutionError) as exc:
        raise HTTPException(status_code=422, detail=str(exc)) from exc


@app.post("/analyze", response_model=AnalyzeResponse)
def analyze(payload: AnalyzeRequest):
    try:
        path = resolve_storage_path(payload.file_path, STORAGE_ROOT)
        extraction = extract_from_path(path)
        analysis = analyze_cv(extraction["text"], payload.job_description)
        analysis["keywords"] = keywords(extraction["text"])

        return AnalyzeResponse(
            status="success",
            cv_id=payload.cv_id,
            score=analysis["score"],
            analysis=analysis,
            extracted_text=extraction["text"],
            extraction={
                "source": extraction["source"],
                "file_type": extraction["file_type"],
                "extraction_method": extraction["extraction_method"],
                "character_count": extraction["character_count"],
                "word_count": extraction["word_count"],
                "warnings": extraction["warnings"],
            },
        )
    except (ExtractionError, FileResolutionError) as exc:
        raise HTTPException(status_code=422, detail=str(exc)) from exc


@app.post("/transcribe-audio")
async def transcribe_audio(file: UploadFile = File(...)) -> dict:
    if not file.content_type or not file.content_type.startswith("audio/"):
        raise HTTPException(status_code=422, detail="File audio tidak valid")

    suffix = Path(file.filename or "interview.webm").suffix or ".webm"

    try:
        with tempfile.NamedTemporaryFile(delete=False, suffix=suffix) as tmp:
            tmp.write(await file.read())
            tmp_path = Path(tmp.name)

        model = get_whisper_model()
        segments, info = model.transcribe(
            str(tmp_path),
            language="id",
            beam_size=5,
            vad_filter=True,
            condition_on_previous_text=False,
        )
        transcript = " ".join(segment.text.strip() for segment in segments).strip()

        return {
            "status": "success",
            "text": transcript,
            "language": info.language,
            "language_probability": info.language_probability,
        }
    except Exception as exc:
        raise HTTPException(status_code=500, detail=f"Gagal transkripsi audio: {exc}") from exc
    finally:
        if "tmp_path" in locals():
            tmp_path.unlink(missing_ok=True)
