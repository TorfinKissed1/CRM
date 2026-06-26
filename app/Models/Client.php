<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Client extends Model
{
    protected $guarded = [];

    public function preferredStaff(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'preferred_staff_id');
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    /** Каналы-мессенджеры для UI: [тип => url]. */
    public function messengers(): array
    {
        return array_filter([
            'vk' => $this->vk,
            'telegram' => $this->telegram,
            'instagram' => $this->instagram,
            'whatsapp' => $this->whatsapp,
        ]);
    }

    public function scopeSearch($query, ?string $term)
    {
        $term = trim((string) $term);
        if ($term === '') {
            return $query;
        }

        return $query->where(function ($q) use ($term) {
            $q->where('name', 'like', "%{$term}%")
                ->orWhere('phone', 'like', "%{$term}%")
                ->orWhere('city', 'like', "%{$term}%");
        });
    }
}
