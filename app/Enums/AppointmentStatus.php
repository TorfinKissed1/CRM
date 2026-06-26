<?php

namespace App\Enums;

enum AppointmentStatus: string
{
    case Planned = 'planned';
    case Completed = 'completed';
    case NoShow = 'no_show';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Planned => 'В планах',
            self::Completed => 'Завершена',
            self::NoShow => 'Не пришёл',
            self::Cancelled => 'Отменена',
        };
    }

    /** Модификатор БЭМ для бейджа статуса (.badge--<modifier>). */
    public function modifier(): string
    {
        return match ($this) {
            self::Planned => 'info',
            self::Completed => 'success',
            self::NoShow => 'warning',
            self::Cancelled => 'muted',
        };
    }

    public static function options(): array
    {
        return array_map(fn (self $s) => ['value' => $s->value, 'label' => $s->label()], self::cases());
    }
}
