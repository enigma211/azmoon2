<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight">پلن‌های اشتراک</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach($plans as $plan)
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-semibold mb-2">{{ $plan->title }}</h3>
                    <p class="text-sm mb-3">{{ $plan->description }}</p>
                    <p class="font-bold mb-1">{{ number_format($plan->price_toman) }} تومان</p>
                    <p class="text-sm mb-4">مدت: {{ $plan->duration_months }} ماه</p>
                    <form action="{{ route('checkout', $plan->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded">خرید</button>
                    </form>
                </div>
            @endforeach
        </div>
    </div>
</x-app-layout>
