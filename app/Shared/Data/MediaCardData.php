<?php

namespace App\Shared\Data;

use App\Movie\Models\Movie;
use App\Shared\Support\Tmdb;
use App\Tv\Models\Tv;
use Illuminate\Contracts\Support\Arrayable;
use JsonSerializable;

/**
 * Lightweight card projection — omits backdrop, logo, trailer, and full overview.
 */
final class MediaCardData implements Arrayable, JsonSerializable
{
    /**
     * @param array<int, string> $genres
     */
    public function __construct(
        public int $id,
        public string $type,
        public ?string $title,
        public ?string $poster,
        public ?string $year,
        public float $rating,
        public array $genres,
        public bool $inWatchlist = false,
        public ?string $overviewSnippet = null,
    ) {}

    public static function fromModel(Movie|Tv $m, bool $inWatchlist = false): self
    {
        return new self(
            id: (int) $m->id,
            type: $m instanceof Movie ? 'movie' : 'tv',
            title: $m->title,
            poster: Tmdb::image($m->poster_path, 'poster'),
            year: Tmdb::monthYear($m->release_date),
            rating: Tmdb::rating($m->vote_average),
            genres: Tmdb::genreNames($m->genre_ids),
            inWatchlist: $inWatchlist,
            overviewSnippet: self::snippet($m->overview),
        );
    }

    public static function fromTmdb(array $d, string $type, bool $inWatchlist = false): self
    {
        return new self(
            id: (int) ($d['id'] ?? 0),
            type: $type,
            title: $d['title'] ?? $d['name'] ?? null,
            poster: Tmdb::image($d['poster_path'] ?? null, 'poster'),
            year: Tmdb::monthYear($d['release_date'] ?? $d['first_air_date'] ?? null),
            rating: Tmdb::rating($d['vote_average'] ?? 0),
            genres: Tmdb::genreNames($d['genre_ids'] ?? null, $d['genres'] ?? null),
            inWatchlist: $inWatchlist,
            overviewSnippet: self::snippet($d['overview'] ?? null),
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'title' => $this->title,
            'poster' => $this->poster,
            'year' => $this->year,
            'rating' => $this->rating,
            'genres' => $this->genres,
            'in_watchlist' => $this->inWatchlist,
            'overview' => $this->overviewSnippet,
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    private static function snippet(?string $text, int $max = 120): ?string
    {
        $text = trim((string) $text);

        if ($text === '') {
            return null;
        }

        if (mb_strlen($text) <= $max) {
            return $text;
        }

        return mb_substr($text, 0, $max).'…';
    }
}
