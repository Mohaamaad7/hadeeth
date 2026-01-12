<?php

declare(strict_types=1);

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Narrator;
use Illuminate\View\View;

class NarratorController extends Controller
{
    /**
     * Show narrator profile with chains and hadiths.
     */
    public function show(int $id): View
    {
        $narrator = Narrator::withCount(['hadiths', 'chains'])
            ->with(['hadiths' => function($query) {
                $query->with(['book', 'sources'])->latest()->take(10);
            }])
            ->findOrFail($id);

        // Get chains where this narrator appears
        $narratorChains = $narrator->chains()
            ->with(['source', 'hadith'])
            ->get();

        return view('frontend.narrator-show', compact('narrator', 'narratorChains'));
    }
}
