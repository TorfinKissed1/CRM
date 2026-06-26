#!/bin/sh
set -e

# Готовим приложение при старте контейнера (идемпотентно).
cd /var/www/html

if [ ! -f .env ]; then
    cp .env.example .env
fi

# Ключ приложения, если не задан
php artisan key:generate --force --no-interaction || true

# Ждём БД и применяем миграции
php artisan migrate --force --no-interaction || true

# Кэш конфигурации/маршрутов/вьюх для прод
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

exec "$@"
