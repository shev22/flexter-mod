<?php

namespace App\Http\Resources;

use App\Enums\MediaType;
use App\Models\User;
use App\Movie\Models\Movie;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class HomeResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this['id'],
            'title' => $this['title'],
            'logo' => $this['logo'] ?? null,
            'trailer' => $this['trailer'] ?? null,
            'backdrop_path' => $this['backdrop_path'] ?? null,
            'poster_path' => $this['poster_path'] ?? null,
            'overview' => $this['overview'],
            'type' =>  ($this->resource instanceof Movie)
            ? ucfirst(MediaType::MOVIE->getLabel())
            : ucfirst(MediaType::TV->getLabel()),
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
        return  $this->genres()->pluck('genre')->implode(', ');
    }

}
