FROM php:8.3-cli

# System deps
RUN apt-get update && apt-get install -y \
    git unzip libpq-dev libzip-dev \
    && docker-php-ext-install pdo pdo_pgsql zip

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# 1. Skopiuj cały kod (z artisan)
COPY . .

# 2. Zainstaluj zależności, ale BEZ uruchamiania skryptów (żeby nie odpalał artisan podczas builda)
RUN composer install --no-interaction --prefer-dist --no-dev --no-scripts

EXPOSE 8000

CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
