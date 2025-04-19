<?php

namespace App\Enums;

use App\Actor\Models\Actor;
use App\Movie\Models\Movie;
use App\Series\Models\Series;

enum MediaType: int
{
    case MOVIE = 1;
    case TV = 2;
    case PERSON = 3;

    /** @return array<int, int> */
    public static function getValues(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function getLabel(): string
    {
        return ( strtolower($this->name));
    }

    public function getMappedClass(): string
    {
        return match ($this) {
            self::MOVIE => Movie::class,
            self::TV => Series::class,
            self::PERSON => Actor::class,
        };
    }
}
