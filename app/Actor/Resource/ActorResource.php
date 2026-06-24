<?php

namespace App\Actor\Resource;

use App\Enums\MediaType;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class ActorResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this['id'],
            'name' => $this['name'],
            'profile_path' => $this['profile_path'],
        ];
    }

}
