# راهنمای سیستم تیکتینگ پشتیبانی

## ویژگی‌ها:

### برای کاربران:
✅ ایجاد تیکت جدید با موضوع و متن  
✅ مشاهده تاریخچه تیکت‌ها  
✅ مشاهده وضعیت (در انتظار پاسخ / پاسخ داده شده)  
✅ مشاهده پاسخ ادمین  
✅ شماره تیکت یونیک برای هر تیکت  

### برای ادمین:
✅ مشاهده همه تیکت‌ها در پنل Filament  
✅ فیلتر بر اساس وضعیت  
✅ پاسخ به تیکت‌ها  
✅ تغییر وضعیت تیکت  
✅ مشاهده اطلاعات کاربر  

## فایل‌های ایجاد شده:

### 1. Database:
- `database/migrations/2025_11_08_071000_create_support_tickets_table.php`

### 2. Models:
- `app/Models/SupportTicket.php`

### 3. Livewire:
- `app/Livewire/SupportTicketsPage.php`
- `resources/views/livewire/support-tickets-page.blade.php`

### 4. Filament Admin:
- `app/Filament/Resources/SupportTicketResource.php`
- `app/Filament/Resources/SupportTicketResource/Pages/ListSupportTickets.php`
- `app/Filament/Resources/SupportTicketResource/Pages/ViewSupportTicket.php`
- `app/Filament/Resources/SupportTicketResource/Pages/EditSupportTicket.php`

### 5. Routes:
- افزودن route در `routes/web.php`

### 6. Views:
- تغییر در `resources/views/livewire/profile-page.blade.php`

## دستورات اجرا:

```bash
cd /var/www/azmoonkade.com

# دریافت تغییرات
git pull origin main

# اجرای migration
php artisan migrate

# پاکسازی cache
php artisan view:clear
php artisan cache:clear
php artisan config:clear
php artisan optimize
```

## نحوه استفاده:

### برای کاربران:
1. وارد پروفایل شوید
2. روی دکمه "مشاهده تیکت‌ها" در بخش پشتیبانی کلیک کنید
3. روی "ایجاد تیکت جدید" کلیک کنید
4. موضوع و متن تیکت را وارد کنید
5. تیکت ارسال می‌شود و شماره تیکت نمایش داده می‌شود
6. در لیست تیکت‌ها، روی هر تیکت کلیک کنید تا جزئیات و پاسخ را ببینید

### برای ادمین:
1. وارد پنل ادمین شوید (`/admin`)
2. از منوی سمت راست، "تیکت پشتیبانی" را انتخاب کنید
3. لیست تیکت‌ها را مشاهده کنید
4. روی "View" یا "Edit" کلیک کنید
5. پاسخ خود را در قسمت "پاسخ ادمین" بنویسید
6. وضعیت را به "پاسخ داده شده" تغییر دهید
7. ذخیره کنید

## ویژگی‌های امنیتی:

✅ **Authentication:** فقط کاربران لاگین شده می‌توانند تیکت ایجاد کنند  
✅ **Authorization:** کاربران فقط تیکت‌های خودشان را می‌بینند  
✅ **Validation:** اعتبارسنجی موضوع و متن تیکت  
✅ **Max Length:** حداکثر 2000 کاراکتر برای متن تیکت  

## جدول دیتابیس:

```sql
support_tickets:
- id
- user_id (foreign key)
- ticket_number (unique)
- subject
- message
- status (pending/answered)
- admin_reply (nullable)
- replied_at (nullable)
- created_at
- updated_at
```

## تست:

1. ✅ ایجاد تیکت جدید
2. ✅ مشاهده لیست تیکت‌ها
3. ✅ مشاهده جزئیات تیکت
4. ✅ پاسخ ادمین در پنل
5. ✅ مشاهده پاسخ توسط کاربر
6. ✅ تغییر وضعیت تیکت
