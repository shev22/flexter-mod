<?php

namespace App\Tv\Providers;


use App\Tv\Repositories\Interfaces\TvRepositoryInterface;
use App\Tv\Repositories\TvRepository;
use App\Tv\Services\Interfaces\TvServiceInterface;
use App\Tv\Services\TvService;
use Illuminate\Support\ServiceProvider;

class TvServiceProvider extends ServiceProvider
{
    public array $bindings = [
        TvServiceInterface::class => TvService::class,
        TvRepositoryInterface::class => TvRepository::class,
    ];
}
