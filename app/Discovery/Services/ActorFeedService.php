<?php

namespace App\Discovery\Services;

use App\Actor\Models\Actor;
use App\Models\User;
use App\Services\MediaService\Interfaces\MediaApiClientInterface;
use App\Shared\Data\MediaCardData;
use App\Shared\Support\AdultContent;
use App\Shared\Support\Watchlist;
use App\WatchList\Models\WatchList as WatchListItem;
use Illuminate\Support\Facades\Cache;

class ActorFeedService
{
    public const POOL_SIZE = 36;

    public function __construct(
        private readonly MediaApiClientInterface $apiClient,
    ) {}

    /**
     * @return array<int, array<string, mixed>>
     */
    public function forUser(?User $user, int $limit = self::POOL_SIZE): array
    {
        if ($user === null) {
            return [];
        }

        $actorIds = WatchListItem::query()
            ->where('user_id', $user->id)
            ->where('media_type', Actor::class)
            ->orderByDesc('created_at')
            ->pluck('media_id');

        if ($actorIds->isEmpty()) {
            return [];
        }

        Watchlist::keys();
        $results = collect();

        foreach ($actorIds as $actorId) {
            $credits = Cache::remember(
                "actor_feed.{$actorId}",
                now()->addHours(12),
                fn () => $this->apiClient->fetchPersonCredits((int) $actorId),
            );

            foreach ($credits as $credit) {
                if ($results->count() >= $limit) {
                    break 2;
                }

                $key = ($credit['media_type'] ?? 'movie').':'.($credit['id'] ?? 0);
                if ($results->has($key)) {
                    continue;
                }

                $type = ($credit['media_type'] ?? '') === 'tv' ? 'tv' : 'movie';

                $results->put($key, MediaCardData::fromTmdb($credit, $type, Watchlist::has($type, (int) ($credit['id'] ?? 0)))->toArray());
            }
        }

        return AdultContent::filterCards($results->shuffle()->values()->all());
    }
}
