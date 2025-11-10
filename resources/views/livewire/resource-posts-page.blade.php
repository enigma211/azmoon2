<div class="min-h-screen bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50 py-12">
    <div class="container mx-auto px-4">
        

        <!-- Header -->
        <div class="mb-8">
            @if($category->type === 'video')
                <div class="text-center">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-green-500 to-emerald-600 rounded-2xl mb-4 shadow-lg">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h1 class="text-3xl font-bold bg-gradient-to-r from-green-600 to-emerald-600 bg-clip-text text-transparent mb-2">ویدیوهای آموزشی آزمون {{ $examType->title }}</h1>
                    <p class="text-gray-600 text-lg">مجموعه کامل ویدیوهای آموزشی برای آمادگی آزمون</p>
                </div>
            @elseif($category->type === 'document')
                <div class="text-center">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl mb-4 shadow-lg">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <h1 class="text-3xl font-bold bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent mb-2">جزوات آموزشی آزمون {{ $examType->title }}</h1>
                    <p class="text-gray-600 text-lg">مجموعه کامل جزوات و PDFهای آموزشی برای آمادگی آزمون</p>
                </div>
            @else
                <div class="text-center">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-purple-500 to-pink-600 rounded-2xl mb-4 shadow-lg">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                    </div>
                    <h1 class="text-3xl font-bold bg-gradient-to-r from-purple-600 to-pink-600 bg-clip-text text-transparent mb-2">{{ $category->title }}</h1>
                    <p class="text-gray-600 text-lg">منابع آموزشی برای آمادگی آزمون {{ $examType->title }}</p>
                </div>
            @endif
        </div>

        <!-- Posts Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($posts as $post)
                <a href="{{ route('educational-resources.post', [$examType->slug, $category->slug, $post->slug]) }}" 
                   wire:navigate
                   class="group bg-gradient-to-br from-white to-gray-50 rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 overflow-hidden border border-gray-100 hover:border-indigo-200 hover:scale-105">
                    <div class="p-6">
                        <!-- Content only (no thumbnail) -->
                        <div class="flex-1">
                            <h3 class="text-base font-bold text-gray-800 mb-3 group-hover:text-indigo-700 transition-colors line-clamp-2">
                                {{ $post->title }}
                            </h3>

                            @if($post->description)
                                <p class="text-gray-600 text-sm mb-0 line-clamp-3 leading-relaxed">{{ $post->description }}</p>
                            @endif
                        </div>
                    </div>
                </a>
            @endforeach
        </div>

        @if($posts->isEmpty())
            <div class="text-center py-12">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-gray-200 rounded-full mb-4">
                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <p class="text-gray-600 text-lg">هنوز محتوایی اضافه نشده است.</p>
            </div>
        @endif

        <!-- Pagination -->
        <div class="mt-8">
            {{ $posts->links() }}
        </div>
    </div>
</div>
