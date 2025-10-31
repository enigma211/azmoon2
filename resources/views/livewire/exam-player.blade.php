@php
    // Defensive checks to avoid errors before data is fully wired
    $q = $question ?? null;
@endphp
<div class="mx-auto max-w-2xl p-4 space-y-4" 
     oncontextmenu="return false;" 
     onselectstart="return false;" 
     oncopy="return false;"
     oncut="return false;"
     style="user-select: none; -webkit-user-select: none; -moz-user-select: none; -ms-user-select: none;">
    <!-- Exam Title -->
    <div class="text-center mb-2">
        <h1 class="text-xl font-bold text-gray-800">{{ $this->exam->title }}</h1>
    </div>
    
    <!-- Progress bar -->
    <div>
        @php $pct = ($total ?? 0) > 0 ? intval((($index ?? 0)+1) / ($total ?? 1) * 100) : 0; @endphp
        <div class="h-2 w-full rounded bg-gray-200">
            <div class="h-2 rounded bg-indigo-600" style="width: {{ $pct }}%"></div>
        </div>
        <div class="mt-1 text-[11px] text-gray-500">پیشرفت: {{ $pct }}%</div>
    </div>

    <div class="flex items-center justify-between">
        <div class="text-sm text-gray-600">سوال {{ ($index ?? 0) + 1 }} از {{ $total ?? 0 }}</div>
        <div class="text-xs text-gray-700">
            @if(!is_null($durationSeconds))
                <span wire:poll.1s="tick">
                    زمان باقیمانده: {{ floor($remainingSeconds / 60) }} دقیقه
                </span>
            @else
                <span>بدون محدودیت زمان</span>
            @endif
        </div>
    </div>

    @if($q)
        <div wire:key="question-{{ $q->id }}">
        <article class="prose prose-sm max-w-none">
            {!! $q->text !!}
        </article>

        @if(!empty($q->image_path))
            <div class="mt-3">
                <img src="{{ Storage::url($q->image_path) }}" alt="image" class="w-full rounded" />
            </div>
        @endif

        @if($q->is_deleted)
            <div class="mt-6 rounded-lg border-2 border-amber-400 bg-amber-50 p-6 text-center">
                <div class="flex items-center justify-center gap-2 text-amber-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                    <span class="text-lg font-bold">این سوال بطور رسمی حذف شده است</span>
                </div>
                <p class="mt-2 text-sm text-amber-600">این سوال در محاسبه نمره نهایی شما لحاظ نخواهد شد. لطفاً به سوال بعدی بروید.</p>
            </div>
        @elseif($q->choices && $q->choices->count())
            <div class="mt-3 space-y-2">
                @foreach($q->choices as $choice)
                    <label wire:key="choice-{{ $q->id }}-{{ $choice->id }}" class="flex items-start gap-3 rounded-lg border-2 p-4 cursor-pointer hover:bg-gray-50 hover:border-indigo-300 transition {{ ($answers[$q->id][$choice->id] ?? false) ? 'bg-indigo-50 border-indigo-400' : 'border-gray-200' }}">
                        @php $inputId = 'q'.$q->id.'_c'.$choice->id; @endphp
                        <input type="radio"
                               id="{{ $inputId }}"
                               name="question_{{ $q->id }}"
                               value="{{ $choice->id }}"
                               class="h-5 w-5 mt-0.5 border-gray-300 text-indigo-600 focus:ring-indigo-500"
                               @checked(($answers[$q->id][$choice->id] ?? false) === true)
                               wire:click="saveAnswer({{ $q->id }}, {{ $choice->id }}, true)" />
                        <span class="flex-1">{{ $choice->text }}</span>
                    </label>
                @endforeach
            </div>
        @endif

        <div class="mt-8 space-y-4">
            <!-- Navigation buttons -->
            <div class="flex items-center justify-center gap-6">
                <button wire:click="prev" 
                        class="rounded bg-gray-100 px-8 py-2 text-gray-700 disabled:opacity-50 hover:bg-gray-200 transition font-medium" 
                        @disabled(($index ?? 0) === 0)>
                    قبلی
                </button>
                <button wire:click="next" 
                        class="rounded bg-indigo-600 px-8 py-2 text-white hover:bg-indigo-700 transition font-medium" 
                        @disabled((($index ?? 0) + 1) >= ($total ?? 0))>
                    بعدی
                </button>
            </div>
            
            <!-- Submit button -->
            <div class="flex justify-center pt-4 border-t border-gray-200">
                @auth
                    <form method="POST" action="{{ route('exam.finish', ['exam' => $this->exam->id]) }}" id="finishForm-{{ $this->exam->id }}" class="inline" data-loading-delay="4000">
                        @csrf
                        <input type="hidden" name="answers" value="{{ json_encode($this->answers) }}">
                        <button type="submit"
                                class="rounded-lg px-6 py-2 text-white font-medium text-sm {{ ($this->requireAllAnswered && $this->unansweredCount() > 0) ? 'bg-gray-400 cursor-not-allowed' : 'bg-green-600 hover:bg-green-700 shadow hover:shadow-lg' }} transition-all"
                                @disabled($this->requireAllAnswered && $this->unansweredCount() > 0)>
                            پایان آزمون و مشاهده کارنامه
                        </button>
                    </form>
                @else
                    <button wire:click="submit"
                            class="rounded-lg px-6 py-2 text-white font-medium text-sm {{ ($this->requireAllAnswered && $this->unansweredCount() > 0) ? 'bg-gray-400 cursor-not-allowed' : 'bg-green-600 hover:bg-green-700 shadow hover:shadow-lg' }} transition-all"
                            @disabled($this->requireAllAnswered && $this->unansweredCount() > 0)>
                        پایان آزمون و مشاهده کارنامه
                    </button>
                @endauth
            </div>
            
            <!-- Report Issue Button -->
            <div class="flex justify-center pt-2">
                <button wire:click="$set('showReportModal', true)" 
                        class="text-sm text-gray-600 hover:text-red-600 underline transition">
                    گزارش ایراد سوال
                </button>
            </div>
    </div>
    @else
        <div class="rounded border p-4 text-sm text-gray-600">سوالی برای نمایش وجود ندارد.</div>
    @endif
