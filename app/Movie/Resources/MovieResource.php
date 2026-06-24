<?php

namespace App\Movie\Resources;

use App\Enums\MediaType;
use App\Genre\Models\Genre;
use App\Models\User;
use App\Movie\Models\Movie;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class MovieResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this['id'],
            'title' => $this['title'],
            'logo' => $this['logo'] ?? null,
            'trailer' => $this['trailer'] ?? null,
            'backdrop_path' => $this['backdrop_path'] ?? null,
            'poster_path' => $this['poster_path'] ?? null,
            'overview' => $this['overview'],
            'type' =>  ucfirst(MediaType::MOVIE->getLabel()),
            'in_watchlist' => $this->inWatchlist(),
            'year' => Carbon::parse($this['release_date'])->format('M, Y'),
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
