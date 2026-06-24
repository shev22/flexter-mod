<?php

namespace App\Shared\Support;

use App\Shared\Data\MediaFilterData;
use Illuminate\Database\Eloquent\Builder;

/**
 * Applies the shared filter/sort rules used by both the Movie and Tv indexes.
 * Both tables have an identical schema so the logic lives in one place.
 */
final class MediaQuery
{
    public static function apply(Builder $query, MediaFilterData $filter): Builder
    {
        $query->when($filter->search, fn (Builder $q, string $term) => $q->where('title', 'like', "%{$term}%"));

        $query->when($filter->genres, fn (Builder $q, array $genres) => $q->whereHas(
            'genres',
            fn (Builder $g) => $g->whereIn('genres.id', $genres)
        ));

        $query->when($filter->years, function (Builder $q, array $years) {
            $q->where(function (Builder $inner) use ($years) {
                foreach ($years as $year) {
                    $inner->orWhere('release_date', 'like', "{$year}%");
                }
            });
        });

        $query->when($filter->ratings, function (Builder $q, array $ratings) {
            $q->where(function (Builder $inner) use ($ratings) {
                foreach ($ratings as $rating) {
                    $inner->orWhereRaw('FLOOR(CAST(vote_average AS DECIMAL(4,1))) = ?', [(int) $rating]);
                }
            });
        });

        return match ($filter->sort) {
            'rating' => $query->orderByRaw('CAST(vote_average AS DECIMAL(4,1)) DESC')->orderByRaw('CAST(vote_count AS UNSIGNED) DESC'),
            'newest' => $query->orderBy('release_date', 'desc'),
            'oldest' => $query->orderBy('release_date', 'asc'),
            'title' => $query->orderBy('title', 'asc'),
            default => $query->orderByRaw('CAST(popularity AS DECIMAL(12,3)) DESC'),
        };
    }
}
