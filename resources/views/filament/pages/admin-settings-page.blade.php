<x-filament-panels::page>
    <div class="space-y-6" dir="rtl">
        @if(session('success'))
            <div class="fi-alert fi-color-success">
                <div class="fi-alert-content">
                    <p class="text-sm">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.settings.update') }}" class="space-y-6">
            @csrf
            <div class="fi-section rounded-xl border bg-white dark:bg-gray-900">
                <div class="fi-section-header px-6 py-4 border-b">
                    <h2 class="text-base font-semibold">تنظیمات پیامک و OTP</h2>
                    <p class="text-sm text-gray-500">پیکربندی ارسال کد یک‌بارمصرف و ارائه‌دهنده پیامک</p>
                </div>
                <div class="fi-section-content p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="col-span-1 md:col-span-2">
                        <label class="inline-flex items-center gap-2">
                            <input type="checkbox" name="otp_enabled" value="1" @checked(strtolower($this->settings->otp_enabled ?? 'false') === 'true')>
                            <span>فعال‌سازی OTP</span>
                        </label>
                        @error('otp_enabled')
                            <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">ارائه‌دهنده SMS</label>
                        <select name="sms_provider" class="fi-input w-full">
                            @php $prov = $this->settings->sms_provider ?? 'dummy'; @endphp
                            <option value="dummy" @selected($prov==='dummy')>Dummy (تستی)</option>
                            <option value="kavenegar" @selected($prov==='kavenegar')>Kavenegar</option>
                            <option value="ghasedak" @selected($prov==='ghasedak')>Ghasedak</option>
                            <option value="melipayamak" @selected($prov==='melipayamak')>Melipayamak</option>
                        </select>
                        @error('sms_provider')
                            <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">API Key</label>
                        <input type="text" name="sms_api_key" class="fi-input w-full" value="{{ old('sms_api_key', $this->settings->sms_api_key ?? '') }}">
                        @error('sms_api_key')
                            <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">ارسال از (Sender)</label>
                        <input type="text" name="sms_from" class="fi-input w-full" value="{{ old('sms_from', $this->settings->sms_from ?? '') }}">
                        @error('sms_from')
                            <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="px-6 py-4 border-t flex justify-end">
                    <button type="submit" class="fi-btn fi-color-primary">ذخیره تنظیمات</button>
                </div>
            </div>
        </form>
    </div>
</x-filament-panels::page>
