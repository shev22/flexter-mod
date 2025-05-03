<?php

namespace App\Genre\Repositories;

use App\Genre\Models\Genre;
use App\Genre\Repositories\Interfaces\GenreRepositoryInterface;
use App\Movie\Models\Movie;
use Illuminate\Support\Collection;

class GenreRepository implements GenreRepositoryInterface
{

    public function genres(): collection
    {
        return Genre::all();
    }

    public function createRecord(array $genres): void
    {
        Genre::upsert($genres, ['id'], [
            'genre',
        ]);
    }
}
