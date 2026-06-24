<?php

namespace App\Tv\Repositories;

use App\Enums\Categories;
use App\Shared\Data\MediaFilterData;
use App\Shared\Support\AppCache;
use App\Shared\Support\GenrePivotSync;
use App\Shared\Support\MediaQuery;
use App\Tv\Models\Tv;
use App\Tv\Repositories\Interfaces\TvRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;

class TvRepository implements TvRepositoryInterface
{

    /**
     * @inheritDoc
     */
    public function tv(MediaFilterData $filter): LengthAwarePaginator
    {
        return AppCache::catalogue(
            'tv.'.$filter->cacheKey(),
            fn () => MediaQuery::apply(Tv::query(), $filter)
                ->paginate($filter->perPage, ['*'], 'page', $filter->page)
                ->withQueryString(),
        );
    }

    /**
     * @inheritDoc
     */
    public function byCategory(int $category, int $limit = 20): EloquentCollection
    {
        return Tv::where('category', $category)
            ->orderByRaw('CAST(popularity AS DECIMAL(12,3)) DESC')
            ->limit($limit)
            ->get();
    }

    /**
     * @inheritDoc
     */
    public function createRecord(int $category, Collection $data): void
    {
        $tvIds = collect($data)->pluck('id')->toArray();

        $tvs = collect($data)->map(fn($tv) => [
            'id' => $tv['id'],
            'title' => $tv['name'] ?? $tv['title'] ?? null,
            'logo' => $tv['logo'] ?? null,
            'backdrop_path' => $tv['backdrop_path'],
            'poster_path' => $tv['poster_path'],
            'genre_ids' => $this->extractGenreIds($tv),
            'overview' => $tv['overview'],
            'release_date' => $tv['first_air_date'] ?? $tv['release_date'] ?? null,
            'trailer' => $tv['trailer'] ?? null,
            'vote_average' => $tv['vote_average'],
            'vote_count' => $tv['vote_count'],
            'original_language' => $tv['original_language'],
            'popularity' => $tv['popularity'],
            'is_trending' => $tv['is_trending'] ?? false,
            'category' => $category,
            'created_at' => now(),
            'updated_at' => now(),
        ])->toArray();

        // logo + trailer are enriched separately — never overwrite them during list sync.
        Tv::upsert($tvs, ['id'], [
            'title', 'poster_path', 'backdrop_path', 'overview', 'release_date',
            'vote_average', 'vote_count', 'original_language', 'is_trending', 'popularity',
            'category', 'genre_ids', 'updated_at',
        ]);

        $insertedTvs = Tv::whereIn('id', $tvIds)->get();
        $this->associateGenre($insertedTvs);
    }

    /**
     * @inheritDoc
     */
    public function getTrending(): EloquentCollection
    {
        return Tv::where('category', Categories::TRENDING->value)
            ->where('is_trending', true)
            ->get();
    }

    /**
     * @inheritDoc
     */
    public function find(int $mediaId): Tv|null
    {
        return Tv::find($mediaId);
    }

    /**
     * @param $tv
     * @return void
     */
    private function associateGenre($tv):void
    {
        GenrePivotSync::syncTv(collect($tv));
    }

    private function extractGenreIds(array $tv): array|string|null
    {
        if (isset($tv['genre_ids'])) {
            return is_array($tv['genre_ids'])
                ? json_encode($tv['genre_ids'])
                : $tv['genre_ids'];
        }

        return json_encode(collect($tv['genres'] ?? [])->pluck('id')->toArray());
    }
}
