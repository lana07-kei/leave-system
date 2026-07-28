FROM php:8.2-cli

RUN apt-get update && apt-get install -y \
    libicu-dev \
    libzip-dev \
    libpng-dev \
    libexif-dev \
    libonig-dev \
    unzip \
    && docker-php-ext-install intl zip pdo_mysql mbstring gd bcmath exif opcache \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

COPY . .

RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts \
    && mkdir -p storage/framework/sessions storage/framework/cache storage/framework/views storage/logs \
    && chmod -R 775 storage bootstrap/cache public

EXPOSE 8000

CMD sh -c 'export SESSION_DRIVER=file && export CACHE_STORE=file && export APP_DEBUG=true && cp .env.example .env && php artisan key:generate --force 2>&1 && php artisan package:discover --ansi 2>&1 && php artisan migrate --force 2>&1; php artisan db:seed --force 2>&1; php artisan filament:assets --no-interaction 2>&1; exec php artisan serve --host=0.0.0.0 --port=${PORT:-8000}'
