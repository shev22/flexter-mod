<?php

namespace App\Movie\Repositories\Interfaces;

use App\Movie\Models\Movie;
use App\Shared\Data\MediaFilterData;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;

interface MovieRepositoryInterface
{
    public function createRecord(int $category, Collection $data): void;

    public function getTrending(): EloquentCollection;

    /**
     * Paginated, filtered library listing for the Movies index.
     */
    public function movies(MediaFilterData $filter): LengthAwarePaginator;

    /**
     * A capped collection of movies for a single category (home rails).
     */
    public function byCategory(int $category, int $limit = 20): EloquentCollection;

    public function find(int $mediaId): Movie|null;
}
