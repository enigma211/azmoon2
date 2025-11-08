<div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
    <h2 class="text-xl font-semibold mb-6 text-gray-900 dark:text-white">تنظیمات نمایش</h2>
    
    <!-- Font Size Settings -->
    <div class="mb-8">
        <h3 class="text-base font-medium text-gray-900 dark:text-white mb-4">اندازه فونت</h3>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
            <button 
                wire:click="updateFontSize('small')"
                class="px-4 py-3 rounded-lg border-2 transition-all duration-200 {{ $fontSize === 'small' ? 'border-indigo-600 bg-indigo-50 text-indigo-700 font-semibold' : 'border-gray-300 bg-white text-gray-700 hover:border-indigo-400' }}"
            >
                <div class="text-xs mb-1">کوچک</div>
                <div class="text-sm">Aa</div>
            </button>
            
            <button 
                wire:click="updateFontSize('medium')"
                class="px-4 py-3 rounded-lg border-2 transition-all duration-200 {{ $fontSize === 'medium' ? 'border-indigo-600 bg-indigo-50 text-indigo-700 font-semibold' : 'border-gray-300 bg-white text-gray-700 hover:border-indigo-400' }}"
            >
                <div class="text-xs mb-1">متوسط</div>
                <div class="text-base">Aa</div>
            </button>
            
            <button 
                wire:click="updateFontSize('large')"
                class="px-4 py-3 rounded-lg border-2 transition-all duration-200 {{ $fontSize === 'large' ? 'border-indigo-600 bg-indigo-50 text-indigo-700 font-semibold' : 'border-gray-300 bg-white text-gray-700 hover:border-indigo-400' }}"
            >
                <div class="text-xs mb-1">بزرگ</div>
                <div class="text-lg">Aa</div>
            </button>
            
            <button 
                wire:click="updateFontSize('xlarge')"
                class="px-4 py-3 rounded-lg border-2 transition-all duration-200 {{ $fontSize === 'xlarge' ? 'border-indigo-600 bg-indigo-50 text-indigo-700 font-semibold' : 'border-gray-300 bg-white text-gray-700 hover:border-indigo-400' }}"
            >
                <div class="text-xs mb-1">خیلی بزرگ</div>
                <div class="text-xl">Aa</div>
            </button>
        </div>
        <p class="text-xs text-gray-500 mt-3">اندازه فونت در تمام صفحات سایت اعمال می‌شود</p>
    </div>

    <!-- Theme Settings -->
    <div>
        <h3 class="text-base font-medium text-gray-900 dark:text-white mb-4">تم رنگی</h3>
        <div class="grid grid-cols-2 gap-3">
            <button 
                wire:click="updateTheme('light')"
                class="px-6 py-4 rounded-lg border-2 transition-all duration-200 {{ $theme === 'light' ? 'border-indigo-600 bg-indigo-50' : 'border-gray-300 bg-white hover:border-indigo-400' }}"
            >
                <div class="flex items-center justify-center gap-3">
                    <svg class="w-6 h-6 {{ $theme === 'light' ? 'text-indigo-600' : 'text-gray-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                    <div>
                        <div class="font-semibold {{ $theme === 'light' ? 'text-indigo-700' : 'text-gray-900' }}">روشن</div>
                        <div class="text-xs text-gray-500">Light Mode</div>
                    </div>
                </div>
            </button>
            
            <button 
                wire:click="updateTheme('dark')"
                class="px-6 py-4 rounded-lg border-2 transition-all duration-200 {{ $theme === 'dark' ? 'border-indigo-600 bg-indigo-50' : 'border-gray-300 bg-white hover:border-indigo-400' }}"
            >
                <div class="flex items-center justify-center gap-3">
                    <svg class="w-6 h-6 {{ $theme === 'dark' ? 'text-indigo-600' : 'text-gray-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
                    </svg>
                    <div>
                        <div class="font-semibold {{ $theme === 'dark' ? 'text-indigo-700' : 'text-gray-900' }}">تاریک</div>
                        <div class="text-xs text-gray-500">Dark Mode</div>
                    </div>
                </div>
            </button>
        </div>
        <p class="text-xs text-gray-500 mt-3">تم رنگی در تمام صفحات سایت اعمال می‌شود</p>
    </div>

    <!-- Preview Text -->
    <div class="mt-8 p-4 rounded-lg bg-gray-50 border border-gray-200">
        <p class="text-sm text-gray-600 mb-2 font-semibold">پیش‌نمایش:</p>
        <p class="text-gray-900 leading-relaxed">
            این یک متن نمونه است که با تنظیمات فعلی شما نمایش داده می‌شود. اندازه فونت و تم رنگی که انتخاب کرده‌اید در تمام صفحات سایت اعمال خواهد شد.
        </p>
    </div>

    <!-- Debug Info -->
    <div class="mt-4 p-3 rounded-lg bg-blue-50 border border-blue-200">
        <p class="text-xs text-blue-800 font-semibold mb-1">اطلاعات فنی (برای اشکال‌زدایی):</p>
        <p class="text-xs text-blue-700">فونت فعلی: <span class="font-mono">{{ $fontSize }}</span></p>
        <p class="text-xs text-blue-700">تم فعلی: <span class="font-mono">{{ $theme }}</span></p>
        <button 
            onclick="alert('localStorage fontSize: ' + localStorage.getItem('userFontSize') + '\nlocalStorage theme: ' + localStorage.getItem('userTheme') + '\nBody fontSize: ' + document.body.style.fontSize + '\nBody theme: ' + document.body.getAttribute('data-theme'))"
            class="mt-2 text-xs px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-600"
        >
            نمایش مقادیر localStorage
        </button>
    </div>
</div>
