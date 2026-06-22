<?php

namespace App\Services\MediaService;

use App\Enums\MediaType;
use App\Models\TmdbApiActivity;
use App\Services\MediaService\Interfaces\MediaApiClientInterface;
use App\Site\Services\Interfaces\SiteSettingsServiceInterface;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Promise\Utils;
use Illuminate\Http\Client\Pool;
use Illuminate\Http\Client\RequestException as LaravelRequestException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class ApiClient implements MediaApiClientInterface
{
    public function __construct(
        protected TmdbApiActivityLogger $activityLogger,
        protected SiteSettingsServiceInterface $siteSettings,
    ) {}

    public function fetchMedia(string $mediaType, string $category, int $pages): Collection
    {
        if ($this->isOverDailyLimit()) {
            return collect([]);
        }

        $promises = [];

        try {
            for ($i = 1; $i <= $pages; $i++) {
                $promises[] = Http::withToken(config('services.tmdb.token'))
                    ->async()
                    ->get("https://api.themoviedb.org/3/{$mediaType}/{$category}", [
                        'page' => $i,
                    ]);
            }

            $responses = Utils::unwrap($promises);

            $results = collect($responses)
                ->map(fn ($response) => $response->json()['results'] ?? [])
                ->collapse();

            $this->activityLogger->log(
                operation: 'fetch_media',
                mediaType: $mediaType,
                requestCount: $pages,
                itemsFetched: $results->count(),
                category: $category,
            );

            return $results;
        } catch (RequestException|LaravelRequestException $e) {
            \Log::error('TMDB API request failed: '.$e->getMessage());

            $this->activityLogger->log(
                operation: 'fetch_media',
                mediaType: $mediaType,
                requestCount: $pages,
                itemsFetched: 0,
                status: 'error',
                errorMessage: $e->getMessage(),
                category: $category,
            );

            return collect([]);
        } catch (\Throwable $e) {
            \Log::error('Unexpected error in fetchMedia: '.$e->getMessage());

            $this->activityLogger->log(
                operation: 'fetch_media',
                mediaType: $mediaType,
                requestCount: $pages,
                itemsFetched: 0,
                status: 'error',
                errorMessage: $e->getMessage(),
                category: $category,
            );

            return collect([]);
        }
    }

    public function fetchGenre(): Collection
    {
        $mediaTypes = [MediaType::MOVIE->getLabel(), MediaType::TV->getLabel()];
        $promises = [];

        foreach ($mediaTypes as $mediaType) {
            $promises[] = Http::withToken(config('services.tmdb.token'))
                ->async()
                ->get("https://api.themoviedb.org/3/genre/{$mediaType}/list");
        }

        $responses = Utils::unwrap($promises);

        $genres = collect($responses)
            ->map(fn ($response) => $response->json()['genres'] ?? [])
            ->collapse();

        $this->activityLogger->log(
            operation: 'fetch_genre',
            mediaType: 'genre',
            requestCount: count($mediaTypes),
            itemsFetched: $genres->count(),
        );

        return $genres;
    }

    public function fetchMediaWithDetails(string $mediaId, string $mediaType, bool $withAttachments = true, bool $withPersonAttachments = false): array
    {
        if ($this->isOverDailyLimit()) {
            return [];
        }

        $movieAttachments = $withAttachments ? '?append_to_response=videos,credits,images' : '';
        $personAttachments = $withPersonAttachments ? '?append_to_response=external_ids,tv_credits,movie_credits,images' : '';

        $response = Http::withToken(config('services.tmdb.token'))
            ->get('https://api.themoviedb.org/3/'.$mediaType.'/'.$mediaId.$movieAttachments.$personAttachments);

        if ($response->failed()) {
            $this->activityLogger->log(
                operation: 'fetch_details',
                mediaType: $mediaType,
                requestCount: 1,
                itemsFetched: 0,
                status: 'error',
                errorMessage: $response->body(),
                metadata: ['media_id' => $mediaId],
            );

            return [];
        }

        $data = $response->json();

        $this->activityLogger->log(
            operation: 'fetch_details',
            mediaType: $mediaType,
            requestCount: 1,
            itemsFetched: isset($data['id']) ? 1 : 0,
            metadata: ['media_id' => $mediaId],
        );

        return $data;
    }

    public function fetchRelatedMedia(string $mediaId, string $mediaType): array
    {
        if ($this->isOverDailyLimit()) {
            return [];
        }

        $response = Http::withToken(config('services.tmdb.token'))
            ->get('https://api.themoviedb.org/3/'.$mediaType.'/'.$mediaId.'/similar');

        if ($response->failed()) {
            $this->activityLogger->log(
                operation: 'fetch_related',
                mediaType: $mediaType,
                requestCount: 1,
                itemsFetched: 0,
                status: 'error',
                errorMessage: $response->body(),
                metadata: ['media_id' => $mediaId],
            );

            return [];
        }

        $data = $response->json();
        $items = count($data['results'] ?? []);

        $this->activityLogger->log(
            operation: 'fetch_related',
            mediaType: $mediaType,
            requestCount: 1,
            itemsFetched: $items,
            metadata: ['media_id' => $mediaId],
        );

        return $data;
    }

    /**
     * @inheritDoc
     */
    public function fetchTrending(string $mediaType, string $period): array
    {
        if ($this->isOverDailyLimit()) {
            return [];
        }

        $response = Http::withToken(config('services.tmdb.token'))
            ->get('https://api.themoviedb.org/3/trending/'.$mediaType.'/'.$period);

        if ($response->failed()) {
            $this->activityLogger->log(
                operation: 'fetch_trending',
                mediaType: $mediaType,
                requestCount: 1,
                itemsFetched: 0,
                status: 'error',
                errorMessage: $response->body(),
                category: $period,
            );

            return [];
        }

        $results = $response->json()['results'] ?? [];

        $this->activityLogger->log(
            operation: 'fetch_trending',
            mediaType: $mediaType,
            requestCount: 1,
            itemsFetched: count($results),
            category: $period,
        );

        return $results;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function fetchPersonCredits(int $actorId): array
    {
        if ($this->isOverDailyLimit()) {
            return [];
        }

        $response = Http::withToken(config('services.tmdb.token'))
            ->get("https://api.themoviedb.org/3/person/{$actorId}/combined_credits");

        if ($response->failed()) {
            $this->activityLogger->log(
                operation: 'fetch_person_credits',
                mediaType: 'person',
                requestCount: 1,
                itemsFetched: 0,
                status: 'error',
                errorMessage: $response->body(),
                metadata: ['media_id' => $actorId],
            );

            return [];
        }

        $cast = collect($response->json()['cast'] ?? [])
            ->filter(fn ($item) => in_array($item['media_type'] ?? '', ['movie', 'tv'], true))
            ->sortByDesc('popularity')
            ->take(6)
            ->values()
            ->all();

        $this->activityLogger->log(
            operation: 'fetch_person_credits',
            mediaType: 'person',
            requestCount: 1,
            itemsFetched: count($cast),
            metadata: ['media_id' => $actorId],
        );

        return $cast;
    }

    /**
     * @inheritDoc
     */
    public function search(string $query, bool $isMultipageSearch = false): Collection
    {
        $settings = $this->siteSettings->get();
        $baseUrl = 'https://api.themoviedb.org/3/search/multi?query='.urlencode($query).'&include_adult=true';
        $lastPage = $isMultipageSearch
            ? $settings->searchFullPages
            : $settings->searchAutocompletePages;

        $cacheKey = 'search.'.md5($query.'.'.($isMultipageSearch ? 'full' : 'auto').".{$lastPage}");

        return Cache::remember($cacheKey, now()->addMinutes(5), function () use ($query, $baseUrl, $lastPage, $isMultipageSearch) {
            if ($this->isOverDailyLimit()) {
                return collect([]);
            }

            $responses = Http::pool(function (Pool $pool) use ($baseUrl, $lastPage) {
                return collect(range(1, $lastPage))
                    ->map(fn ($page) => $pool
                        ->withToken(config('services.tmdb.token'))
                        ->get($baseUrl."&page={$page}")
                    );
            });

            $results = collect($responses)
                ->filter(fn ($response) => $response->successful())
                ->flatMap(fn ($response) => $response->json()['results'] ?? [])
                ->values();

            $failedCount = collect($responses)->filter(fn ($response) => $response->failed())->count();

            $this->activityLogger->log(
                operation: 'search',
                mediaType: 'multi',
                requestCount: $lastPage,
                itemsFetched: $results->count(),
                status: $failedCount === $lastPage ? 'error' : 'success',
                errorMessage: $failedCount > 0 ? "{$failedCount} of {$lastPage} search pages failed" : null,
                metadata: ['query' => $query, 'multipage' => $isMultipageSearch],
            );

            return $results;
        });
    }

    private function isOverDailyLimit(): bool
    {
        $limit = $this->siteSettings->get()->tmdbDailyRequestLimit;

        $today = (int) Cache::remember('tmdb.requests.today', now()->addMinute(), fn () => (int) TmdbApiActivity::query()
            ->whereDate('created_at', today())
            ->sum('request_count'));

        return $today >= $limit;
    }
}
