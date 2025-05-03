<?php

namespace App\Tv\Resource;

use App\Actor\Resource\ActorResource;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class ShowTvResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this['id'],
            'title' => $this['name'],
            'logo' => $this->getLogo($this['images']),
            'trailer' => $this->trailer($this['videos']),
            'poster_path' => $this['poster_path'],
            'backdrop_path' => $this['backdrop_path'],
            'overview' => $this['overview'],
            'in_watchlist' => $this->inWatchlist(),
            'year' => Carbon::parse($this['first_air_date'])->format('F, Y'),
            'genres' => collect($this['genres'])->pluck('name')->flatten()->implode(',  '),
            'seasons' => count($this['seasons']),
            'vote_average' => $this['vote_average'] < 1
                ? 0
                : number_format($this['vote_average'], 1),
            'cast' => ActorResource::collection(collect($this['credits']['cast'])->take(10)),
            'related' => $this['related'],
        ];
    }

    private function inWatchlist(): bool
    {
        /** @var $user User  */
        $user = Auth::user();

        return $user?->watchlist()->where('media_id', $this['id'])
            ->exists() ?? false;
    }

    /**
     * @param array $resource
     * @return string|null
     */
    private function trailer(array $resource): ?string
    {
        if (empty($resource['results'])) {
            return null;
        }

        foreach ($resource['results'] as $item) {
            if ($item['type'] === 'Trailer' && !empty($item['key'])) {
                return $item['key'];
            }
        }
        return null;
    }

    /**
     * @param array $resource
     * @return ?string
     */
    private function getLogo(array $resource):?string
    {
        if (empty($resource)) {
            return null;
        }

        if (!empty($resource['logos'])) {
            foreach ($resource['logos'] as $item) {
                if (!empty($item['iso_639_1']) && $item['iso_639_1'] === 'en') {
                    return $item['file_path'] ;
                }
            }
        }
        return null;
    }
}
