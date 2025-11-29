<?php

namespace App\Filament\Resources\PaymentResource\Widgets;

use App\Models\Payment;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PaymentStats extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('درآمد ۲۴ ساعت گذشته', number_format(Payment::where('status', 'success')->where('created_at', '>=', now()->subDay())->sum('amount')) . ' تومان')
                ->description('مجموع پرداخت‌های موفق')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),

            Stat::make('درآمد ۷ روز گذشته', number_format(Payment::where('status', 'success')->where('created_at', '>=', now()->subWeek())->sum('amount')) . ' تومان')
                ->description('مجموع پرداخت‌های موفق')
                ->descriptionIcon('heroicon-m-calendar')
                ->color('primary'),

            Stat::make('درآمد ۳۰ روز گذشته', number_format(Payment::where('status', 'success')->where('created_at', '>=', now()->subMonth())->sum('amount')) . ' تومان')
                ->description('مجموع پرداخت‌های موفق')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('warning'),
        ];
    }
}
