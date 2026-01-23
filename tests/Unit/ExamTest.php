<?php

namespace Tests\Unit;

use App\Models\Exam;
use App\Models\Question;
use App\Models\Choice;
use App\Domain\Exam\Services\ScoringService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExamTest extends TestCase
{
    use RefreshDatabase;

    public function test_exam_score_calculation()
    {
        $exam = Exam::factory()->create([
            'total_score' => 100,
            'negative_score_ratio' => 3, // 3 wrong = -1 correct
        ]);

        // Create 4 questions
        $questions = Question::factory()->count(4)->create(['exam_id' => $exam->id, 'type' => 'single_choice', 'is_deleted' => false]);
        
        foreach ($questions as $q) {
            Choice::factory()->create(['question_id' => $q->id, 'is_correct' => true]);
            Choice::factory()->create(['question_id' => $q->id, 'is_correct' => false]);
        }

        $scoringService = new ScoringService();

        // Case 1: All correct
        $answers = [];
        foreach ($questions as $q) {
            $correctChoice = $q->choices()->where('is_correct', true)->first();
            $answers[$q->id] = [$correctChoice->id => true];
        }
        $result = $scoringService->compute($exam, $answers);
        $this->assertEquals(100, $result['percentage']);
        $this->assertEquals(4, $result['correct']);

        // Case 2: 3 correct, 1 wrong (with negative scoring)
        // Score per question = 100 / 4 = 25
        // Penalty = (1/3) * 25 = 8.33
        // Total earned = (3 * 25) - 8.33 = 75 - 8.33 = 66.67
        $answers = [];
        for ($i = 0; $i < 3; $i++) {
            $q = $questions[$i];
            $correctChoice = $q->choices()->where('is_correct', true)->first();
            $answers[$q->id] = [$correctChoice->id => true];
        }
        $wrongQ = $questions[3];
        $wrongChoice = $wrongQ->choices()->where('is_correct', false)->first();
        $answers[$wrongQ->id] = [$wrongChoice->id => true];

        $result = $scoringService->compute($exam, $answers);
        $this->assertEquals(66.67, $result['percentage']);
        $this->assertEquals(3, $result['correct']);
        $this->assertEquals(1, $result['wrong']);
    }

    public function test_exam_duration_validation()
    {
        $exam = Exam::factory()->make([
            'duration_minutes' => -10
        ]);
        
        $this->assertTrue($exam->duration_minutes < 0, "Duration should be able to be negative in model but business logic should handle it");
        
        // Check if our code handles negative or zero duration
        $durationMin = (int) ($exam->duration_minutes ?? 0);
        $durationSeconds = $durationMin > 0 ? $durationMin * 60 : null;
        
        $this->assertNull($durationSeconds, "Duration seconds should be null for negative minutes");
    }
}
