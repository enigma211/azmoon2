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

        // Apply font size immediately using JavaScript with inline styles
        $fontSizes = [
            'small' => '14px',
            'medium' => '16px',
            'large' => '18px',
            'xlarge' => '20px'
        ];
        
        $fontSize = $fontSizes[$size] ?? '16px';
        
        $this->js("
            localStorage.setItem('userFontSize', '{$size}');
            document.body.style.fontSize = '{$fontSize}';
            document.body.setAttribute('data-font-size', '{$size}');
            if (typeof window.showNotification === 'function') {
                window.showNotification('اندازه فونت با موفقیت تغییر کرد');
            }
        ");
    }

    public function updateTheme($theme)
    {
        $this->theme = $theme;
        
        if (Auth::check()) {
            Auth::user()->update(['theme' => $theme]);
        }

        // Apply theme immediately using JavaScript with inline styles
        $this->js("
            localStorage.setItem('userTheme', '{$theme}');
            document.body.setAttribute('data-theme', '{$theme}');
            
            if ('{$theme}' === 'dark') {
                document.body.style.backgroundColor = '#1a202c';
                document.body.style.color = '#e2e8f0';
                document.querySelectorAll('.bg-white').forEach(el => {
                    el.style.backgroundColor = '#2d3748';
                    el.style.color = '#e2e8f0';
                });
                document.querySelectorAll('.text-gray-900, .text-gray-800, .text-gray-700').forEach(el => {
                    el.style.color = '#e2e8f0';
                });
                document.querySelectorAll('.bg-gray-50').forEach(el => {
                    el.style.backgroundColor = '#2d3748';
                });
            } else {
                document.body.style.backgroundColor = '';
                document.body.style.color = '';
                document.querySelectorAll('.bg-white').forEach(el => {
                    el.style.backgroundColor = '';
                    el.style.color = '';
                });
                document.querySelectorAll('.text-gray-900, .text-gray-800, .text-gray-700').forEach(el => {
                    el.style.color = '';
                });
                document.querySelectorAll('.bg-gray-50').forEach(el => {
                    el.style.backgroundColor = '';
                });
            }
            
            if (typeof window.showNotification === 'function') {
                window.showNotification('تم با موفقیت تغییر کرد');
            }
        ");
    }

    public function render()
    {
        return view('livewire.profile.user-settings');
    }
}
