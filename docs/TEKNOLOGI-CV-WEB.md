# Dokumentasi Teknologi CV Web

Dokumen ini merangkum teknologi yang digunakan pada project `cv_web`, fungsi tiap teknologi, serta posisi penggunaannya di dalam arsitektur aplikasi.

## Ringkasan Stack

CV Web dibangun sebagai aplikasi berbasis container dengan beberapa service utama:

| Area | Teknologi | Fungsi |
| --- | --- | --- |
| Backend web | PHP 8.4, Laravel 13 | Routing, autentikasi, dashboard, upload CV, pembayaran, integrasi AI |
| Frontend | Blade, Tailwind CSS 4, Vite 8, Axios | Tampilan halaman, styling, bundling asset, request HTTP dari browser |
| AI service | Python 3.12, FastAPI, Uvicorn | Ekstraksi teks CV, analisis ATS, semantic score, skill gap, rekomendasi |
| NLP/ML | PyTorch CPU, Transformers, Sentence Transformers | Embedding semantic, pemrosesan bahasa, model NLP |
| Dokumen CV | PyMuPDF, pypdf, python-docx, Pillow, Tesseract OCR | Membaca PDF, DOCX, gambar, dan OCR dokumen |
| Database | MySQL 8.0 | Penyimpanan user, CV, interview, payment, subscription, rekomendasi lowongan |
| Web server | Nginx Alpine | Reverse proxy HTTP ke PHP-FPM |
| Container | Docker, Docker Compose | Menjalankan backend, AI, database, Nginx, phpMyAdmin |
| Auth eksternal | Laravel Socialite, Google OAuth | Login/register dengan akun Google |
| Payment gateway | Midtrans PHP SDK | Pembayaran, status transaksi, callback notifikasi |
| Admin database | phpMyAdmin | Akses database via browser untuk development/admin |

## Arsitektur Aplikasi

```text
Browser
  |
  v
Nginx :8081
  |
  v
Laravel / PHP-FPM
  |           |
  |           v
  |        MySQL :3306
  |
  v
FastAPI AI Service :8000
```

Alur utama:

1. User membuka aplikasi melalui Nginx.
2. Nginx meneruskan request PHP ke service backend Laravel.
3. Laravel menangani autentikasi, upload CV, dashboard, interview, payment, dan penyimpanan data.
4. Untuk analisis CV, Laravel mengirim path file CV ke FastAPI melalui `AI_API_URL=http://ai:8000`.
5. FastAPI membaca file dari shared storage, mengekstrak teks, menjalankan analisis ATS/semantic/skill gap, lalu mengembalikan hasil ke Laravel.
6. Laravel menyimpan dan menampilkan hasil analisis ke user.

## Backend Laravel

Lokasi utama: `backend/`

Teknologi:

- `php:^8.3` pada dependency Composer, dengan image runtime `php:8.4-fpm`.
- `laravel/framework:^13.0`.
- `laravel/sanctum:^4.3` untuk dukungan autentikasi API/token jika diperlukan.
- `laravel/socialite:^5.24` untuk login Google.
- `midtrans/midtrans-php:^2.6` untuk integrasi payment gateway.
- `laravel/tinker:^3.0` untuk console/debug interaktif Laravel.

Komponen backend yang terlihat dari struktur project:

- `AuthController`: login, register, logout, update profil, forgot/reset password, Google OAuth.
- `DashboardController`: dashboard user/admin dan update data admin.
- `CvController`: upload CV dan proses analisis.
- `InterviewController`: submit dan generate interview.
- `PaymentController`: pembayaran, score, dan status transaksi.
- Model utama: `User`, `Cv`, `Interview`, `Payment`, `Subscription`, `JobRecommendation`, `Post`.

Route web utama:

- `/` landing page.
- `/login`, `/register`, `/logout`.
- `/forgot-password`, `/reset-password/{token}`.
- `/auth/google/redirect`, `/auth/google/callback`.
- `/dashboard`.
- `/upload-cv`.
- `/submit-interview`, `/generate-interview`.
- Payment dan callback Midtrans.

## Frontend

Lokasi utama:

- `backend/resources/views/`
- `backend/resources/views/partials/`
- `backend/resources/css/`
- `backend/resources/js/`

Teknologi:

- Blade sebagai template engine Laravel.
- Tailwind CSS 4 untuk utility-first styling.
- Vite 8 untuk bundling asset frontend.
- Laravel Vite Plugin 3 untuk integrasi Laravel dengan Vite.
- Axios untuk HTTP request dari JavaScript.

Halaman penting:

- `landingpage.blade.php`
- `login.blade.php`
- `register.blade.php`
- `forgot-password.blade.php`
- `reset-password.blade.php`
- `dashboard.blade.php`
- `partials/cv_content.blade.php`
- `partials/jobs_content.blade.php`

Script frontend:

```bash
npm run dev
npm run build
```

## AI Service

Lokasi utama: `ai/`

Service AI menggunakan FastAPI yang berjalan terpisah dari Laravel. Endpoint utama berada di `ai/app/main.py`.

Teknologi:

- Python 3.12.
- FastAPI `0.115.6` untuk REST API.
- Uvicorn `0.34.0` sebagai ASGI server.
- Pydantic `2.10.4` untuk schema request/response.
- python-multipart untuk upload file multipart.

Endpoint AI:

- `GET /health`: status service, konfigurasi semantic model, dan storage root.
- `POST /extract-text`: ekstraksi teks dari upload multipart atau path file.
- `POST /analyze`: analisis CV berdasarkan file path dan optional job description.

Modul AI:

