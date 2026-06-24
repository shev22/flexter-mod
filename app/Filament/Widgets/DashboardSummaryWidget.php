<?php

namespace App\Filament\Widgets;

use App\Actor\Models\Actor;
use App\Enums\Role;
use App\Feedback\Models\Feedback;
use App\Filament\Pages\SiteSettingsPage;
use App\Filament\Pages\TmdbApiActivityPage;
use App\Filament\Resources\ActorResource;
use App\Filament\Resources\GenreResource;
use App\Filament\Resources\MovieResource;
use App\Filament\Resources\TvResource;
use App\Filament\Resources\UserResource;
use App\Genre\Models\Genre;
use App\Models\TmdbApiActivity;
use App\Models\User;
use App\Movie\Models\Movie;
use App\Site\Services\Interfaces\SiteSettingsServiceInterface;
use App\Tv\Models\Tv;
use App\WatchList\Models\WatchList;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Cache;

class DashboardSummaryWidget extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected int|string|array $columnSpan = 'full';

    public static function canView(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    /**
     * @return array<int, Stat>
     */
    protected function getStats(): array
    {
        $snapshot = Cache::remember('filament.dashboard.stats', now()->addMinutes(3), function () {
            $todayActivities = TmdbApiActivity::query()->whereDate('created_at', today());

            return [
                'movies' => Movie::query()->count(),
                'trending_movies' => Movie::query()->where('is_trending', true)->count(),
                'tv' => Tv::query()->count(),
                'trending_tv' => Tv::query()->where('is_trending', true)->count(),
                'actors' => Actor::query()->count(),
                'genres' => Genre::query()->count(),
                'users' => User::query()->count(),
                'admins' => User::role(Role::Admin->value)->count(),
                'members' => User::role(Role::User->value)->count(),
                'watchlist' => WatchList::query()->count(),
                'today_requests' => (int) (clone $todayActivities)->sum('request_count'),
                'today_fetched' => (int) (clone $todayActivities)->sum('items_fetched'),
                'unread_feedback' => Feedback::query()->whereNull('read_at')->count(),
            ];
        });

        $limit = app(SiteSettingsServiceInterface::class)->get()->tmdbDailyRequestLimit;

        return [
            Stat::make('Movies', number_format($snapshot['movies']))
                ->description("{$snapshot['trending_movies']} trending")
                ->descriptionIcon('heroicon-o-film')
                ->color('info')
                ->url(MovieResource::getUrl('index')),

            Stat::make('TV Shows', number_format($snapshot['tv']))
                ->description("{$snapshot['trending_tv']} trending")
                ->descriptionIcon('heroicon-o-tv')
                ->color('info')
                ->url(TvResource::getUrl('index')),

            Stat::make('Actors', number_format($snapshot['actors']))
                ->description('In catalogue')
                ->descriptionIcon('heroicon-o-user-group')
                ->color('success')
                ->url(ActorResource::getUrl('index')),

            Stat::make('Genres', number_format($snapshot['genres']))
                ->description('Linked categories')
                ->descriptionIcon('heroicon-o-tag')
                ->color('gray')
                ->url(GenreResource::getUrl('index')),

            Stat::make('Users', number_format($snapshot['users']))
                ->description("{$snapshot['admins']} admins · {$snapshot['members']} members")
                ->descriptionIcon('heroicon-o-users')
                ->color('warning')
                ->url(UserResource::getUrl('index')),

            Stat::make('Watchlist Items', number_format($snapshot['watchlist']))
                ->description('Saved by members')
                ->descriptionIcon('heroicon-o-bookmark')
                ->color('primary'),

            Stat::make('TMDB Today', number_format($snapshot['today_requests']))
                ->description("{$snapshot['today_fetched']} items · limit {$limit}")
                ->descriptionIcon('heroicon-o-signal')
                ->color($snapshot['today_requests'] >= $limit ? 'danger' : 'success')
                ->url(TmdbApiActivityPage::getUrl()),

            Stat::make('Unread Feedback', number_format($snapshot['unread_feedback']))
                ->description('Needs review')
                ->descriptionIcon('heroicon-o-chat-bubble-left-right')
                ->color($snapshot['unread_feedback'] > 0 ? 'warning' : 'gray')
                ->url(\App\Filament\Resources\FeedbackResource::getUrl('index')),

            Stat::make('Site Settings', 'Configure')
                ->description('Sync pages, features, hero pins')
                ->descriptionIcon('heroicon-o-cog-6-tooth')
                ->color('gray')
                ->url(SiteSettingsPage::getUrl()),
        ];
    }
}
