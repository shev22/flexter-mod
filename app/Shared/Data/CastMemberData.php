<?php

namespace App\Shared\Data;

use App\Shared\Support\Tmdb;
use Illuminate\Contracts\Support\Arrayable;
use JsonSerializable;

final class CastMemberData implements Arrayable, JsonSerializable
{
    public function __construct(
        public int $id,
        public string $name,
        public ?string $character,
        public ?string $profile,
    ) {}

    public static function fromTmdb(array $d): self
    {
        return new self(
            id: (int) ($d['id'] ?? 0),
            name: (string) ($d['name'] ?? ''),
            character: $d['character'] ?? null,
            profile: Tmdb::image($d['profile_path'] ?? null, 'profile'),
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'character' => $this->character,
            'profile' => $this->profile,
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
