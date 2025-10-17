<x-guest-layout>
    <h2 class="text-xl font-semibold mb-4">تایید کد یکبار مصرف</h2>

    @if ($errors->any())
        <div class="mb-4 text-red-600">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('verifyOtp.post') }}" class="space-y-4">
        @csrf
        <div>
            <label for="otp" class="block text-sm font-medium">کد 6 رقمی</label>
            <input id="otp" name="otp" type="text" class="block mt-1 w-full border rounded p-2" required pattern="^\d{6}$" />
        </div>
        <div class="flex items-center gap-2">
            <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded">تایید</button>
            <a href="{{ route('loginOtp') }}" class="px-4 py-2 bg-gray-200 rounded">بازگشت</a>
        </div>
    </form>
</x-guest-layout>
