<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content="آزمون کده: بزرگترین شبیه ساز آزمون نظام مهندسی. با نمونه سوالات واقعی سال‌های گذشته در محیطی مشابه آزمون اصلی تمرین کنید و کارنامه قبولی/مردودی خود را فوراً دریافت نمایید.">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="{{ url()->current() }}">
    <title>{{ $title ?? config('app.name', 'آزمون کده') }}</title>
    <meta property="og:locale" content="fa_IR">
    <meta property="og:type" content="website">
    <meta property="og:title" content="{{ $title ?? config('app.name', 'آزمون کده') }}">
    <meta property="og:description" content="آزمون کده: بزرگترین شبیه ساز آزمون نظام مهندسی. با نمونه سوالات واقعی سال‌های گذشته در محیطی مشابه آزمون اصلی تمرین کنید و کارنامه قبولی/مردودی خود را فوراً دریافت نمایید.">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:site_name" content="{{ config('app.name', 'آزمون کده') }}">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $title ?? config('app.name', 'آزمون کده') }}">
    <meta name="twitter:description" content="آزمون کده: بزرگترین شبیه ساز آزمون نظام مهندسی. با نمونه سوالات واقعی سال‌های گذشته در محیطی مشابه آزمون اصلی تمرین کنید و کارنامه قبولی/مردودی خود را فوراً دریافت نمایید.">
    @php
        $favicon = \App\Helpers\BrandingHelper::getFavicon();
    @endphp
    @if($favicon)
        <link rel="icon" type="image/png" href="{{ $favicon }}">
    @endif
    @vite(['resources/css/app.css','resources/js/app.js'])
    @livewireStyles
    <style>
      .loading-overlay { position: fixed; inset: 0; background: rgba(0,0,0,.45); display: none; align-items: center; justify-content: center; z-index: 9999; }
      .loading-overlay.show { display: flex; }
      .loading-spinner { width: 48px; height: 48px; border: 4px solid #fff; border-top-color: transparent; border-radius: 50%; animation: spin 1s linear infinite; }
      @keyframes spin { to { transform: rotate(360deg); } }
      .touchable { touch-action: manipulation; }
    </style>
</head>
<body class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <div id="lw-overlay" class="loading-overlay"><div class="loading-spinner"></div></div>

    <main class="max-w-screen-md mx-auto">
        {{ $slot }}
        
        <!-- Footer -->
        <x-footer />
    </main>

    <nav class="fixed bottom-0 left-0 w-full bg-white dark:bg-gray-800 border-t-2 border-gray-200 dark:border-gray-700">
        <div class="max-w-screen-md mx-auto grid grid-cols-4 text-center text-sm">
            <a href="{{ route('home') }}" wire:navigate class="py-2 flex flex-col items-center gap-1 touchable">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75V21a.75.75 0 00.75.75h4.5a.75.75 0 00.75-.75v-3.75a.75.75 0 01.75-.75h2.25a.75.75 0 01.75.75V21a.75.75 0 00.75.75h4.5A.75.75 0 0021 21V9.75" /></svg>
                <span>خانه</span>
            </a>
            <a href="{{ route('exams') }}" wire:navigate class="py-2 flex flex-col items-center gap-1 touchable">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6v12m6-6H6" /></svg>
                <span>آزمون‌ها</span>
            </a>
            <a href="{{ route('resources') }}" wire:navigate class="py-2 flex flex-col items-center gap-1 touchable">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.5 5.25h15m-15 4.5h15m-15 4.5h15m-15 4.5h15" /></svg>
                <span>منابع</span>
            </a>
            <a href="{{ route('profile') }}" wire:navigate class="py-2 flex flex-col items-center gap-1 touchable">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.5 20.25a8.25 8.25 0 1115 0v.75H4.5v-.75z" /></svg>
                <span>پروفایل</span>
            </a>
        </div>
    </nav>

    @livewireScripts
    <script>
      // Livewire v3 navigation events
      window.addEventListener('livewire:navigating', () => {
        document.getElementById('lw-overlay')?.classList.add('show');
      });
      window.addEventListener('livewire:navigated', () => {
        document.getElementById('lw-overlay')?.classList.remove('show');
      });

      // Fallback custom events
      document.addEventListener('livewire:load', () => {
        Livewire.on('loading', () => document.getElementById('lw-overlay')?.classList.add('show'));
        Livewire.on('loaded', () => document.getElementById('lw-overlay')?.classList.remove('show'));
      });
    </script>
</body>
</html>
