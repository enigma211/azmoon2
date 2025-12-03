<?php

namespace App\Console\Commands;

use App\Models\UserSubscription;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CheckSubscriptionExpiry extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscription:check-expiry';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for expired subscriptions and update their status';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for expired subscriptions...');

        $expiredCount = UserSubscription::where('status', 'active')
            ->whereNotNull('ends_at')
            ->where('ends_at', '<', now())
            ->update(['status' => 'expired']);

        if ($expiredCount > 0) {
            $this->info("Successfully expired {$expiredCount} subscriptions.");
            Log::info("Expired {$expiredCount} subscriptions via scheduled task.");
        } else {
            $this->info('No expired subscriptions found.');
        }
    }
}
