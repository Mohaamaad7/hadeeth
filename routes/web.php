<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Dashboard\HadithController as DashboardHadithController;
use App\Http\Controllers\Dashboard\BookController;
use App\Http\Controllers\Dashboard\NarratorController;
use App\Http\Controllers\Dashboard\SourceController;
use App\Http\Controllers\Dashboard\UserController;
use App\Http\Controllers\Dashboard\ReviewController;
use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Frontend\HadithController as FrontendHadithController;
use App\Http\Controllers\Frontend\NarratorController as FrontendNarratorController;
use App\Http\Controllers\Frontend\BookController as FrontendBookController;
use App\Http\Controllers\Frontend\TopicController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\Dashboard\CleanupController;
use Illuminate\Support\Facades\Route;

// SEO Routes
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');
Route::get('/robots.txt', [SitemapController::class, 'robots'])->name('robots');

// Frontend Routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/search', [FrontendHadithController::class, 'search'])->name('search');
Route::get('/hadith/{id}/{slug?}', [App\Http\Controllers\Frontend\HadithController::class, 'show'])->name('hadith.show');
Route::get('/random-hadith', [FrontendHadithController::class, 'random'])->name('hadith.random');
Route::get('/narrator/{id}', [FrontendNarratorController::class, 'show'])->name('narrator.show');
Route::get('/books', [FrontendBookController::class, 'index'])->name('books.index');
Route::get('/books/{book}', [FrontendBookController::class, 'show'])->name('books.show');
Route::get('/books/{book}/pdf', [FrontendBookController::class, 'exportPdf'])->name('books.pdf')->middleware('throttle:5,1');
Route::get('/topics', [TopicController::class, 'index'])->name('topics.index');
Route::get('/topics/{slug}', [TopicController::class, 'show'])->name('topics.show');
Route::get('/about', function () {
    return view('frontend.about');
})->name('about');

// Custom Auth Routes with hidden login URL for security
Auth::routes(['login' => false, 'register' => false]);

// Hidden login route - /reyada instead of /login
Route::get('/reyada', [App\Http\Controllers\Auth\LoginController::class, 'showLoginForm'])->name('login');
Route::post('/reyada', [App\Http\Controllers\Auth\LoginController::class, 'login']);
Route::post('/logout', [App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::prefix('dashboard')->name('dashboard.')->group(function () {

        // ═══════════════════════════════════════════════
        // إدخال البيانات (الكل: admin + data_entry + reviewer)
        // ═══════════════════════════════════════════════

        // Books CRUD
        Route::get('books/{book}/chapters', [BookController::class, 'getChapters'])->name('books.chapters');
        Route::resource('books', BookController::class);

        // Narrators CRUD + AJAX
        Route::resource('narrators', NarratorController::class);
        Route::get('narrators-search', [NarratorController::class, 'search'])->name('narrators.search');
        Route::post('narrators-quick', [NarratorController::class, 'quickStore'])->name('narrators.quick-store');

        // Sources CRUD
        Route::resource('sources', SourceController::class);
        Route::get('sources-search', [SourceController::class, 'search'])->name('sources.search');
        Route::post('sources-quick', [SourceController::class, 'quickStore'])->name('sources.quick-store');

        // Hadiths CRUD
        Route::resource('hadiths', DashboardHadithController::class);
        Route::post('hadiths/parse', [DashboardHadithController::class, 'parseRawText'])->name('hadiths.parse');
        Route::get('hadiths-bulk', [DashboardHadithController::class, 'bulkCreate'])->name('hadiths.bulk.create');
        Route::post('hadiths-bulk/preview', [DashboardHadithController::class, 'bulkPreview'])->name('hadiths.bulk.preview');
        Route::post('hadiths-bulk/store', [DashboardHadithController::class, 'bulkStore'])->name('hadiths.bulk.store');

        // ═══════════════════════════════════════════════
        // المراجعة (reviewer + admin)
        // ═══════════════════════════════════════════════
        Route::middleware('role:reviewer,admin')->group(function () {
            Route::get('review', [ReviewController::class, 'index'])->name('review.index');
            Route::get('review/{hadith}', [ReviewController::class, 'show'])->name('review.show');
            Route::post('review/{hadith}/approve', [ReviewController::class, 'approve'])->name('review.approve');
            Route::post('review/{hadith}/reject', [ReviewController::class, 'reject'])->name('review.reject');
        });

        // الاعتماد الجماعي (admin فقط)
        Route::middleware('role:admin')->group(function () {
            Route::post('review/bulk-approve', [ReviewController::class, 'bulkApprove'])->name('review.bulk-approve');
            Route::post('review/approve-all', [ReviewController::class, 'approveAll'])->name('review.approve-all');

            // Users CRUD
            Route::resource('users', UserController::class);

            // Database Cleanup
            Route::get('cleanup', [CleanupController::class, 'index'])->name('cleanup.index');
            Route::post('cleanup/hadiths', [CleanupController::class, 'deleteHadiths'])->name('cleanup.hadiths');
            Route::post('cleanup/narrators/orphan', [CleanupController::class, 'deleteOrphanNarrators'])->name('cleanup.narrators.orphan');
            Route::post('cleanup/narrators/all', [CleanupController::class, 'deleteAllNarrators'])->name('cleanup.narrators.all');
            Route::post('cleanup/books/empty', [CleanupController::class, 'deleteEmptyChapters'])->name('cleanup.books.empty');
            Route::post('cleanup/books/all', [CleanupController::class, 'deleteAllBooks'])->name('cleanup.books.all');
            Route::post('cleanup/sources/orphan', [CleanupController::class, 'deleteOrphanSources'])->name('cleanup.sources.orphan');
            Route::post('cleanup/chains', [CleanupController::class, 'deleteChains'])->name('cleanup.chains');
            Route::post('cleanup/nuke', [CleanupController::class, 'nukeAll'])->name('cleanup.nuke');
        });
    });
});
