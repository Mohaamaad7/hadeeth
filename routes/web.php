<?php

use App\Models\Book;
use App\Models\Hadith;
use App\Models\Source;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home', [
        'latestHadiths' => Hadith::with(['book', 'narrator', 'sources'])
            ->latest()
            ->take(3)
            ->get(),
        'totalHadiths' => Hadith::count(),
        'totalBooks' => Book::count(),
        'totalSources' => Source::count(),
    ]);
});
