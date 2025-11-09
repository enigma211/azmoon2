<?php

namespace App\Console\Commands;

use App\Models\UserSubscription;
use App\Models\User;
use Illuminate\Console\Command;

class FixUserRoles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:fix-roles {--user-id= : Specific user ID to fix}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix user roles based on their subscription status';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Fixing user roles based on subscription status...');

        $query = UserSubscription::where('status', 'active')
            ->where(function($query) {
                $query->whereNull('ends_at')
                      ->orWhere('ends_at', '>', now());
            });

        // If specific user ID is provided, filter by that user
        if ($userId = $this->option('user-id')) {
            $query->where('user_id', $userId);
            $this->info("Processing user ID: {$userId}");
        }

        $activeSubscriptions = $query->with('subscriptionPlan', 'user')->get();

        if ($activeSubscriptions->isEmpty()) {
            $this->info('No active subscriptions found.');
            return 0;
        }

        $count = 0;
        foreach ($activeSubscriptions as $subscription) {
            $user = $subscription->user;
            if (!$user) {
                $this->warn("User not found for subscription ID: {$subscription->id}");
                continue;
            }

            $plan = $subscription->subscriptionPlan;
            if (!$plan) {
                $this->warn("Plan not found for subscription ID: {$subscription->id}");
                continue;
            }

            // Assign role based on plan price
            if ($plan->price_toman > 0) {
                $user->syncRoles(['اشتراک ویژه']);
                $this->info("User {$user->id} ({$user->name}): Assigned 'اشتراک ویژه' role");
            } else {
                $user->syncRoles(['رایگان']);
                $this->info("User {$user->id} ({$user->name}): Assigned 'رایگان' role");
            }

            $count++;
        }

        $this->info("Total users processed: {$count}");
        return 0;
    }
}
