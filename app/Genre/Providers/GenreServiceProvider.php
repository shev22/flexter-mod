<?php

namespace App\Genre\Providers;

use App\Genre\Repositories\GenreRepository;
use App\Genre\Repositories\Interfaces\GenreRepositoryInterface;
use App\Genre\Services\GenreService;
use App\Genre\Services\Interfaces\GenreServiceInterface;
use Illuminate\Support\ServiceProvider;

class GenreServiceProvider extends ServiceProvider
{
    public array $bindings = [
        GenreRepositoryInterface::class => GenreRepository::class,
        GenreServiceInterface::class => GenreService::class,
    ];
}
