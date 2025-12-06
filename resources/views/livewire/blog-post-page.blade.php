<div class="min-h-screen bg-gray-50 pb-12">
    <!-- Hero Image -->
    <div class="relative h-[400px] w-full overflow-hidden">
        <div class="absolute inset-0 bg-gray-900/50 z-10"></div>
        @if($post->image_path)
            <img src="{{ Storage::url($post->image_path) }}" alt="{{ $post->title }}" class="w-full h-full object-cover">
        @else
            <div class="w-full h-full bg-gradient-to-r from-indigo-600 to-purple-700"></div>
        @endif
        
        <div class="absolute inset-0 z-20 flex items-center justify-center">
            <div class="max-w-4xl mx-auto px-4 text-center text-white">
                <h1 class="text-3xl md:text-4xl lg:text-5xl font-extrabold tracking-tight mb-6 leading-tight">
                    {{ $post->title }}
                </h1>
                <div class="flex items-center justify-center gap-6 text-sm md:text-base text-gray-200">
                    <span class="flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        {{ $post->published_at->format('Y/m/d') }}
                    </span>
                    <span class="flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                        {{ number_format($post->view_count) }} بازدید
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-20 relative z-30">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Content -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-2xl shadow-lg p-6 sm:p-10">
                    <!-- Summary -->
                    @if($post->summary)
                        <div class="bg-indigo-50 border-r-4 border-indigo-500 p-4 mb-8 rounded-l-lg">
                            <p class="text-indigo-900 font-medium leading-relaxed">
                                {{ $post->summary }}
                            </p>
                        </div>
                    @endif

                    <!-- Body -->
                    <article class="prose prose-lg prose-indigo max-w-none text-gray-800 leading-loose">
                        {!! $post->content !!}
                    </article>
                    
                    <!-- Keywords -->
                    @if($post->meta_keywords)
                        <div class="mt-10 pt-6 border-t border-gray-100">
                            <div class="flex flex-wrap gap-2">
                                @foreach(explode(',', $post->meta_keywords) as $keyword)
                                    <span class="bg-gray-100 text-gray-600 px-3 py-1 rounded-full text-sm"># {{ trim($keyword) }}</span>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-1 space-y-6">
                <!-- Recent Posts -->
                <div class="bg-white rounded-2xl shadow-lg p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4 border-b pb-2">آخرین مطالب</h3>
                    <div class="space-y-4">
                        @foreach($recentPosts as $recent)
                            <a href="{{ route('blog.show', $recent->slug) }}" class="flex gap-3 group">
                                <div class="flex-shrink-0 w-20 h-20 rounded-lg overflow-hidden bg-gray-100">
                                    @if($recent->image_path)
                                        <img src="{{ Storage::url($recent->image_path) }}" alt="{{ $recent->title }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center bg-indigo-50 text-indigo-300">
                                            <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h4 class="text-sm font-semibold text-gray-800 group-hover:text-indigo-600 transition-colors line-clamp-2 mb-1">
                                        {{ $recent->title }}
                                    </h4>
                                    <span class="text-xs text-gray-400 block">
                                        {{ $recent->published_at->diffForHumans() }}
                                    </span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
                
                <!-- CTA Box (Example) -->
                <div class="bg-gradient-to-br from-indigo-600 to-purple-700 rounded-2xl shadow-lg p-6 text-white text-center">
                    <h3 class="text-xl font-bold mb-2">آماده آزمون هستید؟</h3>
                    <p class="text-indigo-100 text-sm mb-4">با شرکت در آزمون‌های آزمایشی، شانس قبولی خود را افزایش دهید.</p>
                    <a href="{{ route('domains') }}" class="block w-full bg-white text-indigo-600 font-bold py-2.5 rounded-xl hover:bg-indigo-50 transition-colors shadow-md">
                        مشاهده آزمون‌ها
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
