<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Ranqueamento extends Model
{
    public function escolhas(): HasMany
    {
        return $this->hasMany(Escolha::class);
    }

    public function habs(): HasMany
    {
        return $this->hasMany(Hab::class)
            ->orderByRaw('nomhab COLLATE utf8mb4_unicode_ci');
    }

    public function declinios(): HasMany
    {
        return $this->hasMany(Declinio::class);
    }

    protected function max(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->tipo=='ingressantes' ? 7:1,
        );
    }

}
