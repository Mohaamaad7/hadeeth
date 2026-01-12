<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Hadith;
use App\Traits\HasDiacriticStripper;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    use HasDiacriticStripper;

    /**
     * Handle the search request.
     *
     * @param Request $request
     * @return View
     */
    public function __invoke(Request $request): View
    {
        $validated = $request->validate([
            'q' => 'required|string|min:2|max:255',
        ]);

        $query = $validated['q'];
        $normalizedQuery = $this->stripDiacritics($query);

        $results = Hadith::search($normalizedQuery)->paginate(15);

        return view('frontend.search-results', [
            'results' => $results,
            'query' => $query,
        ]);
    }
}
