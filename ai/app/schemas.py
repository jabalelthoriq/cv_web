from typing import Any

from pydantic import BaseModel, Field


class AnalyzeRequest(BaseModel):
    cv_id: int | None = None
    file_path: str
    job_description: str | None = None


class ExtractTextRequest(BaseModel):
    file_path: str


class ExtractTextResponse(BaseModel):
    status: str = "success"
    source: str
    file_type: str
    extraction_method: str
    text: str
    character_count: int
    word_count: int
    warnings: list[str] = Field(default_factory=list)


class AnalyzeResponse(BaseModel):
    status: str = "success"
    cv_id: int | None = None
    score: int
    analysis: dict[str, Any]
    extracted_text: str
    extraction: dict[str, Any]
