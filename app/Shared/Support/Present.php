<?php

namespace App\Shared\Support;

use App\Movie\Models\Movie;
use App\Shared\Data\MediaCardData;
use App\Shared\Data\MediaSummaryData;
use App\Tv\Models\Tv;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Presentation helpers that map domain models to DTOs while resolving the
 * authenticated user's watchlist state in a single batched lookup.
 */
final class Present
{
    public static function media(Movie|Tv $model): MediaSummaryData
    {
        $type = $model instanceof Movie ? 'movie' : 'tv';

        return MediaSummaryData::fromModel($model, Watchlist::has($type, (int) $model->id));
    }

    public static function card(Movie|Tv $model): MediaCardData
    {
        $type = $model instanceof Movie ? 'movie' : 'tv';

        return MediaCardData::fromModel($model, Watchlist::has($type, (int) $model->id));
    }

    /**
     * @param  iterable<int, Movie|Tv>  $items
     * @return array<int, array<string, mixed>>
     */
    public static function cardList(iterable $items): array
    {
        Watchlist::keys();

        return collect($items)
            ->map(fn ($model) => self::card($model)->toArray())
            ->values()
            ->all();
    }

    /**
     * @param  iterable<int, Movie|Tv>  $items
     * @return array<int, MediaSummaryData>
     */
    public static function mediaList(iterable $items): array
    {
        Watchlist::keys();

        return collect($items)->map(fn ($model) => self::media($model))->values()->all();
    }

    /**
     * Full hero payloads including backdrop, logo, trailer, and overview.
     *
     * @param  iterable<int, Movie|Tv>  $items
     * @return array<int, array<string, mixed>>
     */
    public static function heroList(iterable $items): array
    {
        Watchlist::keys();

        return collect($items)
            ->map(fn ($model) => self::media($model)->toArray())
            ->values()
            ->all();
    }

    /**
     * Standard paginated payload consumed by the index grids.
     */
    public static function paginated(LengthAwarePaginator $paginator): array
    {
        return [
            'data' => self::cardList($paginator->items()),
            'current_page' => $paginator->currentPage(),
            'last_page' => $paginator->lastPage(),
            'per_page' => $paginator->perPage(),
            'total' => $paginator->total(),
        ];
    }
}
