<?php

namespace App\Repositories\Interfaces;

use Illuminate\Database\Eloquent\Collection as EloquentCollection;

interface HomeRepositoryInterface
{
    public function trendingMovies(int $limit = 8): EloquentCollection;

    public function trendingTv(int $limit = 8): EloquentCollection;

    public function movieRail(int $category, int $limit = 20): EloquentCollection;

    public function tvRail(int $category, int $limit = 20): EloquentCollection;
}
