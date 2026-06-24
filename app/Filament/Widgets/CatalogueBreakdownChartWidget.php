<?php

namespace App\Filament\Widgets;

use App\Filament\Support\DashboardCharts;
use Filament\Widgets\ChartWidget;

class CatalogueBreakdownChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Catalogue breakdown';

    protected static ?string $description = 'Titles stored locally';

    protected static ?string $maxHeight = '280px';

    protected static string $color = 'info';

    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 1;

    public static function canView(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getData(): array
    {
        $chart = DashboardCharts::all()['catalogue'];

        return [
            'datasets' => [
                [
                    'label' => 'Catalogue',
                    'data' => $chart['data'],
                ],
            ],
            'labels' => $chart['labels'],
        ];
    }
}
