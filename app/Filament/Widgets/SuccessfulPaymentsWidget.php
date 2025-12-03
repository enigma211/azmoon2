<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\PaymentResource;
use App\Models\Payment;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SuccessfulPaymentsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        // Count unique users who have at least one successful payment
        $successCount = Payment::where('status', 'success')
            ->distinct('user_id')
            ->count('user_id');

        return [
            Stat::make('کاربران با پرداخت موفق', $successCount)
                ->description('تعداد کاربرانی که حداقل یک تراکنش موفق داشته‌اند')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('success')
                ->url(PaymentResource::getUrl('index')),
        ];
    }
}
