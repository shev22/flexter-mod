<?php

namespace App\Genre\Services\Interfaces;

use Illuminate\Support\Collection;

interface GenreServiceInterface
{
    public function createGenre(): void;

    public function getGenre(): collection;
}
