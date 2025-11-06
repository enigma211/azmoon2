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
                                <span class="font-semibold">{{ $plan->name }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">مدت زمان:</span>
                                <span class="font-semibold">{{ $plan->duration_days }} روز</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">مبلغ قابل پرداخت:</span>
                                <span class="font-bold text-lg text-blue-600">{{ number_format($plan->price) }} تومان</span>
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
                        
                        <div class="mb-6">
                            <div class="flex items-center">
                                <input type="checkbox" id="terms" name="terms" required class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                <label for="terms" class="mr-2 text-sm text-gray-600">
                                    قوانین و مقررات را مطالعه کرده و می‌پذیرم
                                </label>
                            </div>
                        </div>

                        <div class="flex gap-4">
                            <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-lg transition duration-150">
                                پرداخت و فعال‌سازی اشتراک
                            </button>
                            <a href="{{ route('profile') }}" class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-3 px-4 rounded-lg text-center transition duration-150">
                                انصراف
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
