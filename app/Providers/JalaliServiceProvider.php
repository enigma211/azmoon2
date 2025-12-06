<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Ariaieboy\Jalali\Jalali;

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
        // Jalali package (ariaieboy/jalali) is ready to use
        // No additional configuration needed
    }
}
