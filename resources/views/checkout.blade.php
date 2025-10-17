<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight">تایید خرید اشتراک</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold mb-2">پرداخت برای اشتراک: {{ $plan->title }}</h3>
                <p class="mb-1">قیمت: {{ number_format($plan->price_toman) }} تومان</p>
                <p class="mb-4">مدت: {{ $plan->duration_months }} ماه</p>

                <form method="POST" action="{{ route('checkout', $plan->id) }}">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded">فعال کردن اشتراک</button>
                </form>

                <div class="mt-4">
                    <a href="{{ route('pricing') }}" class="px-4 py-2 bg-gray-200 rounded">بازگشت</a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
