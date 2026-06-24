<?php

namespace App\Actor\Repositories;

use App\Actor\Models\Actor;
use App\Actor\Repositories\Interfaces\ActorRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class ActorRepository implements ActorRepositoryInterface
{
    public function actors(?string $search, string $sort, int $perPage): LengthAwarePaginator
    {
        $query = Actor::query()
            ->when($search, fn ($q, $term) => $q->where(function ($inner) use ($term) {
                $inner->where('name', 'like', "%{$term}%")
                    ->orWhere('known_for', 'like', "%{$term}%");
            }));

        if ($sort === 'name') {
            $query->orderBy('name');
        } else {
            $query->orderByRaw('CAST(popularity AS DECIMAL(12,3)) DESC');
        }

        return $query->paginate($perPage)->withQueryString();
    }

    public function createRecord(?int $value, Collection $data): void
    {
        $actors = collect($data)->map(fn ($actor) => [
            'id' => $actor['id'],
            'name' => $actor['name'],
            'profile_path' => $actor['profile_path'],
            'known_for' => collect($actor['known_for'] ?? [])
                ->flatMap(fn ($item) => $item['media_type'] === 'movie'
                    ? [$item['title'] ?? null]
                    : ($item['media_type'] === 'tv' ? [$item['name'] ?? null] : []))
                ->filter()
                ->implode(', '),
            'popularity' => json_encode($actor['popularity']),
            'created_at' => now(),
            'updated_at' => now(),
        ])->toArray();

        Actor::upsert($actors, ['id'], ['name', 'profile_path', 'known_for', 'popularity']);
    }
}
