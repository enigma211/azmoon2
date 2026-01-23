<?php

namespace Tests\Feature;

use App\Models\Exam;
use App\Models\User;
use App\Models\Question;
use App\Models\Choice;
use App\Models\SubscriptionPlan;
use App\Models\UserSubscription;
use App\Models\ExamAttempt;
use App\Models\AttemptAnswer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Livewire\Livewire;
use App\Livewire\ExamPlayer;

class ExamEngineTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_view_exam_page()
    {
        $exam = Exam::factory()->create(['is_published' => true]);
        
        $response = $this->get(route('exam.play', $exam));

        $response->assertStatus(200);
        $response->assertSee($exam->title);
    }

    public function test_user_can_submit_answer()
    {
        $user = User::factory()->create();
        $exam = Exam::factory()->create(['is_published' => true]);
        $plan = SubscriptionPlan::factory()->create(['price_toman' => 10000]);
        UserSubscription::create([
            'user_id' => $user->id,
            'subscription_plan_id' => $plan->id,
            'status' => 'active',
            'starts_at' => now(),
            'ends_at' => now()->addDays(30),
        ]);

        $question = Question::factory()->create(['exam_id' => $exam->id, 'type' => 'single_choice']);
        $choice = Choice::factory()->create(['question_id' => $question->id, 'is_correct' => true]);

        $this->actingAs($user);

        Livewire::test(ExamPlayer::class, ['exam' => $exam])
            ->call('saveAnswer', $question->id, $choice->id, true)
            ->assertSet('answers.' . $question->id, [$choice->id => true]);

        $this->assertDatabaseHas('attempt_answers', [
            'question_id' => $question->id,
            'choice_id' => $choice->id,
            'selected' => 1,
        ]);
    }

    public function test_exam_completion_flow()
    {
        $user = User::factory()->create();
        $exam = Exam::factory()->create(['is_published' => true, 'pass_threshold' => 50]);
        $plan = SubscriptionPlan::factory()->create(['price_toman' => 10000]);
        UserSubscription::create([
            'user_id' => $user->id,
            'subscription_plan_id' => $plan->id,
            'status' => 'active',
            'starts_at' => now(),
            'ends_at' => now()->addDays(30),
        ]);

        $question = Question::factory()->create(['exam_id' => $exam->id, 'type' => 'single_choice']);
        $choice = Choice::factory()->create(['question_id' => $question->id, 'is_correct' => true]);

        $this->actingAs($user);

        Livewire::test(ExamPlayer::class, ['exam' => $exam])
            ->call('saveAnswer', $question->id, $choice->id, true)
            ->call('submit')
            ->assertRedirect(route('exam.result', ['exam' => $exam->id, 'attempt' => 1]));

        $this->assertDatabaseHas('exam_attempts', [
            'user_id' => $user->id,
            'exam_id' => $exam->id,
            'status' => 'submitted',
            'score' => 100,
            'passed' => 1,
        ]);
    }
}
