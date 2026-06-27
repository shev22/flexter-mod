<?php

namespace App\Auth\Services;

use App\Models\User;
use App\TonightQueue\Services\Interfaces\TonightQueueServiceInterface;
use App\WatchHistory\Services\Interfaces\WatchHistoryServiceInterface;

class GuestDataMergeService
{
    public function __construct(
        private readonly WatchHistoryServiceInterface $history,
        private readonly TonightQueueServiceInterface $tonightQueue,
    ) {}

    /**
     * @param  array<string, mixed>  $payload
     */
    public function merge(User $user, array $payload): void
    {
        $progress = $payload['progress'] ?? [];

        if (is_array($progress)) {
            $this->history->mergeGuestEntries($user, array_values($progress));
        }

        $tonight = $payload['tonight'] ?? null;

        if (is_array($tonight)) {
            $items = $tonight['items'] ?? $tonight;
            $startedAt = isset($tonight['startedAt']) ? (int) $tonight['startedAt'] : null;

            if (is_array($items)) {
                $this->tonightQueue->mergeGuestQueue($user, $items, $startedAt);
            }
        }
    }
}
