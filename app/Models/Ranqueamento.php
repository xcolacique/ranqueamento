<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ranqueamento extends Model
{
    public function escolhas(): HasMany
    {
        return $this->hasMany(Escolha::class);
    }

    public function habs(): HasMany
    {
        return $this->hasMany(Hab::class);
    }

    public function declinios(): HasMany
    {
        return $this->hasMany(Declinio::class);
    }

}
