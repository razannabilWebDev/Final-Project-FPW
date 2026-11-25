#!/bin/sh
set -e

echo "[entrypoint] Menunggu database siap..."

# Tunggu koneksi MySQL (menggunakan variabel DB_HOST dari .env / compose).
until php -r '
    $host = getenv("DB_HOST") ?: "db";
    $port = getenv("DB_PORT") ?: "3306";
    $err  = @fsockopen($host, (int) $port, $errno, $errstr, 2);
    exit($err ? 0 : 1);
' 2>/dev/null; do
    echo "[entrypoint] Database belum siap, mencoba lagi..."
    sleep 3
done

echo "[entrypoint] Database siap. Menjalankan migrasi..."

cd /var/www/html

php artisan migrate --force --no-interaction

# Pastikan symlink storage tersedia (idempoten).
php artisan storage:link --force >/dev/null 2>&1 || true

# Cache konfigurasi untuk performa. Perubahan .env butuh container restart.
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

chown -R www-data:www-data storage bootstrap/cache public

echo "[entrypoint] Selesai. Menjalankan: $*"

exec "$@"
