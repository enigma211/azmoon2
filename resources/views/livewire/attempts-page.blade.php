<div class="mx-auto max-w-4xl p-6 space-y-6">
    <h1 class="text-xl font-medium text-gray-900">تاریخچه تلاش‌ها</h1>

    <div class="flex items-center gap-3">
        <label class="text-sm text-gray-600">وضعیت:</label>
        <select wire:model.live="status" class="rounded border-gray-300 text-sm">
            <option value="all">همه</option>
            <option value="in_progress">در حال انجام</option>
            <option value="submitted">کامل‌شده</option>
            <option value="abandoned">ناتمام</option>
        </select>
    </div>

    @if($attempts->count())
        <div class="space-y-3">
            @foreach($attempts as $attempt)
                <div class="rounded-lg border p-4 bg-white shadow-sm">
                    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                        <div class="min-w-0">
                            <div class="text-xs text-gray-500">آزمون</div>
                            <div class="font-semibold text-gray-900 truncate">{{ $attempt->exam->title ?? '---' }}</div>
                        </div>
                        <div class="flex flex-wrap items-center gap-4">
                            <div>
                                <div class="text-xs text-gray-500">شروع آزمون</div>
                                <div class="text-sm">{{ $attempt->started_at ? jdate($attempt->started_at, 'Y/m/d') : '—' }}</div>
                            </div>
                            <div>
                                <div class="text-xs text-gray-500">نمره</div>
                                <div class="text-sm font-semibold">{{ is_null($attempt->score) ? '—' : number_format($attempt->score, 1).'%' }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div>
            {{ $attempts->links() }}
        </div>
    @else
        <div class="rounded border p-4 text-sm text-gray-600">تلاشی ثبت نشده است.</div>
    @endif
</div>
