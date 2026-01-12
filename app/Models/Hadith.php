<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Traits\HasDiacriticStripper;


class Hadith extends Model
{
    use HasDiacriticStripper;

    protected $fillable = [
        'content',
        'content_searchable',
        'explanation',
        'number_in_book',
        'grade',
        'book_id',
        'narrator_id',
        'sharh_context',
        'sharh_obstacles',
        'sharh_commands',
        'sharh_conclusion',
    ];

    protected $casts = [
        'sharh_obstacles' => 'array',
        'sharh_commands' => 'array',
    ];

    /**
     * Get the book that this hadith belongs to.
     */
    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    /**
     * Get the narrator that this hadith belongs to.
     */
    public function narrator(): BelongsTo
    {
        return $this->belongsTo(Narrator::class);
    }

    /**
     * The sources that this hadith belongs to.
     */
    public function sources(): BelongsToMany
    {
        return $this->belongsToMany(Source::class, 'hadith_source');
    }

    /**
     * Get the chains (سلاسل الإسناد) for this hadith.
     */
    public function chains(): HasMany
    {
        return $this->hasMany(HadithChain::class);
    }
}
