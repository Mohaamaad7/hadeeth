<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Narrator extends Model
{
    protected $fillable = ['name', 'fame_name', 'bio', 'grade_status', 'color_code', 'is_companion'];

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

    /**
     * @deprecated Use hadithsM2M() for the M2M relationship via hadith_narrator pivot.
     */
    public function hadiths(): HasMany
    {
        return $this->hasMany(Hadith::class);
    }

    /**
     * الأحاديث المرتبطة بالراوي عبر جدول hadith_narrator (M2M).
     */
    public function hadithsM2M(): BelongsToMany
    {
        return $this->belongsToMany(Hadith::class, 'hadith_narrator');
    }

    /**
     * الأسماء البديلة (أخطاء نساخ، تهجئات بديلة، ألقاب، كنى)
     */
    public function alternatives(): HasMany
    {
        return $this->hasMany(NarratorAlternative::class);
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
