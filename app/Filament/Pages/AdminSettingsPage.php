<?php

namespace App\Filament\Pages;

use App\Models\SystemSetting;
use Filament\Pages\Page;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Section;
use Filament\Notifications\Notification;

class AdminSettingsPage extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static string $view = 'filament.pages.admin-settings-page';
    protected static ?string $navigationLabel = 'تنظیمات سیستم';
    protected static ?string $title = 'تنظیمات سیستم';
    protected static ?string $navigationGroup = 'سیستم';
    protected static ?int $navigationSort = 99;

    public ?array $data = [];

    public static function getSlug(): string
    {
        return 'system-settings';
    }

    public function mount(): void
    {
        $settings = $this->getSettingsProperty();
        $this->form->fill($settings->toArray());
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('تنظیمات عمومی')
                    ->description('تنظیمات کلی سامانه')
                    ->schema([
                        TextInput::make('free_trial_hours')
                            ->label('مدت زمان اشتراک هدیه (ساعت)')
                            ->helperText('کاربران جدید پس از ثبت‌نام به این مدت دسترسی رایگان خواهند داشت.')
                            ->numeric()
                            ->required()
                            ->minValue(1)
                            ->maxValue(720)
                            ->default(48),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $settings = $this->getSettingsProperty();
        $settings->update($this->form->getState());

        Notification::make() 
            ->title('تنظیمات با موفقیت ذخیره شد')
            ->success()
            ->send();
    }

    public function getSettingsProperty(): SystemSetting
    {
        $settings = SystemSetting::first();
        if (! $settings) {
            $settings = SystemSetting::create([
                'key' => 'global',
                'free_trial_hours' => 48,
                'otp_enabled' => 'false',
                'sms_provider' => 'dummy',
            ]);
        }
        return $settings;
    }
}
