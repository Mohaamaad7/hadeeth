<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class HadithChain extends Model
{
    protected $fillable = [
        'hadith_id',
        'source_id',
        'description',
    ];

    /**
     * Get the hadith that this chain belongs to.
     */
    public function hadith(): BelongsTo
    {
        return $this->belongsTo(Hadith::class);
    }

    /**
     * Get the source for this chain.
     */
    public function source(): BelongsTo
    {
        return $this->belongsTo(Source::class);
    }

    /**
     * Get the narrators in this chain with their positions.
     */
    public function narrators(): BelongsToMany
    {
        return $this->belongsToMany(Narrator::class, 'chain_narrators', 'chain_id', 'narrator_id')
            ->withPivot(['position', 'role'])
            ->withTimestamps()
            ->orderBy('chain_narrators.position');
    }

    /**
     * Get the first narrator (المصنف).
     */
    public function firstNarrator()
    {
        return $this->narrators()->wherePivot('position', 1)->first();
    }

    /**
     * Get the last narrator (الصحابي).
     */
    public function lastNarrator()
    {
        return $this->narrators()->orderByDesc('chain_narrators.position')->first();
    }
}
