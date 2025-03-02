<?php

namespace App\Actor\Repositories;

use App\Actor\Models\Actor;
use App\Actor\Repositories\Interfaces\ActorsRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ActorsRepository implements ActorsRepositoryInterface
{
    /**
     * @param string|null $search
     * @param ?int $page
     * @param ?int $perPage
     * @return LengthAwarePaginator
     */
    public function actors(?string $search, ?int $page, ?int$perPage): LengthAwarePaginator
    {
       return Actor::when($search, function ($e) use ($search) {
            return $e
                ->where('name', 'like', '%' . $search . '%')
                ->orWhere('known_for', 'like', '%' . $search . '%');
        })->paginate($perPage);

    }

}
