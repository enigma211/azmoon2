<div class="mx-auto max-w-3xl p-4 sm:p-6">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900 mb-1">فهرست آزمون‌ها</h1>
        <p class="text-sm text-gray-500">آزمون مورد نظر خود را انتخاب کنید</p>
    </div>

    <!-- Exams Grid -->
    <div class="grid gap-4 sm:grid-cols-2">
        @foreach($exams as $exam)
            @php
                // Array of beautiful gradients for exams
                $examGradients = [
                    'from-indigo-400 via-purple-400 to-pink-400',
                    'from-cyan-400 via-blue-400 to-indigo-400',
                    'from-emerald-400 via-teal-400 to-cyan-400',
                    'from-amber-400 via-orange-400 to-red-400',
                    'from-pink-400 via-rose-400 to-red-400',
                    'from-violet-400 via-purple-400 to-fuchsia-400',
                    'from-lime-400 via-green-400 to-emerald-400',
                    'from-orange-400 via-amber-400 to-yellow-400',
                ];
                $examGradient = $examGradients[$loop->index % count($examGradients)];
            @endphp
            <a href="{{ route('exam.play', ['exam' => $exam->id]) }}" 
               wire:navigate 
               class="group relative bg-gradient-to-br {{ $examGradient }} rounded-xl p-5 hover:shadow-2xl hover:scale-[1.02] transition-all duration-300 overflow-hidden">
                
                <!-- Overlay for better text readability -->
                <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent"></div>
                
                <!-- Content -->
                <div class="relative">
                    <!-- Title -->
                    <h3 class="font-bold text-white mb-3 text-lg drop-shadow-md">
                        {{ $exam->title }}
                    </h3>
                    
                    <!-- Meta Info -->
                    <div class="flex items-center gap-4 mb-3 text-sm text-white drop-shadow">
                        @php
                            $questionCount = $exam->questions()->where('is_deleted', false)->count();
                        @endphp
                        @if($questionCount > 0)
                            <div class="flex items-center gap-1.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span>{{ $questionCount }} سوال</span>
                            </div>
                        @endif
                        
                        @if($exam->duration_minutes)
                            <div class="flex items-center gap-1.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span>{{ $exam->duration_minutes }} دقیقه</span>
                            </div>
                        @else
                            <div class="flex items-center gap-1.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span>بدون محدودیت زمان</span>
                            </div>
                        @endif
                    </div>
                    
                    <!-- Description -->
                    @if($exam->description)
                        <p class="text-sm text-white/90 line-clamp-2 leading-relaxed drop-shadow">
                            {{ \Illuminate\Support\Str::limit($exam->description, 100) }}
                        </p>
                    @endif
                    
                    <!-- Arrow icon -->
                    <div class="mt-4 flex items-center text-white text-sm font-bold bg-black/20 backdrop-blur-sm rounded-lg px-4 py-2.5 w-fit group-hover:bg-black/30 transition-all shadow-lg">
                        <span class="group-hover:translate-x-1 transition-transform">شروع آزمون</span>
                        <svg class="w-4 h-4 mr-1.5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"></path>
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
