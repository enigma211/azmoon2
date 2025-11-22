<x-filament-panels::page>
    <form wire:submit="save">
        {{ $this->form }}
        
        <div class="mt-4 flex justify-end">
            <x-filament::button type="submit">
                ذخیره تنظیمات
            </x-filament::button>
        </div>
    </form>

    <hr class="my-8 border-gray-200 dark:border-gray-700">

    <div class="space-y-6" dir="rtl">
        @if(session('success'))
            <div class="fi-alert fi-color-success">
                <div class="fi-alert-content">
                    <p class="text-sm">{{ session('success') }}</p>
                </div>
            </div>
        @endif
        @if(session('error'))
            <div class="fi-alert fi-color-danger">
                <div class="fi-alert-content">
                    <p class="text-sm">{{ session('error') }}</p>
                </div>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.sms.test') }}" class="space-y-6">
            @csrf
            <div class="fi-section rounded-xl border bg-white dark:bg-gray-900">
                <div class="fi-section-header px-6 py-4 border-b">
                    <h2 class="text-base font-semibold">ارسال پیامک تست</h2>
                    <p class="text-sm text-gray-500">برای اطمینان از اتصال صحیح به سامانه پیامکی، شماره و متن را وارد و ارسال کنید.</p>
                </div>
                <div class="fi-section-content p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium mb-1">شماره موبایل</label>
                        <input type="text" name="mobile" class="fi-input w-full" placeholder="09xxxxxxxxx" value="{{ old('mobile') }}">
                        @error('mobile')
                            <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium mb-1">متن پیامک</label>
                        <textarea name="text" rows="3" class="fi-input w-full">{{ old('text') }}</textarea>
                        @error('text')
                            <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="px-6 py-4 border-t flex justify-end">
                    <button type="submit" class="fi-btn fi-color-primary">ارسال پیامک تست</button>
                </div>
            </div>
        </form>
    </div>
</x-filament-panels::page>
