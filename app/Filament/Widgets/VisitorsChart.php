<?php

namespace App\Filament\Widgets;

use App\Models\VisitLog;
use Filament\Widgets\ChartWidget;
use Morilog\Jalali\Jalalian;

class VisitorsChart extends ChartWidget
{
    protected static ?string $heading = 'آمار بازدید (کاربران واقعی)';
    protected static ?int $sort = 3;
    protected int | string | array $columnSpan = 'full';
    public ?string $filter = '24h';

    protected function getFilters(): ?array
    {
        return [
            '24h' => '۲۴ ساعت گذشته',
            '7d' => '۷ روز گذشته',
            '30d' => '۳۰ روز گذشته',
        ];
    }

    protected function getData(): array
    {
        $filter = $this->filter;
        $dates = [];
        $counts = [];

        if ($filter === '24h') {
            // Hourly logic
            for ($i = 23; $i >= 0; $i--) {
                $date = now()->subHours($i);
                $dates[] = $date->format('H:00');
                
                $counts[] = VisitLog::query()
                    ->where('created_at', '>=', $date->copy()->startOfHour())
                    ->where('created_at', '<=', $date->copy()->endOfHour())
                    ->distinct('ip')
                    ->count('ip');
            }
        } else {
            // Daily logic
            $days = $filter === '7d' ? 6 : 29;
            for ($i = $days; $i >= 0; $i--) {
                $date = now()->subDays($i);
                try {
                    $dates[] = Jalalian::fromDateTime($date)->format('Y/m/d');
                } catch (\Exception $e) {
                    $dates[] = $date->format('Y-m-d');
                }
                
                $counts[] = VisitLog::query()
                    ->whereDate('created_at', $date)
                    ->distinct('ip')
                    ->count('ip');
            }
        }

        return [
            'datasets' => [
                [
                    'label' => 'تعداد بازدیدکننده یکتا',
                    'data' => $counts,
                    'backgroundColor' => 'rgba(245, 158, 11, 0.2)', // Amber-500 style to match theme
                    'borderColor' => '#F59E0B',
                    'fill' => true,
                    'tension' => 0.3,
                ],
            ],
            'labels' => $dates,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
