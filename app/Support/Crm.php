<?php

namespace App\Support;

use App\Models\Setting;

/**
 * Единая точка доступа к настройкам вертикала: сливает дефолты из config/crm.php
 * с переопределениями из таблицы settings (редактируются в Настройках).
 */
class Crm
{
    public static function setting(string $key, $default = null)
    {
        try {
            $value = Setting::get($key);

            return ($value === null || $value === '') ? $default : $value;
        } catch (\Throwable) {
            return $default;
        }
    }

    public static function businessName(): string
    {
        return self::setting('business_name', config('crm.business_name', 'CRM'));
    }

    public static function currencySymbol(): string
    {
        return self::setting('currency_symbol', config('crm.currency_symbol', '₽'));
    }

    public static function label(string $key): string
    {
        return config("crm.labels.$key", $key);
    }

    public static function moduleEnabled(string $key): bool
    {
        return (bool) config("crm.modules.$key", true);
    }

    public static function workingHours(): array
    {
        return [
            'start' => (int) config('crm.working_hours.start', 9),
            'end' => (int) config('crm.working_hours.end', 21),
        ];
    }

    /** Денежное форматирование: 12 500 ₽ */
    public static function money($amount): string
    {
        return number_format((float) $amount, 0, ',', ' ').' '.self::currencySymbol();
    }
}
