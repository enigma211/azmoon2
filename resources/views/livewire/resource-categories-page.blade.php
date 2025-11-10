<div class="min-h-screen bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50 py-12">
    <div class="container mx-auto px-4">
        <!-- Breadcrumb -->
        <nav class="mb-8">
            <ol class="flex items-center space-x-2 space-x-reverse text-sm">
                <li>
                    <a href="{{ route('educational-resources') }}" wire:navigate class="text-indigo-600 hover:text-indigo-800">
                        منابع آموزشی
                    </a>
                </li>
                <li class="text-gray-400">/</li>
                <li class="text-gray-600">{{ $examType->title }}</li>
            </ol>
        </nav>

        <!-- Header -->
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold text-gray-900 mb-4">{{ $examType->title }}</h1>
            @if($examType->description)
                <p class="text-lg text-gray-600">{{ $examType->description }}</p>
            @endif
        </div>

        <!-- Categories Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-12 max-w-5xl mx-auto">
            @foreach($examType->resourceCategories as $category)
                <a href="{{ route('educational-resources.posts', [$examType->slug, $category->slug]) }}" 
                   wire:navigate
                   class="group bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 overflow-hidden transform hover:-translate-y-2">
                    <div class="p-8">
                        <!-- Icon -->
                        <div class="w-24 h-24 mx-auto mb-6 {{ $category->type === 'video' ? 'bg-red-500' : 'bg-indigo-600' }} rounded-2xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300 shadow-lg">
                            @if($category->type === 'video')
                                <!-- آیکون ویدیو -->
                                <svg class="w-12 h-12 text-white" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M8 5v14l11-7z"/>
                                </svg>
                            @else
                                <!-- آیکون کتاب -->
                                <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                </svg>
                            @endif
                        </div>

                        <!-- Title -->
                        <h2 class="text-2xl font-bold text-gray-900 mb-3 text-center group-hover:text-indigo-600 transition-colors">
                            {{ $category->title }}
                        </h2>

                        <!-- Description -->
                        @if($category->description)
                            <p class="text-gray-600 text-center mb-4">{{ $category->description }}</p>
                        @endif

                        <!-- Stats -->
                        <div class="flex items-center justify-center text-sm text-gray-500">
                            <span>{{ $category->posts()->count() }} مطلب</span>
                        </div>

                        <!-- Arrow -->
                        <div class="flex items-center justify-center text-indigo-600 font-semibold mt-4">
                            <span class="ml-2">مشاهده محتوا</span>
                            <svg class="w-5 h-5 transform group-hover:translate-x-2 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                            </svg>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</div>