- `extractor.py`: ekstraksi teks dari PDF, DOCX, gambar, dan upload.
- `analyzer.py`: analisis ATS, score, keyword, kekuatan, dan kekurangan.
- `semantic.py`: semantic similarity dengan Sentence Transformers.
- `skill_gap.py`: analisis gap skill berdasarkan kebutuhan lowongan.
- `recommendation.py`: rekomendasi lowongan.
- `language.py`, `bilingual_terms.py`, `normalizer.py`, `text_cleaning.py`: normalisasi bahasa dan teks.
- `schemas.py`: schema Pydantic untuk API.
- `file_resolver.py`: validasi dan resolusi path file dari storage.

## NLP, Machine Learning, dan OCR

Dependency AI:

- `torch==2.5.1+cpu`: runtime PyTorch CPU.
- `transformers==4.41.2`: model NLP berbasis Hugging Face.
- `sentence-transformers==3.3.1`: embedding kalimat untuk semantic similarity.
- `tokenizers==0.19.1`: tokenisasi model.
- `PyMuPDF==1.25.1`: ekstraksi teks PDF.
- `pypdf==5.1.0`: pembacaan PDF alternatif.
- `python-docx==1.1.2`: pembacaan dokumen DOCX.
- `pillow==11.0.0`: pemrosesan gambar.
- `pytesseract==0.3.13`: integrasi OCR Tesseract dari Python.

Dependency sistem pada container AI:

- `tesseract-ocr`
- `tesseract-ocr-ind`
- `libglib2.0-0`
- `libgl1`

Model semantic dikonfigurasi melalui environment:

```env
USE_SENTENCE_TRANSFORMER=true
SEMANTIC_MODEL=/app/models/indoSBERT-large
```

## Database

Database menggunakan MySQL 8.0 melalui service `db` di Docker Compose.

Konfigurasi container:

```env
MYSQL_DATABASE=careersense
MYSQL_USER=careersense
MYSQL_PASSWORD=careersense
MYSQL_ROOT_PASSWORD=password
```

Data disimpan pada Docker volume:

```text
db_data:/var/lib/mysql
```

Laravel menggunakan environment:

```env
DB_HOST=db
DB_DATABASE=careersense
DB_USERNAME=careersense
DB_PASSWORD=careersense
```

## Payment Gateway

Payment gateway menggunakan Midtrans melalui package:

```text
midtrans/midtrans-php:^2.6
```

Peran Midtrans di aplikasi:

- Membuat transaksi pembayaran.
- Menerima callback/notifikasi server-to-server.
- Mengubah status payment/subscription berdasarkan hasil transaksi.
- Membatasi fitur tertentu berdasarkan status langganan/pembayaran.

Konfigurasi Midtrans biasanya diletakkan pada `.env` dan `config/services.php`.

## Google OAuth

Login Google menggunakan Laravel Socialite:

```text
laravel/socialite:^5.24
```

Route yang digunakan:

- `/auth/google/redirect`
- `/auth/google/callback`

Data identitas Google disimpan di model/user table melalui field Google identity yang ditambahkan oleh migration terkait.

## Docker dan Service

Project dijalankan dengan Docker Compose dari file `docker-compose.yml`.

Service:

- `backend`: Laravel PHP-FPM.
- `nginx`: web server publik di port `8081`.
- `ai`: FastAPI AI service di port `8000`.
- `db`: MySQL 8.0 di port `3306`.
- `phpmyadmin`: UI database di port `8080`.

Command umum:

```bash
docker compose build
docker compose up -d
docker compose ps
docker compose logs -f
```

## Development Tools

Dependency development PHP:

- `fakerphp/faker`: fake data untuk testing/seeding.
- `laravel/pail`: realtime log viewer Laravel.
- `laravel/pint`: formatter PHP/Laravel.
- `mockery/mockery`: mock object untuk testing.
- `nunomaduro/collision`: error output CLI yang lebih readable.
- `phpunit/phpunit`: testing framework.

Script Composer penting:

```bash
composer run dev
composer run test
composer run setup
```

Script `composer run dev` menjalankan beberapa proses sekaligus:

- Laravel dev server.
- Queue listener.
- Laravel Pail logs.
- Vite dev server.

## Environment Penting

Environment backend:

```env
APP_KEY=
APP_URL=
DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=careersense
DB_USERNAME=careersense
DB_PASSWORD=careersense
AI_API_URL=http://ai:8000
```

Environment AI:

```env
STORAGE_ROOT=/var/www/html/public/storage
USE_SENTENCE_TRANSFORMER=true
SEMANTIC_MODEL=/app/models/indoSBERT-large
```

Environment integrasi eksternal:

```env
GOOGLE_CLIENT_ID=
GOOGLE_CLIENT_SECRET=
GOOGLE_REDIRECT_URI=

MIDTRANS_SERVER_KEY=
MIDTRANS_CLIENT_KEY=
MIDTRANS_IS_PRODUCTION=false
```

Nama environment bisa disesuaikan dengan implementasi di `config/services.php` dan controller terkait.

## Ringkasan Peran Tiap Teknologi

- Laravel menjadi pusat aplikasi: auth, dashboard, upload CV, payment, dan komunikasi ke AI.
- FastAPI menjadi microservice AI yang stateless dan fokus pada ekstraksi serta analisis CV.
- MySQL menyimpan data aplikasi.
- Nginx menjadi entry point HTTP saat container berjalan.
- Docker Compose menyatukan seluruh service agar mudah dijalankan secara lokal/deployment.
- Tailwind, Blade, dan Vite membentuk UI aplikasi.
- PyTorch, Transformers, dan Sentence Transformers menangani analisis semantic/NLP.
- Tesseract, PyMuPDF, pypdf, python-docx, dan Pillow menangani pembacaan dokumen CV dari berbagai format.
- Midtrans menangani pembayaran.
- Google OAuth menangani login dengan akun Google.
