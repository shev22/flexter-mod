<?php

namespace App\Shared\Support;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

final class GenrePivotSync
{
    /**
     * @param  Collection<int, object{id: int, genre_ids: string|null}>  $movies
     */
    public static function syncMovies(Collection $movies): void
    {
        self::sync($movies, 'genre_movie', 'movie_id');
    }

    /**
     * @param  Collection<int, object{id: int, genre_ids: string|null}>  $tvs
     */
    public static function syncTv(Collection $tvs): void
    {
        self::sync($tvs, 'genre_tv', 'tv_id');
    }

    /**
     * @param  Collection<int, object{id: int, genre_ids: string|null}>  $items
     */
    private static function sync(Collection $items, string $table, string $foreignKey): void
    {
        if ($items->isEmpty()) {
            return;
        }

        $ids = $items->pluck('id')->all();
        $rows = [];

        foreach ($items as $item) {
            $genreIds = json_decode($item->genre_ids ?? '[]', true) ?? [];

            foreach ($genreIds as $genreId) {
                $rows[] = [
                    'genre_id' => (int) $genreId,
                    $foreignKey => (int) $item->id,
                ];
            }
        }

        DB::table($table)->whereIn($foreignKey, $ids)->delete();

        if ($rows !== []) {
            foreach (array_chunk($rows, 500) as $chunk) {
                DB::table($table)->insert($chunk);
            }
        }
    }
}
