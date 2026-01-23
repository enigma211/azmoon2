<?php

namespace Tests\Feature;

use App\Models\Payment;
use App\Models\SubscriptionPlan;
use App\Models\User;
use App\Models\UserSubscription;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\Log;
use Shetabit\Payment\Facade\Payment as PaymentGateway;
use Shetabit\Multipay\Receipt;

class PaymentFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_zibal_callback_success()
    {
        $user = User::factory()->create();
        $plan = SubscriptionPlan::factory()->create(['price_toman' => 5000, 'duration_days' => 30]);
        
        $payment = Payment::create([
            'user_id' => $user->id,
            'subscription_plan_id' => $plan->id,
            'amount' => $plan->price_toman,
            'status' => 'pending',
            'authority' => 'test_authority_123',
        ]);

        // Mock the Payment Gateway verification
        $receipt = new Receipt('test_ref_id_456');
        PaymentGateway::shouldReceive('amount')->andReturnSelf();
        PaymentGateway::shouldReceive('transactionId')->andReturnSelf();
        PaymentGateway::shouldReceive('verify')->andReturn($receipt);

        $response = $this->actingAs($user)
            ->get(route('payment.verify', [
                'trackId' => 'test_authority_123',
                'success' => '1'
            ]));

        $response->assertRedirect(route('profile'));
        $response->assertSessionHas('subscription_success');

        $this->assertDatabaseHas('payments', [
            'id' => $payment->id,
            'status' => 'success',
            'ref_id' => 'test_ref_id_456',
        ]);
    }

    public function test_subscription_activation_after_payment()
    {
        $user = User::factory()->create();
        $plan = SubscriptionPlan::factory()->create(['price_toman' => 5000, 'duration_days' => 30]);
        
        $payment = Payment::create([
            'user_id' => $user->id,
            'subscription_plan_id' => $plan->id,
            'amount' => $plan->price_toman,
            'status' => 'pending',
            'authority' => 'test_authority_789',
        ]);

        $receipt = new Receipt('test_ref_id_999');
        PaymentGateway::shouldReceive('amount')->andReturnSelf();
        PaymentGateway::shouldReceive('transactionId')->andReturnSelf();
        PaymentGateway::shouldReceive('verify')->andReturn($receipt);

        $this->actingAs($user)
            ->get(route('payment.verify', [
                'trackId' => 'test_authority_789',
                'success' => '1'
            ]));

        $this->assertDatabaseHas('user_subscriptions', [
            'user_id' => $user->id,
            'subscription_plan_id' => $plan->id,
            'status' => 'active',
        ]);

        $this->assertTrue($user->fresh()->hasPaidSubscription());
    }
}
