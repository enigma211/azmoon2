<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Morilog\Jalali\Jalalian;

class JalaliServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Jalali package is ready to use
        // No additional configuration needed
    }
}
