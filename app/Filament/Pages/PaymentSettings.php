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
                    ->description('برای دریافت Merchant ID به پنل زیبال مراجعه کنید: https://zibal.ir')
                    ->schema([
                        TextInput::make('merchant_id')
                            ->label('Merchant ID')
                            ->placeholder('zibal')
                            ->helperText('برای تست از مقدار "zibal" استفاده کنید')
                            ->required()
                            ->maxLength(255),
                    ])
                    ->columns(1),

                Section::make('اطلاعات تکمیلی')
                    ->schema([
                        TextInput::make('callback_url')
                            ->label('آدرس بازگشت (Callback URL)')
                            ->default(url('/payment/verify'))
                            ->disabled()
                            ->helperText('این آدرس به صورت خودکار تنظیم می‌شود'),
                    ])
                    ->columns(1),
            ])
            ->statePath('data');
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('ذخیره تنظیمات')
                ->submit('save'),
        ];
    }

    public function save(): void
    {
        $data = $this->form->getState();

        // Update .env file
        $this->updateEnvFile([
            'ZIBAL_MERCHANT_ID' => $data['merchant_id'],
        ]);

        Notification::make()
            ->success()
            ->title('تنظیمات با موفقیت ذخیره شد')
            ->body('تنظیمات درگاه پرداخت زیبال بروزرسانی شد.')
            ->send();
    }

    protected function updateEnvFile(array $data): void
    {
        $envFile = base_path('.env');
        $envContent = file_get_contents($envFile);

        foreach ($data as $key => $value) {
            $pattern = "/^{$key}=.*/m";
            $replacement = "{$key}={$value}";

            if (preg_match($pattern, $envContent)) {
                $envContent = preg_replace($pattern, $replacement, $envContent);
            } else {
                $envContent .= "\n{$replacement}";
            }
        }

        file_put_contents($envFile, $envContent);
    }
}
