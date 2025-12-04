<div class="mx-auto max-w-md p-4 sm:p-6">
    <!-- Header -->
    <div class="mb-8 text-center">
        <h1 class="text-2xl font-bold text-gray-900">فهرست آزمون‌ها</h1>
        
        @if($canAccess)
            <!-- Alerts Container -->
            <div class="mt-6 space-y-3">
                <!-- Alert 1 -->
                <div class="p-4 bg-amber-50 border border-amber-200 rounded-xl text-right">
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-amber-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                        <div>
                            <p class="text-sm font-bold text-amber-800 mb-1">هشدار ۱:</p>
                            <p class="text-xs text-amber-800 leading-relaxed">
                                این آزمون‌ها دارای نمره منفی می‌باشند و هر ۳ سوال منفی، ۱ سوال مثبت را از بین می‌برد.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Alert 2 -->
                <div class="p-4 bg-blue-50 border border-blue-200 rounded-xl text-right">
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div>
                            <p class="text-sm font-bold text-blue-800 mb-1">هشدار ۲:</p>
                            <p class="text-xs text-blue-800 leading-relaxed">
                                کاربر گرامی برای استفاده راحت‌تر از سامانه، می‌توانید از طریق مرورگر کامپیوتر به وب‌سایت ما مراجعه کنید:
                                <a href="https://www.azmoonkade.com" class="font-bold underline decoration-blue-400 hover:text-blue-600 mr-1 dir-ltr inline-block">https://www.azmoonkade.com</a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <!-- Access Info Message -->
            <div class="mt-6 p-4 bg-gray-50 border border-gray-200 rounded-xl text-center">
                <p class="text-sm text-gray-600">
                    برای شرکت در آزمون‌ها نیاز به اشتراک ویژه دارید. می‌توانید عناوین آزمون‌ها را مشاهده کنید.
                </p>
            </div>
        @endif
    </div>

    <!-- Exams List -->
    <div class="flex flex-col gap-5">
        @php
            $styles = [
                ['color' => 'bg-rose-500', 'text' => 'text-rose-600', 'border' => 'border-rose-100', 'shadow' => 'shadow-rose-100'],
                ['color' => 'bg-violet-600', 'text' => 'text-violet-600', 'border' => 'border-violet-100', 'shadow' => 'shadow-violet-100'],
                ['color' => 'bg-blue-600', 'text' => 'text-blue-600', 'border' => 'border-blue-100', 'shadow' => 'shadow-blue-100'],
                ['color' => 'bg-teal-600', 'text' => 'text-teal-600', 'border' => 'border-teal-100', 'shadow' => 'shadow-teal-100'],
                ['color' => 'bg-amber-500', 'text' => 'text-amber-600', 'border' => 'border-amber-100', 'shadow' => 'shadow-amber-100'],
            ];
        @endphp
        
        @foreach($exams as $index => $exam)
            @php
                $style = $styles[$index % count($styles)];
                $accentColor = $style['color'];
                $textColor = $style['text'];
                $shadowColor = $style['shadow'];
            @endphp

            <div class="group relative block bg-white rounded-2xl p-6 shadow-lg {{ $shadowColor }} hover:shadow-xl transition-all duration-300 overflow-hidden border border-gray-50">
                
                <!-- Left Accent Bar -->
                <div class="absolute left-0 top-3 bottom-3 w-1.5 rounded-r-full {{ $canAccess ? $accentColor : 'bg-gray-400' }}"></div>
                
                <!-- Content Container -->
                <div class="relative z-10 pl-3 flex flex-col items-center">
                    
                    <!-- Title -->
                    <h3 class="font-black text-xl text-gray-800 mb-4 text-center leading-tight flex items-center gap-2">
                        {{ $exam->title }}
                        @if(!$canAccess)
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5 text-gray-400">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                            </svg>
                        @endif
                    </h3>
                    
                    <!-- Meta Info -->
                    <div class="flex items-center justify-center gap-6 mb-6 w-full">
                        <!-- Question Count -->
                        @php
                            $questionCount = $exam->questions()->where('is_deleted', false)->count();
                        @endphp
                        <div class="flex items-center gap-1.5 text-gray-600">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 5.25h.008v.008H12v-.008Z" />
                            </svg>
                            <span class="text-sm font-bold">{{ $questionCount }} سوال</span>
                        </div>

                        <!-- Duration -->
                        <div class="flex items-center gap-1.5 text-gray-600">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                            </svg>
                            <span class="text-sm font-bold">
                                {{ $exam->duration_minutes ? $exam->duration_minutes . ' دقیقه' : 'بدون محدودیت' }}
                            </span>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    @if($canAccess)
                        <div class="flex flex-col sm:flex-row gap-3 w-full">
                            <a href="{{ route('exam.play', ['exam' => $exam->id]) }}" wire:navigate class="w-full">
                                <div class="w-full {{ $accentColor }} text-white font-bold text-lg py-3 rounded-xl shadow-md text-center transition-transform hover:scale-[1.02]">
                                    شروع آزمون
                                </div>
                            </a>
                            
                            <a href="{{ route('exam.study', ['exam' => $exam->id]) }}" wire:navigate class="w-full">
                                <div class="w-full bg-emerald-500 text-white font-bold text-lg py-3 rounded-xl shadow-md text-center transition-transform hover:scale-[1.02] flex items-center justify-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
                                    </svg>
                                    مطالعه آموزشی
                                </div>
                            </a>
                        </div>
                    @else
                        <a href="{{ route('profile') }}" wire:navigate class="w-full">
                            <div class="w-full bg-gray-500 text-white font-bold text-lg py-3 rounded-xl shadow-md text-center transition-transform hover:scale-[1.02] flex items-center justify-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                                </svg>
                                خرید اشتراک
                            </div>
                        </a>
                    @endif
                    
                </div>
            </div>
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
