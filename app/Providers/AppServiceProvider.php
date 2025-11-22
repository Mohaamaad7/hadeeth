<?php

namespace App\Providers;


use Illuminate\Support\ServiceProvider;
use App\Models\Hadith;
use App\Observers\HadithObserver;

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
    }
}
