<?php

namespace App\Tv\Services;

use App\Enums\Categories;
use App\Enums\MediaType;
use App\Services\MediaService\Interfaces\MediaApiClientInterface;
use App\Shared\Data\MediaFilterData;
use App\Site\Services\Interfaces\SiteSettingsServiceInterface;
use App\Tv\Repositories\Interfaces\TvRepositoryInterface;
use App\Tv\Services\Interfaces\TvServiceInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class TvService implements TvServiceInterface
{
    public function __construct(
        protected MediaApiClientInterface $mediaApiClient,
        protected TvRepositoryInterface $tvRepository,
        protected SiteSettingsServiceInterface $siteSettings,
    ) {
    }

    /**
     * @inheritDoc
     */
    public function popular(): void
    {
        $pages = $this->siteSettings->get()->syncPages(MediaType::TV->getLabel(), Categories::POPULAR->getLabel());
        $popular = $this->mediaApiClient->fetchMedia(MediaType::TV->getLabel(), Categories::POPULAR->getLabel(), $pages);

        $this->tvRepository->createRecord(Categories::POPULAR->value, $popular);
    }

    /**
     * @inheritDoc
     */
    public function onTheAir(): void
    {
        $pages = $this->siteSettings->get()->syncPages(MediaType::TV->getLabel(), Categories::ON_THE_AIR->getLabel());
        $onTheAir = $this->mediaApiClient->fetchMedia(MediaType::TV->getLabel(), Categories::ON_THE_AIR->getLabel(), $pages);
        $this->tvRepository->createRecord(Categories::ON_THE_AIR->value, $onTheAir);
    }

    /**
     * @inheritDoc
     */
    public function topRated(): void
    {
        $pages = $this->siteSettings->get()->syncPages(MediaType::TV->getLabel(), Categories::TOP_RATED->getLabel());
        $topRated = $this->mediaApiClient->fetchMedia(MediaType::TV->getLabel(), Categories::TOP_RATED->getLabel(), $pages);
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
        $pages = $this->siteSettings->get()->syncPages(MediaType::TV->getLabel(), Categories::AIRING_TODAY->getLabel());
        $airingToday = $this->mediaApiClient->fetchMedia(MediaType::TV->getLabel(), Categories::AIRING_TODAY->getLabel(), $pages);
        $this->tvRepository->createRecord(Categories::AIRING_TODAY->value, $airingToday);
    }

    /**
     * @inheritDoc
     */
    public function getTvWithRelatedTv(string $tvId): array
    {
        return \Illuminate\Support\Facades\Cache::remember(
            "media.detail:tv:{$tvId}",
            now()->addDay(),
            function () use ($tvId) {
                $tv = $this->mediaApiClient->fetchMediaWithDetails($tvId, MediaType::TV->getLabel());

                if ($tv) {
                    $resource = $this->mediaApiClient->fetchRelatedMedia($tvId, MediaType::TV->getLabel());
                    $tv['related'] = $resource['results'] ?? [];
                }

                return $tv;
            },
        );
    }

    /**
     * @inheritDoc
     */
    public function getTv(MediaFilterData $filter): LengthAwarePaginator
    {
        return $this->tvRepository->tv($filter);
    }
}
