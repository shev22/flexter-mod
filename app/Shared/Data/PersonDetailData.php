<?php

namespace App\Shared\Data;

use App\Shared\Support\Tmdb;
use Carbon\Carbon;
use Illuminate\Contracts\Support\Arrayable;
use JsonSerializable;

/**
 * Detail projection for an actor / person, built from a TMDB
 * "append_to_response=external_ids,tv_credits,movie_credits,images" payload.
 */
final class PersonDetailData implements Arrayable, JsonSerializable
{
    /**
     * @param array<string, ?string>      $socials
     * @param array<int, string>          $images
     * @param array<int, MediaSummaryData> $movies
     * @param array<int, MediaSummaryData> $tv
     */
    public function __construct(
        public int $id,
        public string $name,
        public ?string $profile,
        public ?string $biography,
        public ?string $birthday,
        public ?int $age,
        public ?string $placeOfBirth,
        public string $gender,
        public ?string $knownForDepartment,
        public float $popularity,
        public array $socials,
        public array $images,
        public array $movies,
        public array $tv,
    ) {}

    public static function fromTmdb(array $d): self
    {
        $birthday = $d['birthday'] ?? null;

        $movies = collect($d['movie_credits']['cast'] ?? [])
            ->sortByDesc('popularity')
            ->take(20)
            ->map(fn ($m) => MediaSummaryData::fromTmdb($m, 'movie'))
            ->values()
            ->all();

        $tv = collect($d['tv_credits']['cast'] ?? [])
            ->sortByDesc('popularity')
            ->take(20)
            ->map(fn ($t) => MediaSummaryData::fromTmdb($t, 'tv'))
            ->values()
            ->all();

        $images = collect($d['images']['profiles'] ?? [])
            ->take(10)
            ->map(fn ($p) => Tmdb::image($p['file_path'] ?? null, 'profile_lg'))
            ->filter()
            ->values()
            ->all();

        return new self(
            id: (int) ($d['id'] ?? 0),
            name: (string) ($d['name'] ?? ''),
            profile: Tmdb::image($d['profile_path'] ?? null, 'profile_lg'),
            biography: $d['biography'] ?? null,
            birthday: $birthday ? Carbon::parse($birthday)->format('M d, Y') : null,
            age: $birthday ? Carbon::parse($birthday)->age : null,
            placeOfBirth: $d['place_of_birth'] ?? null,
            gender: ($d['gender'] ?? 0) === 2 ? 'Male' : (($d['gender'] ?? 0) === 1 ? 'Female' : 'Non-binary'),
            knownForDepartment: $d['known_for_department'] ?? null,
            popularity: Tmdb::rating($d['popularity'] ?? 0),
            socials: [
                'instagram' => $d['external_ids']['instagram_id'] ?? null,
                'twitter' => $d['external_ids']['twitter_id'] ?? null,
                'facebook' => $d['external_ids']['facebook_id'] ?? null,
                'imdb' => $d['external_ids']['imdb_id'] ?? null,
            ],
            images: $images,
            movies: $movies,
            tv: $tv,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'profile' => $this->profile,
            'biography' => $this->biography,
            'birthday' => $this->birthday,
            'age' => $this->age,
            'place_of_birth' => $this->placeOfBirth,
            'gender' => $this->gender,
            'known_for_department' => $this->knownForDepartment,
            'popularity' => $this->popularity,
            'socials' => $this->socials,
            'images' => $this->images,
            'movies' => $this->movies,
            'tv' => $this->tv,
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
