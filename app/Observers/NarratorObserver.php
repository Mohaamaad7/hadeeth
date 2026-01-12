<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Narrator;

class NarratorObserver
{
    /**
     * Handle the Narrator "saving" event.
     * 
     * Automatically set is_companion based on grade_status.
     * If grade_status is "صحابي", set is_companion to true.
     * Otherwise, set it to false.
     */
    public function saving(Narrator $narrator): void
    {
        // Auto-set is_companion based on grade_status
        $narrator->is_companion = ($narrator->grade_status === 'صحابي');
    }
}
