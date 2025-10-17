<div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <section class="py-6">
            <h2 class="text-base font-semibold mb-4">دسته‌بندی آزمون‌ها</h2>

            @if(($domains ?? collect())->isEmpty())
                <div class="p-6 rounded-lg bg-white dark:bg-gray-800 shadow text-gray-500 dark:text-gray-400 text-sm">
                    هنوز دسته‌بندی فعالی وجود ندارد.
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @foreach ($domains as $domain)
                        @php
                            $colors = [
                                ['bg' => 'bg-gradient-to-br from-blue-50 to-blue-100', 'icon' => 'text-blue-600', 'text' => 'text-blue-900'],
                                ['bg' => 'bg-gradient-to-br from-purple-50 to-purple-100', 'icon' => 'text-purple-600', 'text' => 'text-purple-900'],
                                ['bg' => 'bg-gradient-to-br from-green-50 to-green-100', 'icon' => 'text-green-600', 'text' => 'text-green-900'],
                                ['bg' => 'bg-gradient-to-br from-amber-50 to-amber-100', 'icon' => 'text-amber-600', 'text' => 'text-amber-900'],
                                ['bg' => 'bg-gradient-to-br from-rose-50 to-rose-100', 'icon' => 'text-rose-600', 'text' => 'text-rose-900'],
                                ['bg' => 'bg-gradient-to-br from-cyan-50 to-cyan-100', 'icon' => 'text-cyan-600', 'text' => 'text-cyan-900'],
                            ];
                            $color = $colors[$loop->index % count($colors)];
                        @endphp
                        <a href="{{ route('batches', $domain) }}" wire:navigate class="group block p-6 rounded-xl {{ $color['bg'] }} shadow hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1">
                            <div class="flex flex-col items-center text-center">
                                {{-- Icon --}}
                                @svg('heroicon-o-academic-cap', 'w-12 h-12 ' . $color['icon'] . ' group-hover:scale-110 transition-transform duration-300')
                                {{-- Title --}}
                                <div class="mt-3 font-semibold {{ $color['text'] }}">{{ $domain->title }}</div>
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif
        </section>
    </div>
</div>
