<?php

namespace App\Actor\Providers;

use App\Actor\Repositories\ActorsRepository;
use App\Actor\Repositories\Interfaces\ActorsRepositoryInterface;
use Illuminate\Support\ServiceProvider;
class ActorsServiceProvider extends ServiceProvider
{
    public array $bindings = [
        ActorsRepositoryInterface::class => ActorsRepository::class,
    ];

}
