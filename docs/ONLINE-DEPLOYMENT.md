# Online Deployment - CareerSense

Target domain:

```text
https://careersense.jabalelthoriq.online
```

## Status Lokal

Aplikasi sudah disiapkan untuk domain production:

- `APP_URL=https://careersense.jabalelthoriq.online`
- `GOOGLE_REDIRECT_URI=https://careersense.jabalelthoriq.online/auth/google/callback`
- Nginx menerima `server_name careersense.jabalelthoriq.online`.
- Service internal Docker dibatasi ke `127.0.0.1` untuk port host:
  - Laravel Nginx: `127.0.0.1:8081`
  - AI service: `127.0.0.1:8000`
  - MySQL: `127.0.0.1:3306`
  - phpMyAdmin: `127.0.0.1:8080`

## Cloudflare Tunnel

Domain belum bisa diakses publik sampai hostname ditambahkan ke Cloudflare Tunnel.

Pada Cloudflare Zero Trust, buka tunnel yang sama dengan deployment `skripsicare.jabalelthoriq.online`, lalu tambahkan Public Hostname:

```text
Subdomain: careersense
Domain: jabalelthoriq.online
Path: kosong
Service type: HTTP
Service URL: cv_web-nginx-1:80
```

Container tunnel yang sedang berjalan sudah ditempelkan ke network Docker `cv_web_default`, sehingga hostname `cv_web-nginx-1` bisa diakses dari tunnel.

Setelah hostname ditambahkan, validasi:

```bash
dig +short careersense.jabalelthoriq.online
curl -I https://careersense.jabalelthoriq.online
```

## Google OAuth

Tambahkan redirect URI berikut di Google Cloud Console pada OAuth Client:

```text
https://careersense.jabalelthoriq.online/auth/google/callback
```

Jika belum ditambahkan, login Google akan gagal walaupun domain sudah online.

## Validasi Lokal

Validasi dari server:

```bash
curl -I -H 'Host: careersense.jabalelthoriq.online' http://127.0.0.1:8081
docker exec cv_web-backend-1 php artisan config:show app.url
```

Expected:

- HTTP `200 OK` dari Nginx/Laravel.
- `app.url` mengarah ke `https://careersense.jabalelthoriq.online`.
