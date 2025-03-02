<?php

namespace App\Providers;

use App\Services\MediaService\Interfaces\MediaApiClientInterface;
use App\Services\MediaService\MediaApiClient;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{

    public array $bindings = [
        MediaApiClientInterface::class => MediaApiClient::class,
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