</div>

<!-- Report Issue Modal -->
@if($showReportModal)
<div class="fixed inset-0 z-50 overflow-y-auto" x-data="{ show: @entangle('showReportModal') }" x-show="show" x-cloak>
    <div class="flex min-h-screen items-center justify-center p-4">
        <!-- Backdrop -->
        <div x-show="show" 
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="$wire.set('showReportModal', false)"
             class="fixed inset-0 bg-black bg-opacity-50"></div>
        
        <!-- Modal Content -->
        <div x-show="show"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="relative bg-white rounded-2xl shadow-2xl max-w-md w-full p-6">
            
            <!-- Header -->
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-gray-900">گزارش ایراد سوال</h3>
                <button @click="$wire.set('showReportModal', false)" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <!-- Flash Messages -->
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
            
            <!-- Form -->
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
                
                <!-- Actions -->
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

<!-- Periodic autosave flush (debounced) -->
<div wire:poll.2s="flushDirty" class="hidden" aria-hidden="true"></div>

<!-- Upgrade Modal -->
<div x-data="{ show: false }" 
     @show-upgrade-modal.window="show = true"
     x-show="show" 
     x-cloak
     class="fixed inset-0 z-50 overflow-y-auto" 
     style="display: none;">
    <div class="flex min-h-screen items-center justify-center p-4">
        <!-- Backdrop -->
        <div x-show="show" 
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="show = false"
             class="fixed inset-0 bg-black bg-opacity-50"></div>
        
        <!-- Modal Content -->
        <div x-show="show"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="relative bg-white rounded-2xl shadow-2xl max-w-md w-full p-8 text-center">
            
            <!-- Icon -->
            <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-amber-100 mb-4">
                <svg class="h-10 w-10 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                </svg>
            </div>
            
            <!-- Title -->
            <h3 class="text-2xl font-bold text-gray-900 mb-3">
                محدودیت پلن رایگان
            </h3>
            
            <!-- Message -->
            <p class="text-gray-600 mb-6 leading-relaxed">
                در پلن رایگان فقط می‌توانید به <strong class="text-indigo-600">4 سوال اول</strong> هر آزمون پاسخ دهید.
                <br><br>
                برای دسترسی به تمام سوالات و امکانات، اشتراک ماهیانه خریداری کنید.
            </p>
            
            <!-- Actions -->
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

<script>
    // Prevent keyboard shortcuts for copy, cut, and developer tools
    document.addEventListener('keydown', function(e) {
        // Prevent Ctrl+C (Copy)
        if (e.ctrlKey && e.key === 'c') {
            e.preventDefault();
            return false;
        }
        
        // Prevent Ctrl+X (Cut)
        if (e.ctrlKey && e.key === 'x') {
            e.preventDefault();
            return false;
        }
        
        // Prevent Ctrl+A (Select All)
        if (e.ctrlKey && e.key === 'a') {
            e.preventDefault();
            return false;
        }
        
        // Prevent Ctrl+U (View Source)
        if (e.ctrlKey && e.key === 'u') {
            e.preventDefault();
            return false;
        }
        
        // Prevent Ctrl+S (Save)
        if (e.ctrlKey && e.key === 's') {
            e.preventDefault();
            return false;
        }
        
        // Prevent F12 (Developer Tools)
        if (e.key === 'F12') {
            e.preventDefault();
            return false;
        }
        
        // Prevent Ctrl+Shift+I (Developer Tools)
        if (e.ctrlKey && e.shiftKey && e.key === 'I') {
            e.preventDefault();
            return false;
        }
        
        // Prevent Ctrl+Shift+J (Console)
        if (e.ctrlKey && e.shiftKey && e.key === 'J') {
            e.preventDefault();
            return false;
        }
        
        // Prevent Ctrl+Shift+C (Inspect Element)
        if (e.ctrlKey && e.shiftKey && e.key === 'C') {
            e.preventDefault();
            return false;
        }
    });
    
    // Additional protection: disable drag and drop
    document.addEventListener('dragstart', function(e) {
        e.preventDefault();
        return false;
    });
</script>
