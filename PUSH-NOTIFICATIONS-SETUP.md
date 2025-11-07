# 🔔 راهنمای نصب Push Notifications

## مرحله 1️⃣: نصب کتابخانه

```bash
composer require minishlink/web-push
```

---

## مرحله 2️⃣: تولید VAPID Keys

```bash
php artisan vapid:generate
```

خروجی شبیه این خواهد بود:
```
VAPID_PUBLIC_KEY=BKxT...
VAPID_PRIVATE_KEY=abc123...
```

این کلیدها را به فایل `.env` اضافه کنید.

---

## مرحله 3️⃣: اجرای Migration

```bash
php artisan migrate
```

این جدول `push_subscriptions` را ایجاد می‌کند.

---

## مرحله 4️⃣: تست در محیط Local

### 1. باز کردن صفحه تست:
```
http://localhost:8000/push-test
```

### 2. مراحل تست:
1. ✅ کلیک روی "درخواست اجازه"
2. ✅ اجازه دادن در پاپ‌آپ مرورگر
3. ✅ کلیک روی "فعال کردن اعلان‌ها"
4. ✅ لاگین کردن (اگر نیستید)
5. ✅ کلیک روی "ارسال اعلان تستی"
6. ✅ باید اعلان را ببینید! 🎉

---

## مرحله 5️⃣: Deploy روی سرور

### 1. Push کردن تغییرات:
```bash
git add .
git commit -m "Add Push Notifications feature"
git push origin main
```

### 2. روی سرور:
```bash
cd /var/www/azmoonkade.com
git pull origin main

# نصب کتابخانه
composer install --no-dev --optimize-autoloader

# تولید VAPID keys
php artisan vapid:generate

# کپی کلیدها به .env
nano .env
# اضافه کردن:
# VAPID_PUBLIC_KEY=...
# VAPID_PRIVATE_KEY=...

# اجرای migration
php artisan migrate --force

# پاک کردن cache
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

---

## مرحله 6️⃣: تست روی Production

### باز کردن صفحه تست:
```
https://azmoonkade.com/push-test
```

### مراحل:
1. درخواست اجازه
2. فعال کردن اعلان‌ها
3. لاگین (اگر نیستید)
4. ارسال اعلان تستی

---

## 📱 استفاده در کد

### ارسال اعلان به یک کاربر:

```php
use App\Services\PushNotificationService;

$pushService = app(PushNotificationService::class);

$payload = [
    'title' => 'آزمون جدید!',
    'body' => 'آزمون عمران - نوبت آذر 1403',
    'icon' => '/icons/icon-192x192.png',
    'badge' => '/icons/icon-96x96.png',
    'data' => [
        'url' => '/exam/123',
    ],
];

$pushService->sendToUser($user, $payload);
```

### ارسال به همه کاربران:

```php
$pushService->sendToAll($payload);
```

### ارسال به لیستی از کاربران:

```php
$userIds = [1, 2, 3, 4, 5];
$pushService->sendToUsers($userIds, $payload);
```

---

## 🎯 موارد استفاده

### 1. اعلان آزمون جدید:
```php
// در ExamController یا Event
$users = User::whereHas('activeSubscription')->get();
foreach ($users as $user) {
    $pushService->sendToUser($user, [
        'title' => 'آزمون جدید!',
        'body' => $exam->title,
        'data' => ['url' => "/exam/{$exam->id}"],
    ]);
}
```

### 2. یادآوری آزمون:
```php
// در Scheduled Job
$upcomingExams = Exam::where('starts_at', '>', now())
    ->where('starts_at', '<', now()->addDay())
    ->get();

foreach ($upcomingExams as $exam) {
    $users = $exam->registeredUsers;
    foreach ($users as $user) {
        $pushService->sendToUser($user, [
            'title' => 'یادآوری آزمون',
            'body' => "آزمون {$exam->title} فردا شروع می‌شود",
            'data' => ['url' => "/exam/{$exam->id}"],
        ]);
    }
}
```

### 3. اعلان انقضای اشتراک:
```php
// در Scheduled Job
$expiringUsers = User::whereDate('subscription_end', now()->addDays(3))->get();

foreach ($expiringUsers as $user) {
    $pushService->sendToUser($user, [
        'title' => 'اشتراک شما رو به پایان است',
        'body' => 'اشتراک شما 3 روز دیگر منقضی می‌شود',
        'data' => ['url' => '/pricing'],
    ]);
}
```

### 4. اعلان به‌روزرسانی:
```php
$pushService->sendToAll([
    'title' => 'نسخه جدید آماده است!',
    'body' => 'ویژگی‌های جدید اضافه شده است',
    'data' => ['url' => '/'],
]);
```

---

## 🔧 تنظیمات پیشرفته

### Auto-Subscribe برای کاربران لاگین:

در `resources/views/layouts/app.blade.php`:

```html
@auth
<meta name="user-authenticated" content="true">
@endauth

<script src="/js/push-notifications.js"></script>
```

این کد خودکار کاربران لاگین را subscribe می‌کند.

---

## 🐛 عیب‌یابی

### مشکل: اعلان نمی‌آید

**بررسی‌ها:**
1. ✅ VAPID keys در `.env` تنظیم شده؟
2. ✅ Service Worker ثبت شده؟ (F12 → Application → Service Workers)
3. ✅ اجازه داده شده؟ (Notification.permission === 'granted')
4. ✅ Subscription ثبت شده؟ (جدول push_subscriptions)
5. ✅ HTTPS فعال است؟ (Push فقط روی HTTPS کار می‌کند)

### مشکل: خطای VAPID

```
Error: VAPID keys are not set
```

**راه‌حل:**
```bash
php artisan vapid:generate
# کپی کلیدها به .env
php artisan config:clear
```

### مشکل: Subscription منقضی شده

```
Subscription expired
```

**راه‌حل:**
- Subscription خودکار حذف می‌شود
- کاربر باید دوباره subscribe کند

---

## 📊 مانیتورینگ

### بررسی تعداد subscriptions:

```php
$totalSubscriptions = PushSubscription::count();
$userSubscriptions = PushSubscription::whereNotNull('user_id')->count();
$guestSubscriptions = PushSubscription::whereNull('user_id')->count();
```

### بررسی آخرین اعلان‌ها:

```bash
# در logs
tail -f storage/logs/laravel.log | grep "Push notification"
```

---

## 🚀 بهینه‌سازی

### Queue برای ارسال انبوه:

```php
// ایجاد Job
php artisan make:job SendPushNotification

// در Job:
public function handle(PushNotificationService $pushService)
{
    $pushService->sendToUser($this->user, $this->payload);
}

// استفاده:
SendPushNotification::dispatch($user, $payload);
```

---

## ✅ چک‌لیست نهایی

- [ ] کتابخانه نصب شده
- [ ] VAPID keys تولید و در .env قرار گرفته
- [ ] Migration اجرا شده
- [ ] Service Worker push handler دارد
- [ ] JavaScript فایل لود می‌شود
- [ ] Routes تعریف شده
- [ ] تست در local موفق
- [ ] تست در production موفق
- [ ] اعلان‌ها در موبایل کار می‌کنند
- [ ] اعلان‌ها در desktop کار می‌کنند

---

**Push Notifications آماده است!** 🎉🔔

برای سوالات بیشتر، به مستندات مراجعه کنید:
- https://github.com/web-push-libs/web-push-php
- https://developer.mozilla.org/en-US/docs/Web/API/Push_API
