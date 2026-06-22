<?php

namespace App\Actor\Services;

use App\Actor\Repositories\Interfaces\ActorRepositoryInterface;
use App\Actor\Services\Interfaces\ActorServiceInterface;
use App\Enums\Categories;
use App\Enums\MediaType;
use App\Services\MediaService\Interfaces\MediaApiClientInterface;
use App\Site\Services\Interfaces\SiteSettingsServiceInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
class ActorService implements ActorServiceInterface
{
    public function __construct(
        protected MediaApiClientInterface $mediaApiClient,
        protected ActorRepositoryInterface $actorRepository,
        protected SiteSettingsServiceInterface $siteSettings,
    ) {
    }

    public function createActors(): void
    {
        $pages = $this->siteSettings->get()->syncPages(MediaType::PERSON->getLabel(), Categories::POPULAR->getLabel());
        $actors = $this->mediaApiClient->fetchMedia(MediaType::PERSON->getLabel(), Categories::POPULAR->getLabel(), $pages);
        $this->actorRepository->createRecord( null, $actors);
    }

    public function getActors(?string $search, string $sort, int $perPage): LengthAwarePaginator
    {
        return $this->actorRepository->actors($search, $sort, $perPage);
    }

    /**
     * @inheritDoc
     */
    public function getActorWithAttachments(string $actorId): array
    {
        return \Illuminate\Support\Facades\Cache::remember(
            "media.detail:person:{$actorId}",
            now()->addDay(),
            fn () => $this->mediaApiClient->fetchMediaWithDetails($actorId, MediaType::PERSON->getLabel(), false, true),
        );
    }
}
