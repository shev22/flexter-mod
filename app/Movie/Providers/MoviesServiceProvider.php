<?php

namespace App\Movie\Providers;

use App\Movie\Repositories\Interfaces\MovieRepositoryInterface;
use App\Movie\Repositories\MovieRepository;
use App\Movie\Services\Interfaces\MovieServiceInterface;
use App\Movie\Services\MovieService;
use Illuminate\Support\ServiceProvider;

class MoviesServiceProvider extends ServiceProvider
{
    public array $bindings = [
      MovieServiceInterface::class => MovieService::class,
      MovieRepositoryInterface::class => MovieRepository::class,
    ];

}
