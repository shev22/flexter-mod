<?php
namespace App\Services\MediaService;

use App\Enums\MediaType;
use App\Services\MediaService\Interfaces\MediaApiClientInterface;
use Illuminate\Http\Client\Pool;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Promise\Utils;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\Client\RequestException as LaravelRequestException;
class ApiClient implements MediaApiClientInterface
{
    function fetchMedia(string $mediaType, string $category, int $pages): Collection
    {
        $promises = [];

        try {
            for ($i = 1; $i <= $pages; $i++) {
                $promises[] = Http::withToken(config('services.tmdb.token'))
                    ->async()
                    ->get("https://api.themoviedb.org/3/{$mediaType}/{$category}", [
                        'page' => $i
                    ]);
            }

            $responses = Utils::unwrap($promises);

            return collect($responses)
                ->map(fn($response) => $response->json()['results'] ?? [])
                ->collapse();
        } catch (RequestException | LaravelRequestException $e) {

            \Log::error('TMDB API request failed: ' . $e->getMessage());

            return collect([]);
        } catch (\Throwable $e) {

            \Log::error('Unexpected error in fetchMedia: ' . $e->getMessage());
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

        return collect($responses)
            ->map(fn($response) => $response->json()['genres'] ?? [])
            ->collapse();
    }

    public function fetchMediaWithDetails(string $mediaId, string $mediaType, bool $withAttachments = true, bool $withPersonAttachments = false):array
    {
        $movieAttachments = $withAttachments ? '?append_to_response=videos,credits,images' : '';
        $personAttachments = $withPersonAttachments ? '?append_to_response=external_ids,tv_credits,movie_credits,images' : '';

        return Http::withToken(config('services.tmdb.token'))
            ->get('https://api.themoviedb.org/3/' . $mediaType . '/' . $mediaId . $movieAttachments . $personAttachments )
            ->json();
    }

    public function fetchRelatedMedia(string $mediaId, string $mediaType):array
    {
        return Http::withToken(config('services.tmdb.token'))
            ->get('https://api.themoviedb.org/3/' . $mediaType . '/' . $mediaId . '/similar')
            ->json();
    }

    /**
     * @inheritDoc
     */
    public function fetchTrending(string $mediaType, string $period): array
    {
        return Http::withToken(config('services.tmdb.token'))
            ->get('https://api.themoviedb.org/3/trending/' . $mediaType . '/' . $period)
            ->json()['results'];
    }

    /**
     * @inheritDoc
     */
    public function search(string $query, bool $isMultipageSearch = false): collection
    {
        $baseUrl = 'https://api.themoviedb.org/3/search/multi?query=' . urlencode($query) . '&include_adult=true';
        $isMultipageSearch ? $lastPage = 10 : $lastPage = 2;

        $responses = Http::pool(function (Pool $pool) use ($baseUrl, $lastPage) {
            return collect(range(1, $lastPage))
            ->map(fn ($page) => $pool
                ->withToken(config('services.tmdb.token'))
                ->get($baseUrl . "&page={$page}")
            );
        });

        return  collect($responses)
            ->filter(fn ($response) => $response->successful())
            ->flatMap(fn ($response) => $response->json()['results'])
            ->values()
            ;
    }

}
