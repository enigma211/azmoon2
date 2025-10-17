<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>پرداخت اشتراک - {{ $plan->title }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 dark:bg-gray-900">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full">
            <!-- Header -->
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
                    پرداخت اشتراک
                </h1>
                <p class="text-gray-600 dark:text-gray-400">
                    در حال خرید اشتراک {{ $plan->title }}
                </p>
            </div>

            <!-- Plan Card -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden mb-6">
                <div class="bg-gradient-to-r from-indigo-500 to-purple-600 px-6 py-8 text-white">
                    <h2 class="text-2xl font-bold mb-2">{{ $plan->title }}</h2>
                    <p class="text-indigo-100 text-sm">{{ $plan->description }}</p>
                </div>

                <div class="p-6 space-y-4">
                    <!-- Price -->
                    <div class="flex items-center justify-between py-4 border-b border-gray-200 dark:border-gray-700">
                        <span class="text-gray-600 dark:text-gray-400">مبلغ قابل پرداخت:</span>
                        <span class="text-3xl font-bold text-indigo-600 dark:text-indigo-400">
                            {{ number_format($plan->price_toman) }} تومان
                        </span>
                    </div>

                    <!-- Duration -->
                    <div class="flex items-center justify-between py-3 border-b border-gray-200 dark:border-gray-700">
                        <span class="text-gray-600 dark:text-gray-400">مدت اعتبار:</span>
                        <span class="font-semibold text-gray-900 dark:text-white">
                            @if($plan->duration_days > 0)
                                {{ $plan->duration_days }} روز
                            @else
                                نامحدود
                            @endif
                        </span>
                    </div>

                    <!-- User Info -->
                    <div class="flex items-center justify-between py-3">
                        <span class="text-gray-600 dark:text-gray-400">خریدار:</span>
                        <span class="font-semibold text-gray-900 dark:text-white">
                            {{ $user->name }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Payment Button -->
            <form action="{{ route('payment.request', $plan) }}" method="POST">
                @csrf
                <button type="submit" 
                    class="w-full bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-bold py-4 px-6 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 flex items-center justify-center gap-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                    </svg>
                    <span>پرداخت از طریق زرین‌پال</span>
                </button>
            </form>

            <!-- Secure Payment Notice -->
            <div class="mt-6 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4">
                <div class="flex items-start gap-3">
                    <svg class="w-6 h-6 text-green-600 dark:text-green-400 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    <div>
                        <p class="text-sm font-semibold text-green-800 dark:text-green-200 mb-1">
                            پرداخت امن
                        </p>
                        <p class="text-xs text-green-700 dark:text-green-300">
                            پرداخت شما از طریق درگاه معتبر زرین‌پال انجام می‌شود و کاملاً ایمن است.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Back Link -->
            <div class="mt-6 text-center">
                <a href="{{ route('profile') }}" 
                   class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 font-medium text-sm inline-flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    بازگشت به پروفایل
                </a>
            </div>

            @if(session('error'))
                <div class="mt-6 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
                    <p class="text-sm text-red-800 dark:text-red-200">
                        {{ session('error') }}
                    </p>
                </div>
            @endif
        </div>
    </div>
</body>
</html>
