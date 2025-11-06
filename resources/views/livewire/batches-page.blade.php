<div class="min-h-screen bg-gradient-to-br from-gray-50 to-white">
    <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header -->
        <header class="mb-8">
            <div class="text-center">
                <h1 class="text-3xl font-bold text-gray-900 mb-2">نوبت‌های آزمون</h1>
                <p class="text-gray-600">نوبت مورد نظر خود را برای شرکت در آزمون انتخاب کنید</p>
            </div>
        </header>

        <!-- Batches Grid -->
        <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
            @php
                $gradients = [
                    'bg-gradient-to-br from-pink-400 via-rose-400 to-red-400 hover:from-pink-500 hover:via-rose-500 hover:to-red-500',
                    'bg-gradient-to-br from-purple-400 via-violet-400 to-indigo-400 hover:from-purple-500 hover:via-violet-500 hover:to-indigo-500',
                    'bg-gradient-to-br from-blue-400 via-cyan-400 to-teal-400 hover:from-blue-500 hover:via-cyan-500 hover:to-teal-500',
                    'bg-gradient-to-br from-green-400 via-emerald-400 to-teal-400 hover:from-green-500 hover:via-emerald-500 hover:to-teal-500',
                    'bg-gradient-to-br from-yellow-400 via-amber-400 to-orange-400 hover:from-yellow-500 hover:via-amber-500 hover:to-orange-500',
                    'bg-gradient-to-br from-orange-400 via-red-400 to-pink-400 hover:from-orange-500 hover:via-red-500 hover:to-pink-500',
                    'bg-gradient-to-br from-fuchsia-400 via-purple-400 to-pink-400 hover:from-fuchsia-500 hover:via-purple-500 hover:to-pink-500',
                    'bg-gradient-to-br from-sky-400 via-blue-400 to-indigo-400 hover:from-sky-500 hover:via-blue-500 hover:to-indigo-500',
                ];
            @endphp
            @foreach($batches as $index => $batch)
                @php
                    $gradientClass = $gradients[$index % count($gradients)];
                @endphp
                <a href="{{ route('exams', ['batch' => $batch->id]) }}" 
                   wire:navigate 
                   class="group relative rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 overflow-hidden {{ $gradientClass }} border-2 border-white/30">
                    
                    <!-- Content -->
                    <div class="p-6">
                        <h3 class="font-bold text-gray-900 mb-3 text-lg line-clamp-2">
                            {{ $batch->title }}
                        </h3>
                        
                        <!-- Additional Info -->
                        <div class="flex items-center justify-between text-sm mb-4">
                            <span class="text-gray-900 font-medium">نوبت آزمون</span>
                            
                            @if($batch->is_active)
                                <span class="inline-flex items-center rounded-full bg-green-500 text-white text-xs font-medium px-3 py-1 shadow-sm">
                                    فعال
                                </span>
                            @else
                                <span class="inline-flex items-center rounded-full bg-gray-400 text-white text-xs font-medium px-3 py-1 shadow-sm">
                                    غیرفعال
                                </span>
                            @endif
                        </div>
                        
                        <!-- Arrow -->
                        <div class="flex items-center text-gray-900 text-sm font-semibold">
                            <span class="group-hover:translate-x-1 transition-transform">مشاهده آزمون‌ها</span>
                            <svg class="w-4 h-4 mr-1 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                            </svg>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
        
        <!-- Empty State -->
        @if($batches->isEmpty())
            <div class="text-center py-16">
                <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-gray-100 mb-6">
                    <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">نوبتی یافت نشد</h3>
                <p class="text-gray-500">در حال حاضر هیچ نوبت آزمونی در این دامنه موجود نیست.</p>
            </div>
        @endif
    </div>
</div>
