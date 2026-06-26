<?php

namespace App\Enums;

enum Role: string
{
    case Owner = 'owner';
    case Manager = 'manager';

    public function label(): string
    {
        return match ($this) {
            self::Owner => 'Владелец',
            self::Manager => 'Менеджер',
        };
    }

    public static function options(): array
    {
        return array_map(fn (self $r) => ['value' => $r->value, 'label' => $r->label()], self::cases());
    }
}
