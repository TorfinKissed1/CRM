<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tag extends Model
{
    protected $guarded = [];

    public $timestamps = true;

    public function clients(): BelongsToMany
    {
        return $this->belongsToMany(Client::class);
    }
}
