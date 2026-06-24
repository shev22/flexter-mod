<?php

namespace App\Shared\Data;

use App\Movie\Models\Movie;
use App\Shared\Support\Tmdb;
use App\Tv\Models\Tv;
use Illuminate\Contracts\Support\Arrayable;
use JsonSerializable;

/**
 * Lightweight projection of a movie / tv title used by cards, rails and the
 * hero carousel. All image fields are already fully-qualified URLs.
 */
final class MediaSummaryData implements Arrayable, JsonSerializable
{
    /**
     * @param array<int, string> $genres
     */
    public function __construct(
        public int $id,
        public string $type,
        public ?string $title,
        public ?string $overview,
        public ?string $poster,
        public ?string $backdrop,
        public ?string $logo,
        public ?string $trailer,
        public ?string $year,
        public float $rating,
        public array $genres,
        public bool $inWatchlist = false,
    ) {}

    public static function fromModel(Movie|Tv $m, bool $inWatchlist = false): self
    {
        return new self(
            id: (int) $m->id,
            type: $m instanceof Movie ? 'movie' : 'tv',
            title: $m->title,
            overview: $m->overview,
            poster: Tmdb::image($m->poster_path, 'poster'),
            backdrop: Tmdb::image($m->backdrop_path, 'backdrop'),
            logo: Tmdb::image($m->logo, 'logo'),
            trailer: Tmdb::youtubeKey($m->trailer),
            year: Tmdb::monthYear($m->release_date),
            rating: Tmdb::rating($m->vote_average),
            genres: Tmdb::genreNames($m->genre_ids),
            inWatchlist: $inWatchlist,
        );
    }

    public static function fromTmdb(array $d, string $type, bool $inWatchlist = false): self
    {
        return new self(
            id: (int) ($d['id'] ?? 0),
            type: $type,
            title: $d['title'] ?? $d['name'] ?? null,
            overview: $d['overview'] ?? null,
            poster: Tmdb::image($d['poster_path'] ?? null, 'poster'),
            backdrop: Tmdb::image($d['backdrop_path'] ?? null, 'backdrop'),
            logo: null,
            trailer: $d['trailer'] ?? null,
            year: Tmdb::monthYear($d['release_date'] ?? $d['first_air_date'] ?? null),
            rating: Tmdb::rating($d['vote_average'] ?? 0),
            genres: Tmdb::genreNames($d['genre_ids'] ?? null, $d['genres'] ?? null),
            inWatchlist: $inWatchlist,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'title' => $this->title,
            'overview' => $this->overview,
            'poster' => $this->poster,
            'backdrop' => $this->backdrop,
            'logo' => $this->logo,
            'trailer' => $this->trailer,
            'year' => $this->year,
            'rating' => $this->rating,
            'genres' => $this->genres,
            'in_watchlist' => $this->inWatchlist,
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
