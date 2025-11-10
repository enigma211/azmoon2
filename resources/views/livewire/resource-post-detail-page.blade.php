<div class="min-h-screen bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50 py-12">
    <div class="container mx-auto px-4 max-w-4xl">
        

        <!-- Post Content -->
        <article class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <!-- Header -->
            <div class="p-8 border-b border-gray-200">
                <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">{{ $post->title }}</h1>
                
                @if($post->description)
                    <p class="text-lg text-gray-600 mb-4">{{ $post->description }}</p>
                @endif

                <!-- Meta Info -->
                <div class="flex items-center space-x-6 space-x-reverse text-sm text-gray-500">
                    <span class="flex items-center">
                        <svg class="w-5 h-5 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        @php
                            $published = $post->published_at;
                            $dateText = '';
                            if ($published) {
                                if (function_exists('jdate')) {
                                    try {
                                        $dateText = jdate($published, 'Y/m/d');
                                    } catch (\Throwable $e) {
                                        $dateText = $published->format('Y/m/d');
                                    }
                                } elseif (class_exists(\Morilog\Jalali\Jalalian::class)) {
                                    try {
                                        $dateText = \Morilog\Jalali\Jalalian::fromDateTime($published)->format('Y/m/d');
                                    } catch (\Throwable $e) {
                                        $dateText = $published->format('Y/m/d');
                                    }
                                } else {
                                    $dateText = $published->format('Y/m/d');
                                }
                            }
                        @endphp
                        {{ $dateText }}
                    </span>
                    <span class="flex items-center">
                        <svg class="w-5 h-5 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                        {{ number_format($post->view_count) }} بازدید
                    </span>
                    @if($category->type === 'document')
                        <span class="flex items-center">
                            <svg class="w-5 h-5 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                            </svg>
                            {{ number_format($post->download_count) }} دانلود
                        </span>
                    @endif
                </div>
            </div>

            <!-- Video Content -->
            @if($category->type === 'video' && $post->video_embed_code)
                <div class="p-8">
                    <div class="aspect-video bg-gray-900 rounded-xl overflow-hidden">
                        {!! $post->video_embed_code !!}
                    </div>
                </div>
            @endif

            <!-- PDF Download Button -->
            @if($category->type === 'document' && $post->pdf_file)
                <div class="p-8 bg-gradient-to-r from-indigo-50 to-purple-50">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 mb-1">دانلود فایل PDF</h3>
                            @if($post->file_size)
                                <p class="text-sm text-gray-600">حجم فایل: {{ $post->getFileSizeFormatted() }}</p>
                            @endif
                        </div>
                        <button wire:click="downloadPdf" 
                                class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white px-6 py-3 rounded-xl font-semibold hover:shadow-lg transition-all duration-300 flex items-center">
                            <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                            </svg>
                            دانلود فایل
                        </button>
                    </div>
                </div>
            @endif

            <!-- Text Content -->
            @if($post->content)
                <div class="p-8 prose prose-lg max-w-none">
                    {!! $post->content !!}
                </div>
            @endif
        </article>

        <!-- Back Button -->
        <div class="mt-8 text-center">
            <a href="{{ route('educational-resources.posts', [$examType->slug, $category->slug]) }}" 
               wire:navigate
               class="inline-flex items-center text-indigo-600 hover:text-indigo-800 font-semibold">
                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"></path>
                </svg>
                بازگشت به لیست
            </a>
        </div>
    </div>
</div>
