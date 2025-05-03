<?php

namespace App\Actor\Services;

use App\Actor\Repositories\Interfaces\ActorRepositoryInterface;
use App\Actor\Services\Interfaces\ActorServiceInterface;
use App\Enums\Categories;
use App\Enums\MediaType;
use App\Services\MediaService\Interfaces\MediaApiClientInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
class ActorService implements ActorServiceInterface
{
    public function __construct(protected  MediaApiClientInterface $mediaApiClient, protected ActorRepositoryInterface $actorRepository)
    {
    }

    public function createActors(): void
    {
        $actors = $this->mediaApiClient->fetchMedia(MediaType::PERSON->getLabel(), Categories::POPULAR->getLabel(), 20);
        $this->actorRepository->createRecord( null, $actors);
    }

    public function getActors(?string $search, ?int $page, ?int$perPage): LengthAwarePaginator
    {
        return $this->actorRepository->actors($search,  $page, $perPage);
    }

    /**
     * @inheritDoc
     */
    public function getActorWithAttachments(string $actorId): array
    {
        return $this->mediaApiClient->fetchMediaWithDetails($actorId, MediaType::PERSON->getLabel(), false, true);
    }
}
