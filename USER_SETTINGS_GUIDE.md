# راهنمای تنظیمات کاربر - نسخه 3 (رفع مشکل Navigation)

## ⚠️ مشکل‌های قبلی و راه‌حل:
1. CSS classes در موبایل کار نمی‌کردند → از **inline styles** استفاده کردیم
2. تنظیمات در صفحات دیگر (خانه، آزمون‌ها) ریست می‌شدند → JavaScript را به layout اصلی اضافه کردیم

## تغییرات نسخه 3:

### 1. فایل‌های تغییر یافته:
- `app/Livewire/Profile/UserSettings.php` - استفاده از inline styles
- `resources/views/layouts/app.blade.php` - اضافه کردن JavaScript به layout اصلی (خانه، آزمون‌ها و...)
- `resources/views/livewire/layout/app.blade.php` - JavaScript با inline styles
- `resources/views/livewire/profile/user-settings.blade.php` - حذف بخش debug

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
4. **مهم:** به صفحه خانه یا آزمون‌ها بروید - تنظیمات باید حفظ شوند
5. رفرش کنید - تنظیمات باید حفظ شوند

### 5. تغییرات این نسخه:
✅ تنظیمات در **همه صفحات** اعمال می‌شود (خانه، آزمون‌ها، منابع، پروفایل)
✅ بعد از navigation با Livewire، تنظیمات حفظ می‌شود
✅ بخش debug حذف شد (رابط کاربری تمیزتر)
✅ همه 4 سایز فونت کار می‌کند
✅ تم روشن و تاریک در همه جا اعمال می‌شود

### 6. اگر باز هم مشکلی بود:
1. Cache مرورگر را کاملاً پاک کنید (Settings → Privacy → Clear browsing data)
2. یا از حالت Incognito استفاده کنید
3. Console مرورگر را باز کنید و خطا را بفرستید
