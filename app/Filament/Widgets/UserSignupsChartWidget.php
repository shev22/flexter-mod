<?php

namespace App\Filament\Widgets;

use App\Filament\Support\DashboardCharts;
use Filament\Widgets\ChartWidget;

class UserSignupsChartWidget extends ChartWidget
{
    protected static ?string $heading = 'New members';

    protected static ?string $description = 'User registrations (last 6 months)';

    protected static ?string $maxHeight = '280px';

    protected static string $color = 'success';

    protected static ?int $sort = 4;

    protected int|string|array $columnSpan = 1;

    public static function canView(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getData(): array
    {
        $chart = DashboardCharts::all()['signups'];

        return [
            'datasets' => [
                [
                    'label' => 'Sign-ups',
                    'data' => $chart['data'],
                ],
            ],
            'labels' => $chart['labels'],
        ];
    }
}
