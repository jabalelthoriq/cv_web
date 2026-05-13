import json
import pandas as pd
from datasets import Dataset
from sklearn.preprocessing import LabelEncoder
from transformers import BertTokenizer, BertForSequenceClassification
from transformers import Trainer, TrainingArguments
import torch
import numpy as np
from sklearn.metrics import accuracy_score, precision_recall_fscore_support
import joblib

# =========================
# LOAD DATASET JSONL
# =========================
data = []
with open("dataset/resumes_dataset.jsonl", "r", encoding="utf-8") as f:
    for line in f:
        data.append(json.loads(line))

df = pd.DataFrame(data)

# =========================
# COMBINE TEXT (IMPORTANT)
# =========================
df["text"] = df["Summary"] + " " + df["Skills"] + " " + df["Experience"]

# =========================
# LABEL ENCODING
# =========================
label_encoder = LabelEncoder()
df["label"] = label_encoder.fit_transform(df["Category"])

# simpan label encoder
joblib.dump(label_encoder, "label_encoder.pkl")

# =========================
# CONVERT TO HF DATASET
# =========================
dataset = Dataset.from_pandas(df[["text", "label"]])

# =========================
# TOKENIZER
# =========================
tokenizer = BertTokenizer.from_pretrained("bert-base-uncased")

def tokenize(example):
    return tokenizer(
        example["text"],
        padding="max_length",
        truncation=True,
        max_length=256
    )

dataset = dataset.map(tokenize, batched=True)

# split
dataset = dataset.train_test_split(test_size=0.2)

train_dataset = dataset["train"]
test_dataset = dataset["test"]

# =========================
# MODEL
# =========================
num_labels = len(label_encoder.classes_)

model = BertForSequenceClassification.from_pretrained(
    "bert-base-uncased",
    num_labels=num_labels
)

# =========================
# METRICS
# =========================
def compute_metrics(p):
    preds = np.argmax(p.predictions, axis=1)
    precision, recall, f1, _ = precision_recall_fscore_support(
        p.label_ids, preds, average="weighted"
    )
    acc = accuracy_score(p.label_ids, preds)
    return {"accuracy": acc, "f1": f1, "precision": precision, "recall": recall}

# =========================
# TRAINING CONFIG
# =========================
training_args = TrainingArguments(
    output_dir="./results",
    eval_strategy="epoch",
    save_strategy="epoch",
    learning_rate=2e-5,
    per_device_train_batch_size=8,
    per_device_eval_batch_size=8,
    num_train_epochs=3,
    weight_decay=0.01,
    logging_dir="./logs",
)

# =========================
# TRAINER
# =========================
trainer = Trainer(
    model=model,
    args=training_args,
    train_dataset=train_dataset,
    eval_dataset=test_dataset,
    tokenizer=tokenizer,
    compute_metrics=compute_metrics,
)

# =========================
# TRAIN
# =========================
trainer.train()

# =========================
# SAVE MODEL
# =========================
model.save_pretrained("job_classifier_model")
tokenizer.save_pretrained("job_classifier_model")

print("Training selesai & model disimpan!")