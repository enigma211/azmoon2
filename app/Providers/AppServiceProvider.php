<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Morilog\Jalali\Jalalian;

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
        Blade::directive('jdate', function ($expression) {
            // Usage: @jdate($date, 'Y/m/d') or @jdate($date)
            return "<?php [\$__d, \$__f] = array_pad([$expression], 2, 'Y/m/d'); echo \\Morilog\\Jalali\\Jalalian::fromDateTime(\$__d)->format(\$__f); ?>";
        });
    }
}
