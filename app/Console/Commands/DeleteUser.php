<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\UserSubscription;
use App\Models\Payment;
use Illuminate\Console\Command;

class DeleteUser extends Command
{
    protected $signature = 'user:delete {user_id}';
    protected $description = 'Delete a user and all related data';

    public function handle()
    {
        $userId = $this->argument('user_id');
        $user = User::find($userId);

        if (!$user) {
            $this->error("User with ID {$userId} not found!");
            return 1;
        }

        $this->warn("You are about to delete user:");
        $this->line("ID: {$user->id}");
        $this->line("Name: {$user->name}");
        $this->line("Mobile: {$user->mobile}");
        $this->line("Email: {$user->email}");

        if (!$this->confirm('Are you sure you want to delete this user?', false)) {
            $this->info('Deletion cancelled.');
            return 0;
        }

        // Delete related data
        $subscriptionsCount = UserSubscription::where('user_id', $userId)->count();
        $paymentsCount = Payment::where('user_id', $userId)->count();

        UserSubscription::where('user_id', $userId)->delete();
        Payment::where('user_id', $userId)->delete();
        
        // Delete user
        $user->delete();

        $this->info("✓ User deleted successfully!");
        $this->line("  - Deleted {$subscriptionsCount} subscriptions");
        $this->line("  - Deleted {$paymentsCount} payments");

        return 0;
    }
}
