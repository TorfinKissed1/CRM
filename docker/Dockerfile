# syntax=docker/dockerfile:1

# --- Сборка фронт-ассетов (Vite + SCSS) ---
FROM node:22-alpine AS assets
WORKDIR /app
COPY package.json package-lock.json vite.config.js ./
RUN npm ci
COPY resources ./resources
RUN npm run build

# --- PHP-зависимости ---
FROM composer:2 AS vendor
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --prefer-dist --no-interaction

# --- Рантайм: PHP-FPM 8.4 ---
FROM php:8.4-fpm-alpine AS app
RUN apk add --no-cache libzip-dev icu-dev oniguruma-dev postgresql-dev libpng-dev \
    && docker-php-ext-install pdo pdo_pgsql zip intl gd bcmath opcache \
    && rm -rf /var/cache/apk/*

WORKDIR /var/www/html
COPY . .
COPY --from=vendor /app/vendor ./vendor
COPY --from=assets /app/public/build ./public/build

RUN chown -R www-data:www-data storage bootstrap/cache \
    && cp docker/entrypoint.sh /usr/local/bin/entrypoint && chmod +x /usr/local/bin/entrypoint

EXPOSE 9000
ENTRYPOINT ["entrypoint"]
CMD ["php-fpm"]
