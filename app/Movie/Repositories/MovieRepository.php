<?php

namespace App\Movie\Repositories;

use App\Enums\Categories;
use App\Movie\Models\Movie;
use App\Movie\Repositories\Interfaces\MovieRepositoryInterface;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class MovieRepository implements MovieRepositoryInterface
{
    /**
     * @inheritdoc
     */
    public function createRecord(int $category, collection $data): void
    {
        $movieIds = collect($data)->pluck('id')->toArray();

        $movies = collect($data)->map(fn($movie) => [
            'id' => $movie['id'],
            'title' => $movie['title'],
            'logo' => $movie['logo'] ?? null,
            'backdrop_path' => $movie['backdrop_path'],
            'poster_path' => $movie['poster_path'],
            'genre_ids' => $this->extractGenreIds($movie),
            'overview' => $movie['overview'],
            'release_date' => $movie['release_date'],
            'trailer' => $movie['trailer'] ?? null,
            'vote_average' => $movie['vote_average'],
            'vote_count' => $movie['vote_count'],
            'original_language' => $movie['original_language'],
            'popularity' => $movie['popularity'],
            'is_trending' => $movie['is_trending'] ?? false,
            'category' => $category,
            'created_at' => now(),
            'updated_at' => now(),
        ])->toArray();

        Movie::upsert($movies, ['id'], [
            'title', 'poster_path', 'backdrop_path', 'overview', 'release_date', 'trailer', 'logo',
            'vote_average', 'vote_count', 'original_language', 'is_trending', 'popularity',
            'category', 'genre_ids', 'updated_at'
        ]);

        $insertedMovies = Movie::whereIn('id', $movieIds)->get();
        $this->associateGenre($insertedMovies);
    }

    /**
     * @inheritdoc
     */
    public function movies(): LengthAwarePaginator
    {
        return Movie::latest()->paginate(20);
    }

    /**
     * @inheritDoc
     */
    public function getTrending():EloquentCollection
    {
        return Movie::where('category', Categories::TRENDING->value)
            ->where('is_trending', true)
            ->get();
    }

    /**
     * @inheritDoc
     */
    public function find(int $mediaId): Movie|null
    {
        return Movie::find($mediaId);
    }


    /**
     * @param $movies
     * @return void
     */
    private function associateGenre($movies):void
    {
        $movies->each(function ($movie) {
            $genres = json_decode($movie->genre_ids, true);

            if (!empty($genres)) {
                $movie->genres()->sync($genres);
            }
        });
    }

    private function extractGenreIds(array $movie): array|string|null
    {
        if (isset($movie['genre_ids'])) {
            return is_array($movie['genre_ids'])
                ? json_encode($movie['genre_ids'])
                : $movie['genre_ids'];
        }

        return json_encode(collect($movie['genres'] ?? [])->pluck('id')->toArray());
    }
}
