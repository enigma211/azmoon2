<div class="max-w-md mx-auto">
    <div class="bg-white rounded-lg shadow-lg p-6">
        @if (session()->has('success'))
            <div class="mb-4 p-3 bg-green-100 text-green-800 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        {{-- Step 1: Enter Mobile Number --}}
        @if ($step === 'mobile')
            <div>
                <h2 class="text-2xl font-bold text-gray-900 mb-2">ورود / ثبت‌نام</h2>
                <p class="text-sm text-gray-600 mb-6">لطفا شماره موبایل خود را وارد کنید</p>

                <form wire:submit.prevent="sendOtp">
                    <div class="mb-4">
                        <label for="mobile" class="block text-sm font-medium text-gray-700 mb-2">
                            شماره موبایل
                        </label>
                        <input 
                            type="text" 
                            id="mobile"
                            wire:model.defer="mobile"
                            placeholder="09361694020"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('mobile') border-red-500 @enderror"
                            maxlength="11"
                            dir="ltr"
                        >
                        @error('mobile')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <button 
                        type="submit"
                        class="w-full bg-indigo-600 text-white py-3 px-4 rounded-lg font-medium hover:bg-indigo-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                        wire:loading.attr="disabled"
                    >
                        <span wire:loading.remove>ارسال کد تایید</span>
                        <span wire:loading>در حال ارسال...</span>
                    </button>
                </form>
            </div>
        @endif

        {{-- Step 2: Enter OTP --}}
        @if ($step === 'otp')
            <div>
                <h2 class="text-2xl font-bold text-gray-900 mb-2">تایید کد</h2>
                <p class="text-sm text-gray-600 mb-6">
                    کد 6 رقمی ارسال شده به شماره <span class="font-bold" dir="ltr">{{ $mobile }}</span> را وارد کنید
                </p>

                <form wire:submit.prevent="verifyOtp">
                    <div class="mb-4">
                        <label for="otp" class="block text-sm font-medium text-gray-700 mb-2">
                            کد تایید
                        </label>
                        <input 
                            type="text" 
                            id="otp"
                            wire:model.defer="otp"
                            placeholder="123456"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-center text-2xl tracking-widest @error('otp') border-red-500 @enderror"
                            maxlength="6"
                            dir="ltr"
                            autofocus
                        >
                        @error('otp')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <button 
                        type="submit"
                        class="w-full bg-indigo-600 text-white py-3 px-4 rounded-lg font-medium hover:bg-indigo-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed mb-3"
                        wire:loading.attr="disabled"
                    >
                        <span wire:loading.remove>تایید کد</span>
                        <span wire:loading>در حال بررسی...</span>
                    </button>

                    <button 
                        type="button"
                        wire:click="resetForm"
                        class="w-full bg-gray-200 text-gray-700 py-2 px-4 rounded-lg font-medium hover:bg-gray-300 transition-colors"
                    >
                        بازگشت
                    </button>
                </form>

                @if ($countdown > 0)
                    <div class="mt-4 text-center text-sm text-gray-600">
                        ارسال مجدد کد تا <span class="font-bold">{{ $countdown }}</span> ثانیه دیگر
                    </div>
                @endif
            </div>
        @endif

        {{-- Step 3: Register (New User) --}}
        @if ($step === 'register')
            <div>
                <h2 class="text-2xl font-bold text-gray-900 mb-2">تکمیل ثبت‌نام</h2>
                <p class="text-sm text-gray-600 mb-6">لطفا اطلاعات خود را وارد کنید</p>

                <form wire:submit.prevent="completeRegistration">
                    <div class="mb-4">
                        <label for="firstName" class="block text-sm font-medium text-gray-700 mb-2">
                            نام
                        </label>
                        <input 
                            type="text" 
                            id="firstName"
                            wire:model.defer="firstName"
                            placeholder="رضا"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('firstName') border-red-500 @enderror"
                            autofocus
                        >
                        @error('firstName')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="lastName" class="block text-sm font-medium text-gray-700 mb-2">
                            نام خانوادگی
                        </label>
                        <input 
                            type="text" 
                            id="lastName"
                            wire:model.defer="lastName"
                            placeholder="عطریانفر"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('lastName') border-red-500 @enderror"
                        >
                        @error('lastName')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                            ایمیل
                        </label>
                        <input 
                            type="email" 
                            id="email"
                            wire:model.defer="email"
                            placeholder="example@email.com"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('email') border-red-500 @enderror"
                            dir="ltr"
                        >
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <button 
                        type="submit"
                        class="w-full bg-indigo-600 text-white py-3 px-4 rounded-lg font-medium hover:bg-indigo-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                        wire:loading.attr="disabled"
                    >
                        <span wire:loading.remove>تکمیل ثبت‌نام</span>
                        <span wire:loading>در حال ثبت‌نام...</span>
                    </button>
                </form>
            </div>
        @endif
    </div>

    <div class="mt-4 text-center text-sm text-gray-600">
        با ورود یا ثبت‌نام، <a href="#" class="text-indigo-600 hover:text-indigo-700">قوانین و مقررات</a> را می‌پذیرید
    </div>
</div>
