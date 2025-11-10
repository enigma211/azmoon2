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
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 max-w-4xl mx-auto">
            @foreach($examType->resourceCategories as $category)
                <a href="{{ route('educational-resources.posts', [$examType->slug, $category->slug]) }}" 
                   wire:navigate
                   class="group bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 overflow-hidden transform hover:-translate-y-2">
                    <div class="p-8">
                        <!-- Icon -->
                        <div class="w-20 h-20 mx-auto mb-6 bg-gradient-to-br {{ $category->type === 'video' ? 'from-green-400 to-emerald-600' : 'from-blue-400 to-indigo-600' }} rounded-2xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                            @if($category->type === 'video')
                                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            @else
                                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
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
