<?php

namespace App\Tv\Resource;

use App\Enums\MediaType;
use App\Models\User;
use App\Movie\Models\Movie;
use App\Tv\Models\Tv;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class TvResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this['id'],
            'title' => $this['title'] ?? $this['name'],
            'poster_path' => $this['poster_path'] ?? 'https://via.placeholder.com/300x450',
            'type' => ucfirst(MediaType::TV->getLabel()),
            'in_watchlist' => $this->inWatchlist(),
            'year' => Carbon::parse($this['first_air_date'])->format('M, Y'),
            'genre' => $this->getGenre(),
            'vote_average' => $this['vote_average'] < 1
                ? 0
                : number_format($this['vote_average'], 1),
        ];
    }

    private function inWatchlist(): bool
    {
        /** @var $user User  */
        $user = Auth::user();

        return $user?->watchlist()->where('media_id', $this['id'])
            ->exists() ?? false;
    }

    private function getGenre(): string
    {
        $names = [];
        $ids = !is_array($this['genre_ids']) ? json_decode($this['genre_ids']): $this['genre_ids'] ;
        $genres = config('genres');

        foreach ($ids as $id) {
            if (isset($genres[$id])) {
                $names[] = $genres[$id];
            }
        }

        return implode(', ', $names);
    }
}
