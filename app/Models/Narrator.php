<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Narrator extends Model
{
    protected $fillable = ['name', 'bio', 'grade_status', 'color_code', 'is_companion'];

    protected $casts = [
        'is_companion' => 'boolean',
    ];

    /**
     * Scope: الصحابة فقط
     */
    public function scopeCompanions($query)
    {
        return $query->where('is_companion', true);
    }

    public function hadiths(): HasMany
    {
        return $this->hasMany(Hadith::class);
    }

    /**
     * Get all chains this narrator appears in.
     */
    public function chains(): BelongsToMany
    {
        return $this->belongsToMany(HadithChain::class, 'chain_narrators', 'narrator_id', 'chain_id')
            ->withPivot(['position', 'role'])
            ->withTimestamps();
    }
}
