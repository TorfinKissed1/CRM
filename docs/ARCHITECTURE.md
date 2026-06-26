# Архитектура

## Принципы
- **Многостраничный серверный Laravel** (Livewire для реактивных мест), **не SPA**.
- **Тонкие компоненты** → бизнес-логика выносится в `app/Actions` и `app/Support`.
- **БЭМ + SCSS**, 1 блок = 1 файл в `resources/scss/blocks/`, токены в CSS-переменных.
- **Мало кода на файл**, нейтральные имена сущностей в коде, отображаемые названия — из конфига.

## Структура
```
app/
  Enums/            Role, AppointmentStatus, TransactionType
  Models/           User, Client, Staff, Service, Appointment, Transaction, Tag, Setting
  Livewire/         страницы по модулям: Auth/, Dashboard/, Clients/, Schedule/, Finance/, Settings/
  Actions/          ImportClients, ExportClients, ExportTransactions, CompleteAppointment
  Support/Crm.php   доступ к настройкам/лейблам/теме/деньгам (config + таблица settings)
  Http/Middleware/  EnsureModuleEnabled (гейт модулей по config/crm.php)
  Console/Commands/ InstallCommand (crm:install)
config/crm.php      лейблы, модули, валюта, тема (слой переиспользования)
resources/
  scss/             _tokens.scss, _base.scss, app.scss, blocks/*.scss (БЭМ)
  views/
    components/layouts/  app.blade.php (каркас), guest.blade.php (логин)
    livewire/<module>/   вьюхи компонентов
    partials/            icons, messengers
docker/             Dockerfile, nginx.conf, entrypoint.sh
```

## Данные (Eloquent)
- **User** `role: owner|manager`, `is_active`.
- **Client** — контакты + мессенджеры (vk/telegram/instagram/whatsapp), город, предпочитаемый мастер, теги, заметки. Сюда импортируется xlsx.
- **Staff** — мастер/сотрудник. **Service** — услуга (длительность, цена).
- **Appointment** — запись (client/staff/service, время, статус, цена-снимок). Завершение создаёт **Transaction** (идемпотентно).
- **Transaction** — доход/расход (для финансов). **Setting** — key/value рантайм-настроек.

## Роли и доступ
- Гейт `owner` (`AppServiceProvider`) → раздел **Настройки** только владельцу (`can:owner`).
- Модули **Расписание/Финансы** включаются в `config/crm.php` и гейтятся middleware `module:*`.

## Темизация
Цвет бренда хранится в `settings.theme_primary` и инжектится в `--color-primary` в layout.
Все блоки используют только `var(--…)` — перекраска не требует пересборки SCSS.
