@php
    // Defensive checks to avoid errors before data is fully wired
    $q = $question ?? null;
@endphp

<div class="min-h-screen bg-gradient-to-br from-indigo-50 via-white to-purple-50">
    <!-- Sticky Header with Exam Info -->
    <div class="sticky top-0 z-40 bg-white/95 backdrop-blur-sm border-b border-gray-200 shadow-sm">
        <div class="max-w-5xl mx-auto px-4 py-4">
            <div class="flex items-center justify-between gap-4">
                <!-- Exam Title -->
                <div class="flex-1">
                    <h1 class="text-lg md:text-xl font-bold text-gray-900 truncate">{{ $this->exam->title }}</h1>
                    <p class="text-xs text-gray-500 mt-0.5">سوال {{ ($index ?? 0) + 1 }} از {{ $total ?? 0 }}</p>
                </div>
                
                <!-- Timer Badge -->
                @if(!is_null($durationSeconds))
                    <div wire:poll.1s="tick" class="flex items-center gap-2 bg-gradient-to-r from-orange-500 to-red-500 text-white px-4 py-2 rounded-full shadow-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span class="font-bold text-sm">{{ floor($remainingSeconds / 60) }}:{{ str_pad($remainingSeconds % 60, 2, '0', STR_PAD_LEFT) }}</span>
                    </div>
                @endif
            </div>
            
            <!-- Progress Bar -->
            @php $pct = ($total ?? 0) > 0 ? intval((($index ?? 0)+1) / ($total ?? 1) * 100) : 0; @endphp
            <div class="mt-3">
                <div class="h-2 w-full rounded-full bg-gray-200 overflow-hidden">
                    <div class="h-2 rounded-full bg-gradient-to-r from-indigo-500 to-purple-500 transition-all duration-300 ease-out" 
                         style="width: {{ $pct }}%"></div>
                </div>
                <div class="flex justify-between items-center mt-1">
                    <span class="text-xs text-gray-600">پیشرفت: {{ $pct }}%</span>
                    <span class="text-xs text-indigo-600 font-medium">{{ $total - ($index ?? 0) - 1 }} سوال باقیمانده</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-4xl mx-auto px-4 py-8">
        @if($q)
            <div wire:key="question-{{ $q->id }}" class="space-y-6">
                <!-- Question Card -->
                <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
                    <!-- Question Number Badge -->
                    <div class="bg-gradient-to-r from-indigo-600 to-purple-600 px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="bg-white/20 backdrop-blur-sm rounded-full px-4 py-1">
                                <span class="text-white font-bold text-sm">سوال {{ ($index ?? 0) + 1 }}</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Question Content -->
                    <div class="p-6 md:p-8">
                        <article class="prose prose-lg max-w-none text-gray-800 leading-relaxed">
                            {!! $q->text !!}
                        </article>

                        @if(!empty($q->image_path))
                            <div class="mt-6">
                                <img src="{{ Storage::url($q->image_path) }}" 
                                     alt="تصویر سوال" 
                                     class="w-full rounded-xl shadow-lg border border-gray-200" />
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Choices Section -->
                @if($q->is_deleted)
                    <div class="bg-gradient-to-r from-amber-50 to-orange-50 rounded-2xl border-2 border-amber-300 p-8 text-center shadow-lg">
                        <div class="flex items-center justify-center gap-3 text-amber-700 mb-3">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                            <span class="text-xl font-bold">این سوال حذف شده است</span>
                        </div>
                        <p class="text-amber-600">این سوال در محاسبه نمره نهایی شما لحاظ نخواهد شد.</p>
                    </div>
                @elseif($q->choices && $q->choices->count())
                    <div class="space-y-3">
                        <h3 class="text-sm font-semibold text-gray-600 mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                            گزینه‌های پاسخ:
                        </h3>
                        @foreach($q->choices as $choice)
                            @php 
                                $inputId = 'q'.$q->id.'_c'.$choice->id;
                                $isSelected = ($answers[$q->id][$choice->id] ?? false);
                            @endphp
                            <label wire:key="choice-{{ $q->id }}-{{ $choice->id }}" 
                                   class="group relative flex items-start gap-4 rounded-xl border-2 p-5 cursor-pointer transition-all duration-200 
                                          {{ $isSelected 
                                             ? 'bg-gradient-to-r from-indigo-50 to-purple-50 border-indigo-400 shadow-lg scale-[1.02]' 
                                             : 'bg-white border-gray-200 hover:border-indigo-300 hover:shadow-md hover:scale-[1.01]' }}">
                                
                                <!-- Custom Radio -->
                                <div class="relative flex-shrink-0 mt-0.5">
                                    <input type="radio"
                                           id="{{ $inputId }}"
                                           name="question_{{ $q->id }}"
                                           value="{{ $choice->id }}"
                                           class="peer sr-only"
                                           @checked($isSelected)
                                           wire:click="saveAnswer({{ $q->id }}, {{ $choice->id }}, true)" />
                                    <div class="w-6 h-6 rounded-full border-2 transition-all duration-200
                                                {{ $isSelected ? 'border-indigo-600 bg-indigo-600' : 'border-gray-300 bg-white group-hover:border-indigo-400' }}">
                                        @if($isSelected)
                                            <svg class="w-full h-full text-white p-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                            </svg>
                                        @endif
                                    </div>
                                </div>
                                
                                <!-- Choice Text -->
                                <span class="flex-1 text-base leading-relaxed {{ $isSelected ? 'text-gray-900 font-medium' : 'text-gray-700' }}">
                                    {{ $choice->text }}
                                </span>
                                
                                <!-- Selected Badge -->
                                @if($isSelected)
                                    <div class="absolute top-3 left-3 bg-indigo-600 text-white text-xs px-2 py-1 rounded-full font-medium">
                                        انتخاب شده
                                    </div>
                                @endif
                            </label>
                        @endforeach
                    </div>
                @endif

                <!-- Navigation & Actions -->
                <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 space-y-4">
                    <!-- Navigation Buttons -->
                    <div class="flex items-center justify-center gap-4">
                        <button wire:click="prev" 
                                @disabled(($index ?? 0) === 0)
                                class="group flex items-center gap-2 px-6 py-3 rounded-xl font-medium transition-all duration-200
                                       {{ ($index ?? 0) === 0 
                                          ? 'bg-gray-100 text-gray-400 cursor-not-allowed' 
                                          : 'bg-gray-100 text-gray-700 hover:bg-gray-200 hover:shadow-md active:scale-95' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                            </svg>
                            سوال قبلی
                        </button>
                        
                        <button wire:click="next" 
                                @disabled((($index ?? 0) + 1) >= ($total ?? 0))
                                class="group flex items-center gap-2 px-6 py-3 rounded-xl font-medium transition-all duration-200
                                       {{ (($index ?? 0) + 1) >= ($total ?? 0)
                                          ? 'bg-gray-100 text-gray-400 cursor-not-allowed' 
                                          : 'bg-gradient-to-r from-indigo-600 to-purple-600 text-white hover:shadow-lg active:scale-95' }}">
                            سوال بعدی
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </button>
                    </div>
                    
                    <!-- Divider -->
                    <div class="border-t border-gray-200"></div>
                    
                    <!-- Submit & Report -->
                    <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
                        @auth
                            <form method="POST" action="{{ route('exam.finish', ['exam' => $this->exam->id]) }}" id="finishForm-{{ $this->exam->id }}" class="w-full sm:w-auto" data-loading-delay="4000">
                                @csrf
                                <input type="hidden" name="answers" value="{{ json_encode($this->answers) }}">
                                <button type="submit"
                                        @disabled($this->requireAllAnswered && $this->unansweredCount() > 0)
                                        class="w-full sm:w-auto flex items-center justify-center gap-2 px-8 py-3 rounded-xl font-bold text-white transition-all duration-200
                                               {{ ($this->requireAllAnswered && $this->unansweredCount() > 0) 
                                                  ? 'bg-gray-400 cursor-not-allowed' 
                                                  : 'bg-gradient-to-r from-green-600 to-emerald-600 hover:shadow-xl active:scale-95' }}">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    پایان آزمون و مشاهده کارنامه
                                </button>
                            </form>
                        @else
                            <button wire:click="submit"
                                    @disabled($this->requireAllAnswered && $this->unansweredCount() > 0)
                                    class="w-full sm:w-auto flex items-center justify-center gap-2 px-8 py-3 rounded-xl font-bold text-white transition-all duration-200
                                           {{ ($this->requireAllAnswered && $this->unansweredCount() > 0) 
                                              ? 'bg-gray-400 cursor-not-allowed' 
                                              : 'bg-gradient-to-r from-green-600 to-emerald-600 hover:shadow-xl active:scale-95' }}">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                پایان آزمون و مشاهده کارنامه
                            </button>
                        @endauth
                        
                        <button wire:click="$set('showReportModal', true)" 
                                class="flex items-center gap-2 text-sm text-gray-600 hover:text-red-600 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9"></path>
                            </svg>
                            گزارش ایراد سوال
                        </button>
                    </div>
                </div>
            </div>
        @else
            <div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-12 text-center">
                <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <p class="text-gray-600">سوالی برای نمایش وجود ندارد.</p>
            </div>
        @endif
    </div>
</div>

<!-- Report Issue Modal -->
@if($showReportModal)
<div class="fixed inset-0 z-50 overflow-y-auto" x-data="{ show: @entangle('showReportModal') }" x-show="show" x-cloak>
    <div class="flex min-h-screen items-center justify-center p-4">
        <div x-show="show" 
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="$wire.set('showReportModal', false)"
             class="fixed inset-0 bg-black/60 backdrop-blur-sm"></div>
        
        <div x-show="show"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="relative bg-white rounded-2xl shadow-2xl max-w-md w-full p-6">
            
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-gray-900">گزارش ایراد سوال</h3>
                <button @click="$wire.set('showReportModal', false)" class="text-gray-400 hover:text-gray-600 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            @if(session('success'))
                <div class="mb-4 p-3 bg-green-100 border border-green-300 text-green-800 rounded-lg text-sm">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="mb-4 p-3 bg-red-100 border border-red-300 text-red-800 rounded-lg text-sm">
                    {{ session('error') }}
                </div>
            @endif
            
            <form wire:submit.prevent="submitReport">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">توضیحات ایراد:</label>
                    <textarea wire:model="reportText" 
                              rows="4" 
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                              placeholder="لطفاً ایراد سوال را به طور دقیق توضیح دهید..."></textarea>
                    @error('reportText')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="flex gap-3">
                    <button type="submit" 
                            class="flex-1 rounded-lg bg-red-600 px-4 py-2 text-white font-medium hover:bg-red-700 transition">
                        ارسال گزارش
                    </button>
                    <button type="button" 
                            @click="$wire.set('showReportModal', false)"
                            class="flex-1 rounded-lg bg-gray-200 px-4 py-2 text-gray-700 font-medium hover:bg-gray-300 transition">
                        انصراف
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<!-- Periodic autosave flush -->
<div wire:poll.2s="flushDirty" class="hidden" aria-hidden="true"></div>

<!-- Upgrade Modal -->
<div x-data="{ show: false }" 
     @show-upgrade-modal.window="show = true"
     x-show="show" 
     x-cloak
     class="fixed inset-0 z-50 overflow-y-auto" 
     style="display: none;">
    <div class="flex min-h-screen items-center justify-center p-4">
        <div x-show="show" 
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="show = false"
             class="fixed inset-0 bg-black/60 backdrop-blur-sm"></div>
        
        <div x-show="show"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="relative bg-white rounded-2xl shadow-2xl max-w-md w-full p-8 text-center">
            
            <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-amber-100 mb-4">
                <svg class="h-10 w-10 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                </svg>
            </div>
            
            <h3 class="text-2xl font-bold text-gray-900 mb-3">
                محدودیت پلن رایگان
            </h3>
            
            <p class="text-gray-600 mb-6 leading-relaxed">
                در پلن رایگان فقط می‌توانید به <strong class="text-indigo-600">4 سوال اول</strong> هر آزمون پاسخ دهید.
                <br><br>
                برای دسترسی به تمام سوالات و امکانات، اشتراک ماهیانه خریداری کنید.
            </p>
            
            <div class="flex flex-col gap-3">
                <a href="{{ route('profile') }}" 
                   class="inline-block w-full rounded-lg bg-indigo-600 px-6 py-3 text-white font-bold hover:bg-indigo-700 transition shadow-lg">
                    خرید اشتراک
                </a>
                <button @click="show = false" 
                        class="w-full rounded-lg bg-gray-100 px-6 py-3 text-gray-700 font-medium hover:bg-gray-200 transition">
                    بستن
                </button>
            </div>
        </div>
    </div>
</div>
