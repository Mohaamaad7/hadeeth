<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use App\Traits\HasDiacriticStripper;


class Hadith extends Model
{
    use HasDiacriticStripper;

    protected $fillable = [
        'content',
        'raw_text',
        'content_searchable',
        'explanation',
        'number_in_book',
        'grade',
        'status',
        'book_id',
        'narrator_id',
        'entered_by',
        'reviewed_by',
        'reviewed_at',
        'review_notes',
        'additions',
        'sharh_context',
        'sharh_obstacles',
        'sharh_commands',
        'sharh_conclusion',
    ];

    protected $casts = [
        'additions' => 'array',
        'sharh_obstacles' => 'array',
        'sharh_commands' => 'array',
        'reviewed_at' => 'datetime',
    ];

    // ===== Relationships =====

    /**
     * Get the book that this hadith belongs to.
     */
    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    /**
     * Get the narrator (legacy single relation).
     */
    public function narrator(): BelongsTo
    {
        return $this->belongsTo(Narrator::class);
    }

    /**
     * رواة الحديث (Many-to-Many).
     */
    public function narrators(): BelongsToMany
    {
        return $this->belongsToMany(Narrator::class, 'hadith_narrator');
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

    /**
     * المستخدم الذي أدخل هذا الحديث.
     */
    public function enteredBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'entered_by');
    }

    /**
     * المستخدم الذي راجع هذا الحديث.
     */
    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    // ===== Scopes =====

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected(Builder $query): Builder
    {
        return $query->where('status', 'rejected');
    }

    // ===== Status Helpers =====

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    /**
     * اسم الحالة بالعربي.
     */
    public function getStatusNameAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'بانتظار المراجعة',
            'approved' => 'معتمد',
            'rejected' => 'مرفوض',
            default => $this->status,
        };
    }

    /**
     * لون الـ badge حسب الحالة.
     */
    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'warning',
            'approved' => 'success',
            'rejected' => 'danger',
            default => 'secondary',
        };
    }
}
