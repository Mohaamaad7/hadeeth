<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Traits\HasDiacriticStripper;
use Laravel\Scout\Searchable;


class Hadith extends Model
{
    use HasDiacriticStripper, Searchable;
    /**
     * Get the data array for full-text search.
     */
    public function toSearchableArray(): array
    {
        return [
            'content_searchable' => $this->content_searchable,
            'grade' => $this->grade,
            'narrator_name' => $this->narrator?->name,
            'book_name' => $this->book?->name,
        ];
    }

    protected $fillable = [
        'content',
        'content_searchable',
        'explanation',
        'number_in_book',
        'grade',
        'book_id',
        'narrator_id',
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
}
