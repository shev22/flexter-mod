<?php

namespace App\Actor\Services\Interfaces;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface ActorServiceInterface
{
    public function createActors(): void;

    public function getActors(?string $search, string $sort, int $perPage, int $page = 1): LengthAwarePaginator;

    public function getActorWithAttachments(string $actorId): array;
}
