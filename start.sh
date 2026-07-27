#!/bin/sh
php artisan migrate --force 2>&1
php artisan db:seed --force 2>&1 || true
php artisan serve --host=0.0.0.0 --port=${PORT:-8000}
