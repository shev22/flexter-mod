<?php

namespace App\Actor\Providers;

use App\Actor\Repositories\ActorRepository;
use App\Actor\Repositories\Interfaces\ActorRepositoryInterface;
use App\Actor\Services\ActorService;
use App\Actor\Services\Interfaces\ActorServiceInterface;
use Illuminate\Support\ServiceProvider;
class ActorsServiceProvider extends ServiceProvider
{
    public array $bindings = [
        ActorRepositoryInterface::class => ActorRepository::class,
        ActorServiceInterface::class => ActorService::class,
    ];

}
