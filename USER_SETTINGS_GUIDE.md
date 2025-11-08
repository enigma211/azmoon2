# راهنمای تنظیمات کاربر

## تغییرات انجام شده:

### 1. فایل‌های تغییر یافته:
- `app/Livewire/Profile/UserSettings.php` - استفاده از `$this->js()` برای اجرای JavaScript
- `resources/views/livewire/layout/app.blade.php` - بهبود استایل‌های CSS و JavaScript

### 2. نحوه کار:
- وقتی کاربر روی دکمه‌ها کلیک می‌کند، متد Livewire اجرا می‌شود
- از `$this->js()` برای اجرای مستقیم JavaScript استفاده می‌شود
- تنظیمات در localStorage و دیتابیس ذخیره می‌شوند
- تغییرات بلافاصله اعمال می‌شوند

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
2. روی دکمه‌های اندازه فونت کلیک کنید - باید فوراً تغییر کند
3. روی دکمه‌های تم کلیک کنید - باید فوراً تغییر کند
4. یک نوتیفیکیشن سبز رنگ در بالای صفحه نمایش داده می‌شود

### 5. اگر همچنان کار نمی‌کند:
- Console مرورگر را باز کنید (F12)
- به تب Console بروید
- اگر خطایی وجود دارد، آن را بررسی کنید
- مطمئن شوید که JavaScript فعال است
