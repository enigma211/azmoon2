<?php

namespace App\Livewire;

use App\Models\Exam;
use App\Models\ExamAttempt;
use App\Models\AttemptAnswer;
use App\Domain\Exam\Services\ScoringService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Url;
use Livewire\Component;
use App\Support\ActivityLogger;

class ExamResult extends Component
{
    public Exam $exam;

    #[Url]
    public ?int $attempt = null;

    public array $stats = [];
    public array $userAnswers = [];

    protected ?ExamAttempt $attemptModel = null;

    public function mount(Exam $exam): void
    {
        $this->exam = $exam->load(['questions.choices']);

        // Try to resolve attempt from query or latest submitted attempt for user
        $attempt = null;
        if (!is_null($this->attempt)) {
            $query = ExamAttempt::query()->where('id', $this->attempt)->where('exam_id', $this->exam->id);
            if (Auth::check()) {
                $query->where('user_id', Auth::id());
            }
            $attempt = $query->first();
        } elseif (Auth::check()) {
            $attempt = ExamAttempt::where('exam_id', $this->exam->id)
                ->where('user_id', Auth::id())
                ->whereNotNull('submitted_at')
                ->latest('submitted_at')
                ->first();
        }

        $this->attemptModel = $attempt;

        if ($attempt) {
            // Build answers array: [question_id => [choice_id => bool]]
            $answers = [];
            $rows = AttemptAnswer::where('exam_attempt_id', $attempt->id)->get();
            foreach ($rows as $row) {
                $answers[$row->question_id][$row->choice_id] = (bool) $row->selected;
            }
            $this->userAnswers = $answers;

            // Compute stats using ScoringService
            $scores = app(ScoringService::class)->compute($this->exam, $answers);
            $percentage = (float)($scores['percentage'] ?? 0.0);
            $this->stats = [
                'percentage' => $percentage,
                'earned' => (float)($scores['earned'] ?? 0.0),
                'total' => (float)($scores['total'] ?? 100.0),
                'correct' => (int)($scores['correct'] ?? 0),
                'wrong' => (int)($scores['wrong'] ?? 0),
                'unanswered' => (int)($scores['unanswered'] ?? 0),
                'passed' => $percentage >= (float)($this->exam->pass_threshold ?? 0),
            ];

            // Log viewing result with attempt
            ActivityLogger::log('result_viewed', [
                'percentage' => $percentage,
            ], $this->exam->id, $attempt->id);
        } else {
            // Fallback to session when no attempt is available (e.g., guest)
            $this->stats = session('exam_result_stats', []);
            $this->userAnswers = session('exam_user_answers', []);

            if (!empty($this->stats)) {
                ActivityLogger::log('result_viewed_session', [
                    'percentage' => (float)($this->stats['percentage'] ?? 0.0),
                ], $this->exam->id, null);
            }
        }
    }

    public function render()
    {
        return view('livewire.exam-result', [
            'stats' => $this->stats,
            'userAnswers' => $this->userAnswers,
        ])->layout('layouts.app');
    }
}
