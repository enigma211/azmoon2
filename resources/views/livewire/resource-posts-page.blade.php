<div class="min-h-screen bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50 py-12">
    <div class="container mx-auto px-4">
        

        <!-- Header -->
        <div class="text-center mb-12">
            @if($category->type === 'video')
                <h1 class="text-2xl font-bold text-gray-900 mb-4">ویدیوهای آموزشی آزمون {{ $examType->title }}</h1>
            @elseif($category->type === 'document')
                <h1 class="text-2xl font-bold text-gray-900 mb-4">جزوات آموزشی آزمون {{ $examType->title }}</h1>
            @else
                <h1 class="text-2xl font-bold text-gray-900 mb-4">{{ $category->title }}</h1>
            @endif
            @if($category->description)
                <p class="text-base text-gray-600">{{ $category->description }}</p>
            @endif
        </div>

        <!-- Posts Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            @foreach($posts as $post)
                <a href="{{ route('educational-resources.post', [$examType->slug, $category->slug, $post->slug]) }}" 
                   wire:navigate
                   class="group bg-white rounded-xl shadow-md hover:shadow-xl transition-all duration-300 overflow-hidden">
                    <!-- Thumbnail -->
                    @if($post->thumbnail)
                        <div class="aspect-video overflow-hidden bg-gray-200">
                            <img src="{{ $post->getThumbnailUrl() }}" 
                                 alt="{{ $post->title }}"
                                 class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300">
                        </div>
                    @else
                        <div class="aspect-video bg-gradient-to-br {{ $category->type === 'video' ? 'from-green-400 to-emerald-600' : 'from-blue-400 to-indigo-600' }} flex items-center justify-center">
                            @if($category->type === 'video')
                                <svg class="w-12 h-12 text-white opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            @else
                                <svg class="w-12 h-12 text-white opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            @endif
                        </div>
                    @endif

                    <!-- Content -->
                    <div class="p-4">
                        <h3 class="text-base font-bold text-gray-900 mb-2 group-hover:text-indigo-600 transition-colors line-clamp-2">
                            {{ $post->title }}
                        </h3>

                        @if($post->description)
                            <p class="text-gray-600 text-sm mb-4 line-clamp-2">{{ $post->description }}</p>
                        @endif

                        <!-- Meta -->
                        <div class="flex items-center justify-between text-sm text-gray-500">
                            <div class="flex items-center space-x-4 space-x-reverse">
                                <span class="flex items-center">
                                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                    {{ number_format($post->view_count) }}
                                </span>
                                @if($category->type === 'document')
                                    <span class="flex items-center">
                                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                        </svg>
                                        {{ number_format($post->download_count) }}
                                    </span>
                                @endif
                            </div>
                            @if($category->type === 'document' && $post->file_size)
                                <span class="text-xs bg-gray-100 px-2 py-1 rounded">{{ $post->getFileSizeFormatted() }}</span>
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
