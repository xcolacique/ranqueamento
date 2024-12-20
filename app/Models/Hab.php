<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Hab extends Model
{
    public function escolhas(): HasMany
    {
        return $this->hasMany(Escolha::class);
    }

    public function ranqueamentos(): BelongsTo
    {
        return $this->belongsTo(Escolha::class);
    }

    protected function nomhab(): Attribute
    {
        return Attribute::make(
            get:fn (string $value) => trim(str_replace('Bacharelado - Habilitação:','', $value)),
        );
    }
}
