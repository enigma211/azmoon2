<x-app-layout>
    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h2 class="text-2xl font-bold mb-6 text-center">تکمیل خرید</h2>

                    <!-- Plan Details -->
                    <div class="bg-gray-50 rounded-lg p-6 mb-6">
                        <h3 class="text-lg font-semibold mb-4">جزئیات اشتراک</h3>
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-gray-600">نام پلن:</span>
                                <span class="font-semibold">{{ $plan->title }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">مدت زمان:</span>
                                <span class="font-semibold">{{ $plan->duration_days }} روز</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">مبلغ قابل پرداخت:</span>
                                <span class="font-bold text-lg text-blue-600">{{ number_format($plan->price_toman) }} تومان</span>
                            </div>
                        </div>
                    </div>

                    <!-- User Info -->
                    <div class="bg-gray-50 rounded-lg p-6 mb-6">
                        <h3 class="text-lg font-semibold mb-4">اطلاعات خریدار</h3>
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-gray-600">نام:</span>
                                <span class="font-semibold">{{ $user->name }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">شماره موبایل:</span>
                                <span class="font-semibold">{{ $user->mobile }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Form -->
                    <form action="{{ route('payment.request', $plan) }}" method="POST">
                        @csrf
                        
                        <div class="flex flex-col sm:flex-row gap-4" dir="rtl">
                            <button type="submit" class="w-full sm:flex-1 bg-green-600 hover:bg-green-700 text-white font-bold py-4 px-6 rounded-lg transition duration-150 shadow-lg">
                                💳 پرداخت و فعال‌سازی اشتراک
                            </button>
                            <a href="{{ route('profile') }}" class="w-full sm:flex-1 bg-gray-400 hover:bg-gray-500 text-white font-bold py-4 px-6 rounded-lg text-center transition duration-150 shadow-lg">
                                ❌ انصراف
                            </a>
                        </div>
                    </form>

                    @if(session('error'))
                        <div class="mt-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                            {{ session('error') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
