<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Book extends Model
{
    protected $fillable = ['name', 'sort_order'];

    public function hadiths(): HasMany
    {
        return $this->hasMany(Hadith::class);
    }
}
