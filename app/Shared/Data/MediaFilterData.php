<?php

namespace App\Shared\Data;

use Illuminate\Contracts\Support\Arrayable;
use App\Shared\Support\AdultContent;
use App\Site\Services\Interfaces\SiteSettingsServiceInterface;
use Illuminate\Http\Request;
use JsonSerializable;

/**
 * Normalised, validated filter state for the Movies / TV index pages. Doubles
 * as the prop sent back to the client so the UI can reflect the active filters.
 */
final class MediaFilterData implements Arrayable, JsonSerializable
{
    public const SORTS = ['popularity', 'rating', 'newest', 'oldest', 'title'];

    /**
     * @param array<int> $genres
     * @param array<int> $years
     * @param array<int> $ratings
     */
    public function __construct(
        public ?string $search = null,
        public array $genres = [],
        public string $sort = 'popularity',
        public array $years = [],
        public array $ratings = [],
        public int $perPage = 24,
        public int $page = 1,
    ) {}

    public static function fromRequest(Request $request): self
    {
        $sort = (string) $request->query('sort', 'popularity');
        $perPage = app(SiteSettingsServiceInterface::class)->get()->cataloguePerPage;

        return new self(
            search: $request->filled('search') ? trim((string) $request->query('search')) : null,
            genres: self::intList($request->query('genre')),
            sort: in_array($sort, self::SORTS, true) ? $sort : 'popularity',
            years: self::intList($request->query('year')),
            ratings: self::intList($request->query('rating')),
            perPage: $perPage,
            page: max(1, (int) $request->query('page', 1)),
        );
    }

    public function cacheKey(): string
    {
        return md5(json_encode([
            'search' => $this->search,
            'genres' => $this->genres,
            'sort' => $this->sort,
            'years' => $this->years,
            'ratings' => $this->ratings,
            'perPage' => $this->perPage,
            'page' => $this->page,
            'allow_adult' => AdultContent::allowed(),
        ], JSON_THROW_ON_ERROR));
    }

    /**
     * @return array<int>
     */
    private static function intList(mixed $value): array
    {
        if ($value === null || $value === '' || $value === []) {
            return [];
        }

        if (is_array($value)) {
            return array_values(array_unique(array_filter(array_map('intval', $value))));
        }

        if (is_string($value) && str_contains($value, ',')) {
            return array_values(array_unique(array_filter(array_map('intval', explode(',', $value)))));
        }

        return is_numeric($value) ? [(int) $value] : [];
    }

    public function toArray(): array
    {
        return [
            'search' => $this->search,
            'genre' => $this->genres,
            'sort' => $this->sort,
            'year' => $this->years,
            'rating' => $this->ratings,
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
