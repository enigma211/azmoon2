<?php

namespace App\Livewire;

use App\Models\ExamDomain;
use Livewire\Attributes\Url;
use Livewire\Component;

class BatchesPage extends Component
{
    public ExamDomain $domain;

    public function mount(ExamDomain $domain): void
    {
        $this->domain = $domain;
    }

    public function render()
    {
        return view('livewire.batches-page', [
            'batches' => $this->domain->batches()->latest()->get(),
        ])->layout('layouts.app');
    }
}
