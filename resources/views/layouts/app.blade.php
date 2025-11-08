<!DOCTYPE html>
<html lang="fa" dir="rtl">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        
        <!-- Prevent browser caching -->
        <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
        <meta http-equiv="Pragma" content="no-cache">
        <meta http-equiv="Expires" content="0">
        
        <meta name="description" content="آزمون کده: بزرگترین شبیه ساز آزمون نظام مهندسی. با نمونه سوالات واقعی سال‌های گذشته در محیطی مشابه آزمون اصلی تمرین کنید و کارنامه قبولی/مردودی خود را فوراً دریافت نمایید.">
        <meta name="robots" content="index, follow">
        <link rel="canonical" href="{{ url()->current() }}">

        <title>{{ config('app.name', 'آزمون کده') }}</title>

        <meta property="og:locale" content="fa_IR">
        <meta property="og:type" content="website">
        <meta property="og:title" content="{{ config('app.name', 'آزمون کده') }}">
        <meta property="og:description" content="آزمون کده: بزرگترین شبیه ساز آزمون نظام مهندسی. با نمونه سوالات واقعی سال‌های گذشته در محیطی مشابه آزمون اصلی تمرین کنید و کارنامه قبولی/مردودی خود را فوراً دریافت نمایید.">
        <meta property="og:url" content="{{ url()->current() }}">
        <meta property="og:site_name" content="{{ config('app.name', 'آزمون کده') }}">

        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:title" content="{{ config('app.name', 'آزمون کده') }}">
        <meta name="twitter:description" content="آزمون کده: بزرگترین شبیه ساز آزمون نظام مهندسی. با نمونه سوالات واقعی سال‌های گذشته در محیطی مشابه آزمون اصلی تمرین کنید و کارنامه قبولی/مردودی خود را فوراً دریافت نمایید.">

        <!-- Fonts: Vazirmatn -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Vazirmatn:wght@300;400;500;600;700&display=swap" rel="stylesheet">

        <!-- Vite & Livewire -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
        <link rel="manifest" href="/manifest.webmanifest" />
        <meta name="theme-color" content="#4f46e5" />
        @php
            $favicon = \App\Helpers\BrandingHelper::getFavicon();
        @endphp
        @if($favicon)
            <link rel="icon" type="image/png" href="{{ $favicon }}">
        @endif
    </head>
    <body class="min-h-dvh bg-gray-50 text-gray-900 antialiased selection:bg-indigo-200 selection:text-indigo-900" style="font-family: Vazirmatn, system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial, 'Apple Color Emoji', 'Segoe UI Emoji', 'Segoe UI Symbol'">
        <div id="app" class="min-h-dvh flex flex-col">
            <!-- Top Navigation (optional) -->
            <livewire:layout.navigation />

            <!-- Page Heading -->
            @if (isset($header))
                <header class="bg-white shadow">
                    <div class="mx-auto max-w-7xl py-4 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endif

            <!-- Page Content -->
            <main class="flex-1 pb-16"> <!-- pb for bottom tab bar space -->
                {{ $slot }}
            </main>

            <!-- Bottom Tab Bar (Mobile) -->
            <nav class="fixed inset-x-0 bottom-0 z-40 border-t border-gray-200 bg-white/95 backdrop-blur supports-[backdrop-filter]:bg-white/60 md:hidden">
                <ul class="mx-auto grid max-w-md grid-cols-4 text-xs">
                    <li>
                        <a href="{{ url('/') }}" wire:navigate class="flex flex-col items-center gap-1 py-2 text-gray-600 hover:text-indigo-600">
                            <!-- Heroicon: home -->
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l9-7.5 9 7.5M4.5 10.5V21h15V10.5" />
                            </svg>
                            <span>خانه</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ url('/domains') }}" wire:navigate class="flex flex-col items-center gap-1 py-2 text-gray-600 hover:text-indigo-600">
                            <!-- Heroicon: clipboard-document-check -->
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75l1.5 1.5L15 9.75M8.25 6h7.5A2.25 2.25 0 0 1 18 8.25v9A2.25 2.25 0 0 1 15.75 19.5H8.25A2.25 2.25 0 0 1 6 17.25v-9A2.25 2.25 0 0 1 8.25 6z" />
                            </svg>
                            <span>آزمون‌ها</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ url('/resources') }}" wire:navigate class="flex flex-col items-center gap-1 py-2 text-gray-600 hover:text-indigo-600">
                            <!-- Heroicon: squares-2x2 -->
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 3.75H4.5v5.25h5.25V3.75zm9.75 0h-5.25v5.25H19.5V3.75zM9.75 15H4.5v5.25h5.25V15zm9.75 0h-5.25v5.25H19.5V15z" />
                            </svg>
                            <span>منابع</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ url('/profile') }}" wire:navigate class="flex flex-col items-center gap-1 py-2 text-gray-600 hover:text-indigo-600">
                            <!-- Heroicon: user -->
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 7.5a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0zM4.5 19.5a7.5 7.5 0 0 1 15 0" />
                            </svg>
                            <span>پروفایل</span>
                        </a>
                    </li>
                </ul>
            </nav>

            <!-- Global Loading Overlay -->
            <div id="global-loading" class="hidden fixed inset-0 z-50 grid place-items-center bg-white/80 backdrop-blur-sm">
                <div class="flex items-center gap-3 rounded-full bg-white px-4 py-2 shadow">
                    <svg class="h-5 w-5 animate-spin text-indigo-600" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                    </svg>
                    <span class="text-sm">در حال بارگیری…</span>
                </div>
            </div>
        </div>

        @livewireScripts
        
        {{-- User Preferences Script --}}
        <script>
            // User Preferences Management with inline styles
            function applyUserPreferences() {
                const fontSize = localStorage.getItem('userFontSize') || '{{ auth()->check() ? auth()->user()->font_size ?? "medium" : "medium" }}';
                const theme = localStorage.getItem('userTheme') || '{{ auth()->check() ? auth()->user()->theme ?? "light" : "light" }}';
                
                // Font size mapping
                const fontSizeMap = {
                    'small': '14px',
                    'medium': '16px',
                    'large': '18px',
                    'xlarge': '20px'
                };
                
                // Apply font size
                document.body.style.fontSize = fontSizeMap[fontSize] || '16px';
                document.body.setAttribute('data-font-size', fontSize);
                
                // Apply theme
                document.body.setAttribute('data-theme', theme);
                if (theme === 'dark') {
                    document.body.style.backgroundColor = '#1a202c';
                    document.body.style.color = '#e2e8f0';
                    
                    // Apply dark theme to elements
                    setTimeout(() => {
                        document.querySelectorAll('.bg-white').forEach(el => {
                            el.style.backgroundColor = '#2d3748';
                            el.style.color = '#e2e8f0';
                        });
                        document.querySelectorAll('.text-gray-900, .text-gray-800, .text-gray-700, .text-gray-600').forEach(el => {
                            el.style.color = '#e2e8f0';
                        });
                        document.querySelectorAll('.bg-gray-50').forEach(el => {
                            el.style.backgroundColor = '#2d3748';
                        });
                    }, 100);
                } else {
                    document.body.style.backgroundColor = '';
                    document.body.style.color = '';
                    
                    // Reset to light theme
                    setTimeout(() => {
                        document.querySelectorAll('.bg-white').forEach(el => {
                            el.style.backgroundColor = '';
                            el.style.color = '';
                        });
                        document.querySelectorAll('.text-gray-900, .text-gray-800, .text-gray-700, .text-gray-600').forEach(el => {
                            el.style.color = '';
                        });
                        document.querySelectorAll('.bg-gray-50').forEach(el => {
                            el.style.backgroundColor = '';
                        });
                    }, 100);
                }
            }

            // Apply preferences on page load
            document.addEventListener('DOMContentLoaded', applyUserPreferences);
            
            // Apply preferences after Livewire navigation
            window.addEventListener('livewire:navigated', applyUserPreferences);

            // Global notification function
            window.showNotification = function(message) {
                const notification = document.createElement('div');
                notification.className = 'fixed top-4 left-1/2 transform -translate-x-1/2 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-[9999] transition-opacity duration-300';
                notification.textContent = message;
                notification.style.opacity = '1';
                document.body.appendChild(notification);
                
                setTimeout(() => {
                    notification.style.opacity = '0';
                    setTimeout(() => notification.remove(), 300);
                }, 2000);
            };
        </script>
        
        {{-- PWA Service Worker Registration --}}
        <script>
            if ('serviceWorker' in navigator) {
                window.addEventListener('load', () => {
                    navigator.serviceWorker.register('/service-worker.js')
                        .then((registration) => {
                            console.log('✅ Service Worker registered:', registration.scope);
                            
                            // بررسی به‌روزرسانی
                            registration.addEventListener('updatefound', () => {
                                const newWorker = registration.installing;
                                console.log('🔄 Service Worker جدید در حال نصب...');
                                
                                newWorker.addEventListener('statechange', () => {
                                    if (newWorker.state === 'installed' && navigator.serviceWorker.controller) {
                                        // نسخه جدید آماده است
                                        if (confirm('نسخه جدید اپلیکیشن آماده است. می‌خواهید الان به‌روزرسانی کنید؟')) {
                                            newWorker.postMessage({ type: 'SKIP_WAITING' });
                                            window.location.reload();
                                        }
                                    }
                                });
                            });
                        })
                        .catch((error) => {
                            console.error('❌ Service Worker registration failed:', error);
                        });
                    
                    // رفرش خودکار وقتی SW جدید فعال شد
                    let refreshing = false;
                    navigator.serviceWorker.addEventListener('controllerchange', () => {
                        if (!refreshing) {
                            refreshing = true;
                            window.location.reload();
                        }
                    });
                });
            }
        </script>
        
        <script>
            // Show overlay only if navigation is >200ms to prevent flicker
            (function(){
                let navTimer = null;
                const show = () => document.getElementById('global-loading')?.classList.remove('hidden');
                const hide = () => document.getElementById('global-loading')?.classList.add('hidden');
                window.addEventListener('livewire:navigating', () => {
                    if (navTimer) clearTimeout(navTimer);
                    navTimer = setTimeout(show, 200);
                });
                window.addEventListener('livewire:navigated', () => {
                    if (navTimer) clearTimeout(navTimer);
                    hide();
                });
            })();
        </script>
        <script>
            // Global Livewire request loader (4s threshold)
            (function(){
                const overlay = document.getElementById('global-loading');
                if (!overlay) return;
                const show = () => overlay.classList.remove('hidden');
                const hide = () => overlay.classList.add('hidden');

                let pending = 0;
                let timer = null;

                window.addEventListener('livewire:request-start', () => {
                    pending++;
                    if (timer) clearTimeout(timer);
                    // only show if still pending after 4s
                    timer = setTimeout(() => { if (pending > 0) show(); }, 4000);
                });
                window.addEventListener('livewire:request-finish', () => {
                    pending = Math.max(0, pending - 1);
                    if (pending === 0) {
                        if (timer) clearTimeout(timer);
                        hide();
                    }
                });
            })();
        </script>
        <script>
            // Delayed loader for normal form submissions (e.g., exam.finish)
            (function(){
                const overlay = document.getElementById('global-loading');
                if (!overlay) return;
                document.addEventListener('submit', (e) => {
                    const form = e.target;
                    if (!(form instanceof HTMLFormElement)) return;
                    if (!form.matches('form[data-loading-delay]')) return;
                    const delay = parseInt(form.getAttribute('data-loading-delay') || '4000', 10);
                    const timer = setTimeout(() => overlay.classList.remove('hidden'), delay);
                    window.addEventListener('beforeunload', () => clearTimeout(timer), { once: true });
                }, true);
            })();
        </script>
    </body>
</html>
