<?php

namespace App\Repositories\HomeRepository;

use App\Enums\Categories;
use App\Movie\Models\Movie;
use App\Repositories\Interfaces\HomeRepositoryInterface;
use App\Shared\Support\AppCache;
use App\Shared\Support\HomeCache;
use App\Tv\Models\Tv;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Facades\Cache;

class HomeRepository implements HomeRepositoryInterface
{
    /** @var array<int, string> */
    private const LIST_COLUMNS = [
        'id', 'title', 'poster_path', 'backdrop_path', 'logo', 'trailer',
        'release_date', 'vote_average', 'popularity', 'genre_ids', 'category',
        'is_trending', 'overview',
    ];

    public function trendingMovies(int $limit = 8): EloquentCollection
    {
        return AppCache::catalogue(
            "trending.movies.{$limit}",
            fn () => Movie::query()
                ->select(self::LIST_COLUMNS)
                ->where('is_trending', true)
                ->whereNotNull('backdrop_path')
                ->orderByRaw('CAST(popularity AS DECIMAL(12,3)) DESC')
                ->limit($limit)
                ->get(),
        );
    }

    public function trendingTv(int $limit = 8): EloquentCollection
    {
        return AppCache::catalogue(
            "trending.tv.{$limit}",
            fn () => Tv::query()
                ->select(self::LIST_COLUMNS)
                ->where('is_trending', true)
                ->whereNotNull('backdrop_path')
                ->orderByRaw('CAST(popularity AS DECIMAL(12,3)) DESC')
                ->limit($limit)
                ->get(),
        );
    }

    public function movieRail(int $category, int $limit = 20): EloquentCollection
    {
        return Cache::remember(
            HomeCache::railKey('movie', $category, $limit),
            config('flexter.cache.catalogue_ttl'),
            fn () => Movie::query()
                ->select(self::LIST_COLUMNS)
                ->where('category', $category)
                ->orderByRaw('CAST(popularity AS DECIMAL(12,3)) DESC')
                ->limit($limit)
                ->get(),
        );
    }

    public function tvRail(int $category, int $limit = 20): EloquentCollection
    {
        return Cache::remember(
            HomeCache::railKey('tv', $category, $limit),
            config('flexter.cache.catalogue_ttl'),
            fn () => Tv::query()
                ->select(self::LIST_COLUMNS)
                ->where('category', $category)
                ->orderByRaw('CAST(popularity AS DECIMAL(12,3)) DESC')
                ->limit($limit)
                ->get(),
        );
    }
}
