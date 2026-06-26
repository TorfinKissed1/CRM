# Деплой

Сейчас проект запускается локально, но инфраструктура заложена под сервер.

## Стек запуска (Docker Compose)
`docker-compose.yml` поднимает три сервиса:
- **web** — nginx (reverse-proxy, отдаёт статику, проксирует PHP на app:9000)
- **app** — php-fpm 8.4 (собранный образ: `docker/Dockerfile`, ассеты Vite + composer-зависимости внутри)
- **db** — PostgreSQL 16

Это и есть ответ на «nginx или docker»: nginx работает контейнером **внутри** Docker.

## Локально
```bash
docker compose up -d --build
docker compose exec app php artisan crm:install   # создать владельца
# http://localhost:8080
```
Миграции применяются автоматически при старте контейнера (`docker/entrypoint.sh`).

## На сервере (заготовка)
1. Установить Docker + Docker Compose.
2. Склонировать репозиторий, задать секреты окружения (`APP_KEY`, пароли БД) — через `.env` или
   переменные окружения сервиса `app` в compose. **Не коммитить секреты.**
3. Поставить домен и TLS: добавить термінацию HTTPS (например, Caddy/Traefik или nginx с certbot)
   перед сервисом `web`, либо вынести 443 на reverse-proxy хоста.
4. `docker compose up -d --build`, затем `docker compose exec app php artisan crm:install`.

## Заметки
- БД-том `db_data` и `app_storage` персистентны (named volumes).
- Для прод-кэша конфигурации/маршрутов entrypoint вызывает `config:cache`/`route:cache`/`view:cache`.
- Очереди/планировщик при необходимости добавляются отдельными сервисами (`php artisan queue:work`, `schedule:work`).
