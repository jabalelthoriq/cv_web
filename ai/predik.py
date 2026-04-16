import torch
from transformers import BertTokenizer, BertForSequenceClassification
import joblib
import numpy as np

# =========================
# LOAD MODEL
# =========================
model = BertForSequenceClassification.from_pretrained("job_classifier_model")
tokenizer = BertTokenizer.from_pretrained("job_classifier_model")
label_encoder = joblib.load("label_encoder.pkl")

model.eval()

# =========================
# FUNCTION PREDICT
# =========================
def predict_job(text):
    inputs = tokenizer(
        text,
        return_tensors="pt",
        truncation=True,
        padding=True,
        max_length=256
    )

    with torch.no_grad():
        outputs = model(**inputs)

    logits = outputs.logits
    pred = torch.argmax(logits, dim=1).item()

    return label_encoder.inverse_transform([pred])[0]

# =========================
# TEST
# =========================
cv_text = """
Experienced in Python, AWS, Docker, leading teams and building scalable applications.
"""

result = predict_job(cv_text)

print("Predicted Job:", result)