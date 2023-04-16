<?php

namespace App\Providers;

use App\Console\Commands\TMDB;
use App\Repositories\MovieRepository;
use App\Services\Movie\MovieService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
