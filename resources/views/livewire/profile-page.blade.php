<div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @if ($isGuest)
            {{-- Guest User: Show Login Form --}}
            <header class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900">پروفایل کاربری</h1>
                <p class="text-sm text-gray-600 mt-2">برای دسترسی به پروفایل خود، لطفا وارد شوید</p>
            </header>

            <livewire:auth.otp-login />
        @else
            {{-- Logged In User: Show Profile --}}
            <header class="mb-8">
                <div class="bg-gradient-to-r from-indigo-500 to-purple-600 rounded-lg shadow-lg p-4 text-white">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <h1 class="text-xl font-bold mb-1">
                                خوش آمدید، {{ auth()->user()->name }}! 👋
                            </h1>
                            <p class="text-indigo-100 text-sm">
                                پلن شما: 
                                @if($subscription)
                                    <span class="font-semibold">{{ $subscription->title }}</span>
                                    @if($daysRemaining !== null)
                                        <span class="text-xs">- {{ ceil($daysRemaining) }} روز باقیمانده</span>
                                    @endif
                                @else
                                    <span class="font-semibold">رایگان</span>
                                @endif
                            </p>
                        </div>
                        <button 
                            wire:click="logout"
                            class="px-3 py-1.5 bg-white/20 hover:bg-white/30 rounded-lg text-white text-xs font-medium transition-colors backdrop-blur"
                        >
                            خروج
                        </button>
                    </div>
                </div>
                
                <div class="mt-4 flex justify-center gap-3">
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

            <!-- Support Tickets Link -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div class="p-3 bg-blue-100 rounded-lg">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">پشتیبانی</h3>
                            <p class="text-sm text-gray-600">ارسال تیکت و مشاهده تاریخچه تیکت‌های پشتیبانی</p>
                        </div>
                    </div>
                    <a 
                        href="{{ route('support-tickets') }}" 
                        wire:navigate
                        class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium"
                    >
                        مشاهده تیکت‌ها
                    </a>
                </div>
            </div>

            <!-- Spacer -->
            <div class="h-8"></div>

            <!-- User Settings Component -->
            <livewire:profile.user-settings />

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
                        <span class="font-semibold text-lg {{ $subscription->price_toman > 0 ? 'text-green-600' : 'text-gray-600' }}">
                            {{ $subscription->title }}
                        </span>
                    </div>

                    <!-- Start Date -->
                    <div class="flex items-center justify-between border-b border-gray-200 dark:border-gray-700 pb-3">
                        <span class="text-gray-600 dark:text-gray-400">تاریخ فعال‌سازی:</span>
                        <span class="font-medium">{{ jdate(auth()->user()->subscription_start, 'Y/m/d') }}</span>
                    </div>

                    <!-- End Date -->
                    <div class="flex items-center justify-between border-b border-gray-200 dark:border-gray-700 pb-3">
                        <span class="text-gray-600 dark:text-gray-400">تاریخ پایان:</span>
                        <span class="font-medium">
                            @if(auth()->user()->subscription_end)
                                {{ jdate(auth()->user()->subscription_end, 'Y/m/d') }}
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
        @endif

        
    </div>
</div>
