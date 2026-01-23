<?php

namespace Tests\Feature;

use App\Models\Choice;
use App\Models\Exam;
use App\Models\Question;
use App\Models\User;
use App\Models\UserSubscription;
use App\Models\SubscriptionPlan;
use App\Models\ExamAttempt;
use App\Models\AttemptAnswer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ExamPlayerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Setup initial data
        $this->user = User::factory()->create();
        $this->exam = Exam::factory()->create(['title' => 'Test Exam', 'duration_minutes' => 10]);
        $this->questions = Question::factory()->count(3)->create(['exam_id' => $this->exam->id]);
        
        foreach ($this->questions as $question) {
            Choice::factory()->count(4)->create(['question_id' => $question->id]);
        }
    }

    /** @test */
    public function guest_can_see_exam_landing_and_player_pages()
    {
        $this->get(route('exam.landing', $this->exam))
            ->assertStatus(200)
            ->assertSee($this->exam->title);

        $this->get(route('exam.play', $this->exam))
            ->assertStatus(200)
            ->assertSee($this->exam->title);
    }

    /** @test */
    public function authenticated_user_can_see_exam_player()
    {
        $this->actingAs($this->user)
            ->get(route('exam.play', $this->exam))
            ->assertStatus(200);
    }

    /** @test */
    public function navigation_between_questions_works_correctly()
    {
        Livewire::test(\App\Livewire\ExamPlayer::class, ['exam' => $this->exam])
            ->assertSet('page', 1)
            ->call('next')
            ->assertSet('page', 2)
            ->call('next')
            ->assertSet('page', 3)
            ->call('next')
            ->assertSet('page', 3) // Should stay at last page
            ->call('prev')
            ->assertSet('page', 2)
            ->call('goTo', 1)
            ->assertSet('page', 1);
    }

    /** @test */
    public function authenticated_user_with_subscription_can_save_answers()
    {
        // Give user a subscription
        $plan = SubscriptionPlan::factory()->create();
        UserSubscription::create([
            'user_id' => $this->user->id,
            'subscription_plan_id' => $plan->id,
            'starts_at' => now(),
            'ends_at' => now()->addDays(30),
            'status' => 'active',
        ]);

        $question = $this->questions->first();
        $choice = $question->choices->first();

        Livewire::actingAs($this->user)
            ->test(\App\Livewire\ExamPlayer::class, ['exam' => $this->exam])
            ->call('saveAnswer', $question->id, $choice->id, true)
            ->assertSet('answers.' . $question->id . '.' . $choice->id, true);

        $this->assertDatabaseHas('attempt_answers', [
            'question_id' => $question->id,
            'choice_id' => $choice->id,
            'selected' => true,
        ]);
    }

    /** @test */
    public function guest_cannot_save_answers()
    {
        $question = $this->questions->first();
        $choice = $question->choices->first();

        Livewire::test(\App\Livewire\ExamPlayer::class, ['exam' => $this->exam])
            ->call('saveAnswer', $question->id, $choice->id, true)
            ->assertSet('answers', []);

        $this->assertDatabaseCount('attempt_answers', 0);
    }

    /** @test */
    public function exam_attempt_is_properly_closed_after_submission()
    {
        // Give user a subscription
        $plan = SubscriptionPlan::factory()->create();
        UserSubscription::create([
            'user_id' => $this->user->id,
            'subscription_plan_id' => $plan->id,
            'starts_at' => now(),
            'ends_at' => now()->addDays(30),
            'status' => 'active',
        ]);

        $question = $this->questions->first();
        $choice = $question->choices->first();

        Livewire::actingAs($this->user)
            ->test(\App\Livewire\ExamPlayer::class, ['exam' => $this->exam])
            ->call('saveAnswer', $question->id, $choice->id, true)
            ->call('submit')
            ->assertRedirect();

        $this->assertDatabaseHas('exam_attempts', [
            'user_id' => $this->user->id,
            'exam_id' => $this->exam->id,
            'status' => 'submitted',
        ]);

        $attempt = ExamAttempt::where('user_id', $this->user->id)->first();
        $this->assertNotNull($attempt->submitted_at);
        $this->assertEquals('submitted', $attempt->status);
    }
}
