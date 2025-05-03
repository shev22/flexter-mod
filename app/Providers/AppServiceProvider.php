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
use App\WatchList\Services\Interfaces\WatchListServiceInterface;
use App\WatchList\Services\WatchListServices;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{

    public array $bindings = [
        MediaApiClientInterface::class => ApiClient::class,
        HomeRepositoryInterface::class => HomeRepository::class,
        HomeServiceInterface::class => HomeService::class,
        WatchListServiceInterface::class => WatchListServices::class,
        MediaAttachmentServiceInterface::class => MediaAttachmentService::class,
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
        //
    }
}
