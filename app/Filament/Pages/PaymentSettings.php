<?php

namespace App\Filament\Pages;

use Filament\Actions\Action;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Notifications\Notification;

class PaymentSettings extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-credit-card';

    protected static string $view = 'filament.pages.payment-settings';

    protected static ?string $navigationGroup = 'تنظیمات';

    protected static ?int $navigationSort = 10;

    public ?array $data = [];

    public static function getNavigationLabel(): string
    {
        return 'تنظیمات درگاه پرداخت';
    }

    public function getTitle(): string
    {
        return 'تنظیمات درگاه پرداخت زرین‌پال';
    }

    public function mount(): void
    {
        $this->form->fill([
            'merchant_id' => env('ZARINPAL_MERCHANT_ID', ''),
            'sandbox' => env('ZARINPAL_SANDBOX', false),
            'zaringate' => env('ZARINPAL_ZARINGATE', false),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('تنظیمات زرین‌پال')
                    ->description('اطلاعات درگاه پرداخت زرین‌پال را وارد کنید')
                    ->schema([
                        Forms\Components\TextInput::make('merchant_id')
                            ->label('Merchant ID')
                            ->placeholder('XXXXXXXX-XXXX-XXXX-XXXX-XXXXXXXXXXXX')
                            ->helperText('کد مرچنت خود را از پنل زرین‌پال دریافت کنید')
                            ->required()
                            ->maxLength(36)
                            ->columnSpanFull(),

                        Forms\Components\Toggle::make('sandbox')
                            ->label('حالت تست (Sandbox)')
                            ->helperText('برای تست پرداخت فعال کنید. در محیط واقعی غیرفعال باشد.')
                            ->default(false)
                            ->inline(false),

                        Forms\Components\Toggle::make('zaringate')
                            ->label('زرین‌گیت (ZarinGate)')
                            ->helperText('پرداخت مستقیم بدون انتقال به صفحه زرین‌پال')
                            ->default(false)
                            ->inline(false),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('راهنما')
                    ->description('نحوه دریافت Merchant ID')
                    ->schema([
                        Forms\Components\Placeholder::make('guide')
                            ->label('')
                            ->content('
                                1. وارد پنل زرین‌پال شوید: https://www.zarinpal.com/panel
                                2. از منوی سمت راست، گزینه "درگاه‌های پرداخت" را انتخاب کنید
                                3. Merchant ID خود را کپی کنید
                                4. در فیلد بالا وارد کنید و ذخیره کنید
                            ')
                            ->columnSpanFull(),
                    ])
                    ->collapsed(),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        // Update .env file
        $this->updateEnvFile([
            'ZARINPAL_MERCHANT_ID' => $data['merchant_id'],
            'ZARINPAL_SANDBOX' => $data['sandbox'] ? 'true' : 'false',
            'ZARINPAL_ZARINGATE' => $data['zaringate'] ? 'true' : 'false',
        ]);

        Notification::make()
            ->success()
            ->title('تنظیمات ذخیره شد')
            ->body('تنظیمات درگاه پرداخت با موفقیت ذخیره شد.')
            ->send();
    }

    protected function updateEnvFile(array $data): void
    {
        $envFile = base_path('.env');
        $envContent = file_get_contents($envFile);

        foreach ($data as $key => $value) {
            // Check if key exists
            if (preg_match("/^{$key}=/m", $envContent)) {
                // Update existing key
                $envContent = preg_replace(
                    "/^{$key}=.*/m",
                    "{$key}={$value}",
                    $envContent
                );
            } else {
                // Add new key at the end
                $envContent .= "\n{$key}={$value}";
            }
        }

        file_put_contents($envFile, $envContent);

        // Clear config cache
        \Artisan::call('config:clear');
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('ذخیره تنظیمات')
                ->action('save'),
        ];
    }
}
