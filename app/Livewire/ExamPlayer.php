<?php

namespace App\Livewire;

use App\Models\Exam;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use App\Models\ExamAttempt;
use App\Models\AttemptAnswer;
use App\Models\Question;
use App\Domain\Exam\Services\ScoringService;
use Livewire\Attributes\Url;
use Livewire\Component;
use Illuminate\Support\Facades\RateLimiter;
use App\Support\ActivityLogger;

class ExamPlayer extends Component
{
    public Exam $exam;

    public int $index = 0;

    public array $answers = [];

    public ?int $durationSeconds = null;
    public int $remainingSeconds = 0;

    protected ?ExamAttempt $attempt = null;
    protected array $dirtyQueue = [];

    public bool $requireAllAnswered = false;
    
    public string $reportText = '';
    public bool $showReportModal = false;

    public function mount(Exam $exam): void
    {
        // Questions are linked directly to Exam now
        $this->exam = $exam->load(['questions.choices']);

        // Always start with a clean state on each entry (as per requirement)
        $this->answers = [];
        Session::forget($this->sessionKey());

        // Initialize or create DB attempt for authenticated users
        if (Auth::check()) {
            // Delete any previous attempts for this exam/user to ensure clean state
            // This handles the unique constraint on (exam_id, user_id)
            ExamAttempt::where('exam_id', $this->exam->id)
                ->where('user_id', Auth::id())
                ->delete();

            // Create a fresh attempt with in_progress status
            $this->attempt = ExamAttempt::create([
                'exam_id' => $this->exam->id,
                'user_id' => Auth::id(),
                'started_at' => now(),
                'status' => 'in_progress',
            ]);

            // Log start
            ActivityLogger::log('exam_started', [
                'index' => $this->index,
                'duration_seconds' => $this->durationSeconds,
                'remaining_seconds' => $this->remainingSeconds,
            ], $this->exam->id, $this->attempt->id);
        }

        // Initialize countdown timer based on duration_minutes
        $durationMin = (int) ($this->exam->duration_minutes ?? 0);
        $this->durationSeconds = $durationMin > 0 ? $durationMin * 60 : null;
        $this->remainingSeconds = $this->durationSeconds ?? 0;
    }

    public function next(): void
    {
        $newIndex = $this->index + 1;
        $this->index = min($newIndex, $this->questionsCount() - 1);
    }

    public function prev(): void
    {
        $this->index = max($this->index - 1, 0);
    }

    public function questionsCount(): int
    {
        return $this->exam->questions->where('is_deleted', false)->count();
    }

    public function unansweredCount(): int
    {
        $count = 0;
        foreach ($this->exam->questions as $q) {
            // Skip deleted questions
            if ($q->is_deleted) {
                continue;
            }
            
            $ans = $this->answers[$q->id] ?? null;
            $answered = false;
            if (in_array($q->type, ['single_choice','multi_choice','true_false'])) {
                $answered = is_array($ans) && collect($ans)->filter()->count() > 0;
            } else {
                $text = is_array($ans) ? ($ans['text'] ?? '') : (string) $ans;
                $answered = trim((string)$text) !== '';
            }
            if (! $answered) { $count++; }
        }
        return $count;
    }

    public function goTo(int $to): void
    {
        $total = $this->questionsCount();
        if ($to >= 0 && $to < $total) {
            $this->index = $to;
        }
    }

    public function saveAnswer(int $questionId, int $choiceId, bool $checked): void
    {
        // Rate limit: max 10 calls per minute per user/exam (fallback to IP for guests)
        $who = Auth::id() ? ('user:'.Auth::id()) : ('ip:'.(request()->ip() ?? 'unknown'));
        $rateKey = sprintf('saveAnswer:%d:%s', $this->exam->id, $who);
        if (RateLimiter::tooManyAttempts($rateKey, 10)) {
            return; // silently drop to protect server
        }
        RateLimiter::hit($rateKey, 60);
        // Enforce single vs multi choice based on question type
        $question = $this->findQuestion($questionId);
        $current = $this->answers[$questionId] ?? [];

        if ($question && ($question->type === 'single_choice' || $question->type === 'true_false')) {
            // Reset others if single-choice
            $current = [];
            if ($checked) {
                $current[$choiceId] = true;
            }
        } else {
            // Multi-choice
            $current[$choiceId] = $checked;
        }

        $this->answers[$questionId] = $current;

        // Persist in session (debounced from UI side)
        Session::put($this->sessionKey(), $this->answers);

        // Queue for debounced DB flush
        $key = $questionId . ':' . $choiceId;
        $this->dirtyQueue[$key] = [
            'question_id' => $questionId,
            'choice_id' => $choiceId,
            'checked' => $checked,
            'type' => $question?->type,
        ];
    }

