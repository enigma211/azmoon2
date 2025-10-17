<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight">
            تنظیمات سیستم (OTP)
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form action="{{ route('admin.settings.update') }}" method="POST" class="space-y-4">
                    @csrf
                    @method('POST')

                    <div>
                        <label class="inline-flex items-center gap-2">
                            <input type="checkbox" name="otp_enabled" value="1" {{ (strtolower($settings->otp_enabled ?? 'false') === 'true') ? 'checked' : '' }}>
                            <span>فعال‌سازی OTP</span>
                        </label>
                        @error('otp_enabled')
                            <div class="text-red-600 text-sm">{{ $message }}</div>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">SMS Provider</label>
                        <select name="sms_provider" class="border rounded p-2 w-full">
                            <option value="dummy" {{ ($settings->sms_provider ?? 'dummy') === 'dummy' ? 'selected' : '' }}>Dummy</option>
                            <option value="kavenegar" {{ ($settings->sms_provider ?? '') === 'kavenegar' ? 'selected' : '' }}>Kavenegar</option>
                            <option value="ghasedak" {{ ($settings->sms_provider ?? '') === 'ghasedak' ? 'selected' : '' }}>Ghasedak</option>
                            <option value="melipayamak" {{ ($settings->sms_provider ?? '') === 'melipayamak' ? 'selected' : '' }}>Melipayamak</option>
                        </select>
                        @error('sms_provider')
                            <div class="text-red-600 text-sm">{{ $message }}</div>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">API Key</label>
                        <input type="text" name="sms_api_key" class="border rounded p-2 w-full" value="{{ old('sms_api_key', $settings->sms_api_key ?? '') }}">
                        @error('sms_api_key')
                            <div class="text-red-600 text-sm">{{ $message }}</div>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">SMS From</label>
                        <input type="text" name="sms_from" class="border rounded p-2 w-full" value="{{ old('sms_from', $settings->sms_from ?? '') }}">
                        @error('sms_from')
                            <div class="text-red-600 text-sm">{{ $message }}</div>
                        @enderror
                    </div>

                    <div>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded">ذخیره</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
