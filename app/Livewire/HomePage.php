<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Slider;
use App\Models\ExamDomain;

class HomePage extends Component
{
    public function render()
    {
        $sliders = Slider::query()
            ->where('is_active', true)
            ->orderBy('order', 'asc')
            ->get();

        $domains = ExamDomain::query()
            ->where('is_active', true)
            ->orderBy('id', 'asc')
            ->get();

        return view('livewire.home-page', [
                'sliders' => $sliders,
                'domains' => $domains,
            ])
            ->layout('layouts.app', [
                'seoTitle' => 'آزمون کده - بزرگترین شبیه ساز آزمون نظام مهندسی',
                'seoDescription' => 'آزمون کده: بزرگترین شبیه ساز آزمون نظام مهندسی. با نمونه سوالات واقعی سال‌های گذشته در محیطی مشابه آزمون اصلی تمرین کنید و کارنامه قبولی/مردودی خود را فوراً دریافت نمایید.',
            ]);
    }
}
