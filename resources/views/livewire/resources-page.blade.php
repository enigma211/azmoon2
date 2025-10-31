<div class="min-h-screen bg-gradient-to-br from-gray-50 to-white">
    <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header -->
        <header class="mb-8">
            <div class="text-center">
                <h1 class="text-3xl font-bold text-gray-900 mb-2">منابع آموزشی</h1>
                <p class="text-gray-600">راهنماها، فایل‌های PDF و ویدیوهای آموزشی</p>
            </div>
        </header>

        @if(isset($items) && $items->count())
            <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                @foreach($items as $item)
                    <a href="{{ route('resource.detail', ['resource' => $item->id]) }}" 
                       wire:navigate
                       class="group relative bg-white rounded-2xl shadow-md hover:shadow-xl transition-all duration-300 overflow-hidden border border-gray-100 hover:border-gray-200">
                        
                        <!-- Icon Header -->
                        <div class="relative h-20 bg-gradient-to-br from-indigo-50 to-purple-50 flex items-center justify-center">
                            @switch($item->type)
                                @case('pdf')
                                    <div class="w-12 h-12 bg-red-500 rounded-lg flex items-center justify-center shadow-lg">
                                        <svg class="w-7 h-7 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                    @break
                                
                                @case('video')
                                    <div class="w-12 h-12 bg-blue-500 rounded-lg flex items-center justify-center shadow-lg">
                                        <svg class="w-7 h-7 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                    @break
                                
                                @default
                                    <div class="w-12 h-12 bg-gray-500 rounded-lg flex items-center justify-center shadow-lg">
                                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                    </div>
                                    @break
                            @endswitch
                        </div>
                        
                        <!-- Content -->
                        <div class="p-5">
                            <h3 class="font-bold text-gray-900 mb-2 line-clamp-2 group-hover:text-indigo-600 transition-colors">
                                {{ $item->title }}
                            </h3>
                            
                            @if($item->description)
                                <p class="text-sm text-gray-600 leading-relaxed mb-3 line-clamp-3">
                                    {{ \Illuminate\Support\Str::limit($item->description, 100) }}
                                </p>
                            @endif
                            
                            <!-- Type Badge -->
                            <div class="flex items-center justify-between">
                                <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-medium
                                    @switch($item->type)
                                        @case('pdf')
                                            bg-red-100 text-red-800
                                            @break
                                        @case('video')
                                            bg-blue-100 text-blue-800
                                            @break
                                        @default
                                            bg-gray-100 text-gray-800
                                            @break
                                    @endswitch
                                ">
                                    @switch($item->type)
                                        @case('pdf')
                                            PDF
                                            @break
                                        @case('video')
                                            ویدیو
                                            @break
                                        @default
                                            {{ $item->type }}
                                            @break
                                    @endswitch
                                </span>
                                
                                <svg class="w-4 h-4 text-gray-400 group-hover:text-indigo-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>

            <div class="mt-6">
                {{ $items->onEachSide(1)->links() }}
            </div>
        @else
            <!-- Empty State -->
            <div class="text-center py-16">
                <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-gray-100 mb-6">
                    <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">منبعی یافت نشد</h3>
                <p class="text-gray-500">در حال حاضر هیچ فایل PDF یا ویدیویی در این بخش موجود نیست.</p>
            </div>
        @endif
    </div>
</div>
