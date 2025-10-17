<div class="mx-auto max-w-4xl p-4">
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        @foreach($batches as $batch)
            <a href="{{ route('exams', ['batch' => $batch->id]) }}" wire:navigate class="group block rounded-xl bg-white shadow hover:shadow-md transition p-5">
                <div class="flex items-center gap-3">
                    {{-- Icon: calendar --}}
                    @svg('heroicon-o-calendar', 'w-10 h-10 text-amber-600 group-hover:text-amber-700')
                    <div>
                        <div class="font-semibold text-gray-800">{{ $batch->title }}</div>
                        {{-- Optional subtitle can be re-added later if needed --}}
                    </div>
                </div>
            </a>
        @endforeach
    </div>
</div>
