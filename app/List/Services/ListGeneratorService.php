<?php

namespace App\List\Services;

use App\List\Models\FlexterList;
use App\List\Models\FlexterListItem;
use App\Movie\Models\Movie;
use App\Tv\Models\Tv;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class ListGeneratorService
{
    /**
     * Build list items from the list's stored generation rules.
     */
    public function generate(FlexterList $list, bool $replace = true): int
    {
        if ($replace) {
            $list->items()->delete();
        }

        $genreIds = array_values(array_filter(array_map('intval', $list->genre_ids ?? [])));

        if ($genreIds === []) {
            return 0;
        }

        $limit = max(1, min(100, (int) ($list->item_limit ?: 20)));
        $candidates = $this->queryCandidates(
            mediaType: (string) ($list->media_type ?: 'movie'),
            genreIds: $genreIds,
            minRating: $list->min_rating !== null ? (float) $list->min_rating : null,
            minYear: $list->min_year !== null ? (int) $list->min_year : null,
            limit: $limit,
        );

        $order = 0;

        foreach ($candidates as $candidate) {
            FlexterListItem::query()->create([
                'flexter_list_id' => $list->id,
                'media_type' => $candidate['type'],
                'media_id' => $candidate['id'],
                'sort_order' => ++$order,
            ]);
        }

        return $order;
    }

    /**
     * @param  array<int>  $genreIds
     * @return Collection<int, array{type: string, id: int, popularity: float}>
     */
    private function queryCandidates(
        string $mediaType,
        array $genreIds,
        ?float $minRating,
        ?int $minYear,
        int $limit,
    ): Collection {
        $items = collect();

        if ($mediaType === 'movie' || $mediaType === 'both') {
            $items = $items->merge(
                $this->queryMovies($genreIds, $minRating, $minYear, $mediaType === 'both' ? $limit : $limit)
            );
        }

        if ($mediaType === 'tv' || $mediaType === 'both') {
            $items = $items->merge(
                $this->queryTv($genreIds, $minRating, $minYear, $mediaType === 'both' ? $limit : $limit)
            );
        }

        return $items
            ->sortByDesc('popularity')
            ->unique(fn (array $item) => "{$item['type']}:{$item['id']}")
            ->take($limit)
            ->values();
    }

    /**
     * @param  array<int>  $genreIds
     * @return Collection<int, array{type: string, id: int, popularity: float}>
     */
    private function queryMovies(array $genreIds, ?float $minRating, ?int $minYear, int $limit): Collection
    {
        return $this->applyFilters(Movie::query(), $genreIds, $minRating, $minYear)
            ->select(['id', 'popularity'])
            ->orderByRaw('CAST(popularity AS DECIMAL(12,3)) DESC')
            ->limit($limit)
            ->get()
            ->map(fn (Movie $movie) => [
                'type' => 'movie',
                'id' => (int) $movie->id,
                'popularity' => (float) $movie->popularity,
            ]);
    }

    /**
     * @param  array<int>  $genreIds
     * @return Collection<int, array{type: string, id: int, popularity: float}>
     */
    private function queryTv(array $genreIds, ?float $minRating, ?int $minYear, int $limit): Collection
    {
        return $this->applyFilters(Tv::query(), $genreIds, $minRating, $minYear)
            ->select(['id', 'popularity'])
            ->orderByRaw('CAST(popularity AS DECIMAL(12,3)) DESC')
            ->limit($limit)
            ->get()
            ->map(fn (Tv $show) => [
                'type' => 'tv',
                'id' => (int) $show->id,
                'popularity' => (float) $show->popularity,
            ]);
    }

    /**
     * @param  array<int>  $genreIds
     */
    private function applyFilters(Builder $query, array $genreIds, ?float $minRating, ?int $minYear): Builder
    {
        $query->whereHas('genres', fn (Builder $q) => $q->whereIn('genres.id', $genreIds));

        if ($minRating !== null) {
            $query->where('vote_average', '>=', $minRating);
        }

        if ($minYear !== null) {
            $query->whereNotNull('release_date')
                ->where('release_date', '>=', sprintf('%d-01-01', $minYear));
        }

        return $query;
    }
}
