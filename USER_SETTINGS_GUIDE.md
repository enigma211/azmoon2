# راهنمای تنظیمات کاربر - نسخه 2 (با Inline Styles)

## ⚠️ مشکل قبلی و راه‌حل:
مشکل این بود که CSS classes در موبایل به درستی اعمال نمی‌شدند. حالا از **inline styles** استفاده می‌کنیم که مطمئن‌تر و سریع‌تر هستند.

## تغییرات نسخه 2:

### 1. فایل‌های تغییر یافته:
- `app/Livewire/Profile/UserSettings.php` - استفاده از inline styles
- `resources/views/livewire/layout/app.blade.php` - JavaScript با inline styles
- `resources/views/livewire/profile/user-settings.blade.php` - افزودن بخش debug

### 2. نحوه کار جدید:
- وقتی روی دکمه کلیک می‌کنید، JavaScript مستقیماً `style` المان‌ها را تغییر می‌دهد
- از `document.body.style.fontSize` استفاده می‌شود (نه CSS class)
- برای تم تاریک، همه المان‌ها را پیدا کرده و رنگشان را تغییر می‌دهد
- تنظیمات در localStorage و دیتابیس ذخیره می‌شوند

### 3. دستورات اجرا روی سرور:

```bash
cd /var/www/azmoonkade.com
git pull origin main
php artisan view:clear
php artisan cache:clear
php artisan config:clear
php artisan optimize
```

### 4. تست:
1. وارد پروفایل شوید
2. روی هر کدام از دکمه‌های فونت کلیک کنید - همه باید کار کنند
3. روی دکمه تم تاریک کلیک کنید - کل سایت باید تاریک شود
4. روی دکمه "نمایش مقادیر localStorage" کلیک کنید - باید مقادیر را نشان دهد

### 5. بخش Debug:
در انتهای صفحه تنظیمات یک بخش آبی رنگ وجود دارد که:
- فونت و تم فعلی را نشان می‌دهد
- دکمه "نمایش مقادیر localStorage" دارد
- با کلیک روی آن، تمام مقادیر ذخیره شده را نمایش می‌دهد

### 6. اگر باز هم کار نکرد:
1. روی دکمه debug کلیک کنید و اسکرین‌شات بگیرید
2. Console مرورگر را باز کنید (F12 در کامپیوتر یا Remote Debug در موبایل)
3. اگر خطایی هست، آن را بفرستید
