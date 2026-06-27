<?php

namespace App\Providers;

use App\Repositories\HomeRepository\HomeRepository;
use App\Repositories\Interfaces\HomeRepositoryInterface;
use App\Services\HomeService\HomeService;
use App\Services\HomeService\Interfaces\HomeServiceInterface;
use App\Services\MediaService\ApiClient;
use App\Services\MediaService\Interfaces\MediaApiClientInterface;
use App\Services\MediaService\Interfaces\MediaAttachmentServiceInterface;
use App\Services\MediaService\MediaAttachmentService;
use App\Settings\Services\Interfaces\SettingsServiceInterface;
use App\Settings\Services\SettingsService;
use App\Site\Services\Interfaces\SiteSettingsServiceInterface;
use App\Site\Services\SiteSettingsService;
use App\Billing\Services\BillingService;
use App\Billing\Services\Interfaces\BillingServiceInterface;
use App\Comment\Services\CommentService;
use App\Comment\Services\Interfaces\CommentServiceInterface;
use App\TonightQueue\Services\Interfaces\TonightQueueServiceInterface;
use App\TonightQueue\Services\TonightQueueService;
use App\WatchHistory\Services\Interfaces\WatchHistoryServiceInterface;
use App\WatchHistory\Services\WatchHistoryService;
use App\WatchList\Services\Interfaces\WatchListServiceInterface;
use App\WatchList\Services\WatchListServices;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{

    public array $bindings = [
        MediaApiClientInterface::class => ApiClient::class,
        HomeRepositoryInterface::class => HomeRepository::class,
        HomeServiceInterface::class => HomeService::class,
        WatchListServiceInterface::class => WatchListServices::class,
        MediaAttachmentServiceInterface::class => MediaAttachmentService::class,
        SettingsServiceInterface::class => SettingsService::class,
        SiteSettingsServiceInterface::class => SiteSettingsService::class,
        WatchHistoryServiceInterface::class => WatchHistoryService::class,
        CommentServiceInterface::class => CommentService::class,
        BillingServiceInterface::class => BillingService::class,
        TonightQueueServiceInterface::class => TonightQueueService::class,
    ];

    /**
     * Register any application services.
     */
    public function register(): void
    {

    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Event::listen(Registered::class, SendEmailVerificationNotification::class);

        RateLimiter::for('search', fn (Request $request) => Limit::perMinute(60)->by($request->ip()));
        RateLimiter::for('feedback', fn (Request $request) => Limit::perMinute(3)->by($request->ip()));
        RateLimiter::for('history', fn (Request $request) => Limit::perMinute(120)->by($request->user()?->id ?: $request->ip()));
        RateLimiter::for('comments', fn (Request $request) => Limit::perMinute(15)->by($request->user()?->id ?: $request->ip()));
    }
}
