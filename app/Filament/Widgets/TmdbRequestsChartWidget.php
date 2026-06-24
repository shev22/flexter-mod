<?php

namespace App\Filament\Widgets;

use App\Filament\Support\DashboardCharts;
use Filament\Widgets\ChartWidget;

class TmdbRequestsChartWidget extends ChartWidget
{
    protected static ?string $heading = 'TMDB API requests';

    protected static ?string $description = 'Daily request volume (last 7 days)';

    protected static ?string $maxHeight = '280px';

    protected static string $color = 'warning';

    protected static ?int $sort = 2;

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
        $chart = DashboardCharts::all()['tmdb'];

        return [
            'datasets' => [
                [
                    'label' => 'Requests',
                    'data' => $chart['data'],
                    'fill' => true,
                    'tension' => 0.3,
                ],
            ],
            'labels' => $chart['labels'],
        ];
    }
}
