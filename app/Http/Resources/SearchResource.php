<?php


namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SearchResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this['id'],
            'type' => $this['media_type'],
            'title' => $this['title'] ?? $this['name'] ?? null,
            'poster_path' => $this['poster_path'] ?? $this['profile_path'] ?? null,
            'vote_average' => $this['vote_average'] ?? $this['popularity'] ?? 0,
            'overview' => $this['overview'] ?? '',
            'release_date' => $this['release_date'] ?? $this['first_air_date'] ?? null,
            'route' => $this->getRoute($this['media_type']),
        ];
    }

    protected function getRoute(string $media_type): string
    {
        return match ($media_type) {
            'movie' => 'movie.show',
            'tv' => 'tv.show',
            'person' => 'actor.show',
            default => 'search',
        };
    }
}
