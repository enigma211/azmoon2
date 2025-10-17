# اسکلت پروژه (Laravel 12 + Livewire v3 + Filament v3 + Tailwind) – فارسی، RTL و سریع برای WebView

این ریپو یک اسکلت تمیز با حس SPA برای شروع MVP فراهم می‌کند. صفحه‌بندی‌ها با Livewire v3 و `wire:navigate` کار می‌کنند، تب‌بار موبایل پایین صفحه دارد، پنل ادمین Filament (پس از نصب) فارسی/RTL می‌شود، و تاریخ‌ها با Morilog/Jalali شمسی نمایش داده می‌شوند.

## پیش‌نیازها
- PHP 8.2+
- Composer
- Node.js 18+ و NPM
- MySQL/PostgreSQL/SQLite (یکی کافی است)

## نسخه‌های اصلی و پکیج‌ها
- Laravel 12
- Livewire v3 (`livewire/livewire`)
- Filament v3 (`filament/filament`) – نصب در مراحل زیر
- Spatie Permission v6 (`spatie/laravel-permission`)
- Breeze v2 (استک Livewire)
- Tailwind + PostCSS + Autoprefixer + `@tailwindcss/forms`
- Morilog/Jalali (تاریخ شمسی)

## راه‌اندازی سریع (دستورات قابل کپی)

1) کلون/دریافت و نصب PHP پکیج‌ها:
```bash
composer install
```

2) تنظیم محیط و کلید اپ:
```bash
copy .env.example .env  # در ویندوز پاورشِل: Copy-Item .env.example .env
php artisan key:generate
```

3) نصب وابستگی‌های فرانت‌اند:
```bash
npm install
```

4) ساخت پایگاه‌داده و اجرای مایگریشن‌ها و سیدرها:
```bash
php artisan migrate
php artisan db:seed
```

5) اجرای محیط توسعه (دو ترمینال مجزا):
```bash
php artisan serve
```
```bash
npm run dev
```

> آدرس پیش‌فرض: http://127.0.0.1:8000

## نصب و پیکربندی Filament v3 (پنل ادمین /admin)
1) نصب پکیج:
```bash
composer require filament/filament:^3.2 --with-all-dependencies
```

2) ساخت پنل ادمین (آدرس `/admin`):
```bash
php artisan make:filament-panel admin
```
به سؤالات نصب پاسخ دهید (نام پنل: admin، مسیر: /admin، تم پیش‌فرض و ...).

3) محدودسازی دسترسی فقط برای نقش `admin`:
- در مدل `App\Models\User` متد زیر را اضافه کنید تا فقط ادمین وارد پنل شود:
```php
public function canAccessPanel(\Filament\Panels\Panel $panel): bool
{
    return $this->hasRole('admin');
}
```

4) فارسی و RTL:
- با توجه به اینکه `config/app.php` هم‌اکنون `locale=fa` و `timezone=Asia/Tehran` است، پنل را نیز فارسی کنید. در Service Provider پنل (مثل `AdminPanelProvider` که در `app/Providers/Filament/` ساخته می‌شود) می‌توانید تنظیمات تکمیلی مثل اعمال CSS RTL سفارشی را اضافه کنید. ساده‌ترین راه، اضافه‌کردن CSS سفارشی با `direction: rtl; text-align: right;` برای بدنه پنل است.

5) داشبورد نمونه:
- پس از نصب، یک Dashboard ساده به‌صورت Resource یا Widget بسازید و یک کارت «سلام» و لینک‌های سریع اضافه کنید.

## نقش‌ها و ادمین اولیه
- مایگریشن‌های Spatie Permission اضافه شده‌اند.
- سیدر `database/seeders/RolesAndAdminSeeder.php` نقش‌های `admin, editor, student` را می‌سازد و یک کاربر ادمین اولیه با مشخصات زیر می‌سازد:
  - ایمیل: `admin@example.com`
  - گذرواژه: `Admin@12345`
- اجرای سیدرها با `php artisan db:seed` انجام می‌شود (در `DatabaseSeeder` اضافه شده است).

