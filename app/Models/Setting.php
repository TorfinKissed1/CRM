<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Key/value настройки. Без кэша/override all() — настроек мало, прямой запрос дешевле
 * и не упирается в проблему сериализации Collection в database-кэше.
 */
class Setting extends Model
{
    protected $primaryKey = 'key';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $guarded = [];

    public static function get(string $key, $default = null)
    {
        return static::query()->where('key', $key)->value('value') ?? $default;
    }

    public static function put(string $key, $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value]);
    }
}
