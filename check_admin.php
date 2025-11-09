<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Spatie\Permission\Models\Role;

/**
 * Script to check and fix admin user
 * Run this with: php artisan tinker
 * Then copy and paste this entire script
 */

echo "=== Checking Admin User Status ===\n";

// 1. Check if tables exist
$tablesToCheck = ['users', 'user_subscriptions', 'subscription_plans', 'roles', 'model_has_roles'];

foreach ($tablesToCheck as $table) {
    if (Schema::hasTable($table)) {
        echo "✅ Table '$table' exists\n";
    } else {
        echo "❌ Table '$table' does NOT exist\n";
    }
}

echo "\n=== Checking Admin User ===\n";

// 2. Check if admin role exists
$adminRole = Role::where('name', 'admin')->first();
if ($adminRole) {
    echo "✅ Admin role exists (ID: {$adminRole->id})\n";
} else {
    echo "❌ Admin role does NOT exist\n";
}

// 3. Check if admin user exists
$adminUser = User::where('email', 'admin@example.com')->first();
if ($adminUser) {
    echo "✅ Admin user exists (ID: {$adminUser->id}, Name: {$adminUser->name})\n";
    
    // Check if user has admin role
    if ($adminUser->hasRole('admin')) {
        echo "✅ Admin user has admin role\n";
    } else {
        echo "❌ Admin user does NOT have admin role\n";
    }
    
    // Check if user can access Filament panel
    if ($adminUser->canAccessPanel(app(\Filament\Panel::class))) {
        echo "✅ Admin user can access Filament panel\n";
    } else {
        echo "❌ Admin user cannot access Filament panel\n";
    }
} else {
    echo "❌ Admin user does NOT exist\n";
}

echo "\n=== Creating Admin User (if needed) ===\n";

// 4. Create admin role if it doesn't exist
if (!$adminRole) {
    $adminRole = Role::firstOrCreate(['name' => 'admin']);
    echo "✅ Created admin role\n";
}

// 5. Create admin user if it doesn't exist
if (!$adminUser) {
    $adminUser = User::firstOrCreate(
        ['email' => 'admin@example.com'],
        [
            'name' => 'Admin User',
            'password' => Hash::make('Admin@12345'),
            'mobile' => '09000000000',
            'username' => 'admin',
        ]
    );
    echo "✅ Created admin user\n";
}

// 6. Assign admin role if not assigned
if (!$adminUser->hasRole('admin')) {
    $adminUser->assignRole('admin');
    echo "✅ Assigned admin role to user\n";
}

echo "\n=== Final Check ===\n";
echo "Admin Email: admin@example.com\n";
echo "Admin Password: Admin@12345\n";
echo "Login URL: " . url('/admin/login') . "\n";

// 7. Test panel access
if ($adminUser->fresh()->canAccessPanel(app(\Filament\Panel::class))) {
    echo "✅ Admin panel access confirmed\n";
} else {
    echo "❌ Admin panel access failed\n";
}

echo "\n=== Done ===\n";