## Locales، RTL و فونت
- `config/app.php` روی `locale=fa`، `fallback_locale=fa` و `timezone=Asia/Tehran` تنظیم شده است.
- لایه‌ی پایه `resources/views/layouts/app.blade.php` با `<html lang="fa" dir="rtl">` و فونت Vazirmatn بارگذاری می‌شود.

## Livewire v3 با حس SPA
- لینک‌های ناوبری موبایل با `wire:navigate` هستند تا بدون رفرش کامل جابه‌جا شوید.
- فایل `resources/js/livewire-hooks.js` رویدادهای ناوبری Livewire را گوش می‌دهد و اگر ناوبری/لود بیش از ۲ ثانیه طول بکشد، یک Global Loading Overlay نشان می‌دهد.
- Overlay در `layouts/app.blade.php` تعریف شده است.

## ساختار صفحات و ناوبری موبایل
- صفحات Livewire: `HomePage`, `DomainsPage`, `ResourcesPage`, `ProfilePage`.
- مسیرها در `routes/web.php` به این صفحات متصل شده‌اند:
  - `/` → `HomePage`
  - `/domains` → `DomainsPage`
  - `/resources` → `ResourcesPage`
  - `/profile` → `ProfilePage` (با میان‌افزار auth)
- یک تب‌بار پایین (Bottom Tab Bar) برای موبایل در لایه‌ی پایه قرار داده شده است.

## احراز هویت پایه (Breeze + Livewire)
- Breeze با استک Livewire نصب و اسکَفولد شده است. صفحات login/register/forgot-password موجود و ورود/خروج کار می‌کند.

## تاریخ شمسی (Morilog/Jalali) و دایرکتیو Blade
- دایرکتیو `@jdate($date, $format='Y/m/d')` در `app/Providers/AppServiceProvider.php` ثبت شده است.
- نمونه‌ی استفاده در `resources/views/livewire/home-page.blade.php` آمده است: `@jdate(now())`.

## System Settings برای OTP آینده
- جدول `system_settings` و مدل `App\Models\SystemSetting` اضافه شده‌اند.
- سیدر `SystemSettingsSeeder` کلیدهای حداقلی را اضافه می‌کند:
  - `otp_enabled = false`
  - `sms_provider = dummy`

## Tailwind/Vite/PostCSS و بهینه‌سازی
- `tailwind.config.js` شامل مسیرهای Blade و JS/TS برای Purge است تا فقط کلاس‌های استفاده‌شده وارد باندل شوند.
- تولید (Production Build):
```bash
npm run build
```
- بررسی اندازه‌ی باندل (PowerShell روی ویندوز):
```powershell
Get-ChildItem -Recurse -File .\public\build | Measure-Object -Sum Length | ForEach-Object { '{0:N0} bytes' -f ($_.Sum) }
```

## اجرای توسعه
- ترمینال ۱:
```bash
php artisan serve
```
- ترمینال ۲:
```bash
npm run dev
```

## ورود به پنل ادمین
1) گام‌های «نصب Filament» را انجام دهید.
2) با کاربر ادمین `admin@example.com` و گذرواژه `Admin@12345` وارد `/admin` شوید.

## معیار پذیرش
- صفحات `Home, Domains, Resources, Profile` با `wire:navigate` بدون رفرش کامل جابه‌جا شوند.
- تب‌بار پایین روی موبایل ریسپانسیو باشد.
- پنل `/admin` بالا بیاید و با نقش `admin` وارد شویم.
- `@jdate(now())` تاریخ شمسی نشان دهد.
- Global Loading Overlay اگر ناوبری >۲ ثانیه طول کشید، نمایش داده شود.
- هیچ دایرکتوری/کانفیگ تستی وجود ندارد.

## نکته مهم درباره تست‌ها
در این پروژه «هیچ تست خودکاری» (Pest/PhpUnit/Feature/Unit) وجود ندارد تا فرآیند ساخت MVP سریع‌تر باشد. در صورت نیاز، می‌توانید بعداً آن‌ها را اضافه کنید.
