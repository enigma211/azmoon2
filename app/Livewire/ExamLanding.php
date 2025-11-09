<?php

namespace App\Livewire;

use App\Models\Exam;
use Livewire\Component;

class ExamLanding extends Component
{
    public Exam $exam;

    public function mount(Exam $exam): void
    {
        $this->exam = $exam->load(['batch', 'sections']);
    }

    public function startExam(): void
    {
        // Redirect to player
        $this->redirectRoute('exam.play', ['exam' => $this->exam->id], navigate: true);
    }

    public function render()
    {
        return view('livewire.exam-landing')->layout('layouts.app', [
            'seoTitle' => $this->exam->title . ' - آزمون کده',
            'seoDescription' => $this->exam->seo_description ?: 'آزمون کده: بزرگترین شبیه ساز آزمون نظام مهندسی. با نمونه سوالات واقعی سال‌های گذشته در محیطی مشابه آزمون اصلی تمرین کنید و کارنامه قبولی/مردودی خود را فوراً دریافت نمایید.',
        ]);
    }
}
