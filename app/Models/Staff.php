<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Staff extends Model
{
    protected $table = 'staff';

    protected $guarded = [];

    protected $casts = [
        'is_active' => 'boolean',
        'sort' => 'integer',
    ];

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    public function initials(): string
    {
        $parts = preg_split('/\s+/', trim($this->name)) ?: [];
        $letters = array_map(fn ($p) => Str::upper(Str::substr($p, 0, 1)), array_slice($parts, 0, 2));

        return implode('', $letters) ?: '—';
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
