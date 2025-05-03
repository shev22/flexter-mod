<?php

namespace  App\Movie\Services;

use App\Enums\Categories;
use App\Enums\MediaType;
use App\Movie\Repositories\Interfaces\MovieRepositoryInterface;
use App\Movie\Resources\MovieResource;
use App\Movie\Services\Interfaces\MovieServiceInterface;
use App\Services\MediaService\Interfaces\MediaApiClientInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;


class MovieService implements MovieServiceInterface
{
    public function __construct(protected  MediaApiClientInterface $mediaApiClient, protected movieRepositoryInterface $movieRepository)
    {
    }

    public function popular():void
    {
        $popular = $this->mediaApiClient->fetchMedia(MediaType::MOVIE->getLabel(), Categories::POPULAR->getLabel(), 20);
        $this->movieRepository->createRecord(Categories::POPULAR->value, $popular);
    }

    public function nowPlaying():void
    {
        $nowPlaying = $this->mediaApiClient->fetchMedia(MediaType::MOVIE->getLabel(), Categories::NOW_PLAYING->getLabel(), 20);
        $this->movieRepository->createRecord(Categories::NOW_PLAYING->value, $nowPlaying);
    }

    public function upcoming():void
    {
        $upcoming = $this->mediaApiClient->fetchMedia(MediaType::MOVIE->getLabel(), Categories::UPCOMING->getLabel(), 20);
        $this->movieRepository->createRecord(Categories::UPCOMING->value, $upcoming);
    }

    public function topRated():void
    {
        $topRated = $this->mediaApiClient->fetchMedia(MediaType::MOVIE->getLabel(), Categories::TOP_RATED->getLabel(), 20);
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
    public function getMovies(): LengthAwarePaginator
    {
        return $this->movieRepository->movies();
    }

    /**
     * @inheritDoc
     */
    public function getMovieWithRelatedMovies(string $movieId): array
    {
        $movie = $this->mediaApiClient->fetchMediaWithDetails($movieId, MediaType::MOVIE->getLabel());

        if ($movie) {
            $resource = $this->mediaApiClient->fetchRelatedMedia($movieId, MediaType::MOVIE->getLabel());

            $results = $resource['results'] ?? [];
            $related = MovieResource::collection($results)->resolve();

            $movie['related'] = $related;
        }

        return $movie;
    }
}


