<?php

namespace App\Tv\Repositories\Interfaces;

use App\Movie\Models\Movie;
use App\Tv\Models\Tv;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;

interface TvRepositoryInterface
{
    /**
     * @param int $category
     * @param Collection $data
     * @return void
     */
    public function createRecord(int $category, collection $data): void;

    /**
     * @return EloquentCollection
     */
    public function getTrending(): EloquentCollection;

    /**
     * @return LengthAwarePaginator
     */
    public function tv(): LengthAwarePaginator;

    /**
     * @param int $mediaId
     * @return Tv|null
     */
    public function find(int $mediaId): Tv|null;

}
