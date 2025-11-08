<?php

namespace App\Livewire\Profile;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class UserSettings extends Component
{
    public $fontSize = 'medium';
    public $theme = 'light';

    public function mount()
    {
        if (Auth::check()) {
            $user = Auth::user();
            $this->fontSize = $user->font_size ?? 'medium';
            $this->theme = $user->theme ?? 'light';
        }
    }

    public function updateFontSize($size)
    {
        $this->fontSize = $size;
        
        if (Auth::check()) {
            Auth::user()->update(['font_size' => $size]);
        }

        $this->dispatch('font-size-updated', fontSize: $size);
        $this->dispatch('show-notification', message: 'اندازه فونت با موفقیت تغییر کرد');
    }

    public function updateTheme($theme)
    {
        $this->theme = $theme;
        
        if (Auth::check()) {
            Auth::user()->update(['theme' => $theme]);
        }

        $this->dispatch('theme-updated', theme: $theme);
        $this->dispatch('show-notification', message: 'تم با موفقیت تغییر کرد');
    }

    public function render()
    {
        return view('livewire.profile.user-settings');
    }
}
