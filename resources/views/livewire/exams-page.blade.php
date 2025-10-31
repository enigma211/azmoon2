<div class="mx-auto max-w-2xl p-4">
    <h1 class="mb-4 text-lg font-bold">فهرست آزمون‌ها</h1>
    <ul class="space-y-2">
        @foreach($exams as $exam)
            <li>
                <a href="{{ route('exam.play', ['exam' => $exam->id]) }}" wire:navigate class="block rounded border p-3 hover:bg-gray-50">
                    <div class="font-medium">{{ $exam->title }}</div>
                    <div class="text-xs text-gray-500">{{ \Illuminate\Support\Str::limit($exam->description, 80) }}</div>
                </a>
            </li>
        @endforeach
    </ul>
</div>
