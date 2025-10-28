<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Models\SubscriptionPlan;
use App\Models\UserSubscription;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Set username to display (readonly)
        $data['username'] = $this->record->username ?? $this->record->mobile;
        
        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Ensure username is always the mobile number
        if (isset($data['mobile'])) {
            $data['username'] = $data['mobile'];
        }
        
        return $data;
    }

    protected function afterSave(): void
    {
        // Check if admin wants to grant subscription
        $grantSubscription = $this->data['grant_subscription'] ?? false;
        $subscriptionDays = $this->data['subscription_days'] ?? 90;

        if ($grantSubscription && $subscriptionDays > 0) {
            // Find or create a premium plan
            $premiumPlan = SubscriptionPlan::where('price_toman', '>', 0)
                ->where('is_active', true)
                ->first();

            if (!$premiumPlan) {
                // Create a default premium plan if none exists
                $premiumPlan = SubscriptionPlan::create([
                    'title' => 'اشتراک ویژه (توسط مدیر)',
                    'description' => 'اشتراک اعطا شده توسط مدیر سیستم',
                    'price_toman' => 1,
                    'duration_days' => $subscriptionDays,
                    'is_active' => true,
                ]);
            }

            // Deactivate current active subscriptions
            UserSubscription::where('user_id', $this->record->id)
                ->where('status', 'active')
                ->update(['status' => 'expired']);

            // Create new subscription
            UserSubscription::create([
                'user_id' => $this->record->id,
                'subscription_plan_id' => $premiumPlan->id,
                'starts_at' => now(),
                'ends_at' => now()->addDays($subscriptionDays),
                'status' => 'active',
            ]);

            \Filament\Notifications\Notification::make()
                ->title('اشتراک با موفقیت اعطا شد')
                ->success()
                ->body("اشتراک {$subscriptionDays} روزه به کاربر اعطا شد.")
                ->send();
        }
    }
}
