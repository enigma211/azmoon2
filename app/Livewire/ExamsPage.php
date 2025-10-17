<?php

namespace App\Livewire;

use App\Models\ExamBatch;
use Livewire\Component;

class ExamsPage extends Component
{
    public ExamBatch $batch;

    public function mount(ExamBatch $batch): void
    {
        $this->batch = $batch->load('exams');
    }

    public function render()
    {
        return view('livewire.exams-page', [
            'exams' => $this->batch->exams()->latest()->get(),
        ])->layout('layouts.app');
    }
}
