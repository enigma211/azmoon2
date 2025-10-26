<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>عیب‌یابی پیامک</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
            <h1 class="text-2xl font-bold mb-4">عیب‌یابی تنظیمات پیامک</h1>
            
            <div class="space-y-4">
                <div class="border-b pb-3">
                    <h2 class="text-lg font-semibold mb-2">تنظیمات Melipayamak</h2>
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="font-medium">Username:</span>
                            <code class="bg-gray-100 px-2 py-1 rounded">{{ config('melipayamak.username') ?: 'تنظیم نشده' }}</code>
                        </div>
                        <div>
                            <span class="font-medium">Password:</span>
                            <code class="bg-gray-100 px-2 py-1 rounded">{{ config('melipayamak.password') ? '***' : 'تنظیم نشده' }}</code>
                        </div>
                        <div>
                            <span class="font-medium">From (خط ارسال):</span>
                            <code class="bg-gray-100 px-2 py-1 rounded">{{ config('melipayamak.from') ?: 'تنظیم نشده' }}</code>
                        </div>
                        <div>
                            <span class="font-medium">OTP Enabled:</span>
                            <code class="bg-gray-100 px-2 py-1 rounded">{{ config('melipayamak.otp_enabled', env('OTP_ENABLED', true)) ? 'فعال' : 'غیرفعال' }}</code>
                        </div>
                    </div>
                </div>

                <div class="border-b pb-3">
                    <h2 class="text-lg font-semibold mb-2">متغیرهای محیطی (.env)</h2>
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="font-medium">MELIPAYAMAK_USERNAME:</span>
                            <code class="bg-gray-100 px-2 py-1 rounded">{{ env('MELIPAYAMAK_USERNAME') ?: 'تنظیم نشده' }}</code>
                        </div>
                        <div>
                            <span class="font-medium">MELIPAYAMAK_PASSWORD:</span>
                            <code class="bg-gray-100 px-2 py-1 rounded">{{ env('MELIPAYAMAK_PASSWORD') ? '***' : 'تنظیم نشده' }}</code>
                        </div>
                        <div>
                            <span class="font-medium">MELIPAYAMAK_FROM:</span>
                            <code class="bg-gray-100 px-2 py-1 rounded">{{ env('MELIPAYAMAK_FROM') ?: 'تنظیم نشده' }}</code>
                        </div>
                        <div>
                            <span class="font-medium">OTP_ENABLED:</span>
                            <code class="bg-gray-100 px-2 py-1 rounded">{{ env('OTP_ENABLED', 'true') }}</code>
                        </div>
                    </div>
                </div>

                <div class="border-b pb-3">
                    <h2 class="text-lg font-semibold mb-2">وضعیت Facade</h2>
                    <div class="text-sm">
                        @php
                            try {
                                $facadeExists = class_exists('Melipayamak\Laravel\Facade');
                                $facadeStatus = $facadeExists ? 'نصب شده ✓' : 'نصب نشده ✗';
                            } catch (\Throwable $e) {
                                $facadeStatus = 'خطا: ' . $e->getMessage();
                            }
                        @endphp
                        <span class="font-medium">Melipayamak Facade:</span>
                        <code class="bg-gray-100 px-2 py-1 rounded">{{ $facadeStatus }}</code>
                    </div>
                </div>

                <div class="bg-yellow-50 border border-yellow-200 rounded p-4">
                    <h3 class="font-semibold text-yellow-800 mb-2">⚠️ نکات مهم</h3>
                    <ul class="text-sm text-yellow-700 space-y-1 list-disc list-inside">
                        <li>پس از تغییر فایل <code>.env</code> حتماً کش را پاک کنید: <code class="bg-yellow-100 px-1">php artisan config:clear</code></li>
                        <li>اطمینان حاصل کنید که پکیج نصب شده: <code class="bg-yellow-100 px-1">composer require melipayamak/laravel</code></li>
                        <li>کانفیگ را پابلیش کنید: <code class="bg-yellow-100 px-1">php artisan vendor:publish --tag="melipayamak"</code></li>
                        <li>فایل <code>config/melipayamak.php</code> را بررسی کنید</li>
                        <li>لاگ‌ها را چک کنید: <code class="bg-yellow-100 px-1">storage/logs/laravel.log</code></li>
                    </ul>
                </div>

                <div class="bg-blue-50 border border-blue-200 rounded p-4">
                    <h3 class="font-semibold text-blue-800 mb-2">📝 دستورات لازم</h3>
                    <div class="text-sm text-blue-700 space-y-2">
                        <div class="bg-blue-100 p-2 rounded font-mono">
                            composer require melipayamak/laravel:1.0.0
                        </div>
                        <div class="bg-blue-100 p-2 rounded font-mono">
                            php artisan vendor:publish --tag="melipayamak"
                        </div>
                        <div class="bg-blue-100 p-2 rounded font-mono">
                            php artisan config:clear
                        </div>
                        <div class="bg-blue-100 p-2 rounded font-mono">
                            php artisan cache:clear
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-lg p-6">
            <h2 class="text-xl font-bold mb-4">آخرین لاگ‌ها</h2>
            <div class="bg-gray-900 text-green-400 p-4 rounded font-mono text-xs overflow-x-auto" style="max-height: 400px; overflow-y: auto;">
                @php
                    $logFile = storage_path('logs/laravel.log');
                    if (file_exists($logFile)) {
                        $logs = file($logFile);
                        $lastLogs = array_slice($logs, -50);
                        foreach ($lastLogs as $log) {
                            echo htmlspecialchars($log) . "\n";
                        }
                    } else {
                        echo "فایل لاگ یافت نشد.";
                    }
                @endphp
            </div>
        </div>
    </div>
</body>
</html>
