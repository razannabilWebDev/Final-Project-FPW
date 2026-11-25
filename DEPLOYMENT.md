# Panduan Deploy SmartFood Home ke VPS (Docker Compose)

Paket ini menjalankan: PHP-FPM (app), Nginx, MySQL, Redis, dan queue worker
dalam satu VPS. Cocok untuk single-server.

## 1. Prasyarat

- VPS Linux dengan Docker ≥ 24 & Docker Compose plugin ≥ 2
- Port 80 & 443 terbuka
- Domain mengarah ke IP VPS (A record)

## 2. Siapkan environment

```bash
cp .env.production.example .env
nano .env
```

Wajib diisi:
- `APP_URL` → domain produksi (https://…)
- `DB_PASSWORD`, `DB_ROOT_PASSWORD` → password kuat
- `APP_KEY` → **kosongkan**, akan di-generate setelah container pertama jalan

> Jangan pernah commit `.env`. File sudah masuk `.dockerignore` & `.gitignore`.

## 3. Build & jalankan

```bash
docker compose up -d --build
docker compose exec app php artisan key:generate
docker compose exec app php artisan config:clear
docker compose restart app queue nginx
```

Entrypoint akan otomatis:
- menunggu MySQL & Redis siap
- `php artisan migrate --force`
- `php artisan storage:link`
- `config:cache`, `route:cache`, `view:cache`, `event:cache`

Cek kesehatan:

```bash
curl http://localhost/health          # -> ok
docker compose ps
docker compose logs -f app nginx
```

## 4. Data dummy (opsional, hanya untuk demo)

```bash
docker compose exec app php artisan db:seed --force
```

Akun demo (password: `password`): `admin@smartfood.test`,
`pengelola@smartfood.test`, `staf@smartfood.test`.

## 5. HTTPS / SSL (Certbot)

Pastikan domain sudah mengarah ke VPS, lalu:

```bash
mkdir -p docker/certs

# Minta sertifikat (HTTP-01 via webroot yang sudah disiapkan nginx)
docker compose run --rm certbot certonly \
  --webroot -w /var/www/certbot \
  -d domain-kamu.example.com \
  --email admin@domain-kamu.example.com \
  --agree-tos --no-eff-email
```

Lalu aktifkan blok SSL di `docker/nginx.conf` (contoh):

```nginx
server {
    listen 443 ssl http2;
    server_name domain-kamu.example.com;
    ssl_certificate     /etc/letsencrypt/live/domain-kamu.example.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/domain-kamu.example.com/privkey.pem;
    root /var/www/html/public;
    index index.php;
    client_max_body_size 12M;
    location / { try_files $uri $uri/ /index.php?$query_string; }
    location ~ \.php$ {
        fastcgi_pass app:9000;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }
}

server {
    listen 80;
    server_name domain-kamu.example.com;
    return 301 https://$host$request_uri;
}
```

Setelah itu `docker compose restart nginx`.

Perpanjangan otomatis sudah disiapkan lewat service `certbot` (profil):

```bash
docker compose --profile certbot up -d certbot
```

Atau cron di host:

```bash
0 3 * * * cd /path/SmartfoodHome && docker compose run --rm certbot renew --webroot -w /var/www/certbot --quiet && docker compose exec nginx nginx -s reload
```

## 6. Backup & restore

Backup database:

```bash
docker compose exec db mysqldump -uroot -p"$DB_ROOT_PASSWORD" smartfood \
  > backup-$(date +%F).sql
```

Backup storage (foto menu & nota):

```bash
docker run --rm -v smartfoodHome_app_storage:/data -v $(pwd):/backup \
  alpine tar czf /backup/storage-$(date +%F).tar.gz -C /data .
```

Restore:

```bash
docker compose exec -T db mysql -uroot -p"$DB_ROOT_PASSWORD" smartfood < backup.sql
docker run --rm -v smartfoodHome_app_storage:/data -v $(pwd):/backup \
  alpine tar xzf /backup/storage.tar.gz -C /data
docker compose restart app queue
```

> Nama volume bisa dicek dengan `docker volume ls | grep smartfood`.

## 7. Update aplikasi

```bash
git pull
docker compose up -d --build
docker compose logs -f app
```

Migrasi berjalan otomatis via entrypoint. Jika ada perubahan `.env`,
restart: `docker compose restart app queue`.

## 8. Catatan penting

- `APP_DEBUG=false` wajib di produksi.
- `config:cache` berarti perubahan `.env` baru生效 setelah container restart.
- Upload maksimal 10 MB (nota 5 MB, foto menu 2 MB).
- Timezone aplikasi & PHP: `Asia/Jakarta`.
- Semua uang & pecahan memakai `decimal` + `bcmath` (ekstensi sudah terpasang di image).
- Jangan pakai `root` untuk aplikasi; image berjalan sebagai `www-data`.
- Jika memakai MySQL eksternal, hapus service `db` dari compose dan isi `DB_HOST` di `.env`.