    public function flushDirty(): void
    {
        if (!$this->attempt || empty($this->dirtyQueue)) {
            return;
        }

        // Group dirty by question id
        $byQuestion = [];
        foreach ($this->dirtyQueue as $item) {
            $qid = (int)$item['question_id'];
            $byQuestion[$qid] = true;
        }

        foreach (array_keys($byQuestion) as $qid) {
            $question = $this->findQuestion($qid);
            if (!$question) { continue; }

            // For single choice/true_false: delete all existing rows for this question and reinsert current selections
            if (in_array($question->type, ['single_choice','true_false'], true)) {
                AttemptAnswer::where('exam_attempt_id', $this->attempt->id)
                    ->where('question_id', $qid)
                    ->delete();

                $current = $this->answers[$qid] ?? [];
                foreach ($current as $cid => $isOn) {
                    if ($isOn) {
                        AttemptAnswer::updateOrCreate(
                            [
                                'exam_attempt_id' => $this->attempt->id,
                                'question_id' => $qid,
                                'choice_id' => (int)$cid,
                            ],
                            [
                                'selected' => true,
                            ]
                        );
                    }
                }
            } else {
                // Multi-choice: upsert only dirty choices for this question based on current answers state
                $current = $this->answers[$qid] ?? [];
                foreach ($current as $cid => $isOn) {
                    AttemptAnswer::updateOrCreate(
                        [
                            'exam_attempt_id' => $this->attempt->id,
                            'question_id' => $qid,
                            'choice_id' => (int)$cid,
                        ],
                        [
                            'selected' => (bool)$isOn,
                        ]
                    );
                }
            }
        }

        // Clear queue after flush
        $this->dirtyQueue = [];
    }

    public function tick(): void
    {
        if (is_null($this->durationSeconds)) {
            return;
        }
        // Count down from duration to zero
        if ($this->remainingSeconds > 0) {
            $this->remainingSeconds -= 1;
        } else {
            $this->remainingSeconds = 0; // clamp at zero
        }
    }

    protected function sessionKey(): string
    {
        return 'exam_answers_' . $this->exam->id;
    }

    protected function findQuestion(int $id): ?Question
    {
        foreach ($this->exam->questions as $q) {
            if ($q->id === $id) return $q;
        }
        return null;
    }

    public function submit(ScoringService $scoring = null)
    {
        // Ensure pending changes are saved to DB before scoring/redirect
        $this->flushDirty();
        if ($this->requireAllAnswered && $this->unansweredCount() > 0) {
            // Still allow submission based on request, just proceed
        }

        // Compute score using the updated ScoringService with exam-level rules
        $service = $scoring ?: app(ScoringService::class);
        $scores = $service->compute($this->exam, $this->answers);
        
        $percentage = (float)($scores['percentage'] ?? 0.0);
        $correct = (int)($scores['correct'] ?? 0);
        $wrong = (int)($scores['wrong'] ?? 0);
        $unanswered = (int)($scores['unanswered'] ?? 0);
        $earned = (float)($scores['earned'] ?? 0.0);
        $total = (float)($scores['total'] ?? 100.0);

        if ($this->attempt) {
            $passThreshold = property_exists($this->exam, 'pass_threshold') ? ((float) ($this->exam->pass_threshold ?? 0)) : 0.0;
            $this->attempt->update([
                'submitted_at' => now(),
                'score' => $percentage,
                'passed' => $percentage >= $passThreshold,
                'status' => 'submitted',
            ]);

            ActivityLogger::log('exam_finished', [
                'percentage' => $percentage,
                'correct' => $correct,
                'wrong' => $wrong,
                'unanswered' => $unanswered,
            ], $this->exam->id, $this->attempt->id);
        }

        // Persist results for result page (not flash to be safe with SPA navigation)
        session()->put('exam_result_stats', [
            'percentage' => $percentage,
            'earned' => $earned,
            'total' => $total,
            'correct' => $correct,
            'wrong' => $wrong,
            'unanswered' => $unanswered,
            'passed' => $percentage >= (float)($this->exam->pass_threshold ?? 0),
        ]);

        // Save user answers for review
        session()->put('exam_user_answers', $this->answers);
        
        // Force session save before redirect
        session()->save();

        // Server-side redirect ensures a full navigation without relying on Alpine/Livewire SPA
        $params = ['exam' => $this->exam->id];
        if ($this->attempt) {
            $params['attempt'] = $this->attempt->id;
        }
        return redirect()->route('exam.result', $params);
    }

    public function submitReport(): void
    {
        $this->validate([
            'reportText' => 'required|string|min:10|max:1000',
        ], [
            'reportText.required' => 'لطفاً متن گزارش را وارد کنید.',
            'reportText.min' => 'گزارش باید حداقل 10 کاراکتر باشد.',
            'reportText.max' => 'گزارش نباید بیشتر از 1000 کاراکتر باشد.',
        ]);

        $questions = $this->exam->questions->where('is_deleted', false)->values();
        $currentQuestion = $questions[$this->index] ?? null;

        if (!$currentQuestion) {
            session()->flash('error', 'سوال یافت نشد.');
            return;
        }

        \App\Models\QuestionReport::create([
            'user_id' => auth()->id(),
            'question_id' => $currentQuestion->id,
            'exam_id' => $this->exam->id,
            'report' => $this->reportText,
            'status' => 'pending',
        ]);

        $this->reportText = '';
        $this->showReportModal = false;
        session()->flash('success', 'گزارش شما با موفقیت ثبت شد.');
    }

    public function render()
    {
        // Questions are directly on Exam, filter out deleted questions
        $questions = $this->exam->questions->where('is_deleted', false)->values();
        $question = $questions[$this->index] ?? null;

        return view('livewire.exam-player', [
            'question' => $question,
            'index' => $this->index,
            'total' => $questions->count(),
        ])->layout('layouts.app');
    }
}
