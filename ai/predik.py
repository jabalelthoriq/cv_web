from transformers import BertTokenizer, BertForSequenceClassification
import fitz  # PyMuPDF
import torch
import joblib
import torch.nn.functional as F
import re
import os
import pytesseract
from pdf2image import convert_from_path

# =========================
# LOAD MODEL
# =========================
MODEL_DIR = "model"
model = BertForSequenceClassification.from_pretrained(MODEL_DIR)
tokenizer = BertTokenizer.from_pretrained(MODEL_DIR)
label_encoder = joblib.load(f"{MODEL_DIR}/label_encoder.pkl")

model.eval()

# =========================
# FUNGSI EXTRACTION (WITH OCR FALLBACK)
# =========================
def extract_text_comprehensive(file_path):
    text = ""
    # 1. Coba ekstraksi teks standar (PyMuPDF)
    try:
        doc = fitz.open(file_path)
        for page in doc:
            text += page.get_text()
        doc.close()
    except Exception as e:
        print(f"PyMuPDF Error: {e}")

    # 2. Jika teks sangat pendek atau kosong (kasus Canva/Scan), gunakan OCR
    if len(text.strip()) < 50:
        print("⚠️ Teks tidak terdeteksi secara digital. Menjalankan OCR (Tesseract)...")
        try:
            # Mengubah PDF menjadi gambar
            images = convert_from_path(file_path)
            ocr_text = ""
            for img in images:
                # Menggunakan bahasa Inggris dan Indonesia
                ocr_text += pytesseract.image_to_string(img, lang='eng+ind')
            text = ocr_text
        except Exception as e:
            print(f"OCR Error: {e}")
            
    return text

# =========================
# INPUT CV
# =========================
file_path = "/home/junior/project/web/cv_web/backend/public/storage/cv_uploads/cv3.pdf"

if not os.path.exists(file_path):
    print(f"❌ File tidak ditemukan di: {file_path}")
    exit()

text = extract_text_comprehensive(file_path)

if not text.strip():
    print("❌ Gagal mendapatkan teks dari PDF sama sekali.")
    exit()

print("-" * 30)
print(f"PANJANG TEKS: {len(text)} karakter")
print(f"PREVIEW TEKS: {repr(text[:150])}...")
print("-" * 30)

# =========================
# DETECT STRUCTURE
# =========================
def detect_structure(text):
    text_lower = text.lower()

    # Regex patterns
    name_pattern = r"\b[a-zA-Z]{2,}\s+[a-zA-Z]{2,}\b"
    email_pattern = r"[a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9-.]+"
    phone_pattern = r"(\+62\s?|62\s?|0)8[\d\s\-]{8,15}"

    structure = {
        "Name": bool(re.search(name_pattern, text)),
        "Email": bool(re.search(email_pattern, text)),
        "Phone": bool(re.search(phone_pattern, text)),
        "Location": any(loc in text_lower for loc in ["jakarta", "bandung", "surabaya", "indonesia", "malang"]),
        "Summary": any(x in text_lower for x in ["summary", "profile", "about me", "tentang saya"]),
        "Skills": any(x in text_lower for x in ["skills", "technical skills", "keahlian"]),
        "Experience": any(x in text_lower for x in ["experience", "work experience", "pengalaman kerja"]),
        "Education": any(x in text_lower for x in ["education", "academic", "pendidikan"]),
    }

    detected = sum(structure.values())
    total = len(structure)

    return detected, total, structure

detected, total, detail = detect_structure(text)

# =========================
# PREDICT
# =========================
inputs = tokenizer(text, return_tensors="pt", truncation=True, padding=True, max_length=512)

with torch.no_grad():
    outputs = model(**inputs)

logits = outputs.logits
probs = F.softmax(logits, dim=1)
pred = torch.argmax(logits, dim=1).item()


result = label_encoder.inverse_transform([pred])

# =========================
# OUTPUT
# =========================
print(f"Predicted job  : {result[0]}")
print(f"Structure score: {detected}/{total}")

print("\nDetected Structure Detail:")
for k, v in detail.items():
    status = "✅ Yes" if v else "❌ No"
    print(f"{k:<12}: {status}")

if detected < 3:
    print("\n⚠️ PERINGATAN: Struktur CV sangat minim. Hasil prediksi mungkin tidak akurat.")