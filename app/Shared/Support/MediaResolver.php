<?php

namespace App\Shared\Support;

use App\Movie\Models\Movie;
use App\Tv\Models\Tv;
use App\WatchHistory\Models\WatchHistory;
use Illuminate\Support\Collection;

/**
 * Batch-loads movie and TV models keyed by "type:id" to avoid N+1 queries
 * when resolving watch history, stats, and recommendations.
 */
final class MediaResolver
{
    /** @var array<string, Movie|Tv|null> */
    private array $resolved = [];

    /**
     * @param  Collection<int, WatchHistory>|iterable<int, WatchHistory>  $entries
     */
    public static function fromHistoryEntries(iterable $entries): self
    {
        $movieIds = [];
        $tvIds = [];

        foreach ($entries as $entry) {
            if ($entry->media_type === 'tv') {
                $tvIds[] = (int) $entry->media_id;
            } else {
                $movieIds[] = (int) $entry->media_id;
            }
        }

        return self::fromIds(
            array_values(array_unique($movieIds)),
            array_values(array_unique($tvIds)),
        );
    }

    /**
     * @param  array<int, int>  $movieIds
     * @param  array<int, int>  $tvIds
     */
    public static function fromIds(array $movieIds, array $tvIds): self
    {
        $instance = new self;

        if ($movieIds !== []) {
            Movie::query()
                ->whereIn('id', $movieIds)
                ->get()
                ->each(fn (Movie $m) => $instance->resolved["movie:{$m->id}"] = $m);
        }

        if ($tvIds !== []) {
            Tv::query()
                ->whereIn('id', $tvIds)
                ->get()
                ->each(fn (Tv $t) => $instance->resolved["tv:{$t->id}"] = $t);
        }

        return $instance;
    }

    /**
     * @param  array<int, array{type: string, id: int}>  $items
     */
    public static function fromTypedIds(array $items): self
    {
        $movieIds = [];
        $tvIds = [];

        foreach ($items as $item) {
            if (($item['type'] ?? '') === 'tv') {
                $tvIds[] = (int) $item['id'];
            } else {
                $movieIds[] = (int) $item['id'];
            }
        }

        return self::fromIds(
            array_values(array_unique($movieIds)),
            array_values(array_unique($tvIds)),
        );
    }

    public function get(string $type, int $id): Movie|Tv|null
    {
        $key = Watchlist::normalizeType($type).":{$id}";

        return $this->resolved[$key] ?? null;
    }

    public function getForEntry(WatchHistory $entry): Movie|Tv|null
    {
        return $this->get($entry->media_type, (int) $entry->media_id);
    }

    /**
     * @param  Collection<int, WatchHistory>  $entries
     */
    public static function withGenres(Collection $entries): self
    {
        $movieIds = [];
        $tvIds = [];

        foreach ($entries as $entry) {
            if ($entry->media_type === 'tv') {
                $tvIds[] = (int) $entry->media_id;
            } else {
                $movieIds[] = (int) $entry->media_id;
            }
        }

        $instance = new self;

        if ($movieIds !== []) {
            Movie::query()
                ->with('genres')
                ->whereIn('id', array_unique($movieIds))
                ->get()
                ->each(fn (Movie $m) => $instance->resolved["movie:{$m->id}"] = $m);
        }

        if ($tvIds !== []) {
            Tv::query()
                ->with('genres')
                ->whereIn('id', array_unique($tvIds))
                ->get()
                ->each(fn (Tv $t) => $instance->resolved["tv:{$t->id}"] = $t);
        }

        return $instance;
    }
}
