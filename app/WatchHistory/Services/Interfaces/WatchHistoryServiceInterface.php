<?php

namespace App\WatchHistory\Services\Interfaces;

use App\Models\User;
use App\WatchHistory\Models\WatchHistory;
use Illuminate\Support\Collection;

interface WatchHistoryServiceInterface
{
    public function touch(User $user, string $type, int $mediaId, int $progress = 15, ?int $season = null, ?int $episode = null): int;

    public function markWatched(User $user, string $type, int $mediaId, ?int $season = null, ?int $episode = null): void;

    public function bumpProgress(User $user, string $type, int $mediaId, int $progress, ?int $season = null, ?int $episode = null): int;

    /** @return Collection<int, array<string, mixed>> */
    public function recent(User $user, int $limit = 40): Collection;

    /** @return array{total: int, completed: int, in_progress: int, hours: float} */
    public function stats(User $user): array;

    /** @return array<int, array<string, mixed>> */
    public function continueWatching(User $user, int $limit = 12): array;

    public function remove(User $user, int $historyId): void;

    public function clear(User $user): void;

    public function progressFor(User $user, string $type, int $mediaId): ?WatchHistory;

    public function latestProgressFor(User $user, string $type, int $mediaId): ?WatchHistory;
}
