<?php

namespace Tests\Feature;

use App\Models\Exam;
use App\Models\ExamBatch;
use App\Models\SubscriptionPlan;
use App\Models\User;
use App\Models\Payment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;
use Shetabit\Payment\Facade\Payment as PaymentGateway;
use Shetabit\Multipay\Receipt;

class SubscriptionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create();
        $this->plan = SubscriptionPlan::factory()->create([
            'title' => 'Premium Plan',
            'price_toman' => 10000,
            'duration_days' => 30,
        ]);
        
        $this->batch = ExamBatch::factory()->create(['is_free' => false]);
        $this->exam = Exam::factory()->create([
            'exam_batch_id' => $this->batch->id,
            'is_published' => true
        ]);
    }

    /** @test */
    public function unsubscribed_user_cannot_access_paid_exam_result()
    {
        // Result page is protected by EnsureSubscribed middleware
        $this->actingAs($this->user)
            ->get(route('exam.result', $this->exam))
            ->assertRedirect(route('profile'))
            ->assertSessionHas('warning', 'لطفاً اشتراک ویژه تهیه کنید.');
    }

    /** @test */
    public function subscribed_user_can_access_paid_exam_result()
    {
        $this->user->subscriptions()->create([
            'subscription_plan_id' => $this->plan->id,
            'starts_at' => now(),
            'ends_at' => now()->addDays(30),
            'status' => 'active',
        ]);

        $this->actingAs($this->user)
            ->get(route('exam.result', $this->exam))
            ->assertStatus(200);
    }

    /** @test */
    public function payment_callback_activates_subscription()
    {
        $payment = Payment::create([
            'user_id' => $this->user->id,
            'subscription_plan_id' => $this->plan->id,
            'amount' => $this->plan->price_toman,
            'status' => 'pending',
            'authority' => 'TEST_AUTHORITY',
        ]);

        // Mock Shetabit Payment verification
        $receipt = \Mockery::mock(Receipt::class);
        $receipt->shouldReceive('getReferenceId')->andReturn('REF_123');
        $receipt->shouldReceive('getDriver')->andReturn('zibal');

        PaymentGateway::shouldReceive('amount')->andReturnSelf();
        PaymentGateway::shouldReceive('transactionId')->andReturnSelf();
        PaymentGateway::shouldReceive('verify')->andReturn($receipt);

        $response = $this->get(route('payment.verify', [
            'trackId' => 'TEST_AUTHORITY',
            'success' => '1',
        ]));

        $response->assertRedirect(route('profile'));
        $response->assertSessionHas('subscription_success');

        $this->assertDatabaseHas('payments', [
            'id' => $payment->id,
            'status' => 'success',
            'ref_id' => 'REF_123',
        ]);

        $this->assertDatabaseHas('user_subscriptions', [
            'user_id' => $this->user->id,
            'subscription_plan_id' => $this->plan->id,
            'status' => 'active',
        ]);
        
        $this->assertTrue($this->user->activeSubscription()->exists());
    }
}
