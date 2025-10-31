<div class="mx-auto max-w-4xl p-4">
    <!-- Header -->
    <div class="mb-8 text-center">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">فهرست آزمون‌ها</h1>
        <p class="text-gray-600">آزمون مورد نظر خود را انتخاب کنید</p>
    </div>

    <!-- Exams Grid -->
    <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
        @foreach($exams as $exam)
            <a href="{{ route('exam.play', ['exam' => $exam->id]) }}" 
               wire:navigate 
               class="group block bg-white rounded-xl shadow-md hover:shadow-xl transition-all duration-300 border border-gray-200 hover:border-indigo-300 overflow-hidden">
                
                <!-- Card Header -->
                <div class="bg-gradient-to-r from-indigo-500 to-purple-600 p-4 text-white">
                    <div class="flex items-center justify-between">
                        <div class="w-12 h-12 rounded-full bg-white/20 flex items-center justify-center backdrop-blur">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <div class="text-right">
                            <div class="text-xs opacity-90">آزمون</div>
                            <div class="text-sm font-semibold">{{ $exam->questions_count ?? 0 }} سوال</div>
                        </div>
                    </div>
                </div>
                
                <!-- Card Body -->
                <div class="p-5">
                    <h3 class="text-lg font-bold text-gray-900 mb-2 group-hover:text-indigo-600 transition-colors">
                        {{ $exam->title }}
                    </h3>
                    
                    @if($exam->description)
                        <p class="text-sm text-gray-600 mb-4 leading-relaxed">
                            {{ \Illuminate\Support\Str::limit($exam->description, 120) }}
                        </p>
                    @endif
                    
                    <!-- Exam Info -->
                    <div class="flex items-center justify-between text-xs text-gray-500 mb-4">
                        @if($exam->duration_minutes)
                            <div class="flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                {{ $exam->duration_minutes }} دقیقه
                            </div>
                        @endif
                        <div class="flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                            {{ $exam->questions_count ?? 0 }} سوال
                        </div>
                    </div>
                    
                    <!-- Start Button -->
                    <button class="w-full bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-lg py-2.5 px-4 text-sm font-medium hover:from-indigo-700 hover:to-purple-700 transition-all duration-300 flex items-center justify-center gap-2 group-hover:scale-105 transform">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        شروع آزمون
                    </button>
                </div>
                
                <!-- Hover Effect Overlay -->
                <div class="absolute inset-0 bg-gradient-to-r from-indigo-600/5 to-purple-600/5 opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none"></div>
            </a>
        @endforeach
    </div>

    <!-- Empty State -->
    @if($exams->isEmpty())
        <div class="text-center py-12">
            <div class="w-20 h-20 rounded-full bg-gray-100 mx-auto mb-4 flex items-center justify-center">
                <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">آزمونی یافت نشد</h3>
            <p class="text-gray-600">در حال حاضر آزمونی برای این نوبت تعریف نشده است.</p>
        </div>
    @endif
</div>
