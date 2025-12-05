<?php

namespace App\Livewire;

use App\Models\Exam;
use App\Models\Question;
use Livewire\Component;

class StudyPlayer extends Component
{
    public Exam $exam;
    public $questions = [];
    public int $currentQuestionIndex = 0;
    public bool $isAnswerRevealed = false;
    public $selectedOption = null; // Just for visual feedback

    public function mount(Exam $exam)
    {
        $this->exam = $exam;
        // Load all questions with choices, ordered by order_column
        $this->questions = $exam->questions()
            ->where('is_deleted', false)
            ->with('choices')
            ->orderBy('order_column')
            ->orderBy('id')
            ->get();

        if ($this->questions->isEmpty()) {
            return redirect()->route('exams', ['batch' => $this->exam->exam_batch_id]);
        }

        // If a specific question ID is passed via query string, jump to it
        $questionId = request()->query('q');
        if ($questionId) {
            $index = $this->questions->search(fn($q) => $q->id == $questionId);
            if ($index !== false) {
                $this->currentQuestionIndex = $index;
            }
        }
    }

    public function getCurrentQuestionProperty()
    {
        return $this->questions[$this->currentQuestionIndex] ?? null;
    }

    public function nextQuestion()
    {
        if ($this->currentQuestionIndex < count($this->questions) - 1) {
            $this->currentQuestionIndex++;
            $this->resetState();
        }
    }

    public function prevQuestion()
    {
        if ($this->currentQuestionIndex > 0) {
            $this->currentQuestionIndex--;
            $this->resetState();
        }
    }

    public function jumpToQuestion($index)
    {
        if (isset($this->questions[$index])) {
            $this->currentQuestionIndex = $index;
            $this->resetState();
        }
    }

    public function toggleAnswer()
    {
        $this->isAnswerRevealed = !$this->isAnswerRevealed;
    }

    public function selectOption($choiceId)
    {
        // If answer is already revealed, don't allow changing selection (optional UX choice)
        // if ($this->isAnswerRevealed) return; 
        
        $this->selectedOption = $choiceId;
    }

    private function resetState()
    {
        $this->isAnswerRevealed = false;
        $this->selectedOption = null;
    }

    public function render()
    {
        return view('livewire.study-player')
            ->layout('layouts.app', [
                'seoTitle' => 'مطالعه: ' . $this->exam->title,
                'seoDescription' => 'حالت مطالعه آموزشی برای آزمون ' . $this->exam->title
            ]);
    }
}
