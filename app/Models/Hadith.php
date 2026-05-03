<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Str;
use App\Traits\HasDiacriticStripper;
use Spatie\Tags\HasTags;


class Hadith extends Model
{
    use HasDiacriticStripper, HasTags;

    protected static function booted()
    {
        static::saved(function ($hadith) {
            // Auto tag on create/update logic using the service
            (new \App\Services\AutoTaggerService())->tagHadith($hadith);
        });
    }

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
        return $this->belongsToMany(Narrator::class, 'hadith_narrator')->withPivot('transmission_note');
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
     * Generate a URL-friendly slug for the hadith.
     */
    protected function slug(): Attribute
    {
        return Attribute::make(
            get: function () {
                $text = strip_tags($this->content);
                // Remove diacritics
                $text = preg_replace('/[\x{0610}-\x{061A}\x{064B}-\x{065F}\x{06D6}-\x{06DC}\x{06DF}-\x{06E8}\x{06EA}-\x{06ED}]/u', '', $text);
                // Limit to 50 chars and replace spaces with dashes
                $limited = Str::limit($text, 50, '');
                return preg_replace('/\s+/u', '-', trim($limited));
            }
        );
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
