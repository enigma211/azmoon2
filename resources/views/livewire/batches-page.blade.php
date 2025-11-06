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
            @foreach($batches as $batch)
                @php
                    // Array of beautiful gradients for batches
                    $batchGradients = [
                        'from-purple-400 via-pink-400 to-red-400',
                        'from-blue-400 via-cyan-400 to-teal-400',
                        'from-green-400 via-emerald-400 to-teal-400',
                        'from-orange-400 via-red-400 to-pink-400',
                        'from-indigo-400 via-blue-400 to-cyan-400',
                        'from-fuchsia-400 via-purple-400 to-pink-400',
                        'from-yellow-400 via-orange-400 to-red-400',
                        'from-rose-400 via-pink-400 to-fuchsia-400',
                        'from-teal-400 via-cyan-400 to-blue-400',
                        'from-lime-400 via-green-400 to-emerald-400',
                    ];
                    $batchGradient = $batchGradients[$loop->index % count($batchGradients)];
                @endphp
                <a href="{{ route('exams', ['batch' => $batch->id]) }}" 
                   wire:navigate 
                   class="group relative bg-gradient-to-br {{ $batchGradient }} rounded-2xl shadow-lg hover:shadow-2xl hover:scale-[1.03] transition-all duration-300 overflow-hidden">
                    
                    <!-- Overlay for better text readability -->
                    <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent"></div>
                    
                    <!-- Content -->
                    <div class="relative p-6 text-white">
                        <h3 class="font-bold text-white mb-3 text-xl drop-shadow-sm">
                            {{ $batch->title }}
                        </h3>
                        
                        <!-- Additional Info -->
                        <div class="flex items-center justify-between text-sm text-white/90 mb-4">
                            <div class="flex items-center gap-1.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <span>نوبت آزمون</span>
                            </div>
                            
                            @if($batch->is_active)
                                <span class="inline-flex items-center rounded-full bg-white/30 backdrop-blur-sm text-white text-xs font-medium px-2.5 py-0.5">
                                    فعال
                                </span>
                            @else
                                <span class="inline-flex items-center rounded-full bg-white/20 backdrop-blur-sm text-white/70 text-xs font-medium px-2.5 py-0.5">
                                    غیرفعال
                                </span>
                            @endif
                        </div>
                        
                        <!-- Arrow -->
                        <div class="flex items-center text-white text-sm font-bold bg-black/20 backdrop-blur-sm rounded-lg px-4 py-2.5 w-fit group-hover:bg-black/30 transition-all shadow-lg">
                            <span class="group-hover:translate-x-1 transition-transform">مشاهده آزمون‌ها</span>
                            <svg class="w-4 h-4 mr-1.5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"></path>
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
