<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class ActivityLogsPage extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static string $view = 'filament.pages.activity-logs-page';
    protected static ?string $navigationLabel = 'لاگ و رخدادها';
    protected static ?string $navigationGroup = 'تنظیمات';
    protected static ?int $navigationSort = 3;

    public function mount(): void
    {
        // Redirect to the Livewire admin logs page
        redirect()->route('admin.logs');
    }
}
