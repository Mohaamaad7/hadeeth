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
        $query = Hadith::with(['book', 'narrator', 'sources']);

        // Text search
        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function($q) use ($search) {
                $q->where('content', 'like', "%{$search}%")
                  ->orWhere('content_searchable', 'like', "%{$search}%");
            });
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
            $query->whereHas('sources', function($q) use ($request) {
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
        $hadith = Hadith::with(['book', 'narrator', 'sources', 'chains.source', 'chains.narrators'])
            ->findOrFail($id);

        // Get related hadiths (same book or same narrator)
        $relatedHadiths = Hadith::with(['book', 'narrator'])
            ->where('id', '!=', $hadith->id)
            ->where(function($query) use ($hadith) {
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
        $hadith = Hadith::inRandomOrder()->first();

        if (!$hadith) {
            return redirect()->route('home')
                ->with('error', 'لا توجد أحاديث في قاعدة البيانات');
        }

        return redirect()->route('hadith.show', $hadith->id);
    }
}
