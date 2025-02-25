<?php

namespace App\Actor\Providers;

use App\Actor\Repositories\ActorsRepository;
use App\Actor\Repositories\Interfaces\ActorsRepositoryInterface;
use Illuminate\Support\ServiceProvider;
class ActorsRepositoryServiceProvider extends ServiceProvider
{
    public array $bindings = [
        ActorsRepositoryInterface::class => ActorsRepository::class,
    ];

}
