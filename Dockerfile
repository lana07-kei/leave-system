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

RUN test -f .env || cp .env.example .env \
    && composer install --no-dev --optimize-autoloader --no-interaction --no-scripts \
    && php artisan key:generate --force \
    && chmod -R 775 storage bootstrap/cache

EXPOSE 8000

CMD ["sh", "./start.sh"]
