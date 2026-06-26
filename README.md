# CRM

Операционная CRM для сферы услуг (референс — барбершоп BLADE): учёт клиентов, расписание/записи,
услуги и прайс, финансы, дашборд. Спроектирована как **переиспользуемое ядро**: одна и та же
кодовая база настраивается под другой бизнес через `config/crm.php` (лейблы, модули, тема).

Боевой продукт без демо-данных: данные реально сохраняются, владелец создаётся командой установки.

## Стек
- **Laravel 12** + **Livewire 4** + **Alpine.js** (многостраничный серверный рендеринг, не SPA)
- **SCSS + БЭМ** (без Tailwind), дизайн-токены в CSS-переменных (`resources/scss/_tokens.scss`)
- **PostgreSQL** (прод/Docker) · **SQLite** (быстрый локальный старт)
- **openspout** — импорт/экспорт xlsx/csv
- 2 роли: **владелец** (owner) и **менеджер** (manager)

## Возможности
- **Дашборд** — KPI, график записей по дням, статистика мастеров, расписание на сегодня
- **Клиенты** — таблица с поиском/фильтрами, карточки, мессенджеры (VK/TG/IG/WA), **импорт xlsx/csv с маппингом колонок**, экспорт
- **Расписание** — дневной борд по мастерам, создание/правка записей, завершение → авто-операция в финансах
- **Финансы** — период (7/30/всё), доход/расход/средний чек, операции, экспорт
- **Настройки** — профиль и **цвет темы**, мастера, услуги, пользователи и роли

## Быстрый старт (локально, SQLite)
```bash
composer install
npm install && npm run build
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate
php artisan crm:install            # создаст владельца (спросит email/пароль)
php artisan serve                  # http://localhost:8000
```

## Запуск в Docker (nginx + php-fpm + postgres)
```bash
docker compose up -d --build
docker compose exec app php artisan crm:install
# открыть http://localhost:8080
```

## Документация
- [docs/ARCHITECTURE.md](docs/ARCHITECTURE.md) — структура и принципы
- [docs/CONFIGURATION.md](docs/CONFIGURATION.md) — как переделать под другой бизнес
- [docs/DEPLOYMENT.md](docs/DEPLOYMENT.md) — деплой на сервер
