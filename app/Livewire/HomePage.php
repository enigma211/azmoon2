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
            ->layout('layouts.app');
    }
}
