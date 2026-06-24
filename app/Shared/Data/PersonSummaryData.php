<?php

namespace App\Shared\Data;

use App\Actor\Models\Actor;
use App\Shared\Support\Tmdb;
use Illuminate\Contracts\Support\Arrayable;
use JsonSerializable;

final class PersonSummaryData implements Arrayable, JsonSerializable
{
    public function __construct(
        public int $id,
        public string $name,
        public ?string $profile,
        public ?string $knownFor,
        public float $popularity,
        public bool $inWatchlist = false,
    ) {}

    public static function fromModel(Actor $actor, bool $inWatchlist = false): self
    {
        return new self(
            id: (int) $actor->id,
            name: (string) $actor->name,
            profile: Tmdb::image($actor->profile_path, 'profile'),
            knownFor: $actor->known_for,
            popularity: Tmdb::rating($actor->popularity),
            inWatchlist: $inWatchlist,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'profile' => $this->profile,
            'known_for' => $this->knownFor,
            'popularity' => $this->popularity,
            'in_watchlist' => $this->inWatchlist,
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
