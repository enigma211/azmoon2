<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\UserSubscription;
use Illuminate\Console\Command;

class CheckUserSubscription extends Command
{
    protected $signature = 'user:check-subscription {user_id}';
    protected $description = 'Check detailed subscription information for a specific user';

    public function handle()
    {
        $userId = $this->argument('user_id');
        $user = User::find($userId);

        if (!$user) {
            $this->error("User with ID {$userId} not found!");
            return 1;
        }

        $this->info("=== User Information ===");
        $this->line("ID: {$user->id}");
        $this->line("Name: {$user->name}");
        $this->line("Mobile: {$user->mobile}");
        $this->line("Email: {$user->email}");
        
        // Check roles
        $roles = $user->roles->pluck('name')->toArray();
        $this->line("Roles: " . (empty($roles) ? 'None' : implode(', ', $roles)));

        $this->newLine();
        $this->info("=== Subscription Information ===");

        // Get all subscriptions
        $allSubscriptions = UserSubscription::where('user_id', $userId)
            ->with('subscriptionPlan')
            ->orderBy('created_at', 'desc')
            ->get();

        if ($allSubscriptions->isEmpty()) {
            $this->warn("No subscriptions found for this user.");
        } else {
            $this->line("Total subscriptions: " . $allSubscriptions->count());
            $this->newLine();

            foreach ($allSubscriptions as $index => $sub) {
                $this->line("--- Subscription #" . ($index + 1) . " ---");
                $this->line("ID: {$sub->id}");
                $this->line("Plan: " . ($sub->subscriptionPlan->title ?? 'Unknown'));
                $this->line("Price: " . ($sub->subscriptionPlan->price_toman ?? 0) . " تومان");
                $this->line("Status: {$sub->status}");
                $this->line("Starts: " . $sub->starts_at->format('Y-m-d H:i:s'));
                $this->line("Ends: " . ($sub->ends_at ? $sub->ends_at->format('Y-m-d H:i:s') : 'Unlimited'));
                
                if ($sub->ends_at) {
                    $daysLeft = now()->diffInDays($sub->ends_at, false);
                    if ($daysLeft > 0) {
                        $this->line("Days remaining: " . ceil($daysLeft));
                    } else {
                        $this->line("Expired " . abs(floor($daysLeft)) . " days ago");
                    }
                }
                
                $this->newLine();
            }
        }

        // Get active subscription
        $activeSubscription = $user->activeSubscription()->first();
        
        $this->info("=== Active Subscription ===");
        if ($activeSubscription) {
            $plan = $activeSubscription->subscriptionPlan;
            $this->line("Plan: " . ($plan->title ?? 'Unknown'));
            $this->line("Price: " . ($plan->price_toman ?? 0) . " تومان");
            $this->line("Status: {$activeSubscription->status}");
            $this->line("Starts: " . $activeSubscription->starts_at->format('Y-m-d H:i:s'));
            $this->line("Ends: " . ($activeSubscription->ends_at ? $activeSubscription->ends_at->format('Y-m-d H:i:s') : 'Unlimited'));
            
            if ($plan && $plan->price_toman > 0) {
                $this->info("✓ User should have 'اشتراک ویژه' role");
            } else {
                $this->info("✓ User should have 'رایگان' role");
            }
        } else {
            $this->warn("No active subscription found.");
            $this->info("✓ User should have 'رایگان' role");
        }

        return 0;
    }
}
