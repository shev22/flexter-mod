<?php

namespace App\Http\Resources;

use App\Shared\Support\Tmdb;
use App\Shared\Support\Watchlist;
use Illuminate\Http\Resources\Json\JsonResource;

class SearchResource extends JsonResource
{
    public function toArray($request): array
    {
        $type = $this['media_type'];
        $id = (int) $this['id'];
        $watchlistType = $type === 'person' ? 'actor' : $type;

        return [
            'id' => $id,
            'type' => $type,
            'title' => $this['title'] ?? $this['name'] ?? null,
            'poster_path' => $this['poster_path'] ?? $this['profile_path'] ?? null,
            'rating' => Tmdb::rating($this['vote_average'] ?? $this['popularity'] ?? 0),
            'overview' => self::trimOverview($this['overview'] ?? ''),
            'release_date' => Tmdb::monthYear($this['release_date'] ?? $this['first_air_date'] ?? null),
            'known_for' => $type === 'person' ? $this->knownForLabel() : null,
            'in_watchlist' => Watchlist::has($watchlistType, $id),
            'adult' => (bool) ($this['adult'] ?? false),
            'certification' => Tmdb::usCertification($this->resource instanceof \Illuminate\Contracts\Support\Arrayable ? $this->resource->toArray() : (array) $this->resource),
            'route' => $this->getRoute($type),
        ];
    }

    protected function knownForLabel(): ?string
    {
        if (! empty($this['known_for_department'])) {
            return $this['known_for_department'];
        }

        $titles = collect($this['known_for'] ?? [])
            ->map(fn ($item) => $item['title'] ?? $item['name'] ?? null)
            ->filter()
            ->take(2)
            ->implode(', ');

        return $titles ?: 'Actor';
    }

    protected static function trimOverview(?string $text, int $max = 140): string
    {
        $text = trim((string) $text);

        if ($text === '') {
            return '';
        }

        if (mb_strlen($text) <= $max) {
            return $text;
        }

        return mb_substr($text, 0, $max).'…';
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
