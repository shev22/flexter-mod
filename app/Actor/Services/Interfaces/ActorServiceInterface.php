<?php

namespace App\Actor\Services\Interfaces;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface ActorServiceInterface
{
    /**
     * @return void
     */
    public function createActors(): void;

    /**
     * @param string|null $search
     * @param int|null $page
     * @param int|null $perPage
     * @return LengthAwarePaginator
     */
    public function getActors(?string $search, ?int $page, ?int$perPage): LengthAwarePaginator;

    /**
     * @param string $actorId
     * @return array
     */
    public function getActorWithAttachments(string $actorId):array ;
}
