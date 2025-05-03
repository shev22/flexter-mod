<?php

namespace App\Services\HomeService;

use App\Http\Resources\HomeResource;
use App\Movie\Repositories\Interfaces\MovieRepositoryInterface;
use App\Movie\Resources\MovieResource;
use App\Repositories\Interfaces\HomeRepositoryInterface;
use App\Services\HomeService\Interfaces\HomeServiceInterface;
use App\Tv\Repositories\Interfaces\TvRepositoryInterface;
use App\Tv\Resource\TvResource;

class HomeService implements HomeServiceInterface
{
    public function __construct(protected HomeRepositoryInterface $homeRepository)
    {
    }

    /**
     * @inheritDoc
     */
    public function getHomePageData(): array
    {
        $movies = $this->homeRepository->loadHomePageMoviesData();
        $tv = $this->homeRepository->loadHomePageTvData();

        $trendingMovies = $movies['trending'];
        $trendingTv = $tv['trending'];

        $trending =  $trendingMovies->merge($trendingTv)->shuffle();

        return [
            'trending' => HomeResource::collection($trending),
            'nowPlayingMovies' => MovieResource::collection($movies['nowPlayingMovies']),
            'airingToday' => TvResource::collection($tv['airingToday']),
        ];
    }
}
