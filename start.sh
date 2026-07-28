#!/bin/sh
cat > .env <<EOF
APP_NAME="Sistem Pengajuan Cuti"
APP_ENV=production
APP_KEY=$(php artisan key:generate --show 2>/dev/null || echo "base64:$(openssl rand -base64 32)")
APP_DEBUG=false
APP_TIMEZONE=Asia/Jakarta
APP_URL=https://leave-system-production-3524.up.railway.app
APP_LOCALE=id
APP_FALLBACK_LOCALE=id
APP_FAKER_LOCALE=id_ID
APP_MAINTENANCE_DRIVER=file
BCRYPT_ROUNDS=12
LOG_CHANNEL=stack
LOG_STACK=single
LOG_LEVEL=debug
DB_CONNECTION=${DB_CONNECTION:-mysql}
DB_HOST=${DB_HOST:-tokaido.proxy.rlwy.net}
DB_PORT=${DB_PORT:-29386}
DB_DATABASE=${DB_DATABASE:-railway}
DB_USERNAME=${DB_USERNAME:-root}
DB_PASSWORD=${DB_PASSWORD:-}
SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=null
BROADCAST_CONNECTION=log
FILESYSTEM_DISK=local
QUEUE_CONNECTION=database
CACHE_STORE=database
CACHE_PREFIX=
MAIL_MAILER=log
MAIL_FROM_ADDRESS="noreply@company.com"
MAIL_FROM_NAME="Sistem Pengajuan Cuti"
EOF

php artisan config:clear 2>/dev/null || true
php artisan cache:clear 2>/dev/null || true
php artisan migrate --force 2>&1 || true
php artisan db:seed --force 2>&1 || true
exec php artisan serve --host=0.0.0.0 --port=${PORT:-3000}
