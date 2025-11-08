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

        // Apply font size immediately using JavaScript
        $this->js("
            localStorage.setItem('userFontSize', '{$size}');
            document.body.classList.remove('font-small', 'font-medium', 'font-large', 'font-xlarge');
            document.body.classList.add('font-{$size}');
            window.showNotification('اندازه فونت با موفقیت تغییر کرد');
        ");
    }

    public function updateTheme($theme)
    {
        $this->theme = $theme;
        
        if (Auth::check()) {
            Auth::user()->update(['theme' => $theme]);
        }

        // Apply theme immediately using JavaScript
        $this->js("
            localStorage.setItem('userTheme', '{$theme}');
            document.body.classList.remove('theme-light', 'theme-dark');
            document.body.classList.add('theme-{$theme}');
            window.showNotification('تم با موفقیت تغییر کرد');
        ");
    }

    public function render()
    {
        return view('livewire.profile.user-settings');
    }
}
