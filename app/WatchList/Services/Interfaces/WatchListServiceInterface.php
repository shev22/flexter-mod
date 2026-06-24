<?php

namespace App\WatchList\Services\Interfaces;

use App\Models\User;
use Illuminate\Support\Collection;

interface WatchListServiceInterface
{
    /**
     * @param User $user
     * @param int $mediaId
     * @param string $mediaType
     * @return void
     */
    public function addToWatchList(User $user, int $mediaId, string $mediaType): void;

    /**
     * @param User $user
     * @param int $mediaId
     * @param string $mediaType
     * @return void
     */
    public function removeFromWatchList(User $user, int $mediaId, string $mediaType): void;

    /**
     * @return Collection
     */
    public function myWatchLists(): Collection;

}
