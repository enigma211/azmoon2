<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\URL;
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
        if ($this->app->environment('production') || str_contains(config('app.url'), 'https')) {
            URL::forceScheme('https');
        }

        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('system_settings')) {
                $settings = \App\Models\SystemSetting::first();
                if ($settings) {
                    if ($settings->site_name) {
                        config(['app.name' => $settings->site_name]);
                    }
                    
                    if ($settings->seo_title) {
                        View::share('seoTitle', $settings->seo_title);
                    }
                    if ($settings->seo_description) {
                        View::share('seoDescription', $settings->seo_description);
                    }
                    if ($settings->seo_keywords) {
                        View::share('seoKeywords', $settings->seo_keywords);
                    }
                    if ($settings->site_identity) {
                        View::share('siteIdentity', $settings->site_identity);
                    }
                }
            }
        } catch (\Exception $e) {
            // Ignored during migration
        }

        Blade::directive('jdate', function ($expression) {
            // Usage: @jdate($date, 'Y/m/d') or @jdate($date)
            return "<?php [\$__d, \$__f] = array_pad([$expression], 2, 'Y/m/d'); echo \\Morilog\\Jalali\\Jalalian::fromDateTime(\$__d)->format(\$__f); ?>";
        });
    }
}
