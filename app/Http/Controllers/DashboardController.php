<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Hadith;
use App\Models\Narrator;
use App\Models\Source;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     */
    public function index(): View
    {
        $stats = [
            'total_hadiths' => Hadith::count(),
            'total_books' => Book::count(),
            'total_narrators' => Narrator::count(),
            'total_sources' => Source::count(),
        ];

        $recent_hadiths = Hadith::with(['book', 'narrator'])
            ->latest()
            ->take(10)
            ->get();

        return view('dashboard', compact('stats', 'recent_hadiths'));
    }
}
