<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Dashboard\HadithController as DashboardHadithController;
use App\Http\Controllers\Dashboard\BookController;
use App\Http\Controllers\Dashboard\NarratorController;
use App\Http\Controllers\Dashboard\SourceController;
use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Frontend\HadithController as FrontendHadithController;
use App\Http\Controllers\Frontend\NarratorController as FrontendNarratorController;
use Illuminate\Support\Facades\Route;

// Frontend Routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/search', [FrontendHadithController::class, 'search'])->name('search');
Route::get('/hadith/{id}', [FrontendHadithController::class, 'show'])->name('hadith.show');
Route::get('/random-hadith', [FrontendHadithController::class, 'random'])->name('hadith.random');
Route::get('/narrator/{id}', [FrontendNarratorController::class, 'show'])->name('narrator.show');

Auth::routes();

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Dashboard CRUD Routes
    Route::prefix('dashboard')->name('dashboard.')->group(function () {
        // Books CRUD
        Route::resource('books', BookController::class);
        
        // Narrators CRUD
        Route::resource('narrators', NarratorController::class);
        
        // Sources CRUD
        Route::resource('sources', SourceController::class);
        
        // Users CRUD
        Route::resource('users', UserController::class);
        
        // Hadiths CRUD
        Route::resource('hadiths', DashboardHadithController::class);
        Route::post('hadiths/parse', [DashboardHadithController::class, 'parseRawText'])->name('hadiths.parse');
    });
});
