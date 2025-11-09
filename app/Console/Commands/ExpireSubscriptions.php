<?php

namespace App\Console\Commands;

use App\Models\UserSubscription;
use App\Models\SubscriptionPlan;
use Illuminate\Console\Command;

class ExpireSubscriptions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscriptions:expire';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Expire subscriptions that have passed their end date and assign free plan';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for expired subscriptions...');

        // Find all active subscriptions that have expired
        $expiredSubscriptions = UserSubscription::where('status', 'active')
            ->whereNotNull('ends_at')
            ->where('ends_at', '<=', now())
            ->get();

        if ($expiredSubscriptions->isEmpty()) {
            $this->info('No expired subscriptions found.');
            return 0;
        }

        $freePlan = SubscriptionPlan::where('price_toman', 0)
            ->where('is_active', true)
            ->first();

        if (!$freePlan) {
            $this->error('Free plan not found! Please create a free plan first.');
            return 1;
        }

        $count = 0;
        foreach ($expiredSubscriptions as $subscription) {
            // Mark current subscription as expired
            $subscription->update(['status' => 'expired']);

            // Create new free subscription
            UserSubscription::create([
                'user_id' => $subscription->user_id,
                'subscription_plan_id' => $freePlan->id,
                'starts_at' => now(),
                'ends_at' => null, // Unlimited
                'status' => 'active',
            ]);

            $count++;
            $this->info("User {$subscription->user_id}: Subscription expired and moved to free plan.");
        }

        $this->info("Total expired subscriptions: {$count}");
        return 0;
    }
}
