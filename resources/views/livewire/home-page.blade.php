<div>
    <div class="max-w-4xl mx-auto px-4">
        <!-- Slider Section -->
        <section class="py-6">
            @if(($sliders ?? collect())->isNotEmpty())
                <!-- Slider Container -->
                <div class="relative overflow-hidden rounded-xl shadow-lg bg-white" x-data="slider({{ $sliders->count() }})">
                    <!-- Slides -->
                    <div class="relative h-48 sm:h-56 md:h-64 lg:h-64">
                        @foreach ($sliders as $index => $slider)
                            <div 
                                x-show="currentSlide === {{ $index }}"
                                x-transition:enter="transition ease-out duration-500"
                                x-transition:enter-start="opacity-0 transform translate-x-full"
                                x-transition:enter-end="opacity-100 transform translate-x-0"
                                x-transition:leave="transition ease-in duration-500"
                                x-transition:leave-start="opacity-100 transform translate-x-0"
                                x-transition:leave-end="opacity-0 transform -translate-x-full"
                                class="absolute inset-0"
                            >
                                @if($slider->link)
                                    <a href="{{ $slider->link }}" class="block w-full h-full">
                                        <img 
                                            src="{{ Storage::url($slider->image) }}" 
                                            alt="{{ $slider->title ?? 'اسلایدر' }}" 
                                            class="w-full h-full object-cover"
                                        >
                                    </a>
                                @else
                                    <img 
                                        src="{{ Storage::url($slider->image) }}" 
                                        alt="{{ $slider->title ?? 'اسلایدر' }}" 
                                        class="w-full h-full object-cover"
                                    >
                                @endif
                            </div>
                        @endforeach
                    </div>

                    <!-- Navigation Arrows -->
                    @if($sliders->count() > 1)
                        <!-- Dots Indicator -->
                        <div class="absolute bottom-2 sm:bottom-3 md:bottom-4 left-1/2 -translate-x-1/2 flex gap-1.5 sm:gap-2 z-10">
                            @foreach ($sliders as $index => $slider)
                                <button 
                                    @click="currentSlide = {{ $index }}"
                                    :class="currentSlide === {{ $index }} ? 'bg-white' : 'bg-white/50'"
                                    class="w-2 h-2 sm:w-2.5 sm:h-2.5 md:w-3 md:h-3 rounded-full transition"
                                    aria-label="اسلاید {{ $index + 1 }}"
                                ></button>
                            @endforeach
                        </div>
                    @endif
                </div>

                <script>
                    function slider(totalSlides) {
                        return {
                            currentSlide: 0,
                            totalSlides: totalSlides,
                            autoplayInterval: null,

                            init() {
                                this.startAutoplay();
                            },

                            nextSlide() {
                                this.currentSlide = (this.currentSlide + 1) % this.totalSlides;
                                this.resetAutoplay();
                            },

                            prevSlide() {
                                this.currentSlide = (this.currentSlide - 1 + this.totalSlides) % this.totalSlides;
                                this.resetAutoplay();
                            },

                            startAutoplay() {
                                if (this.totalSlides > 1) {
                                    this.autoplayInterval = setInterval(() => {
                                        this.nextSlide();
                                    }, 7000); // تغییر هر 7 ثانیه
                                }
                            },

                            resetAutoplay() {
                                clearInterval(this.autoplayInterval);
                                this.startAutoplay();
                            }
                        }
                    }
                </script>
            @endif
        </section>

        <!-- Exam Domains Section -->
        <section class="py-6">

            @if(($domains ?? collect())->isEmpty())
                <div class="p-6 rounded-lg bg-white shadow text-gray-500 text-sm">
                    هنوز دسته‌بندی فعالی وجود ندارد.
                </div>
            @else
                <div class="flex flex-col gap-5">
                    @foreach ($domains as $domain)
                        @php
                            $isNezam = str_contains($domain->title, 'نظام مهندسی');
                            $isKarshenas = str_contains($domain->title, 'کارشناس رسمی');
                            
                            $subtitle = $domain->description;

                            // Define colors and text based on type
                            if ($isNezam) {
                                $accentColor = 'bg-blue-600';
                                $shadowColor = 'shadow-blue-100';
                                $btnBgClass = 'bg-blue-600 group-hover:bg-blue-700';
                                if (!$subtitle) $subtitle = 'شبیه سازی دقیق سوالات دوره های گذشته';
                            } elseif ($isKarshenas) {
                                $accentColor = 'bg-amber-600';
                                $shadowColor = 'shadow-amber-100';
                                $btnBgClass = 'bg-[#E67E22] group-hover:bg-[#d35400]';
                                if (!$subtitle) $subtitle = 'بانک جامع سوالات سال های قبل';
                            } else {
                                $accentColor = 'bg-gray-400';
                                $shadowColor = 'shadow-gray-100';
                                $btnBgClass = 'bg-gray-600 group-hover:bg-gray-700';
                                if (!$subtitle) $subtitle = 'مجموعه آزمون‌های تخصصی و شبیه‌سازی';
                            }
                        @endphp
                        
                        <a href="{{ route('batches', $domain) }}" class="group relative block w-full bg-white rounded-2xl p-5 shadow-lg {{ $shadowColor }} hover:shadow-xl transition-all duration-300 overflow-hidden border border-gray-50">
                            <!-- Accent Bar (Left Side) -->
                            <div class="absolute left-0 top-3 bottom-3 w-1.5 rounded-r-full {{ $accentColor }}"></div>

                            <div class="flex items-center justify-between relative z-10 pl-3">
                                <!-- Content (Right Side in RTL) -->
                                <div class="flex flex-col items-start gap-1.5 flex-1">
                                    <!-- Title -->
                                    <h3 class="text-lg font-bold text-gray-800 leading-tight">
                                        {{ $domain->title }}
                                    </h3>
                                    <!-- Subtitle -->
                                    <p class="text-gray-500 text-xs sm:text-sm font-medium leading-relaxed pr-1">
                                        {{ $subtitle }}
                                    </p>
                                </div>

                                <!-- Button (Left Side in RTL) -->
                                <div class="shrink-0">
                                    <div class="flex items-center gap-1 {{ $btnBgClass }} text-white text-sm font-bold py-2 px-3 sm:px-4 rounded-lg shadow-md transition-colors">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                                        </svg>
                                        <span>شروع آزمون</span>
                                    </div>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif
        </section>
    </div>

</div>
