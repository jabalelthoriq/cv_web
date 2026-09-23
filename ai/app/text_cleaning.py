import re


def clean_text(text: str) -> str:
    text = text.replace("\x00", " ")
    text = text.replace("\r\n", "\n").replace("\r", "\n")
    text = "\n".join(_repair_spaced_pdf_line(line) for line in text.splitlines())
    text = re.sub(r"[ \t]+", " ", text)
    text = re.sub(r"\n{3,}", "\n\n", text)
    text = "\n".join(line.strip() for line in text.splitlines())
    return text.strip()


def word_count(text: str) -> int:
    return len(re.findall(r"\b[\w+#.-]+\b", text, flags=re.UNICODE))


def _repair_spaced_pdf_line(line: str) -> str:
    if not line.strip():
        return line

    tokens = re.findall(r"[A-Za-z0-9+#./-]+", line)
    single_token_count = sum(1 for token in tokens if len(token) == 1)
    if len(tokens) < 6 or single_token_count / max(len(tokens), 1) < 0.65:
        return line

    parts = re.split(r" {2,}", line.strip())
    repaired: list[str] = []
    for part in parts:
        part_tokens = re.findall(r"[A-Za-z0-9+#./-]+", part)
        part_single_count = sum(1 for token in part_tokens if len(token) == 1)
        if part_tokens and part_single_count / len(part_tokens) >= 0.65:
            repaired.append(re.sub(r"\s+", "", part))
        else:
            repaired.append(part.strip())

    return " ".join(item for item in repaired if item)
