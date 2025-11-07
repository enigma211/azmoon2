# راهنمای ساخت اپلیکیشن اندروید با TWA

## مرحله 1: نصب ابزارها

### نصب Android Studio:
1. دانلود از: https://developer.android.com/studio
2. نصب Android SDK
3. نصب Java JDK 17+

### نصب Bubblewrap (ابزار ساخت TWA):
```bash
npm install -g @bubblewrap/cli
```

---

## مرحله 2: ساخت Keystore برای امضای اپ

```bash
# ساخت keystore جدید
keytool -genkey -v -keystore azmoonkade.keystore -alias azmoonkade -keyalg RSA -keysize 2048 -validity 10000

# اطلاعات مورد نیاز:
# - نام: Azmoonkade
# - سازمان: Azmoonkade
# - شهر: Tehran
# - استان: Tehran
# - کشور: IR
# - رمز عبور: [یک رمز قوی انتخاب کنید و ذخیره کنید]
```

**⚠️ مهم:** فایل `azmoonkade.keystore` و رمز عبور را در جای امن نگه دارید!

---

## مرحله 3: استخراج SHA256 Fingerprint

```bash
# استخراج SHA256 از keystore
keytool -list -v -keystore azmoonkade.keystore -alias azmoonkade

# خروجی شبیه این است:
# Certificate fingerprints:
#   SHA1: XX:XX:XX:XX:XX:XX:XX:XX:XX:XX:XX:XX:XX:XX:XX:XX:XX:XX:XX:XX
#   SHA256: AA:BB:CC:DD:EE:FF:00:11:22:33:44:55:66:77:88:99:AA:BB:CC:DD:EE:FF:00:11:22:33:44:55:66:77:88:99
```

**SHA256 را کپی کنید** (بدون `:` ها)

---

## مرحله 4: به‌روزرسانی assetlinks.json

1. فایل `public/.well-known/assetlinks.json` را باز کنید
2. `REPLACE_WITH_YOUR_SHA256_FINGERPRINT` را با SHA256 واقعی جایگزین کنید
3. فرمت: `AA:BB:CC:DD:EE:FF:00:11:22:33:44:55:66:77:88:99:AA:BB:CC:DD:EE:FF:00:11:22:33:44:55:66:77:88:99`

مثال:
```json
[
  {
    "relation": ["delegate_permission/common.handle_all_urls"],
    "target": {
      "namespace": "android_app",
      "package_name": "com.azmoonkade.app",
      "sha256_cert_fingerprints": [
        "14:6D:E9:83:C5:73:06:50:D8:EE:B9:95:2F:34:FC:64:16:A0:83:42:E6:1D:BE:A8:8A:04:96:B2:3F:CF:44:E5"
      ]
    }
  }
]
```

4. Commit و push کنید
5. بررسی کنید: `https://azmoonkade.com/.well-known/assetlinks.json`

---

## مرحله 5: ساخت اپلیکیشن با Bubblewrap

```bash
# مقداردهی اولیه پروژه TWA
bubblewrap init --manifest=https://azmoonkade.com/manifest.webmanifest

# پاسخ به سوالات:
# - Domain: azmoonkade.com
# - Package name: com.azmoonkade.app
# - App name: آزمون کده
# - Start URL: https://azmoonkade.com/
# - Icon URL: https://azmoonkade.com/icons/icon-512x512.png
# - Theme color: #4f46e5
# - Background color: #ffffff
# - Display mode: standalone
# - Orientation: portrait
# - Signing key: azmoonkade.keystore
# - Key alias: azmoonkade
```

---

## مرحله 6: Build اپلیکیشن

```bash
# Build APK برای تست
bubblewrap build

# فایل APK در: ./app-release-signed.apk

# نصب روی گوشی برای تست
adb install app-release-signed.apk
```

---

## مرحله 7: Build AAB برای Google Play

```bash
# Build Android App Bundle
bubblewrap build --android-app-bundle

# فایل AAB در: ./app-release-bundle.aab
```

---

## مرحله 8: آپلود به Google Play Console

1. ورود به: https://play.google.com/console
2. ایجاد اپلیکیشن جدید
3. تکمیل اطلاعات:
   - نام: آزمون کده
   - توضیحات کوتاه: شبیه‌ساز آزمون نظام مهندسی
   - توضیحات کامل: [توضیحات کامل اپ]
   - دسته‌بندی: Education
   - آیکون: 512x512
   - اسکرین‌شات‌ها: حداقل 2 عدد
4. آپلود AAB
5. ارسال برای بررسی

---

## بررسی Digital Asset Links

### تست آنلاین:
```
https://digitalassetlinks.googleapis.com/v1/statements:list?source.web.site=https://azmoonkade.com&relation=delegate_permission/common.handle_all_urls
```

باید پاسخ JSON با اطلاعات اپ شما برگردد.

### تست در اپ:
1. نصب اپ روی گوشی
2. باز کردن اپ
3. اگر Digital Asset Links درست باشد: **بدون نوار آدرس**
4. اگر اشتباه باشد: **با نوار آدرس Chrome**

---

## نکات مهم:

### ✅ چک‌لیست قبل از انتشار:
- [ ] Manifest کامل است
- [ ] Service Worker کار می‌کند
- [ ] آیکون‌ها در تمام سایزها موجود است
- [ ] صفحه Offline کار می‌کند
- [ ] assetlinks.json با SHA256 صحیح
- [ ] اپ روی گوشی تست شده
- [ ] نوار آدرس نمایش داده نمی‌شود
- [ ] Deep links کار می‌کنند

### 🔐 امنیت:
- Keystore را در Git قرار ندهید
- رمز عبور را امن نگه دارید
- از keystore برای تمام به‌روزرسانی‌ها استفاده کنید

### 📱 تست:
- روی گوشی‌های مختلف تست کنید
- اندروید 5.0+ (API 21+)
- حالت آفلاین را تست کنید
- Push notifications را تست کنید

---

## فایل‌های مهم:

```
azmoonkade/
├── public/
│   ├── .well-known/
│   │   └── assetlinks.json          ← Digital Asset Links
│   ├── manifest.webmanifest         ← PWA Manifest
│   ├── service-worker.js            ← Service Worker
│   └── icons/                       ← آیکون‌ها
├── azmoonkade.keystore              ← Keystore (خارج از Git)
└── twa-manifest.json                ← Bubblewrap config
```

---

## منابع مفید:

- **Bubblewrap**: https://github.com/GoogleChromeLabs/bubblewrap
- **TWA Guide**: https://developer.chrome.com/docs/android/trusted-web-activity/
- **Digital Asset Links**: https://developers.google.com/digital-asset-links
- **Google Play Console**: https://play.google.com/console

---

## پشتیبانی:

اگر مشکلی پیش آمد:
1. بررسی Console Errors
2. بررسی assetlinks.json
3. بررسی SHA256 fingerprint
4. تست با Google Asset Links Tool

---

**آماده برای ساخت اپ اندروید هستید!** 🚀📱
