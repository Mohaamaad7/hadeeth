<?php

declare(strict_types=1);

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Hadith;
use App\Models\Book;
use App\Models\Source;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Display the homepage.
     */
    public function index(): View
    {
        $latestHadiths = Hadith::with(['book', 'narrator', 'sources'])
            ->latest()
            ->take(3)
            ->get();

        return view('frontend.home', [
            'latestHadiths' => $latestHadiths,
            'totalHadiths' => Hadith::count(),
            'totalBooks' => Book::count(),
            'totalSources' => Source::count(),
        ]);
    }
}
