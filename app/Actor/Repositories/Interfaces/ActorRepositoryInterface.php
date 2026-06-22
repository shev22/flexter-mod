<?php

namespace App\Actor\Repositories\Interfaces;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface ActorRepositoryInterface
{
    public function actors(?string $search, string $sort, int $perPage): LengthAwarePaginator;

    public function createRecord(?int $value, Collection $data): void;
}
