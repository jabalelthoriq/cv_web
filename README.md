
# Download Model AI Manual

Karena file model terlalu besar, model tidak disimpan di repository Git.

Silakan download model manual melalui link berikut:

```text
https://drive.google.com/drive/folders/1AqXaB0OyEllPAJSEs18qyObZg_h7ChZB?usp=drive_link
```

Setelah download:

1. Extract file model
2. Pastikan folder bernama:

```bash
model_bert_cv
```

3. Pindahkan ke:

```bash
cv_web/ai/model_bert_cv
```

Struktur akhirnya harus seperti ini:

```bash
cv_web/
└── ai/
    └── model_bert_cv/
        ├── config.json
        ├── model.safetensors
        ├── tokenizer.json
        ├── tokenizer_config.json
        ├── special_tokens_map.json
        └── vocab.txt
```

---

# CV Web - Deployment Guide

Project ini menggunakan:

* Laravel (Backend API)
* FastAPI + Transformers (AI Service)
* MySQL
* Nginx
* Docker Compose

---

# Struktur Project

```bash
cv_web/
├── ai/
│   ├── model_bert_cv/
│   ├── main.py
│   ├── requirements.txt
│   └── Dockerfile.ai
├── backend/
├── docker-compose.yml
└── README.md
```

---

# Requirement

Pastikan sudah menginstall:

* Docker
* Docker Compose
* Git

Cek versi:

```bash
docker --version
docker compose version
```

---

# Clone Repository

```bash
git clone <repository-url>
cd cv_web
```

---

# Konfigurasi Environment Laravel

Masuk ke folder backend:

```bash
cd backend
```

Copy file environment:

```bash
cp .env.example .env
```

Generate key Laravel:

```bash
php artisan key:generate
```

Atur database pada file `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=cv_web
DB_USERNAME=root
DB_PASSWORD=root
```

---

# Build Docker

Kembali ke root project:

```bash
cd ..
```

Build seluruh container:

```bash
docker compose build --no-cache
```

Jalankan container:

```bash
docker compose up -d
```

---

# Cek Container

```bash
docker ps
```

Container yang berjalan:

* cv_web-nginx-1
* cv_web-backend-1
* cv_web-db-1
* cv_web-ai-1

---

# Migrasi Database

```bash
docker exec -it cv_web-backend-1 php artisan migrate
```

Jika menggunakan seeder:

```bash
docker exec -it cv_web-backend-1 php artisan db:seed
```

---

# Akses Aplikasi

Laravel + Nginx:

```text
http://localhost
```

FastAPI:

```text
http://localhost:8000
```

Swagger FastAPI:

```text
http://localhost:8000/docs
```

---

# Cek Log

Semua container:

```bash
docker compose logs -f
```

AI service:

```bash
docker compose logs -f ai
```

Backend Laravel:

```bash
docker compose logs -f backend
```

---

# Restart Container

```bash
docker compose restart
```

Restart AI saja:

```bash
docker compose restart ai
```

---

# Stop Container

```bash
docker compose down
```

---

# Rebuild Setelah Update

```bash
docker compose down
docker compose build --no-cache
docker compose up -d
```

---

# Troubleshooting

## 1. Container AI Exited (1)

Cek log:

```bash
docker compose logs ai
```

Biasanya karena folder `model2` belum ada.

Pastikan:

```bash
cv_web/ai/model2
```

sudah berisi file model.

---

## 2. No Space Left on Device

Bersihkan cache Docker:

```bash
docker system prune -a -f
docker builder prune -a -f
```

Cek storage:

```bash
df -h
```

---

## 3. NumPy Error

Pastikan menggunakan:

```txt
numpy==1.26.4
```

---

# Notes

* Folder `model2` tidak ikut Git karena ukuran file besar.
* Gunakan Python dependency sesuai `requirements.txt`.
* Jangan mengubah struktur folder model.

---

# Developer

Kelompok 5  JOSJISS
