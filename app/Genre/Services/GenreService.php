<?php

namespace App\Genre\Services;

use App\Genre\Repositories\Interfaces\GenreRepositoryInterface;
use App\Genre\Services\Interfaces\GenreServiceInterface;
use App\Services\MediaService\Interfaces\MediaApiClientInterface;
use Illuminate\Support\Collection;

class GenreService implements GenreServiceInterface
{
    public function __construct(protected  MediaApiClientInterface $mediaApiClient, protected GenreRepositoryInterface $genreRepository)
    {
    }
    public function createGenre(): void
    {
       $response = $this->mediaApiClient->fetchGenre();

        $genres = $response->unique('id')->map(function ($genre, $key) {

            return ['id' => $genre['id'], 'genre' => $genre['name']];
        })->toArray();

       $this->genreRepository->createRecord($genres);
    }

    public function getGenre(): collection
    {
        return $this->genreRepository->genres();
    }
}
