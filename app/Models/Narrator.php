<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Narrator extends Model
{
    protected $fillable = ['name', 'bio', 'grade_status', 'color_code'];

    public function hadiths(): HasMany
    {
        return $this->hasMany(Hadith::class);
    }
}
