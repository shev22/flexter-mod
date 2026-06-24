<?php

namespace App\Enums;

Enum Categories: int
{
    case POPULAR = 1;
    case NOW_PLAYING = 2;
    case TRENDING = 3;
    case UPCOMING = 4;
    case TOP_RATED = 5;

    case ON_THE_AIR = 6;

    case AIRING_TODAY = 7;


    /** @return array<int, int> */
    public static function getValues(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function getLabel(): string
    {
        return ( strtolower($this->name));
    }
}
