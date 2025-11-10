<div class="min-h-screen bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50 py-12">
    <div class="container mx-auto px-4">
        <!-- Header -->
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold text-gray-900 mb-4">منابع آموزشی</h1>
        </div>

        <!-- Exam Types Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($examTypes as $examType)
                <a href="{{ route('educational-resources.categories', $examType->slug) }}" 
                   wire:navigate
                   class="group bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 overflow-hidden transform hover:-translate-y-2">
                    <div class="p-8 text-center">
                        <!-- Icon - ابزار مهندسی (خط کش، تراز، پرگار) -->
                        <div class="w-24 h-24 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-2xl flex items-center justify-center mb-6 mx-auto group-hover:scale-110 transition-transform duration-300 shadow-lg">
                            <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <!-- خط کش و تراز مهندسی -->
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 21h18M3 10h18M3 7l9-4 9 4M4 10v11M20 10v11M8 14v3M12 14v3M16 14v3"/>
                            </svg>
                        </div>

                        <!-- Title -->
                        <h2 class="text-2xl font-bold text-gray-900 mb-2 group-hover:text-indigo-600 transition-colors">
                            {{ $examType->title }}
                        </h2>

                        <!-- Description -->
                        @if($examType->description)
                            <p class="text-gray-600 mb-4">{{ $examType->description }}</p>
                        @endif

                        <!-- Arrow -->
                        <div class="flex items-center justify-center text-indigo-600 font-semibold">
                            <span class="ml-2">مشاهده منابع</span>
                            <svg class="w-5 h-5 transform group-hover:translate-x-2 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                            </svg>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>

        @if($examTypes->isEmpty())
            <div class="text-center py-12">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-gray-200 rounded-full mb-4">
                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                </div>
                <p class="text-gray-600 text-lg">هنوز منابع آموزشی اضافه نشده است.</p>
            </div>
        @endif
    </div>
</div>
