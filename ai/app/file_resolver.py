from pathlib import Path


ALLOWED_EXTENSIONS = {".pdf", ".docx", ".png", ".jpg", ".jpeg", ".webp", ".tif", ".tiff"}


class FileResolutionError(ValueError):
    pass


def resolve_storage_path(file_path: str, storage_root: Path) -> Path:
    if not file_path:
        raise FileResolutionError("file_path wajib diisi")

    raw_path = Path(file_path)
    if raw_path.is_absolute():
        candidate = raw_path.resolve()
    else:
        cleaned = str(file_path).lstrip("/")
        changed = True
        while changed:
            changed = False
            for prefix in ("public/storage/", "storage/public/", "storage/", "public/"):
                if cleaned.startswith(prefix):
                    cleaned = cleaned[len(prefix):]
                    changed = True
        candidate = (storage_root / cleaned).resolve()

    root = storage_root.resolve()
    try:
        candidate.relative_to(root)
    except ValueError:
        raise FileResolutionError("file_path berada di luar storage yang diizinkan")

    if not candidate.exists() or not candidate.is_file():
        raise FileResolutionError(f"file tidak ditemukan: {file_path}")

    if candidate.suffix.lower() not in ALLOWED_EXTENSIONS:
        raise FileResolutionError(f"format file tidak didukung: {candidate.suffix}")

    return candidate
