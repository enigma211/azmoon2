@php
    // Defensive checks to avoid errors before data is fully wired
    $q = $question ?? null;
@endphp
<div class="mx-auto max-w-2xl p-4 space-y-4">
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

    <!-- Navigation buttons (moved to top) -->
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

        <!-- Submit button (kept at bottom) -->
        <div class="mt-8 space-y-4">
            <div class="flex justify-center pt-4 border-t border-gray-200">
                @auth
                    <form method="POST" action="{{ route('exam.finish', ['exam' => $this->exam->id]) }}" id="finishForm-{{ $this->exam->id }}" class="inline" data-loading-delay="4000">
                        @csrf
                        <input type="hidden" name="answers" value="{{ json_encode($this->answers) }}">
                        <button type="submit"
                                class="rounded-lg px-10 py-3 text-white font-bold text-lg {{ ($this->requireAllAnswered && $this->unansweredCount() > 0) ? 'bg-gray-400 cursor-not-allowed' : 'bg-green-600 hover:bg-green-700 shadow-lg hover:shadow-xl' }} transition-all"
                                @disabled($this->requireAllAnswered && $this->unansweredCount() > 0)>
                            پایان آزمون و مشاهده کارنامه
                        </button>
                    </form>
                @else
                    <button wire:click="submit"
                            class="rounded-lg px-10 py-3 text-white font-bold text-lg {{ ($this->requireAllAnswered && $this->unansweredCount() > 0) ? 'bg-gray-400 cursor-not-allowed' : 'bg-green-600 hover:bg-green-700 shadow-lg hover:shadow-xl' }} transition-all"
                            @disabled($this->requireAllAnswered && $this->unansweredCount() > 0)>
                        پایان آزمون و مشاهده کارنامه
                    </button>
                @endauth
    </div>
    @else
        <div class="rounded border p-4 text-sm text-gray-600">سوالی برای نمایش وجود ندارد.</div>
    @endif
</div>
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
