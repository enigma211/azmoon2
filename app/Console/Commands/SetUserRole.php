<?php

namespace App\Console\Commands;

use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Console\Command;

class SetUserRole extends Command
{
    protected $signature = 'user:set-role {user_id} {role}';
    protected $description = 'Manually set a role for a specific user';

    public function handle()
    {
        $userId = $this->argument('user_id');
        $roleName = $this->argument('role');

        $user = User::find($userId);
        if (!$user) {
            $this->error("User with ID {$userId} not found!");
            return 1;
        }

        // Check if role exists
        $role = Role::where('name', $roleName)->first();
        if (!$role) {
            $this->error("Role '{$roleName}' not found!");
            $this->line("Available roles:");
            foreach (Role::all() as $r) {
                $this->line("  - {$r->name}");
            }
            return 1;
        }

        // Assign role
        $user->syncRoles([$roleName]);

        $this->info("✓ Successfully assigned role '{$roleName}' to user {$user->name} (ID: {$userId})");
        
        return 0;
    }
}
