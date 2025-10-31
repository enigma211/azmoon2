<div class="mx-auto max-w-3xl p-4 sm:p-6">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900 mb-1">فهرست آزمون‌ها</h1>
        <p class="text-sm text-gray-500">آزمون مورد نظر خود را انتخاب کنید</p>
    </div>

    <!-- Exams Grid -->
    <div class="grid gap-4 sm:grid-cols-2">
        @foreach($exams as $exam)
            <a href="{{ route('exam.play', ['exam' => $exam->id]) }}" 
               wire:navigate 
               class="group relative bg-white rounded-xl border border-gray-200 p-5 hover:border-indigo-300 hover:shadow-lg transition-all duration-300 overflow-hidden">
                
                <!-- Subtle gradient overlay on hover -->
                <div class="absolute inset-0 bg-gradient-to-br from-indigo-50/0 to-purple-50/0 group-hover:from-indigo-50/50 group-hover:to-purple-50/30 transition-all duration-300"></div>
                
                <!-- Content -->
                <div class="relative">
                    <!-- Icon -->
                    <div class="mb-3 inline-flex items-center justify-center w-10 h-10 rounded-lg bg-indigo-50 text-indigo-600 group-hover:bg-indigo-100 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    
                    <!-- Title -->
                    <h3 class="font-semibold text-gray-900 mb-2 group-hover:text-indigo-600 transition-colors">
                        {{ $exam->title }}
                    </h3>
                    
                    <!-- Description -->
                    @if($exam->description)
                        <p class="text-sm text-gray-500 line-clamp-2 leading-relaxed">
                            {{ \Illuminate\Support\Str::limit($exam->description, 100) }}
                        </p>
                    @endif
                    
                    <!-- Arrow icon -->
                    <div class="mt-3 flex items-center text-indigo-600 text-sm font-medium">
                        <span class="group-hover:translate-x-1 transition-transform">شروع آزمون</span>
                        <svg class="w-4 h-4 mr-1 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                    </div>
                </div>
            </a>
        @endforeach
    </div>
    
    <!-- Empty state -->
    @if($exams->isEmpty())
        <div class="text-center py-12">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 mb-4">
                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
            </div>
            <p class="text-gray-500">هنوز آزمونی در این نوبت موجود نیست.</p>
        </div>
    @endif
</div>
