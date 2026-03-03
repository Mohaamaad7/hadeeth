<?php

declare(strict_types=1);

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Book;
use Illuminate\View\View;

class BookController extends Controller
{
    /**
     * Display books index — all main books as grid cards.
     */
    public function index(): View
    {
        $books = Book::mainBooks()
            ->withCount(['hadiths', 'children'])
            ->with([
                'children' => function ($q) {
                    $q->withCount('hadiths')->orderBy('sort_order');
                }
            ])
            ->orderBy('sort_order')
            ->get();

        // Count total hadiths including children's hadiths
        foreach ($books as $book) {
            $childrenHadithsCount = $book->children->sum('hadiths_count');
            $book->total_hadiths = $book->hadiths_count + $childrenHadithsCount;
        }

        return view('frontend.books.index', compact('books'));
    }

    /**
     * Show a single book: its chapters (if any) or its hadiths directly.
     */
    public function show(Book $book): View
    {
        $book->load(['parent']);

        // If this book has children (chapters), show them
        if ($book->children()->count() > 0) {
            $chapters = $book->children()
                ->withCount('hadiths')
                ->orderBy('sort_order')
                ->get();

            return view('frontend.books.show', [
                'book' => $book,
                'chapters' => $chapters,
                'hadiths' => null,
                'parentBook' => $book->parent,
            ]);
        }

        // Otherwise show hadiths directly (book without chapters, or a chapter itself)
        $hadiths = $book->hadiths()
            ->where('status', 'approved')
            ->with(['narrator', 'sources'])
            ->orderBy('number_in_book')
            ->paginate(20);

        return view('frontend.books.show', [
            'book' => $book,
            'chapters' => null,
            'hadiths' => $hadiths,
            'parentBook' => $book->parent,
        ]);
    }

    /**
     * Export a book/chapter as interactive PDF.
     * ?type=original → uses raw_text (النص الأصلي)
     * Default → uses content (النص المستخرج)
     */
    public function exportPdf(Book $book)
    {
        $book->load(['parent']);
        $parentBook = $book->parent;
        $useOriginal = request('type') === 'original';

        // Determine if this is a main book with chapters
        $chapters = null;
        $hadiths = collect();
        $totalHadiths = 0;

        if ($book->children()->count() > 0) {
            // Main book with chapters: load all chapters and their hadiths
            $chapters = $book->children()
                ->withCount('hadiths')
                ->with([
                    'hadiths' => function ($q) {
                        $q->where('status', 'approved')
                            ->with(['narrator', 'sources'])->orderBy('number_in_book');
                    }
                ])
                ->orderBy('sort_order')
                ->get();

            $totalHadiths = $chapters->sum('hadiths_count');
        } else {
            // Single chapter or book without chapters
            $hadiths = $book->hadiths()
                ->where('status', 'approved')
                ->with(['narrator', 'sources'])
                ->orderBy('number_in_book')
                ->get();

            $totalHadiths = $hadiths->count();
        }

        $html = view('frontend.books.pdf', compact(
            'book',
            'parentBook',
            'chapters',
            'hadiths',
            'totalHadiths',
            'useOriginal'
        ))->render();

        $suffix = $useOriginal ? '_الأصل' : '';
        $fileName = str_replace(' ', '_', $book->name) . $suffix . '.pdf';

        $mpdf = new \Mpdf\Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'default_font_size' => 13,
            'default_font' => 'aealarabiya',
            'direction' => 'rtl',
            'autoArabic' => true,
            'autoLangToFont' => true,
            'margin_top' => 25,
            'margin_bottom' => 20,
            'margin_left' => 20,
            'margin_right' => 20,
        ]);

        $typeLabel = $useOriginal ? 'النص الأصلي' : 'النص المستخرج';
        $mpdf->SetTitle($book->name . ' (' . $typeLabel . ') - موسوعة الحديث الصحيح');
        $mpdf->SetAuthor('موسوعة الحديث الصحيح');
        $mpdf->SetSubject($book->name);

        $mpdf->WriteHTML($html);

        // Prevent browser caching so original vs extracted always regenerates
        header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
        header('Pragma: no-cache');

        return $mpdf->Output($fileName, \Mpdf\Output\Destination::INLINE);
    }
}
