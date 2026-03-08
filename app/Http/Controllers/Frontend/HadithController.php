<?php

declare(strict_types=1);

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Hadith;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class HadithController extends Controller
{
    /**
     * Search hadiths.
     */
    public function search(Request $request): View
    {
        $query = Hadith::with(['book', 'narrators', 'sources'])->approved();

        // Text search — FULLTEXT (fast) with LIKE fallback for short queries
        if ($request->filled('q')) {
            $search = $request->q;
            // Strip Arabic diacritics (tashkeel) to match content_searchable column
            $searchNoDiacritics = preg_replace('/[\x{064B}-\x{0652}\x{0640}]/u', '', $search);
            $searchClean = preg_replace('/[+\-><\(\)~*\"@]+/', ' ', $searchNoDiacritics);
            $searchClean = trim($searchClean);

            if (mb_strlen($searchClean) >= 2) {
                // Prefix each word with + to require ALL words (AND mode)
                $words = preg_split('/\s+/', $searchClean, -1, PREG_SPLIT_NO_EMPTY);
                $booleanQuery = implode(' ', array_map(fn($w) => '+' . $w, $words));

                // Use FULLTEXT (MATCH AGAINST) on the indexed column, with LIKE fallback
                $query->where(function ($q) use ($booleanQuery, $searchNoDiacritics) {
                    $q->whereRaw(
                        'MATCH(content_searchable) AGAINST(? IN BOOLEAN MODE)',
                        [$booleanQuery]
                    )
                        ->orWhere('content_searchable', 'like', "%{$searchNoDiacritics}%");
                })
                    ->addSelect(['*'])
                    ->selectRaw(
                        'MATCH(content_searchable) AGAINST(? IN BOOLEAN MODE) as relevance',
                        [$booleanQuery]
                    )
                    ->orderByDesc('relevance');
            } else {
                // Fallback to LIKE for very short queries
                $query->where(function ($q) use ($search, $searchNoDiacritics) {
                    $q->where('content', 'like', "%{$search}%")
                        ->orWhere('content_searchable', 'like', "%{$searchNoDiacritics}%");
                });
            }
        }

        // Filter by grade
        if ($request->filled('grade')) {
            $query->where('grade', $request->grade);
        }

        // Filter by book
        if ($request->filled('book_id')) {
            $query->where('book_id', $request->book_id);
        }

        // Filter by source
        if ($request->filled('source')) {
            $query->whereHas('sources', function ($q) use ($request) {
                $q->where('code', $request->source);
            });
        }

        $hadiths = $query->paginate(10)->withQueryString();
        $books = Book::mainBooks()->orderBy('sort_order')->get();

        return view('frontend.search', compact('hadiths', 'books'));
    }

    /**
     * Show hadith details.
     */
    public function show(int $id): View
    {
        $hadith = Hadith::with(['book', 'narrators', 'sources', 'chains.source', 'chains.narrators'])
            ->approved()
            ->findOrFail($id);

        // Get related hadiths (same book or same narrator)
        $relatedHadiths = Hadith::with(['book', 'narrators'])
            ->approved()
            ->where('id', '!=', $hadith->id)
            ->where(function ($query) use ($hadith) {
                $query->where('book_id', $hadith->book_id)
                    ->orWhere('narrator_id', $hadith->narrator_id);
            })
            ->take(4)
            ->get();

        return view('frontend.hadith-show', compact('hadith', 'relatedHadiths'));
    }

    /**
     * Show random hadith.
     */
    public function random(): RedirectResponse
    {
        $hadith = Hadith::approved()->inRandomOrder()->first();

        if (!$hadith) {
            return redirect()->route('home')
                ->with('error', 'لا توجد أحاديث في قاعدة البيانات');
        }

        return redirect()->route('hadith.show', $hadith->id);
    }
}
