# CRM

CRM для барбершопа: клиенты, расписание, услуги и прайс, финансы и дашборд — в одном месте.
Боевой продукт: реальные данные, тёмная/светлая тема, импорт и экспорт.

## Стек
- **Laravel 12 · Livewire 4 · Alpine.js** — серверный рендеринг (не SPA)
- **SCSS + БЭМ**, дизайн-токены в CSS-переменных, тёмная/светлая тема
- **PostgreSQL** (Docker) · **SQLite** (локально)
- **openspout** — импорт/экспорт xlsx/csv

## Возможности
- **Дашборд** — KPI с трендами, SVG-графики (записи по дням, динамика выручки, клиенты по городам), топ-услуги, ближайшие записи
- **Клиенты** — поиск и фильтры, карточки, мессенджеры (VK/TG/IG/WA), импорт/экспорт xlsx/csv
- **Расписание** — дневной борд по мастерам, записи, завершение → операция в финансах
- **Финансы** — доход/расход/средний чек по периодам, операции, экспорт
- **Настройки** — профиль, мастера, услуги, пользователи (роли: владелец/менеджер)

## Запуск (Docker)
```bash
docker compose up -d --build
docker compose exec app php artisan crm:install   # создать владельца
# http://localhost:8080
```

## Локально (SQLite)
```bash
composer install && npm install && npm run build
cp .env.example .env && php artisan key:generate
php artisan migrate && php artisan crm:install
php artisan serve   # http://localhost:8000
```
Демо-наполнение (по желанию): `php artisan db:seed --class=SampleDataSeeder`
