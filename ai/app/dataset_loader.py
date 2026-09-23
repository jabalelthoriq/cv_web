"""
Dataset Loader
===============
Membaca dan meng-cache dataset pekerjaan IT dan softskill IT dari file XLSX.
Dataset dimuat sekali saat pertama kali diakses (lazy singleton) dan disimpan
di memori agar tidak perlu membaca ulang file setiap request.
"""

import os
from functools import lru_cache
from pathlib import Path

import openpyxl


DATASET_DIR = Path(os.getenv("DATASET_DIR", str(Path(__file__).resolve().parent.parent / "dataset")))


@lru_cache(maxsize=1)
def load_job_dataset() -> list[dict]:
    """Membaca dataset pekerjaan IT Indonesia dari XLSX.

    Setiap entry berisi:
        no, kategori, nama_pekerjaan, deskripsi_pekerjaan,
        skill_teknis, skill_non_teknis, pendidikan_minimal
    """
    path = DATASET_DIR / "dataset_pekerjaan_IT_indonesia (1).xlsx"
    return _read_xlsx(
        path,
        column_map={
            0: "no",
            1: "kategori",
            2: "nama_pekerjaan",
            3: "deskripsi_pekerjaan",
            4: "skill_teknis",
            5: "skill_non_teknis",
            6: "pendidikan_minimal",
        },
    )


@lru_cache(maxsize=1)
def load_softskill_dataset() -> list[dict]:
    """Membaca dataset softskill IT dari XLSX.

    Setiap entry berisi:
        no, kategori_softskill, nama_softskill, deskripsi,
        contoh_penerapan, tingkat_kepentingan
    """
    path = DATASET_DIR / "dataset_softskill_it.xlsx"
    return _read_xlsx(
        path,
        column_map={
            0: "no",
            1: "kategori_softskill",
            2: "nama_softskill",
            3: "deskripsi",
            4: "contoh_penerapan",
            5: "tingkat_kepentingan",
        },
    )


def _read_xlsx(path: Path, column_map: dict[int, str]) -> list[dict]:
    """Utilitas generik untuk membaca XLSX dan mengembalikan list[dict]."""
    if not path.exists():
        print(f"[WARNING] Dataset tidak ditemukan di jalur: {path}")
        return []

    try:
        wb = openpyxl.load_workbook(path, read_only=True, data_only=True)
    except Exception as exc:
        print(f"[WARNING] Gagal membuka dataset {path}: {exc}")
        return []
    ws = wb.active
    rows: list[dict] = []

    for row_idx, row in enumerate(ws.iter_rows(min_row=2, values_only=True)):
        entry: dict = {}
        for col_idx, field_name in column_map.items():
            value = row[col_idx] if col_idx < len(row) else None
            entry[field_name] = str(value).strip() if value is not None else ""
        rows.append(entry)

    wb.close()
    return rows
