<?php

namespace App\Filament\Resources\UserSubscriptionResource\Pages;

use App\Filament\Resources\UserSubscriptionResource;
use App\Models\SubscriptionPlan;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Carbon;

class CreateUserSubscription extends CreateRecord
{
    protected static string $resource = UserSubscriptionResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // If ends_at is empty, compute it from plan duration.
        if (empty($data['ends_at']) && ! empty($data['subscription_plan_id'])) {
            $durationDays = (int) (SubscriptionPlan::where('id', $data['subscription_plan_id'])->value('duration_days') ?? 0);
            $start = ! empty($data['starts_at']) ? Carbon::parse($data['starts_at']) : Carbon::today();
            if ($durationDays > 0) {
                $data['ends_at'] = $start->copy()->addDays($durationDays)->format('Y-m-d');
            } else {
                // Unlimited plan: set ends_at to null
                $data['ends_at'] = null;
            }
        }

        return $data;
    }
}
