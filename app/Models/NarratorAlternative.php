<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\AlternativeNameType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NarratorAlternative extends Model
{
    protected $fillable = ['narrator_id', 'alternative_name', 'type', 'notes'];

    protected $casts = [
        'type' => AlternativeNameType::class,
    ];

    public function narrator(): BelongsTo
    {
        return $this->belongsTo(Narrator::class);
    }
}
