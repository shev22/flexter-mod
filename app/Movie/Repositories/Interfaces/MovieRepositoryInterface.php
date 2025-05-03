<?php

namespace App\Movie\Repositories\Interfaces;

use App\Movie\Models\Movie;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;

interface MovieRepositoryInterface
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
    public function movies(): LengthAwarePaginator;

    /**
     * @param int $mediaId
     * @return Movie|null
     */
    public function find(int $mediaId): Movie|null;
}
