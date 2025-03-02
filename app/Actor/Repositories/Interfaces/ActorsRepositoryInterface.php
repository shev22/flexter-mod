<?php

namespace App\Actor\Repositories\Interfaces;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface ActorsRepositoryInterface
{
    /**
     * @param ?string $search
     * @param ?int $page
     * @param ?int $perPage
     * @return LengthAwarePaginator
     */
    public function actors(?string $search, ?int $page, ?int$perPage): LengthAwarePaginator;

}
