<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Book extends Model
{
    protected $fillable = ['name', 'sort_order', 'parent_id'];

    // Self-referencing: Parent Book (Kitab)
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Book::class, 'parent_id');
    }

    // Self-referencing: Children Books (Babs)
    public function children(): HasMany
    {
        return $this->hasMany(Book::class, 'parent_id');
    }

    // Query Scope: Get only Main Books (Kitabs)
    public function scopeMainBooks(Builder $query): Builder
    {
        return $query->whereNull('parent_id');
    }

    public function hadiths(): HasMany
    {
        return $this->hasMany(Hadith::class);
    }
}
