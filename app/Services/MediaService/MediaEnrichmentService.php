<?php

namespace App\Services\MediaService;

use App\Movie\Models\Movie;
use App\Services\MediaService\Interfaces\MediaApiClientInterface;
use App\Shared\Support\Tmdb;
use App\Tv\Models\Tv;
use Illuminate\Support\Facades\Cache;

/**
 * Resolves the "rich" media bits (title-treatment logo + trailer key) that the
 * TMDB list endpoints don't return. A single details call yields both; results
 * are cached for a week (negatively too) and persisted to the row, so a
 * populated title never triggers another call.
 */
class MediaEnrichmentService
{
    public function __construct(protected MediaApiClientInterface $api)
    {
    }

    /**
     * Apply cached logo/trailer to an in-memory model without hitting TMDB.
     */
    public function applyCached(Movie|Tv $model): void
    {
        $type = $model instanceof Movie ? 'movie' : 'tv';
        $cached = Cache::get("hero-media:{$type}:{$model->id}");

        if (! is_array($cached)) {
            return;
        }

        if (empty($model->logo) && ! empty($cached['logo'])) {
            $model->logo = $cached['logo'];
        }

        if (empty($model->trailer) && ! empty($cached['trailer'])) {
            $model->trailer = $cached['trailer'];
        }
    }

    /**
     * Enrich a single model's logo + trailer when missing. Returns true if the
     * row was updated.
     */
    public function enrich(Movie|Tv $model, bool $allowApiFetch = true): bool
    {
        $this->applyCached($model);

        $needsLogo = empty($model->logo);
        $needsTrailer = empty($model->trailer);

        if (! $needsLogo && ! $needsTrailer) {
            return false;
        }

        if (! $allowApiFetch) {
            return false;
        }

        $type = $model instanceof Movie ? 'movie' : 'tv';

        $resolved = $this->fetchResolved((string) $model->id, $type);

        $dirty = false;

        if ($needsLogo && ! empty($resolved['logo'])) {
            $model->logo = $resolved['logo'];
            $dirty = true;
        }

        if ($needsTrailer && ! empty($resolved['trailer'])) {
            $model->trailer = $resolved['trailer'];
            $dirty = true;
        }

        if ($dirty) {
            $model->saveQuietly();
        }

        return $dirty;
    }

    /**
     * @param  iterable<Movie|Tv>  $models
     */
    public function enrichMany(iterable $models, ?int $maxApiFetches = null): int
    {
        $changed = 0;
        $apiFetches = 0;
        $max = $maxApiFetches ?? PHP_INT_MAX;

        foreach ($models as $model) {
            $this->applyCached($model);

            $needsFetch = empty($model->logo) || empty($model->trailer);
            $allowApi = $needsFetch && $apiFetches < $max;

            if ($this->enrich($model, $allowApi)) {
                $changed++;
            }

            if ($allowApi) {
                $apiFetches++;
            }
        }

        return $changed;
    }

    /**
     * Enrich every currently-trending movie and tv show.
     *
     * @return array{movies: int, tv: int}
     */
    public function enrichTrending(): array
    {
        return [
            'movies' => $this->enrichMany(Movie::where('is_trending', true)->get()),
            'tv' => $this->enrichMany(Tv::where('is_trending', true)->get()),
        ];
    }

    /**
     * Cache successful lookups for a week; misses are not cached so a wiped row
     * or transient TMDB failure can be retried on the next request.
     *
     * @return array{logo: ?string, trailer: ?string}
     */
    private function fetchResolved(string $id, string $type): array
    {
        $cacheKey = "hero-media:{$type}:{$id}";

        $cached = Cache::get($cacheKey);
        if (is_array($cached) && (! empty($cached['logo']) || ! empty($cached['trailer']))) {
            return $cached;
        }

        $resolved = $this->resolve($id, $type);

        if (! empty($resolved['logo']) || ! empty($resolved['trailer'])) {
            Cache::put($cacheKey, $resolved, now()->addDays(7));
        }

        return $resolved;
    }

    /**
     * @return array{logo: ?string, trailer: ?string}
     */
    private function resolve(string $id, string $type): array
    {
        try {
            $data = $this->api->fetchMediaWithDetails($id, $type);

            return [
                'logo' => $this->pickLogo($data['images']['logos'] ?? []),
            'trailer' => $this->pickTrailer($data['videos']['results'] ?? []),
            ];
        } catch (\Throwable $e) {
            return ['logo' => null, 'trailer' => null];
        }
    }

    private function pickLogo(array $logos): ?string
    {
        if (empty($logos)) {
            return null;
        }

        // Prefer English title treatments, then the highest community rating.
        usort($logos, function ($a, $b) {
            $aEn = ($a['iso_639_1'] ?? null) === 'en' ? 1 : 0;
            $bEn = ($b['iso_639_1'] ?? null) === 'en' ? 1 : 0;

            return $aEn === $bEn
                ? ($b['vote_average'] ?? 0) <=> ($a['vote_average'] ?? 0)
                : $bEn - $aEn;
        });

        return $logos[0]['file_path'] ?? null;
    }

    private function pickTrailer(array $videos): ?string
    {
        return Tmdb::pickTrailer($videos);
    }
}
