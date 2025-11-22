<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Source extends Model
{
    protected $fillable = ['name', 'code', 'type'];

    public function hadiths(): BelongsToMany
    {
        return $this->belongsToMany(Hadith::class, 'hadith_source');
    }
}
