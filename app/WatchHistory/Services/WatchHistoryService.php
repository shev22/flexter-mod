<?php

namespace App\WatchHistory\Services;

use App\Models\User;
use App\Shared\Data\MediaCardData;
use App\Shared\Support\MediaResolver;
use App\Shared\Support\Watchlist;
use App\WatchHistory\Models\WatchHistory;
use App\WatchHistory\Services\Interfaces\WatchHistoryServiceInterface;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class WatchHistoryService implements WatchHistoryServiceInterface
{
    public function touch(User $user, string $type, int $mediaId, int $progress = 15, ?int $season = null, ?int $episode = null): int
    {
        return $this->upsert($user, $type, $mediaId, max(5, min(100, $progress)), false, $season, $episode);
    }

    public function markWatched(User $user, string $type, int $mediaId, ?int $season = null, ?int $episode = null): void
    {
        $this->upsert($user, $type, $mediaId, 100, completed: true, season: $season, episode: $episode);
    }

    public function bumpProgress(User $user, string $type, int $mediaId, int $progress, ?int $season = null, ?int $episode = null): int
    {
        return $this->upsert($user, $type, $mediaId, max(5, min(100, $progress)), false, $season, $episode);
    }

    public function recent(User $user, int $limit = 40): Collection
    {
        $entries = $user->watchHistories()
            ->orderByDesc('last_watched_at')
            ->limit($limit)
            ->get();

        $resolver = MediaResolver::fromHistoryEntries($entries);

        return $entries->map(fn (WatchHistory $entry) => $this->presentEntry($entry, $resolver));
    }

    public function stats(User $user): array
    {
        $aggregates = $user->watchHistories()
            ->selectRaw('COUNT(*) as total')
            ->selectRaw('SUM(CASE WHEN completed = 1 THEN 1 ELSE 0 END) as completed')
            ->selectRaw('SUM(CASE WHEN completed = 0 AND progress_percent > 0 THEN 1 ELSE 0 END) as in_progress')
            ->first();

        $entries = $user->watchHistories()->get(['media_type', 'media_id', 'progress_percent']);
        $resolver = MediaResolver::fromHistoryEntries($entries);

        $minutes = $entries->sum(function (WatchHistory $entry) use ($resolver) {
            $media = $resolver->getForEntry($entry);

            if (! $media) {
                return 0;
            }

            $runtime = $media instanceof \App\Movie\Models\Movie
                ? (int) ($media->runtime ?? 0)
                : 45;

            return ($runtime * $entry->progress_percent) / 100;
        });

        return [
            'total' => (int) ($aggregates->total ?? 0),
            'completed' => (int) ($aggregates->completed ?? 0),
            'in_progress' => (int) ($aggregates->in_progress ?? 0),
            'hours' => round($minutes / 60, 1),
        ];
    }

    public function continueWatching(User $user, int $limit = 12): array
    {
        $entries = $user->watchHistories()
            ->where('completed', false)
            ->where('progress_percent', '>', 0)
            ->orderByDesc('last_watched_at')
            ->limit($limit)
            ->get();

        $resolver = MediaResolver::fromHistoryEntries($entries);

        return $entries
            ->map(function (WatchHistory $entry) use ($resolver) {
                $media = $resolver->getForEntry($entry);

                if (! $media) {
                    return null;
                }

                $type = $entry->media_type;
                $summary = MediaCardData::fromModel($media, Watchlist::has($type, (int) $media->id))->toArray();

                return array_merge($summary, [
                    'history_id' => $entry->id,
                    'progress' => (int) $entry->progress_percent,
                    'season' => $entry->season_number,
                    'episode' => $entry->episode_number,
                    'last_watched' => $entry->last_watched_at?->diffForHumans(),
                ]);
            })
            ->filter()
            ->values()
            ->all();
    }

    public function remove(User $user, int $historyId): void
    {
        $user->watchHistories()->whereKey($historyId)->delete();
    }

    public function clear(User $user): void
    {
        $user->watchHistories()->delete();
    }

    public function progressFor(User $user, string $type, int $mediaId): ?WatchHistory
    {
        return $user->watchHistories()
            ->where('media_type', $type)
            ->where('media_id', $mediaId)
            ->whereNull('season_number')
            ->whereNull('episode_number')
            ->first();
    }

    public function latestProgressFor(User $user, string $type, int $mediaId): ?WatchHistory
    {
        return $user->watchHistories()
            ->where('media_type', $type)
            ->where('media_id', $mediaId)
            ->orderByDesc('last_watched_at')
            ->first();
    }

    private function upsert(User $user, string $type, int $mediaId, int $progress, bool $completed = false, ?int $season = null, ?int $episode = null): int
    {
        $entry = $user->watchHistories()
            ->where('media_type', $type)
            ->where('media_id', $mediaId)
            ->when($season !== null, fn ($q) => $q->where('season_number', $season))
            ->when($season === null, fn ($q) => $q->whereNull('season_number'))
            ->when($episode !== null, fn ($q) => $q->where('episode_number', $episode))
            ->when($episode === null, fn ($q) => $q->whereNull('episode_number'))
            ->first();

        $nextProgress = $entry
            ? max((int) $entry->progress_percent, $progress)
            : $progress;

        $isComplete = $completed || $nextProgress >= 100;
        $nextProgress = $isComplete ? 100 : $nextProgress;

        WatchHistory::query()->updateOrCreate(
            [
                'user_id' => $user->id,
                'media_type' => $type,
                'media_id' => $mediaId,
                'season_number' => $season,
                'episode_number' => $episode,
            ],
            [
                'progress_percent' => $nextProgress,
                'completed' => $isComplete,
                'last_watched_at' => Carbon::now(),
            ],
        );

        return $nextProgress;
    }

    /** @return array<string, mixed> */
    private function presentEntry(WatchHistory $entry, MediaResolver $resolver): array
    {
        $media = $resolver->getForEntry($entry);
        $summary = $media
            ? MediaCardData::fromModel($media, Watchlist::has($entry->media_type, (int) $media->id))
            : null;

        return [
            'id' => $entry->id,
            'media_id' => (int) $entry->media_id,
            'type' => $entry->media_type,
            'title' => $summary?->title,
            'poster' => $summary?->poster,
            'year' => $summary?->year,
            'rating' => $summary?->rating ?? 0,
            'progress' => (int) $entry->progress_percent,
            'completed' => (bool) $entry->completed,
            'last_watched' => $entry->last_watched_at?->diffForHumans(),
            'last_watched_at' => $entry->last_watched_at?->toIso8601String(),
        ];
    }
}
