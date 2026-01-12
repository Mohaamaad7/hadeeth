<?php

namespace App\Providers;


use Illuminate\Support\ServiceProvider;
use App\Models\Hadith;
use App\Models\Narrator;
use App\Observers\HadithObserver;
use App\Observers\NarratorObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Hadith::observe(HadithObserver::class);
        Narrator::observe(NarratorObserver::class);
    }
}
