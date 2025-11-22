<div>
    @php
        $activeSub = auth()->check() ? auth()->user()->activeSubscription()->first() : null;
        $isTrial = $activeSub && $activeSub->subscriptionPlan->price_toman <= 0 && $activeSub->ends_at;
        $hoursRemaining = $isTrial ? now()->diffInHours($activeSub->ends_at, false) : 0;
    @endphp

    @if($isTrial && $hoursRemaining > 0)
        <div class="bg-amber-50 border-b border-amber-200">
            <div class="max-w-7xl mx-auto py-3 px-3 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between flex-wrap">
                    <div class="w-0 flex-1 flex items-center">
                        <span class="flex p-2 rounded-lg bg-amber-500">
                            <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </span>
                        <p class="mr-3 font-medium text-amber-900 truncate">
                            <span class="md:hidden">هدیه ثبت‌نام: {{ $hoursRemaining }} ساعت مانده</span>
                            <span class="hidden md:inline">🎁 اشتراک هدیه ثبت‌نام فعال است: {{ $hoursRemaining }} ساعت فرصت دارید تا سامانه را رایگان تست کنید.</span>
                        </p>
                    </div>
                    <div class="order-3 mt-2 flex-shrink-0 w-full sm:order-2 sm:mt-0 sm:w-auto">
                        <a href="{{ route('domains') }}" class="flex items-center justify-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-amber-800 bg-white hover:bg-amber-50">
                            شروع آزمون
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif

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
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach ($domains as $domain)
                        <a href="{{ route('batches', $domain) }}" class="group block p-6 rounded-xl bg-white shadow hover:shadow-md transition">
                            <div class="flex flex-col items-center text-center">
                                {{-- Icon --}}
                                @svg('heroicon-o-academic-cap', 'w-10 h-10 text-amber-600 group-hover:text-amber-700')
                                {{-- Title --}}
                                <div class="mt-3 font-semibold text-gray-800">{{ $domain->title }}</div>
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif
        </section>
    </div>

</div>
