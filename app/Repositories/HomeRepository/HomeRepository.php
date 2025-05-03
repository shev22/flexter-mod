<?php

namespace App\Repositories\HomeRepository;

use App\Enums\Categories;
use App\Movie\Models\Movie;
use App\Repositories\Interfaces\HomeRepositoryInterface;
use App\Tv\Models\Tv;

class HomeRepository implements HomeRepositoryInterface
{
    public function loadHomePageMoviesData(): array
    {
        $nowPlayingMovies = Movie::where('category', Categories::NOW_PLAYING)
            ->take(40)
            ->orderBy('created_at', 'desc')
            ->get();
        $trending = Movie::where('category', Categories::TRENDING)->where('is_trending', true)->get();

        return [
            'nowPlayingMovies' => $nowPlayingMovies,
            'trending' => $trending,
        ];


    }

    /**
     * @inheritDoc
     */
    public function loadHomePageTvData(): array
    {
        $airingToday = Tv::where('category', Categories::AIRING_TODAY)
            ->take(40)
            ->orderBy('created_at', 'desc')
            ->get();

        $trending = Tv::where('category', Categories::TRENDING)->where('is_trending', true)->get();

        return [
            'airingToday' => $airingToday,
            'trending' => $trending,
        ];
    }
}


