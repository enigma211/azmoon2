# راهنمای نصب و پیکربندی درگاه پرداخت زرین‌پال

## ✅ نصب انجام شده

پکیج `zarinpal/zarinpal` با موفقیت نصب شد و تنظیمات اولیه انجام گردید.

## 📋 مراحل پیکربندی

### 1. دریافت Merchant ID از زرین‌پال

1. وارد پنل زرین‌پال شوید: https://www.zarinpal.com/panel
2. از منوی سمت راست، گزینه **"درگاه‌های پرداخت"** را انتخاب کنید
3. **Merchant ID** خود را کپی کنید (فرمت: `XXXXXXXX-XXXX-XXXX-XXXX-XXXXXXXXXXXX`)

### 2. تنظیم متغیرهای محیطی

فایل `.env` را ویرایش کنید و مقادیر زیر را اضافه/تنظیم کنید:

```env
ZARINPAL_MERCHANT_ID=XXXXXXXX-XXXX-XXXX-XXXX-XXXXXXXXXXXX
ZARINPAL_SANDBOX=true    # برای تست true، برای محیط واقعی false
ZARINPAL_ZARINGATE=false # برای پرداخت مستقیم true
```

**نکته مهم:** در محیط تست (Sandbox)، از Merchant ID تستی زرین‌پال استفاده کنید.

### 3. تنظیمات از پنل ادمین

1. وارد پنل ادمین شوید: `/admin`
2. از منوی **"تنظیمات"** گزینه **"تنظیمات درگاه پرداخت"** را انتخاب کنید
3. Merchant ID خود را وارد کنید
4. حالت Sandbox و ZarinGate را تنظیم کنید
5. روی **"ذخیره تنظیمات"** کلیک کنید

## 🔧 ساختار پایگاه داده

جدول `payments` برای ذخیره تراکنش‌ها ایجاد شده است:

- `id`: شناسه تراکنش
- `user_id`: کاربر خریدار
- `subscription_plan_id`: پلن اشتراک
- `authority`: کد Authority از زرین‌پال
- `amount`: مبلغ به تومان
- `status`: وضعیت (pending, success, failed, canceled)
- `ref_id`: کد پیگیری (RefID)
- `description`: توضیحات
- `response_data`: پاسخ کامل از زرین‌پال (JSON)
- `paid_at`: تاریخ پرداخت موفق

## 🚀 نحوه استفاده

### برای کاربران

1. کاربر وارد پروفایل خود می‌شود
2. یکی از پلن‌های اشتراک را انتخاب می‌کند
3. روی دکمه **"خرید اشتراک"** کلیک می‌کند
4. به صفحه پرداخت هدایت می‌شود
5. روی **"پرداخت از طریق زرین‌پال"** کلیک می‌کند
6. به درگاه زرین‌پال منتقل می‌شود
7. پس از پرداخت، به سایت برگشته و اشتراک فعال می‌شود

### برای ادمین

**مشاهده تراکنش‌ها:**
- منوی **"پرداخت‌ها"** > **"تراکنش‌ها"**
- مشاهده لیست تمام تراکنش‌ها با فیلتر وضعیت
- مشاهده جزئیات هر تراکنش شامل کد پیگیری

## 📁 فایل‌های ایجاد شده

### Models
- `app/Models/Payment.php` - مدل تراکنش‌ها

### Controllers
- `app/Http/Controllers/PaymentController.php` - کنترلر پرداخت

### Services
- `app/Services/ZarinpalService.php` - سرویس مدیریت پرداخت

### Resources (Filament)
- `app/Filament/Resources/PaymentResource.php` - مدیریت تراکنش‌ها در پنل ادمین
- `app/Filament/Pages/PaymentSettings.php` - صفحه تنظیمات درگاه

### Views
- `resources/views/payment/checkout.blade.php` - صفحه پرداخت
- `resources/views/filament/pages/payment-settings.blade.php` - صفحه تنظیمات

### Migrations
- `database/migrations/2025_10_11_182253_create_payments_table.php`

### Routes
```php
// Payment Routes
Route::get('/payment/{plan}', [PaymentController::class, 'show']);
Route::post('/payment/{plan}/request', [PaymentController::class, 'request']);
Route::get('/payment/verify', [PaymentController::class, 'verify']);
```

## 🧪 تست در محیط Sandbox

1. `ZARINPAL_SANDBOX=true` را در `.env` تنظیم کنید
2. از Merchant ID تستی استفاده کنید
3. برای تست پرداخت موفق از کارت‌های تستی زرین‌پال استفاده کنید

**کارت تستی:**
- شماره کارت: `5022-2910-0000-0000`
- CVV2: هر عددی
- تاریخ انقضا: هر تاریخ آینده

## 🔐 امنیت

- تمام اطلاعات پرداخت در جدول `payments` ذخیره می‌شود
- Authority به صورت یکتا (unique) ذخیره می‌شود
- تراکنش‌های موفق قابل تکرار نیستند
- پاسخ کامل زرین‌پال در `response_data` ذخیره می‌شود

## 📊 گزارش‌گیری

از پنل ادمین می‌توانید:
- لیست تمام تراکنش‌ها را مشاهده کنید
- بر اساس وضعیت فیلتر کنید
- کد پیگیری و جزئیات را ببینید
- تاریخ پرداخت‌ها را به صورت شمسی مشاهده کنید

## ⚠️ نکات مهم

1. **قبل از انتقال به محیط واقعی:**
   - `ZARINPAL_SANDBOX=false`
   - Merchant ID واقعی را وارد کنید
   - تست کامل انجام دهید

2. **Callback URL:**
   - URL سایت شما باید در پنل زرین‌پال ثبت شده باشد
   - در محیط لوکال از ngrok یا مشابه استفاده کنید

3. **مبالغ:**
   - حداقل مبلغ قابل پرداخت: 1000 تومان
   - مبالغ باید به تومان باشند

## 🆘 عیب‌یابی

**خطا در ایجاد درخواست:**
- Merchant ID را بررسی کنید
- اتصال اینترنت را چک کنید
- لاگ‌ها را در `storage/logs` بررسی کنید

**خطا در تایید پرداخت:**
- Authority را بررسی کنید
- مبلغ باید با مبلغ درخواست یکسان باشد
- تراکنش نباید قبلاً تایید شده باشد

## 📞 پشتیبانی زرین‌پال

- وب‌سایت: https://www.zarinpal.com
- مستندات: https://docs.zarinpal.com
- پشتیبانی: support@zarinpal.com
