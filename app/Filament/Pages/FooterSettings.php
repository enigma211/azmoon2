<?php

namespace App\Filament\Pages;

use App\Models\SiteSetting;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Notifications\Notification;

class FooterSettings extends Page implements HasForms
{
    use InteractsWithForms;
    
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static string $view = 'filament.pages.footer-settings';

    protected static ?string $navigationGroup = 'تنظیمات';

    protected static ?int $navigationSort = 12;

    public ?array $data = [];

    public static function getNavigationLabel(): string
    {
        return 'تنظیمات فوتر';
    }

    public static function getSlug(): string
    {
        return 'footer-settings';
    }

    public function mount(): void
    {
        $settings = SiteSetting::first();
        $this->form->fill([
            'footer_column_1' => $settings?->footer_column_1 ?? '',
            'footer_column_2' => $settings?->footer_column_2 ?? '',
            'footer_column_3' => $settings?->footer_column_3 ?? '',
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('تنظیمات فوتر سایت')
                    ->description('محتوای HTML هر ستون فوتر را وارد کنید. می‌توانید از تگ‌های HTML استفاده کنید.')
                    ->schema([
                        Forms\Components\Textarea::make('footer_column_1')
                            ->label('ستون اول فوتر')
                            ->helperText('کد HTML برای ستون اول (مثلاً: درباره ما، لینک‌ها)')
                            ->rows(10)
                            ->placeholder('<h3>درباره ما</h3>
<p>توضیحات کوتاه درباره سایت...</p>
<ul>
  <li><a href="#">لینک 1</a></li>
  <li><a href="#">لینک 2</a></li>
</ul>'),

                        Forms\Components\Textarea::make('footer_column_2')
                            ->label('ستون دوم فوتر')
                            ->helperText('کد HTML برای ستون دوم (مثلاً: لینک‌های مفید)')
                            ->rows(10)
                            ->placeholder('<h3>لینک‌های مفید</h3>
<ul>
  <li><a href="#">صفحه اصلی</a></li>
  <li><a href="#">تماس با ما</a></li>
  <li><a href="#">قوانین و مقررات</a></li>
</ul>'),

                        Forms\Components\Textarea::make('footer_column_3')
                            ->label('ستون سوم فوتر')
                            ->helperText('کد HTML برای ستون سوم (مثلاً: شبکه‌های اجتماعی، تماس)')
                            ->rows(10)
                            ->placeholder('<h3>تماس با ما</h3>
<p>ایمیل: info@example.com</p>
<p>تلفن: 021-12345678</p>
<div class="social-links">
  <a href="#">اینستاگرام</a>
  <a href="#">تلگرام</a>
</div>'),
                    ])
                    ->columns(1),
            ])
            ->statePath('data');
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('ذخیره تغییرات')
                ->submit('save'),
        ];
    }

    public function save(): void
    {
        $data = $this->form->getState();

        $settings = SiteSetting::firstOrCreate([]);
        $settings->update([
            'footer_column_1' => $data['footer_column_1'] ?? null,
            'footer_column_2' => $data['footer_column_2'] ?? null,
            'footer_column_3' => $data['footer_column_3'] ?? null,
        ]);

        Notification::make()
            ->success()
            ->title('تنظیمات فوتر ذخیره شد')
            ->body('محتوای فوتر با موفقیت به‌روزرسانی شد.')
            ->send();
    }
}
