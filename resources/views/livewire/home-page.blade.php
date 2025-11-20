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

    <!-- Footer با لوگوی اینماد - فقط دسکتاپ -->
    <footer class="site-footer" style="margin-top: 5rem; padding: 2.5rem 0 2rem; background-color: #f3f4f6; border-top: 2px solid #d1d5db;">
        <div class="flex flex-col items-center justify-center space-y-4">
            <!-- متن کپی‌رایت -->
            <div class="text-center text-sm text-gray-600">
                <p>© {{ date('Y') }} {{ config('app.name', 'آزمون کده') }} - تمامی حقوق محفوظ است.</p>
            </div>
        </div>
    </footer>
    <style>
        /* نمایش فقط روی دسکتاپ بدون Tailwind */
        @media (max-width: 1023.98px) { .site-footer { display: none; } }
        @media (min-width: 1024px) { .site-footer { display: block; } }
    </style>
</div>
