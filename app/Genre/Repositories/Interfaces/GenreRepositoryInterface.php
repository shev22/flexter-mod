<?php

namespace App\Genre\Repositories\Interfaces;

use App\Genre\Models\Genre;
use Illuminate\Support\Collection;

interface GenreRepositoryInterface
{
    public function genres(): collection;

    public function createRecord(array $genres): void;
}
