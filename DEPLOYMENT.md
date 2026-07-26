# Deployment Guide

## Option 1: Docker (Local / VPS)
```bash
docker-compose up -d
# Akses: http://localhost:8000
```

## Option 2: Railway
1. Push repo ke GitHub
2. Buka https://railway.app
3. New Project > Deploy from GitHub Repo
4. Tambah MySQL plugin (Automatic)
5. Set environment variables:
   ```
   APP_ENV=production
   APP_DEBUG=false
   DB_CONNECTION=mysql
   ```
6. Railway otomatis detect Dockerfile
7. Deploy selesai, dapat URL public

## Option 3: Render
1. Push repo ke GitHub
2. Buka https://render.com
3. New > Web Service > Connect repo
4. Build Command: `composer install --no-dev && php artisan migrate --force`
5. Start Command: `php artisan serve --host=0.0.0.0 --port=$PORT`
6. Tambah MySQL database di Render
7. Set env vars sesuai `.env.example`
8. Deploy

## Environment Variables (Production)
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-app-url.com
DB_CONNECTION=mysql
DB_HOST=your-db-host
DB_PORT=3306
DB_DATABASE=leave_system
DB_USERNAME=your-db-user
DB_PASSWORD=your-db-password
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
```

## Post-Deploy
```bash
php artisan migrate --force
php artisan db:seed          # Optional: seed data
php artisan storage:link     # Optional: symlink storage
php artisan schedule:work    # Run scheduler (use cron or supervisor)
```
