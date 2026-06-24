<?php

namespace  App\Movie\Services;

use App\Enums\Categories;
use App\Enums\MediaType;
use App\Movie\Repositories\Interfaces\MovieRepositoryInterface;
use App\Movie\Services\Interfaces\MovieServiceInterface;
use App\Services\MediaService\Interfaces\MediaApiClientInterface;
use App\Shared\Data\MediaFilterData;
use App\Site\Services\Interfaces\SiteSettingsServiceInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;


class MovieService implements MovieServiceInterface
{
    public function __construct(
        protected MediaApiClientInterface $mediaApiClient,
        protected MovieRepositoryInterface $movieRepository,
        protected SiteSettingsServiceInterface $siteSettings,
    ) {
    }

    public function popular():void
    {
        $pages = $this->siteSettings->get()->syncPages(MediaType::MOVIE->getLabel(), Categories::POPULAR->getLabel());
        $popular = $this->mediaApiClient->fetchMedia(MediaType::MOVIE->getLabel(), Categories::POPULAR->getLabel(), $pages);
        $this->movieRepository->createRecord(Categories::POPULAR->value, $popular);
    }

    public function nowPlaying():void
    {
        $pages = $this->siteSettings->get()->syncPages(MediaType::MOVIE->getLabel(), Categories::NOW_PLAYING->getLabel());
        $nowPlaying = $this->mediaApiClient->fetchMedia(MediaType::MOVIE->getLabel(), Categories::NOW_PLAYING->getLabel(), $pages);
        $this->movieRepository->createRecord(Categories::NOW_PLAYING->value, $nowPlaying);
    }

    public function upcoming():void
    {
        $pages = $this->siteSettings->get()->syncPages(MediaType::MOVIE->getLabel(), Categories::UPCOMING->getLabel());
        $upcoming = $this->mediaApiClient->fetchMedia(MediaType::MOVIE->getLabel(), Categories::UPCOMING->getLabel(), $pages);
        $this->movieRepository->createRecord(Categories::UPCOMING->value, $upcoming);
    }

    public function topRated():void
    {
        $pages = $this->siteSettings->get()->syncPages(MediaType::MOVIE->getLabel(), Categories::TOP_RATED->getLabel());
        $topRated = $this->mediaApiClient->fetchMedia(MediaType::MOVIE->getLabel(), Categories::TOP_RATED->getLabel(), $pages);
        $this->movieRepository->createRecord(Categories::TOP_RATED->value, $topRated);
    }

    public function trending():void
    {
        $currentlyTrending = $this->mediaApiClient->fetchTrending(MediaType::MOVIE->getLabel(), 'day');


        $existingTrending = $this->movieRepository->getTrending()->toArray();

        $current = collect($currentlyTrending)->keyBy('id');
        $existing = collect($existingTrending)->keyBy('id');

        $trending = $current->map(function ($item) {
            $item['is_trending'] = true;
            return $item;
        });

        $notTrending = $existing->diffKeys($current)->map(function ($item) {
            $item['is_trending'] = false;
            return $item;
        });
        $mergedMovies = $trending->merge($notTrending)->values()->all();


        $this->movieRepository->createRecord(Categories::TRENDING->value, collect($mergedMovies));
    }

    /**
     * @return LengthAwarePaginator
     */
    public function getMovies(MediaFilterData $filter): LengthAwarePaginator
    {
        return $this->movieRepository->movies($filter);
    }

    /**
     * @inheritDoc
     */
    public function getMovieWithRelatedMovies(string $movieId): array
    {
        return \Illuminate\Support\Facades\Cache::remember(
            "media.detail:movie:{$movieId}",
            now()->addDay(),
            function () use ($movieId) {
                $movie = $this->mediaApiClient->fetchMediaWithDetails($movieId, MediaType::MOVIE->getLabel());

                if ($movie) {
                    $resource = $this->mediaApiClient->fetchRelatedMedia($movieId, MediaType::MOVIE->getLabel());
                    $movie['related'] = $resource['results'] ?? [];
                }

                return $movie;
            },
        );
    }
}


