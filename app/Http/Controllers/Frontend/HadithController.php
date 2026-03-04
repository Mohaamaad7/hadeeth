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
            $searchClean = preg_replace('/[+\-><\(\)~*\"@]+/', ' ', $search);
            $searchClean = trim($searchClean);

            if (mb_strlen($searchClean) >= 2) {
                // Use FULLTEXT (MATCH AGAINST) on the indexed column
                $query->whereRaw(
                    'MATCH(content_searchable) AGAINST(? IN BOOLEAN MODE)',
                    [$searchClean . '*']
                )
                    ->addSelect(['*'])
                    ->selectRaw(
                        'MATCH(content_searchable) AGAINST(? IN BOOLEAN MODE) as relevance',
                        [$searchClean . '*']
                    )
                    ->orderByDesc('relevance');
            } else {
                // Fallback to LIKE for very short queries
                $query->where('content', 'like', "%{$search}%");
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
