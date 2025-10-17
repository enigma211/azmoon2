<div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <section class="py-6">
            <h2 class="text-base font-semibold mb-4">دسته‌بندی آزمون‌ها</h2>

            @if(($domains ?? collect())->isEmpty())
                <div class="p-6 rounded-lg bg-white shadow text-gray-500 text-sm">
                    هنوز دسته‌بندی فعالی وجود ندارد.
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
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

