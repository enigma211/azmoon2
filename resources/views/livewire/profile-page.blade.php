<div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <header class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">پروفایل کاربری</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">مدیریت حساب و اشتراک</p>
            <div class="mt-4">
                <a href="{{ route('attempts') }}" wire:navigate class="inline-flex items-center rounded-lg bg-indigo-600 px-5 py-2.5 text-white text-sm font-medium hover:bg-indigo-700 transition shadow-md hover:shadow-lg">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    تاریخچه آزمون‌ها و نمرات
                </a>
            </div>
        </header>

        <!-- Subscription Management Component -->
        <livewire:profile.manage-subscription />

        <!-- Spacer -->
        <div class="h-8"></div>

        <!-- Old Subscription Card (Hidden, keeping for reference) -->
        <div class="hidden bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 mb-6">
            <h2 class="text-xl font-semibold mb-4 text-gray-900 dark:text-white">وضعیت اشتراک (قدیمی)</h2>
            
            @if($subscription)
                <div class="space-y-4">
                    <!-- Plan Name -->
                    <div class="flex items-center justify-between border-b border-gray-200 dark:border-gray-700 pb-3">
                        <span class="text-gray-600 dark:text-gray-400">نوع اشتراک:</span>
                        <span class="font-semibold text-lg {{ $subscription->subscriptionPlan->price_toman > 0 ? 'text-green-600' : 'text-gray-600' }}">
                            {{ $subscription->subscriptionPlan->title }}
                        </span>
                    </div>

                    <!-- Start Date -->
                    <div class="flex items-center justify-between border-b border-gray-200 dark:border-gray-700 pb-3">
                        <span class="text-gray-600 dark:text-gray-400">تاریخ فعال‌سازی:</span>
                        <span class="font-medium">{{ jdate($subscription->starts_at, 'Y/m/d') }}</span>
                    </div>

                    <!-- End Date -->
                    <div class="flex items-center justify-between border-b border-gray-200 dark:border-gray-700 pb-3">
                        <span class="text-gray-600 dark:text-gray-400">تاریخ پایان:</span>
                        <span class="font-medium">
                            @if($subscription->ends_at)
                                {{ jdate($subscription->ends_at, 'Y/m/d') }}
                            @else
                                <span class="text-green-600">نامحدود</span>
                            @endif
                        </span>
                    </div>

                    <!-- Days Remaining -->
                    @if($daysRemaining !== null)
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600 dark:text-gray-400">روزهای باقی‌مانده:</span>
                            <span class="font-bold text-lg {{ $isExpired ? 'text-red-600' : ($daysRemaining <= 7 ? 'text-yellow-600' : 'text-green-600') }}">
                                @if($isExpired)
                                    منقضی شده
                                @else
                                    {{ $daysRemaining }} روز
                                @endif
                            </span>
                        </div>
                    @else
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600 dark:text-gray-400">مدت اعتبار:</span>
                            <span class="font-bold text-lg text-green-600">نامحدود</span>
                        </div>
                    @endif

                    <!-- Expired Warning -->
                    @if($isExpired)
                        <div class="mt-4 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
                            <p class="text-red-800 dark:text-red-200 font-medium">
                                ⚠️ اشتراک شما منقضی شده است. برای ادامه استفاده از امکانات ویژه، لطفاً اشتراک خود را تمدید کنید.
                            </p>
                        </div>
                    @endif
                </div>
            @else
                <p class="text-gray-600 dark:text-gray-400">اطلاعات اشتراک یافت نشد.</p>
            @endif
        </div>

        <!-- Upgrade Suggestion for Free Users -->
        @if($subscription && $subscription->subscriptionPlan->price_toman == 0 && $availablePlans->count() > 0)
            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-lg shadow-lg p-6">
                <h2 class="text-xl font-semibold mb-4 text-gray-900 dark:text-white">
                    🎯 ارتقا به اشتراک ویژه
                </h2>
                <p class="text-gray-700 dark:text-gray-300 mb-6">
                    با خرید اشتراک ویژه، از تمام امکانات سیستم به صورت نامحدود استفاده کنید!
                </p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($availablePlans as $plan)
                        <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border-2 border-indigo-200 dark:border-indigo-700 hover:border-indigo-400 transition">
                            <h3 class="font-bold text-lg text-gray-900 dark:text-white mb-2">{{ $plan->title }}</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">{{ $plan->description }}</p>
                            <div class="flex items-center justify-between">
                                <span class="text-2xl font-bold text-indigo-600">
                                    {{ number_format($plan->price_toman) }} تومان
                                </span>
                                <span class="text-sm text-gray-500">
                                    {{ $plan->duration_days }} روز
                                </span>
                            </div>
                            <a href="{{ route('checkout.show', $plan->id) }}" 
                               class="mt-4 block w-full text-center bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2 px-4 rounded-lg transition">
                                خرید اشتراک
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>
