<?php

namespace Database\Seeders;

use App\Models\SubscriptionPlan;
use Illuminate\Database\Seeder;

class DefaultSubscriptionPlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if free plan already exists
        $existingFreePlan = SubscriptionPlan::where('price_toman', 0)->first();

        if (!$existingFreePlan) {
            SubscriptionPlan::create([
                'title' => 'پلن رایگان',
                'description' => 'پلن پیش‌فرض برای تمام کاربران جدید. دسترسی نامحدود به تمام امکانات (قابل تغییر در آینده برای اعمال محدودیت‌ها)',
                'price_toman' => 0,
                'duration_days' => 0, // 0 = نامحدود
                'access_scope' => 'all',
                'is_active' => true,
            ]);

            $this->command->info('✓ پلن رایگان پیش‌فرض ایجاد شد.');
        } else {
            $this->command->info('پلن رایگان قبلاً وجود دارد.');
        }
    }
}
