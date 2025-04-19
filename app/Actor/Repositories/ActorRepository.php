<?php

namespace App\Actor\Repositories;

use App\Actor\Models\Actor;
use App\Actor\Repositories\Interfaces\ActorRepositoryInterface;
use App\Movie\Models\Movie;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class ActorRepository implements ActorRepositoryInterface
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

    public function createRecord(?int $value, Collection $data): void
    {
        $actors = collect($data)->map(fn($actor) => [
            'id' => $actor['id'],
            'name' => $actor['name'],
            'profile_path' => $actor['profile_path'],
            'known_for' => collect($actor['known_for'])
                ->flatMap(fn($item) => $item['media_type'] === 'movie' ? [$item['title']] : ($item['media_type'] === 'tv' ? [$item['name']] : []))
                ->implode(', '),
            'popularity' => json_encode($actor['popularity']),
            'created_at' => now(),
            'updated_at' => now(),
        ])->toArray();

        Actor::upsert($actors, ['id'], [
            'name', 'profile_path', 'known_for', 'popularity',
        ]);
    }
}
