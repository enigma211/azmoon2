<x-guest-layout>
    <h2 class="text-xl font-semibold mb-4">ورود با کد یکبار مصرف (OTP)</h2>

    @if ($errors->any())
        <div class="mb-4 text-red-600">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('loginOtp.post') }}" class="space-y-4">
        @csrf
        <div>
            <label for="mobile" class="block text-sm font-medium">شماره موبایل (10 رقمی بدون 0)</label>
            <input id="mobile" name="mobile" type="text" class="block mt-1 w-full border rounded p-2" value="{{ old('mobile') }}" required pattern="^\d{10}$" />
        </div>
        <div>
            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded">دریافت کد</button>
        </div>
    </form>
</x-guest-layout>
