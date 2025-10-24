<section>
    <header>
        <h2 class="text-2xl font-bold text-gray-900">
            مدیریت اشتراک
        </h2>
        <p class="mt-2 text-sm text-gray-600">
            وضعیت اشتراک فعلی خود را مشاهده کنید
        </p>
    </header>

    @if(session('subscription_success'))
        <div class="mt-6 rounded-xl bg-gradient-to-r from-green-50 to-emerald-50 p-5 border-2 border-green-200 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="flex-shrink-0">
                    <svg class="w-6 h-6 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <span class="font-semibold text-green-800">{{ session('subscription_success') }}</span>
            </div>
        </div>
    @endif

    <!-- Current Subscription Status -->
    @if($hasPaidSubscription)
        <!-- Paid Subscription Active -->
        <div class="mt-6 rounded-2xl bg-gradient-to-br from-emerald-50 via-green-50 to-teal-50 border-2 border-emerald-200 p-8 shadow-lg">
            <div class="flex items-start gap-4">
                <div class="flex-shrink-0">
                    <div class="w-16 h-16 rounded-full bg-gradient-to-br from-emerald-400 to-green-500 flex items-center justify-center shadow-md">
                        <svg class="w-9 h-9 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                </div>
                <div class="flex-1">
                    <h3 class="text-2xl font-bold text-emerald-900 mb-3">اشتراک ویژه فعال</h3>
                    @if($currentSubscription)
                        <div class="space-y-3">
                            <div class="flex items-center gap-2 text-emerald-800">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                </svg>
                                <span class="font-medium">پلن: <strong>{{ $currentSubscription->subscriptionPlan->title }}</strong></span>
                            </div>
                            <div class="flex items-center gap-2 text-emerald-800">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path>
                                </svg>
                                <span class="font-medium">شروع: {{ jdate($currentSubscription->starts_at) }}</span>
                            </div>
                            @if($currentSubscription->ends_at)
                                <div class="flex items-center gap-2 text-emerald-800">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                    </svg>
                                    <span class="font-medium">پایان: {{ jdate($currentSubscription->ends_at) }}</span>
                                </div>
                            @else
                                <div class="flex items-center gap-2 text-emerald-800">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                    </svg>
                                    <span class="font-medium">مدت: <strong>نامحدود</strong></span>
                                </div>
                            @endif
                        </div>
                    @endif
                    <div class="mt-4 pt-4 border-t border-emerald-200">
                        <p class="text-sm text-emerald-700 flex items-center gap-2">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"></path>
                                <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm9.707 5.707a1 1 0 00-1.414-1.414L9 12.586l-1.293-1.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            شما به تمام امکانات و سوالات دسترسی کامل دارید
                        </p>
                    </div>
                </div>
            </div>
        </div>
    @else
        <!-- Free Plan - Show Upgrade Options -->
        <div class="mt-6 rounded-2xl bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50 border-2 border-indigo-200 p-8 shadow-lg">
            <div class="flex items-start gap-4">
                <div class="flex-shrink-0">
                    <div class="w-16 h-16 rounded-full bg-gradient-to-br from-indigo-400 to-purple-500 flex items-center justify-center shadow-md">
                        <svg class="w-9 h-9 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                    </div>
                </div>
                <div class="flex-1">
                    <h3 class="text-2xl font-bold text-indigo-900 mb-2">پلن رایگان</h3>
                    <p class="text-indigo-700 leading-relaxed mb-4">
                        شما در حال حاضر از پلن رایگان استفاده می‌کنید. در این پلن:
                    </p>
                    <ul class="space-y-2 text-sm text-indigo-800">
                        <li class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-indigo-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            <span>دسترسی به منابع آموزشی</span>
                        </li>
                        <li class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-indigo-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            <span>پاسخ به 4 سوال اول هر آزمون</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Available Plans for Free Users Only -->
        @if($availablePlans->count() > 0)
            <div class="mt-10">
                
                <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                    @foreach($availablePlans as $plan)
                        <div class="group rounded-2xl bg-white border-2 border-gray-200 p-6 hover:border-indigo-400 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                            <div class="text-center mb-6">
                                <div class="inline-block p-3 rounded-full bg-gradient-to-br from-indigo-100 to-purple-100 mb-4">
                                    <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
                                    </svg>
                                </div>
                                <h4 class="text-2xl font-bold text-gray-900 mb-2">
                                    {{ $plan->title }}
                                </h4>
                                
                                @if($plan->description)
                                    <p class="text-sm text-gray-500 mb-4">
                                        {{ $plan->description }}
                                    </p>
                                @endif
                            </div>
                            
                            <div class="text-center mb-6 pb-6 border-b border-gray-200">
                                <div class="text-4xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">
                                    {{ number_format($plan->price_toman) }}
                                </div>
                                <div class="text-sm text-gray-500 mt-1">تومان</div>
                                <div class="text-xs text-gray-400 mt-2">
                                    @if($plan->duration_days > 0)
                                        {{ round($plan->duration_days / 30, 1) }} ماه ({{ $plan->duration_days }} روز)
                                    @else
                                        نامحدود
                                    @endif
                                </div>
                            </div>
                            
                            <div class="space-y-3 mb-6">
                                <div class="flex items-center gap-3 text-sm text-gray-700">
                                    <div class="flex-shrink-0 w-6 h-6 rounded-full bg-green-100 flex items-center justify-center">
                                        <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                    <span>دسترسی به تمام سوالات</span>
                                </div>
                                <div class="flex items-center gap-3 text-sm text-gray-700">
                                    <div class="flex-shrink-0 w-6 h-6 rounded-full bg-green-100 flex items-center justify-center">
                                        <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                    <span>دسترسی به منابع آموزشی</span>
                                </div>
                                <div class="flex items-center gap-3 text-sm text-gray-700">
                                    <div class="flex-shrink-0 w-6 h-6 rounded-full bg-green-100 flex items-center justify-center">
                                        <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                    <span>دسترسی کامل به آزمون‌ها</span>
                                </div>
                            </div>
                            
                            <button 
                                wire:click="purchaseSubscription({{ $plan->id }})"
                                wire:loading.attr="disabled"
                                class="w-full rounded-xl bg-gradient-to-r from-indigo-600 to-purple-600 px-6 py-4 text-white font-bold text-lg hover:from-indigo-700 hover:to-purple-700 transition-all shadow-lg hover:shadow-xl disabled:opacity-50 disabled:cursor-not-allowed group-hover:scale-105 transform duration-300">
                                <span wire:loading.remove wire:target="purchaseSubscription({{ $plan->id }})">
                                    خرید اشتراک
                                </span>
                                <span wire:loading wire:target="purchaseSubscription({{ $plan->id }})">
                                    در حال پردازش...
                                </span>
                            </button>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    @endif
</section>
