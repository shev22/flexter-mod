<?php

namespace App\Tv\Services;

use App\Enums\Categories;
use App\Enums\MediaType;
use App\Movie\Resources\MovieResource;
use App\Tv\Resource\TvResource;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use App\Services\MediaService\Interfaces\MediaApiClientInterface;
use App\Tv\Repositories\Interfaces\TvRepositoryInterface;
use App\Tv\Services\Interfaces\TvServiceInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class TvService implements TvServiceInterface
{
    public function __construct(protected  MediaApiClientInterface $mediaApiClient, protected TvRepositoryInterface $tvRepository)
    {
    }

    /**
     * @inheritDoc
     */
    public function popular(): void
    {
        $popular = $this->mediaApiClient->fetchMedia(MediaType::TV->getLabel(), Categories::POPULAR->getLabel(), 20);

        $this->tvRepository->createRecord(Categories::POPULAR->value, $popular);
    }

    /**
     * @inheritDoc
     */
    public function onTheAir(): void
    {
        $onTheAir = $this->mediaApiClient->fetchMedia(MediaType::TV->getLabel(), Categories::ON_THE_AIR->getLabel(), 20);
        $this->tvRepository->createRecord(Categories::ON_THE_AIR->value, $onTheAir);
    }

    /**
     * @inheritDoc
     */
    public function topRated(): void
    {
        $topRated = $this->mediaApiClient->fetchMedia(MediaType::TV->getLabel(), Categories::TOP_RATED->getLabel(), 20);
        $this->tvRepository->createRecord(Categories::TOP_RATED->value, $topRated);
    }

    /**
     * @inheritDoc
     */
    public function trending():void
    {
        $currentlyTrending = $this->mediaApiClient->fetchTrending(MediaType::TV->getLabel(), 'day');


        $existingTrending = $this->tvRepository->getTrending()->toArray();

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
        $mergedTv = $trending->merge($notTrending)->values()->all();

        $this->tvRepository->createRecord(Categories::TRENDING->value, collect($mergedTv));
    }

    /**
     * @inheritDoc
     */
    public function AiringToday(): void
    {
        $airingToday = $this->mediaApiClient->fetchMedia(MediaType::TV->getLabel(), Categories::AIRING_TODAY->getLabel(), 20);
        $this->tvRepository->createRecord(Categories::AIRING_TODAY->value, $airingToday);
    }

    /**
     * @inheritDoc
     */
    public function getTvWithRelatedTv(string $tvId): array
    {
        $tv = $this->mediaApiClient->fetchMediaWithDetails($tvId, MediaType::TV->getLabel());

        if ($tv) {
            $resource = $this->mediaApiClient->fetchRelatedMedia($tvId, MediaType::TV->getLabel());

            $results = $resource['results'] ?? [];
            $related = TvResource::collection($results)->resolve();

            $tv['related'] = $related;
        }

        return $tv;

    }

    /**
     * @inheritDoc
     */
    public function getTv(): LengthAwarePaginator
    {
        return $this->tvRepository->tv();
    }
}
