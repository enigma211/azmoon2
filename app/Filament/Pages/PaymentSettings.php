<?php

namespace App\Filament\Pages;

use Filament\Actions\Action;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class PaymentSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';
    protected static ?string $navigationLabel = 'تنظیمات درگاه پرداخت';
    protected static ?string $title = 'تنظیمات درگاه پرداخت زیبال';
    protected static string $view = 'filament.pages.payment-settings';
    protected static ?string $navigationGroup = 'تنظیمات';
    protected static ?int $navigationSort = 3;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'merchant_id' => env('ZIBAL_MERCHANT_ID', 'zibal'),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('تنظیمات درگاه زیبال')
                    ->description('برای تغییر Merchant ID، فایل .env را ویرایش کنید. پنل زیبال: https://zibal.ir')
                    ->schema([
                        TextInput::make('merchant_id')
                            ->label('Merchant ID')
                            ->helperText('این مقدار از فایل .env خوانده می‌شود')
                            ->disabled()
                            ->dehydrated(false),
                    ])
                    ->columns(1),

                Section::make('اطلاعات تکمیلی')
                    ->schema([
                        TextInput::make('callback_url')
                            ->label('آدرس بازگشت (Callback URL)')
                            ->default(url('/payment/verify'))
                            ->disabled()
                            ->dehydrated(false)
                            ->helperText('این آدرس به صورت خودکار تنظیم می‌شود'),
                        
                        TextInput::make('status')
                            ->label('وضعیت')
                            ->default('فعال ✓')
                            ->disabled()
                            ->dehydrated(false)
                            ->helperText('درگاه پرداخت زیبال فعال است'),
                    ])
                    ->columns(1),
            ])
            ->statePath('data');
    }

    protected function getFormActions(): array
    {
        return [];
    }
}
