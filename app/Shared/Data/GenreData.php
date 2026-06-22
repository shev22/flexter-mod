<?php

namespace App\Shared\Data;

use App\Genre\Models\Genre;
use Illuminate\Contracts\Support\Arrayable;
use JsonSerializable;

final class GenreData implements Arrayable, JsonSerializable
{
    public function __construct(
        public int $id,
        public string $name,
    ) {}

    public static function fromModel(Genre $genre): self
    {
        return new self(
            id: (int) $genre->id,
            name: (string) $genre->genre,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
