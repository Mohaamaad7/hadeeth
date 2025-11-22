<?php
declare(strict_types=1);

namespace App\Observers;

use App\Models\Hadith;
use App\Traits\HasDiacriticStripper;

class HadithObserver
{
    use HasDiacriticStripper;

    /**
     * Handle the Hadith "saving" event.
     */
    public function saving(Hadith $hadith): void
    {
        if (!empty($hadith->content)) {
            $hadith->content_searchable = $this->stripDiacritics($hadith->content);
        }
    }
}
