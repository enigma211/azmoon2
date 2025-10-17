<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\ExamDomain;

class HomePage extends Component
{
    public function render()
    {
        $domains = ExamDomain::query()
            ->where('is_active', true)
            ->orderBy('id', 'asc')
            ->get();

        return view('livewire.home-page', [
                'domains' => $domains,
            ])
            ->layout('layouts.app');
    }
}
