<div>
    <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
        <header class="py-4">
            <h1 class="text-lg font-semibold">منابع</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">راهنماها، فایل‌ها و لینک‌های مفید</p>
        </header>

        @if(isset($items) && $items->count())
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @foreach($items as $item)
                    <a href="{{ route('resource.detail', ['resource' => $item->id]) }}" wire:navigate
                       class="block rounded-xl bg-gradient-to-br from-white to-gray-50 dark:from-gray-800 dark:to-gray-900 shadow-lg hover:shadow-xl transition-all duration-300 p-6 border border-gray-200 dark:border-gray-700 hover:border-indigo-400 dark:hover:border-indigo-600">
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex-1">
                                <h3 class="font-bold text-lg text-gray-900 dark:text-white mb-2">{{ $item->title }}</h3>
                                @if($item->description)
                                    <p class="text-sm text-gray-600 dark:text-gray-300 leading-relaxed">
                                        {{ \Illuminate\Support\Str::limit($item->description, 120) }}
                                    </p>
                                @endif
                            </div>
                        </div>
                        <div class="flex items-center justify-between mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                            <span class="inline-flex items-center rounded-full bg-indigo-100 dark:bg-indigo-900 text-indigo-800 dark:text-indigo-200 text-xs font-semibold px-3 py-1">
                                {{ $item->type }}
                            </span>
                            <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </div>
                    </a>
                @endforeach
            </div>

            <div class="mt-6">
                {{ $items->onEachSide(1)->links() }}
            </div>
        @else
            <!-- Skeletons -->
            <div class="grid grid-cols-1 gap-4">
                <div class="animate-pulse p-4 rounded-lg bg-white dark:bg-gray-800 shadow">
                    <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-2/3 mb-3"></div>
                    <div class="h-3 bg-gray-200 dark:bg-gray-700 rounded w-1/2 mb-2"></div>
                    <div class="h-3 bg-gray-200 dark:bg-gray-700 rounded w-1/3"></div>
                </div>
                <div class="animate-pulse p-4 rounded-lg bg-white dark:bg-gray-800 shadow">
                    <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-1/3 mb-3"></div>
                    <div class="h-3 bg-gray-200 dark:bg-gray-700 rounded w-2/3 mb-2"></div>
                    <div class="h-3 bg-gray-200 dark:bg-gray-700 rounded w-1/2"></div>
                </div>
            </div>
        @endif
    </div>
</div>
