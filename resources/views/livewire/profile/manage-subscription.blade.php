<section>

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

    <!-- Available Plans -->
    @if(!$hasPaidSubscription)
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
