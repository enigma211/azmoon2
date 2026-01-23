<?php

namespace Tests\Unit;

use App\Models\User;
use App\Models\UserSubscription;
use App\Models\SubscriptionPlan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Spatie\Permission\Models\Role;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_has_active_subscription()
    {
        $user = User::factory()->create();
        $plan = SubscriptionPlan::factory()->create(['price_toman' => 10000]);

        // No subscription yet
        $this->assertFalse($user->hasPaidSubscription());

        // Create active subscription
        $subscription = UserSubscription::create([
            'user_id' => $user->id,
            'subscription_plan_id' => $plan->id,
            'starts_at' => now(),
            'ends_at' => now()->addDays(30),
            'status' => 'active',
        ]);

        $this->assertTrue($user->hasPaidSubscription());

        // Expired subscription
        $subscription->update(['ends_at' => now()->subDay()]);
        $this->assertFalse($user->hasPaidSubscription());

        // Inactive status
        $subscription->update(['ends_at' => now()->addDays(30), 'status' => 'expired']);
        $this->assertFalse($user->hasPaidSubscription());
    }

    public function test_user_role_label()
    {
        // Setup roles
        Role::create(['name' => 'admin']);
        
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $this->assertEquals('مدیر سیستم', $admin->getRoleStatus());

        $freeUser = User::factory()->create();
        $this->assertEquals('کاربر رایگان', $freeUser->getRoleStatus());

        $paidUser = User::factory()->create();
        $paidPlan = SubscriptionPlan::factory()->create(['price_toman' => 50000]);
        UserSubscription::create([
            'user_id' => $paidUser->id,
            'subscription_plan_id' => $paidPlan->id,
            'starts_at' => now(),
            'ends_at' => now()->addDays(30),
            'status' => 'active',
        ]);
        $this->assertEquals('اشتراک ویژه', $paidUser->getRoleStatus());

        $giftUser = User::factory()->create();
        $freePlan = SubscriptionPlan::factory()->create(['price_toman' => 0]);
        UserSubscription::create([
            'user_id' => $giftUser->id,
            'subscription_plan_id' => $freePlan->id,
            'starts_at' => now(),
            'ends_at' => now()->addDays(7),
            'status' => 'active',
        ]);
        $this->assertEquals('اشتراک هدیه', $giftUser->getRoleStatus());
    }
}
