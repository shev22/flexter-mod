<?php

namespace App\Actor\Repositories\Interfaces;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface ActorRepositoryInterface
{
    /**
     * @param ?string $search
     * @param ?int $page
     * @param ?int $perPage
     * @return LengthAwarePaginator
     */
    public function actors(?string $search, ?int $page, ?int$perPage): LengthAwarePaginator;

    /**
     * @param ?int $value
     * @param Collection $data
     * @return void
     */
    public function createRecord(?int $value, Collection $data): void;

}
