from app.text_cleaning import word_count


def build_recommendations(
    text: str,
    sections: dict[str, bool],
    ats_breakdown: dict,
    skill_gap: dict,
    semantic: dict,
    match_score: int,
) -> dict:
    ats = _ats_recommendations(sections, ats_breakdown, text)
    skills = _skill_recommendations(skill_gap)
    improvements = _improvement_recommendations(text, semantic, match_score)
    priority = _priority_actions(ats, skills, improvements)

    return {
        "summary": _summary(match_score, semantic, skill_gap),
        "ats_recommendations": ats,
        "skill_recommendations": skills,
        "cv_improvement_suggestions": improvements,
        "priority_actions": priority,
    }


def _ats_recommendations(sections: dict[str, bool], ats_breakdown: dict, text: str) -> list[dict]:
    items: list[dict] = []
    words = word_count(text)

    if not sections.get("profile"):
        items.append(_item("high", "Tambahkan ringkasan profil", "Buat 2-3 kalimat ringkas tentang role target, pengalaman utama, dan value yang ditawarkan."))
    if not sections.get("contact") or ats_breakdown.get("identity", {}).get("score", 0) < 12:
        items.append(_item("high", "Lengkapi identitas dan kontak", "Pastikan CV memiliki email, nomor telepon, lokasi, serta link LinkedIn/GitHub/portofolio."))
    if ats_breakdown.get("experience", {}).get("score", 0) < 16:
        items.append(_item("high", "Perkuat pengalaman/proyek", "Tambahkan role, timeline, action verb, teknologi yang dipakai, dan dampak pekerjaan."))
    if ats_breakdown.get("skills", {}).get("score", 0) < 16:
        items.append(_item("medium", "Rapikan bagian skill", "Pisahkan skill teknis berdasarkan kategori seperti backend, database, devops, frontend, atau tools."))
    if words < 250:
        items.append(_item("medium", "Perkaya isi CV", "Tambahkan detail tanggung jawab, pencapaian, dan metrik agar ATS punya konteks lebih kuat."))
    if not items:
        items.append(_item("low", "Pertahankan struktur CV", "Struktur dasar CV sudah cukup baik. Sesuaikan kata kunci untuk setiap lowongan."))

    return items


def _skill_recommendations(skill_gap: dict) -> list[dict]:
    missing = skill_gap.get("missing_skills", [])
    required = skill_gap.get("job_required_skills", [])

    if not missing:
        return [_item("low", "Skill utama sudah sesuai", "Skill utama yang diminta job description sudah muncul di CV.")]

    category_map = {
        item["skill"]: item.get("category", "general")
        for item in required
        if isinstance(item, dict)
    }
    return [
        _item(
            "high" if index < 3 else "medium",
            f"Tambahkan bukti skill {skill.replace('_', ' ')}",
            f"Masukkan proyek, pengalaman, atau pencapaian yang menunjukkan skill {skill.replace('_', ' ')} dalam kategori {category_map.get(skill, 'general')}.",
        )
        for index, skill in enumerate(missing[:8])
    ]


def _improvement_recommendations(text: str, semantic: dict, match_score: int) -> list[dict]:
    items: list[dict] = []
    semantic_score = semantic.get("score")

    if semantic_score is not None and semantic_score < 55:
        items.append(_item("high", "Samakan konteks CV dengan job description", "Gunakan istilah role, tools, dan tanggung jawab yang relevan dengan lowongan target."))
    if match_score < 70:
        items.append(_item("high", "Prioritaskan kata kunci lowongan", "Tambahkan kata kunci penting dari job description ke ringkasan, skill, dan pengalaman."))
    if not _has_metric(text):
        items.append(_item("medium", "Tambahkan metrik pencapaian", "Gunakan angka seperti persentase peningkatan, jumlah user, jumlah proyek, atau waktu efisiensi."))
    if not _has_action_verb(text):
        items.append(_item("medium", "Gunakan action verb", "Mulai bullet pengalaman dengan kata seperti membangun, mengembangkan, mengoptimalkan, memimpin, atau meningkatkan."))
    if not items:
        items.append(_item("low", "Optimasi per lowongan", "CV sudah cukup relevan. Buat variasi CV untuk tiap role agar match score lebih tinggi."))

    return items


def _priority_actions(*groups: list[dict]) -> list[str]:
    actions: list[str] = []
    for group in groups:
        for item in group:
            if item["priority"] == "high":
                actions.append(item["title"])
            if len(actions) == 5:
                return actions

    for group in groups:
        for item in group:
            if item["title"] not in actions:
                actions.append(item["title"])
            if len(actions) == 5:
                return actions
    return actions


def _summary(match_score: int, semantic: dict, skill_gap: dict) -> str:
    missing = skill_gap.get("missing_skills", [])
    semantic_label = semantic.get("label", "not_available")
    if match_score >= 85 and not missing:
        return "CV sudah sangat sesuai dengan target lowongan. Fokus berikutnya adalah polishing bahasa dan bukti impact."
    if match_score >= 70:
        return f"CV sudah cukup relevan. Tingkat semantic saat ini {semantic_label}; perbaikan utama ada pada detail pengalaman dan kata kunci."
    return "CV masih perlu disesuaikan dengan lowongan. Prioritaskan skill yang hilang, kata kunci job description, dan struktur pengalaman."


def _item(priority: str, title: str, detail: str) -> dict:
    return {
        "priority": priority,
        "title": title,
        "detail": detail,
    }


def _has_metric(text: str) -> bool:
    return any(char.isdigit() for char in text)


def _has_action_verb(text: str) -> bool:
    lowered = text.lower()
    verbs = {
        "built",
        "created",
        "developed",
        "improved",
        "optimized",
        "implemented",
        "membangun",
        "membuat",
        "mengembangkan",
        "meningkatkan",
        "mengoptimalkan",
        "mengimplementasikan",
    }
    return any(verb in lowered for verb in verbs)
