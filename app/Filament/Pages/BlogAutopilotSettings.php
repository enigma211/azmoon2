<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Notifications\Notification;
use App\Models\Setting;
use App\Models\Category;
use Filament\Actions\Action;
use Illuminate\Support\Facades\Artisan;

class BlogAutopilotSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-cpu-chip';
    protected static ?string $navigationGroup = 'مدیریت محتوا'; // Translated to Persian based on typical grouping
    protected static ?string $navigationLabel = 'تنظیمات ارسال خودکار (Autopilot)';
    protected static ?string $title = 'تنظیمات ارسال خودکار مقالات';
    protected static ?int $navigationSort = 10;

    protected static string $view = 'filament.pages.admin-settings-page'; // Reusing the view

    public ?array $data = [];

    protected function getHeaderActions(): array
    {
        return [
            Action::make('run_fetch')
                ->label('اجرای دستی (تست)')
                ->icon('heroicon-o-play')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('اجرای ربات دریافت خبر')
                ->modalDescription('با تایید این بخش، ربات به صورت دستی اجرا می‌شود و فرآیند دریافت اخبار، ارسال به هوش مصنوعی و انتشار انجام می‌شود. این فرآیند ممکن است چند لحظه طول بکشد.')
                ->modalSubmitActionLabel('بله، اجرا کن')
                ->action(function () {
                    try {
                        Artisan::call('news:fetch');
                        $output = Artisan::output();
                        
                        $cleanOutput = preg_replace('/\x1b\[[0-9;]*m/', '', $output);
                        $cleanOutput = trim($cleanOutput);
                        
                        Notification::make()
                            ->title('عملیات با موفقیت پایان یافت')
                            ->body("نتیجه:\n" . substr($cleanOutput, 0, 500) . (strlen($cleanOutput) > 500 ? '...' : ''))
                            ->success()
                            ->duration(10000)
                            ->send();
                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('خطا در اجرای فرآیند')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),
        ];
    }

    public function mount(): void
    {
        $settings = Setting::whereIn('key', [
            'avalai_api_key',
            'avalai_base_url',
            'autopilot_prompt',
            'autopilot_rss_feeds',
            'autopilot_category_id',
            'autopilot_min_posts_per_day',
            'autopilot_max_posts_per_day',
            'autopilot_schedule_interval',
            'autopilot_max_article_age_days',
        ])->pluck('value', 'key')->toArray();

        // Default values
        $settings['avalai_base_url'] = $settings['avalai_base_url'] ?? 'https://api.avalai.ir/v1';
        $settings['autopilot_min_posts_per_day'] = $settings['autopilot_min_posts_per_day'] ?? 1;
        $settings['autopilot_max_posts_per_day'] = $settings['autopilot_max_posts_per_day'] ?? 3;
        $settings['autopilot_schedule_interval'] = $settings['autopilot_schedule_interval'] ?? '12';
        $settings['autopilot_max_article_age_days'] = $settings['autopilot_max_article_age_days'] ?? '3';
        
        $defaultPrompt = <<<EOT
شما یک مهندس با تجربه، متخصص سئو و تولید محتوای آموزشی برای داوطلبان آزمون‌های نظام مهندسی هستید.
من یک خبر یا مقاله به شما می‌دهم.

مرحله اول (فیلتر کردن - GATEKEEPER):
ابتدا بررسی کنید آیا این خبر برای مهندسان، داوطلبان آزمون‌های نظام مهندسی (عمران، معماری، برق، مکانیک و...) یا صنعت ساختمان و مهندسی در ایران مفید و مرتبط است یا خیر؟
اگر خبر صرفاً درباره سهام شرکت‌ها، تغییرات مدیریتی، مسائل اقتصادی بی‌ارتباط به حوزه مهندسی، یا موضوعات غیرمهندسی بود، حتماً باید آن را نامرتبط تشخیص دهید.

اگر نامرتبط (IRRELEVANT) بود:
دقیقاً فقط آبجکت JSON زیر را خروجی دهید و هیچ چیز دیگری ننویسید:
{
  "is_relevant": false,
  "title": null,
  "meta_description": null,
  "content": null
}

اگر مرتبط (RELEVANT) بود، کل مقاله را بازنویسی کنید تا کاملاً یونیک باشد.

قوانین مهم برای اخبار مرتبط:
زاویه دید مقاله را تغییر دهید تا نشان دهید این خبر چه تاثیری روی مهندسان جدید، داوطلبان آزمون نظام مهندسی، یا بازار کار مهندسی دارد.

قوانین بازنویسی را به شدت رعایت کنید:
۱. یک تیتر جذاب و سئو شده به فارسی تولید کنید (حداکثر ۶۰ کاراکتر).
۲. توضیحات متای بسیار جذاب به فارسی بنویسید (حداکثر ۱۵۵ کاراکتر).
۳. متن اصلی باید حداقل ۵ پاراگراف باشد. حتماً از فرمت Markdown استفاده کنید (H2, H3, لیست‌های نقطه‌ای).
۴. هیچ جمله‌ای را از متن اصلی کپی نکنید. کاملاً با کلمات خودتان بنویسید.
۵. حداقل طول مقاله باید ۷۰۰ کلمه باشد. در صورت نیاز، توضیحات آموزشی و تخصصی مرتبط با حوزه نظام مهندسی اضافه کنید تا به این تعداد کلمه برسید.

فقط و فقط یک آبجکت JSON معتبر با کلیدهای زیر برگردانید:
{
  "is_relevant": true,
  "title": "تیتر تولید شده شما",
  "meta_description": "توضیحات متای تولید شده شما",
  "content": "محتوای تولید شده شما با فرمت مارک‌داون"
}

متن خبر:

EOT;
        $settings['autopilot_prompt'] = $settings['autopilot_prompt'] ?? $defaultPrompt;

        $this->form->fill($settings);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('تنظیمات API (Avalai)')
                    ->description('تنظیمات مربوط به سرویس هوش مصنوعی')
                    ->schema([
                        TextInput::make('avalai_api_key')
                            ->label('Avalai API Key')
                            ->password()
                            ->required(),
                        TextInput::make('avalai_base_url')
                            ->label('Avalai Base URL')
                            ->required()
                            ->helperText('استفاده از https://api.avalai.ir/v1 یا https://api.avalapis.ir/v1'),
                    ]),

                Section::make('استراتژی تولید محتوا')
                    ->description('نحوه پردازش و بازنویسی اخبار توسط هوش مصنوعی')
                    ->schema([
                        Textarea::make('autopilot_prompt')
                            ->label('دستورالعمل سیستمی (System Prompt)')
                            ->rows(12)
                            ->required()
                            ->helperText('این دستور به همراه متن خبر به هوش مصنوعی ارسال می‌شود.'),
                        Select::make('autopilot_category_id')
                            ->label('دسته‌بندی پیش‌فرض مقالات تولیدی')
                            ->options(Category::pluck('title', 'id'))
                            ->searchable()
                            ->required(),
                        TextInput::make('autopilot_min_posts_per_day')
                            ->label('حداقل تعداد پست در روز (Target)')
                            ->numeric()
                            ->minValue(1)
                            ->required()
                            ->helperText('سیستم سعی می‌کند روزانه حداقل این تعداد خبر پیدا کند.'),
                        TextInput::make('autopilot_max_posts_per_day')
                            ->label('حداکثر پست در روز (Limit)')
                            ->numeric()
                            ->minValue(1)
                            ->required()
                            ->helperText('برای جلوگیری از اسپم، سیستم بیش از این تعداد خبر در روز منتشر نمی‌کند.'),
                    ]),

                Section::make('تنظیمات اجرا و منابع')
                    ->description('زمان‌بندی و لیست آدرس‌های RSS')
                    ->schema([
                        Select::make('autopilot_schedule_interval')
                            ->label('دوره زمانی اجرای ربات')
                            ->options([
                                '1' => 'هر ۱ ساعت',
                                '6' => 'هر ۶ ساعت',
                                '12' => 'هر ۱۲ ساعت',
                                '24' => 'روزی یک بار (هر ۲۴ ساعت)',
                            ])
                            ->required()
                            ->helperText('ربات هر چند ساعت یک‌بار برای پیدا کردن خبر جدید اجرا شود؟'),
                        TextInput::make('autopilot_max_article_age_days')
                            ->label('حداکثر عمر خبر (روز)')
                            ->numeric()
                            ->minValue(1)
                            ->required()
                            ->helperText('اخباری که در RSS قدیمی‌تر از این تعداد روز باشند، نادیده گرفته می‌شوند.'),
                        Textarea::make('autopilot_rss_feeds')
                            ->label('آدرس‌های فید RSS')
                            ->rows(5)
                            ->placeholder("https://example.com/feed/\nhttps://news.com/rss")
                            ->helperText('در هر خط فقط یک آدرس RSS وارد کنید.'),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        foreach ($data as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        Notification::make()
            ->title('تنظیمات ارسال خودکار ذخیره شد')
            ->success()
            ->send();
    }
}
