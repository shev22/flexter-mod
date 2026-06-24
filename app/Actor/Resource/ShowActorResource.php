<?php

namespace App\Actor\Resource;

use App\Movie\Resources\MovieResource;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class ShowActorResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this['id'],
            'name' => $this['name'],
            'birthday' => Carbon::parse($this['birthday'])->format('M d, Y'),
            "place_of_birth" => $this['place_of_birth'],
            'age' => Carbon::parse($this['birthday'])->age,
            'gender' => $this['gender'] == 2 ? 'Male' : 'Female',

            'profile_path' => $this['profile_path']
                ? 'https://image.tmdb.org/t/p/h632/'.$this['profile_path']
                : 'https://via.placeholder.com/300x450',
            "social_media" => $this['external_ids'] ?? [],
            "popularity" => $this['popularity'] < 1
                ? 0
                : number_format($this['popularity'], 1),
            "known_for_department" =>   $this['known_for_department'],
            "biography" => $this['biography'],
        ];
    }
}
