// Copy and paste this into tinker (php artisan tinker)

use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

echo "=== Checking Admin User ===\n\n";

// Check if admin user exists
$admin = User::where('email', 'admin@example.com')->first();

if ($admin) {
    echo "✅ Admin user EXISTS\n";
    echo "   ID: {$admin->id}\n";
    echo "   Name: {$admin->name}\n";
    echo "   Email: {$admin->email}\n";
    echo "   Mobile: {$admin->mobile}\n";
    
    // Check roles
    $roles = $admin->getRoleNames();
    echo "   Roles: " . $roles->implode(', ') . "\n";
    
    if ($admin->hasRole('admin')) {
        echo "   ✅ Has admin role\n";
    } else {
        echo "   ❌ Does NOT have admin role\n";
    }
} else {
    echo "❌ Admin user does NOT exist\n";
    echo "Creating admin user...\n";
    
    // Create admin role if needed
    $adminRole = Role::firstOrCreate(['name' => 'admin']);
    
    // Create admin user
    $admin = User::create([
        'name' => 'Admin',
        'email' => 'admin@example.com',
        'mobile' => '09000000000',
        'username' => 'admin',
        'password' => Hash::make('Admin@12345'),
    ]);
    
    $admin->assignRole('admin');
    
    echo "✅ Admin user created successfully\n";
}

echo "\n=== Login Info ===\n";
echo "Email: admin@example.com\n";
echo "Password: Admin@12345\n";
echo "URL: /admin/login\n";
