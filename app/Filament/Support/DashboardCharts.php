<?php

namespace App\Filament\Support;

use App\Actor\Models\Actor;
use App\Models\TmdbApiActivity;
use App\Models\User;
use App\Movie\Models\Movie;
use App\Tv\Models\Tv;
use App\WatchHistory\Models\WatchHistory;
use Illuminate\Support\Facades\Cache;

final class DashboardCharts
{
    /**
     * @return array<string, mixed>
     */
    public static function all(): array
    {
        return Cache::remember('filament.dashboard.charts', now()->addMinutes(3), function (): array {
            return [
                'tmdb' => self::tmdbRequestsLast7Days(),
                'catalogue' => self::catalogueBreakdown(),
                'signups' => self::userSignupsLast6Months(),
                'watch' => self::watchActivityLast7Days(),
            ];
        });
    }

    /**
     * @return array{labels: array<int, string>, data: array<int, int>}
     */
    public static function tmdbRequestsLast7Days(): array
    {
        $days = collect(range(6, 0))->map(fn (int $i) => today()->subDays($i));

        return [
            'labels' => $days->map(fn ($d) => $d->format('M j'))->all(),
            'data' => $days->map(fn ($d) => (int) TmdbApiActivity::query()
                ->whereDate('created_at', $d)
                ->sum('request_count'))->all(),
        ];
    }

    /**
     * @return array{labels: array<int, string>, data: array<int, int>}
     */
    public static function catalogueBreakdown(): array
    {
        return [
            'labels' => ['Movies', 'TV shows', 'Actors'],
            'data' => [
                Movie::query()->count(),
                Tv::query()->count(),
                Actor::query()->count(),
            ],
        ];
    }

    /**
     * @return array{labels: array<int, string>, data: array<int, int>}
     */
    public static function userSignupsLast6Months(): array
    {
        $months = collect(range(5, 0))->map(fn (int $i) => now()->subMonths($i)->startOfMonth());

        return [
            'labels' => $months->map(fn ($m) => $m->format('M Y'))->all(),
            'data' => $months->map(fn ($m) => User::query()
                ->whereYear('created_at', $m->year)
                ->whereMonth('created_at', $m->month)
                ->count())->all(),
        ];
    }

    /**
     * @return array{labels: array<int, string>, data: array<int, int>}
     */
    public static function watchActivityLast7Days(): array
    {
        $days = collect(range(6, 0))->map(fn (int $i) => today()->subDays($i));

        return [
            'labels' => $days->map(fn ($d) => $d->format('M j'))->all(),
            'data' => $days->map(fn ($d) => WatchHistory::query()
                ->whereDate('last_watched_at', $d)
                ->count())->all(),
        ];
    }
}
