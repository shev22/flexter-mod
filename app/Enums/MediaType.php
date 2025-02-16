<?php

namespace App\Enums;

enum MediaType: int
{
    case MOVIE = 1;
    case SERIES = 2;

    /** @return array<int, int> */
    public static function getValues(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function getLabel(): string
    {
        return ucwords( strtolower($this->name));
    }
}
