<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Narrator;
use App\Enums\NarratorRank;

class NarratorObserver
{
    /**
     * Handle the Narrator "saving" event.
     *
     * - ضبط is_companion تلقائياً بناءً على rank.
     * - مسح judgment إذا كانت الرتبة صحابي/صحابية (لأن الصحابة عدول).
     * - مزامنة grade_status القديم مع rank الجديد (backward compat).
     */
    public function saving(Narrator $narrator): void
    {
        // ضبط is_companion من rank
        if ($narrator->rank) {
            $narrator->is_companion = $narrator->rank->isCompanion();

            // الصحابة عدول — لا حاجة لحكم العلماء
            if ($narrator->rank->isCompanion()) {
                $narrator->judgment = null;
            }

            // مزامنة grade_status القديم (backward compatibility)
            $narrator->grade_status = $narrator->rank->label();
        } else {
            // Fallback: backward compat مع الكود القديم
            $narrator->is_companion = ($narrator->grade_status === 'صحابي' || $narrator->grade_status === 'صحابية');
        }
    }
}
