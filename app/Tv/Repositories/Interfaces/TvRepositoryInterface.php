<?php

namespace App\Tv\Repositories\Interfaces;

use App\Shared\Data\MediaFilterData;
use App\Tv\Models\Tv;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;

interface TvRepositoryInterface
{
    public function createRecord(int $category, Collection $data): void;

    public function getTrending(): EloquentCollection;

    /**
     * Paginated, filtered library listing for the TV index.
     */
    public function tv(MediaFilterData $filter): LengthAwarePaginator;

    /**
     * A capped collection of shows for a single category (home rails).
     */
    public function byCategory(int $category, int $limit = 20): EloquentCollection;

    public function find(int $mediaId): Tv|null;
}
