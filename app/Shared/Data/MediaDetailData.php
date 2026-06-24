<?php

namespace App\Shared\Data;

use App\Shared\Support\Tmdb;
use Illuminate\Contracts\Support\Arrayable;
use JsonSerializable;

/**
 * Rich projection for movie / tv detail pages. Built from a TMDB
 * "append_to_response=videos,credits,images" payload.
 */
final class MediaDetailData implements Arrayable, JsonSerializable
{
    /**
     * @param array<int, string>          $genres
     * @param array<int, CastMemberData>  $cast
     * @param array<int, MediaSummaryData> $related
     * @param array<int, string>          $backdrops
     */
    public function __construct(
        public int $id,
        public string $type,
        public ?string $title,
        public ?string $tagline,
        public ?string $overview,
        public ?string $poster,
        public ?string $backdrop,
        public ?string $logo,
        public ?string $trailer,
        public ?string $year,
        public ?int $runtime,
        public float $rating,
        public int $voteCount,
        public array $genres,
        public array $cast,
        public array $related,
        public array $backdrops,
        public bool $inWatchlist = false,
        public array $seasons = [],
    ) {}

    /**
     * @param array<int, MediaSummaryData> $related
     */
    public static function fromTmdb(array $d, string $type, array $related = [], bool $inWatchlist = false): self
    {
        $videos = $d['videos']['results'] ?? [];
        $logos = $d['images']['logos'] ?? [];
        $backdrops = $d['images']['backdrops'] ?? [];
        $cast = $d['credits']['cast'] ?? [];

        return new self(
            id: (int) ($d['id'] ?? 0),
            type: $type,
            title: $d['title'] ?? $d['name'] ?? null,
            tagline: $d['tagline'] ?? null,
            overview: $d['overview'] ?? null,
            poster: Tmdb::image($d['poster_path'] ?? null, 'poster_lg'),
            backdrop: Tmdb::image($d['backdrop_path'] ?? null, 'backdrop_xl'),
            logo: Tmdb::image(self::pickLogo($logos), 'logo'),
            trailer: self::pickTrailer($videos) ?? Tmdb::youtubeKey($d['trailer'] ?? null),
            year: Tmdb::monthYear($d['release_date'] ?? $d['first_air_date'] ?? null),
            runtime: self::runtime($d),
            rating: Tmdb::rating($d['vote_average'] ?? 0),
            voteCount: (int) ($d['vote_count'] ?? 0),
            genres: Tmdb::genreNames(null, $d['genres'] ?? null),
            cast: collect($cast)->take(16)->map(fn ($c) => CastMemberData::fromTmdb($c))->values()->all(),
            related: $related,
            backdrops: collect($backdrops)->take(8)->map(fn ($b) => Tmdb::image($b['file_path'] ?? null, 'backdrop'))->filter()->values()->all(),
            inWatchlist: $inWatchlist,
            seasons: $type === 'tv' ? self::parseSeasons($d['seasons'] ?? []) : [],
        );
    }

    private static function pickTrailer(array $videos): ?string
    {
        return Tmdb::pickTrailer($videos);
    }

    private static function pickLogo(array $logos): ?string
    {
        foreach ($logos as $logo) {
            if (($logo['iso_639_1'] ?? null) === 'en' && ! empty($logo['file_path'])) {
                return $logo['file_path'];
            }
        }

        return $logos[0]['file_path'] ?? null;
    }

    private static function runtime(array $d): ?int
    {
        if (! empty($d['runtime'])) {
            return (int) $d['runtime'];
        }

        if (! empty($d['episode_run_time'][0])) {
            return (int) $d['episode_run_time'][0];
        }

        return null;
    }

    /**
     * @param  array<int, array<string, mixed>>  $seasons
     * @return array<int, array{season: int, name: string, episodes: int}>
     */
    private static function parseSeasons(array $seasons): array
    {
        return collect($seasons)
            ->filter(fn ($s) => ($s['season_number'] ?? 0) > 0)
            ->sortBy('season_number')
            ->map(fn ($s) => [
                'season' => (int) ($s['season_number'] ?? 0),
                'name' => (string) ($s['name'] ?? 'Season '.($s['season_number'] ?? '')),
                'episodes' => (int) ($s['episode_count'] ?? 0),
            ])
            ->values()
            ->all();
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'title' => $this->title,
            'tagline' => $this->tagline,
            'overview' => $this->overview,
            'poster' => $this->poster,
            'backdrop' => $this->backdrop,
            'logo' => $this->logo,
            'trailer' => $this->trailer,
            'year' => $this->year,
            'runtime' => $this->runtime,
            'rating' => $this->rating,
            'vote_count' => $this->voteCount,
            'genres' => $this->genres,
            'cast' => $this->cast,
            'related' => $this->related,
            'in_watchlist' => $this->inWatchlist,
            'seasons' => $this->seasons,
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
