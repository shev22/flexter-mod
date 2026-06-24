<?php

namespace App\Filament\Widgets;

use App\Filament\Support\DashboardCharts;
use Filament\Widgets\ChartWidget;

class WatchActivityChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Watch activity';

    protected static ?string $description = 'History updates per day (last 7 days)';

    protected static ?string $maxHeight = '280px';

    protected static string $color = 'primary';

    protected static ?int $sort = 5;

    protected int|string|array $columnSpan = 1;

    public static function canView(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getData(): array
    {
        $chart = DashboardCharts::all()['watch'];

        return [
            'datasets' => [
                [
                    'label' => 'Plays tracked',
                    'data' => $chart['data'],
                    'fill' => true,
                    'tension' => 0.3,
                ],
            ],
            'labels' => $chart['labels'],
        ];
    }
}
