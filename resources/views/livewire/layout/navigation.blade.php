<?php

use App\Livewire\Actions\Logout;
use Livewire\Volt\Component;

new class extends Component
{
    /**
     * Log the current user out of the application.
     */
    public function logout(Logout $logout): void
    {
        $logout();

        $this->redirect('/', navigate: true);
    }
}; ?>

<nav x-data="{ open: false }" class="bg-gray-50/95 dark:bg-gray-100/95 border-b border-gray-200 backdrop-blur supports-[backdrop-filter]:bg-gray-50/60 shadow-sm z-40">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-center items-center h-16 relative">
            <!-- Logo - Centered -->
            <div class="absolute left-1/2 transform -translate-x-1/2 flex items-center">
                <a href="{{ route('home') }}" wire:navigate class="flex items-center gap-2">
                    @php
                        $logo = \App\Helpers\BrandingHelper::getLogo();
                    @endphp
                    @if($logo)
                        <img src="{{ $logo }}" alt="{{ config('app.name') }}" class="h-8 w-auto">
                    @else
                        <span class="text-lg font-bold text-indigo-600 dark:text-indigo-400">{{ config('app.name', 'آزمون کده') }}</span>
                    @endif
                </a>
            </div>

            <!-- Settings / User menu - Right Side (Hidden on mobile/tablet) -->
            @auth
                <div class="absolute left-0 hidden lg:flex items-center">
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition ease-in-out duration-150">
                                <div x-data="{{ json_encode(['name' => auth()->user()->name]) }}" x-text="name" x-on:profile-updated.window="name = $event.detail.name"></div>

                                <div class="ms-1">
                                    <svg class="fill-current h-4 w-4" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <x-dropdown-link :href="route('profile')" wire:navigate>
                                {{ __('Profile') }}
                            </x-dropdown-link>

                            <!-- Authentication -->
                            <button wire:click="logout" class="w-full text-start">
                                <x-dropdown-link>
                                    {{ __('Log Out') }}
                                </x-dropdown-link>
                            </button>
                        </x-slot>
                    </x-dropdown>
                </div>
            @else
                <!-- Guest: Login / Register - Right Side (Hidden on mobile/tablet) -->
                <div class="absolute left-0 hidden lg:flex items-center">
                    <a href="{{ route('profile') }}" wire:navigate class="px-4 py-2 text-sm rounded-md bg-indigo-600 text-white hover:bg-indigo-700 transition-colors font-medium">ورود / ثبت‌نام</a>
                </div>
            @endauth
        </div>
    </div>

    
</nav>
