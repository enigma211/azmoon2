<?php

namespace App\Filament\Widgets;

use App\Models\UserSubscription;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ActiveSubscriptionsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        // Total active subscriptions (free or paid)
        $totalActive = UserSubscription::where('status', 'active')
            ->where(function ($query) {
                $query->whereNull('ends_at')
                    ->orWhere('ends_at', '>', now());
            })
            ->distinct('user_id')
            ->count('user_id');

        // Paid active subscriptions (price > 0)
        $paidActive = UserSubscription::where('status', 'active')
            ->whereHas('subscriptionPlan', function ($query) {
                $query->where('price_toman', '>', 0);
            })
            ->where(function ($query) {
                $query->whereNull('ends_at')
                    ->orWhere('ends_at', '>', now());
            })
            ->distinct('user_id')
            ->count('user_id');

        return [
            Stat::make('کاربران دارای اشتراک فعال', $totalActive)
                ->description('تمام اشتراک‌های فعال (رایگان یا پولی)')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),
            Stat::make('اشتراک پولی فعال', $paidActive)
                ->description('کاربرانی که اشتراک پولی فعال دارند')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('warning'),
        ];
    }
}
