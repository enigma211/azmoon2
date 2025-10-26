<?php

namespace App\Filament\Pages;

use App\Models\SystemSetting;
use Filament\Pages\Page;

class AdminSettingsPage extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static string $view = 'filament.pages.admin-settings-page';
    protected static ?string $navigationLabel = 'تنظیمات پیامک';
    protected static ?string $title = 'تنظیمات پیامک';
    protected static ?string $navigationGroup = 'سیستم';
    protected static ?int $navigationSort = 99;
    // Re-enable navigation
    // protected static bool $shouldRegisterNavigation = false;

    public static function getSlug(): string
    {
        return 'system-settings';
    }

    public function getSettingsProperty(): SystemSetting
    {
        $settings = SystemSetting::first();
        if (! $settings) {
            $settings = SystemSetting::create([
                'key' => 'global',
                'otp_enabled' => 'false',
                'sms_provider' => 'dummy',
            ]);
        }
        return $settings;
    }
}
