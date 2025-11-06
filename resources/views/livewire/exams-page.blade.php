<div class="mx-auto max-w-3xl p-4 sm:p-6">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900 mb-1">فهرست آزمون‌ها</h1>
        <p class="text-sm text-gray-500">آزمون مورد نظر خود را انتخاب کنید</p>
    </div>

    <!-- Exams Grid -->
    <div class="grid gap-5 sm:grid-cols-2">
        @php
            $examGradients = [
                'bg-gradient-to-br from-rose-100 via-pink-100 to-fuchsia-100 hover:from-rose-200 hover:via-pink-200 hover:to-fuchsia-200',
                'bg-gradient-to-br from-violet-100 via-purple-100 to-indigo-100 hover:from-violet-200 hover:via-purple-200 hover:to-indigo-200',
                'bg-gradient-to-br from-cyan-100 via-sky-100 to-blue-100 hover:from-cyan-200 hover:via-sky-200 hover:to-blue-200',
                'bg-gradient-to-br from-teal-100 via-emerald-100 to-green-100 hover:from-teal-200 hover:via-emerald-200 hover:to-green-200',
                'bg-gradient-to-br from-amber-100 via-orange-100 to-red-100 hover:from-amber-200 hover:via-orange-200 hover:to-red-200',
                'bg-gradient-to-br from-lime-100 via-green-100 to-emerald-100 hover:from-lime-200 hover:via-green-200 hover:to-emerald-200',
                'bg-gradient-to-br from-indigo-100 via-blue-100 to-cyan-100 hover:from-indigo-200 hover:via-blue-200 hover:to-cyan-200',
                'bg-gradient-to-br from-pink-100 via-rose-100 to-red-100 hover:from-pink-200 hover:via-rose-200 hover:to-red-200',
            ];
        @endphp
        @foreach($exams as $index => $exam)
            @php
                $examGradientClass = $examGradients[$index % count($examGradients)];
            @endphp
            <a href="{{ route('exam.play', ['exam' => $exam->id]) }}" 
               wire:navigate 
               class="group relative rounded-xl p-6 hover:shadow-2xl transition-all duration-300 overflow-hidden {{ $examGradientClass }} border-2 border-white/30 shadow-lg">
                
                <!-- Content -->
                <div class="relative">
                    <!-- Title -->
                    <h3 class="font-bold text-gray-900 mb-3 text-lg">
                        {{ $exam->title }}
                    </h3>
                    
                    <!-- Meta Info -->
                    <div class="flex items-center gap-4 mb-3 text-sm">
                        @php
                            $questionCount = $exam->questions()->where('is_deleted', false)->count();
                        @endphp
                        @if($questionCount > 0)
                            <span class="text-gray-900 font-medium">{{ $questionCount }} سوال</span>
                        @endif
                        
                        @if($exam->duration_minutes)
                            <span class="text-gray-900 font-medium">{{ $exam->duration_minutes }} دقیقه</span>
                        @else
                            <span class="text-gray-900 font-medium">بدون محدودیت زمان</span>
                        @endif
                    </div>
                    
                    <!-- Description -->
                    @if($exam->description)
                        <p class="text-sm text-gray-900 line-clamp-2 leading-relaxed mb-3">
                            {{ \Illuminate\Support\Str::limit($exam->description, 100) }}
                        </p>
                    @endif
                    
                    <!-- Arrow icon -->
                    <div class="flex items-center text-gray-900 text-sm font-semibold">
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
