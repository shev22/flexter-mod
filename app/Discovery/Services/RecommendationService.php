<?php

namespace App\Discovery\Services;

use App\Enums\Categories;
use App\Models\User;
use App\Movie\Models\Movie;
use App\Shared\Data\MediaCardData;
use App\Shared\Support\MediaResolver;
use App\Shared\Support\Watchlist;
use App\Tv\Models\Tv;
use App\WatchHistory\Models\WatchHistory;
use Illuminate\Support\Collection;

class RecommendationService
{
    public const POOL_SIZE = 48;

    /**
     * @return array<int, array<string, mixed>>
     */
    public function forUser(?User $user, int $limit = self::POOL_SIZE): array
    {
        if ($user === null) {
            return $this->shuffledFallback($limit);
        }

        $history = $user->watchHistories()
            ->whereIn('media_type', ['movie', 'tv'])
            ->latest('last_watched_at')
            ->limit(20)
            ->get(['media_type', 'media_id']);

        $genreIds = $this->preferredGenreIds($user, $history);

        if ($genreIds->isEmpty()) {
            return $this->shuffledFallback($limit);
        }

        $watchedMovieIds = $history->where('media_type', 'movie')->pluck('media_id')->map(fn ($id) => (int) $id)->all();
        $watchedTvIds = $history->where('media_type', 'tv')->pluck('media_id')->map(fn ($id) => (int) $id)->all();

        $movies = Movie::query()
            ->select(['id', 'title', 'poster_path', 'release_date', 'vote_average', 'genre_ids'])
            ->where('category', Categories::POPULAR->value)
            ->whereHas('genres', fn ($q) => $q->whereIn('genres.id', $genreIds))
            ->when($watchedMovieIds !== [], fn ($q) => $q->whereNotIn('id', $watchedMovieIds))
            ->orderByRaw('CAST(popularity AS DECIMAL(12,3)) DESC')
            ->limit($limit)
            ->get();

        $items = $movies->map(fn (Movie $m) => MediaCardData::fromModel($m, Watchlist::has('movie', (int) $m->id)));

        if ($items->count() < $limit) {
            $remaining = $limit - $items->count();
            $movieIds = $movies->pluck('id')->all();

            $tv = Tv::query()
                ->select(['id', 'title', 'poster_path', 'release_date', 'vote_average', 'genre_ids'])
                ->where('category', Categories::POPULAR->value)
                ->whereHas('genres', fn ($q) => $q->whereIn('genres.id', $genreIds))
                ->when($watchedTvIds !== [], fn ($q) => $q->whereNotIn('id', $watchedTvIds))
                ->when($movieIds !== [], fn ($q) => $q->whereNotIn('id', $movieIds))
                ->orderByRaw('CAST(popularity AS DECIMAL(12,3)) DESC')
                ->limit($remaining)
                ->get()
                ->map(fn (Tv $t) => MediaCardData::fromModel($t, Watchlist::has('tv', (int) $t->id)));

            $items = $items->merge($tv);
        }

        return $items
            ->shuffle()
            ->map(fn ($dto) => $dto->toArray())
            ->values()
            ->all();
    }

    /**
     * @param  Collection<int, WatchHistory>  $history
     * @return Collection<int, int>
     */
    private function preferredGenreIds(?User $user, Collection $history): Collection
    {
        $user?->loadMissing('settings');

        $favorites = collect($user?->settings?->favorite_genre_ids ?? [])
            ->map(fn ($id) => (int) $id)
            ->filter(fn (int $id) => $id > 0)
            ->unique()
            ->take(5)
            ->values();

        if ($favorites->isNotEmpty()) {
            return $favorites;
        }

        if ($history->isEmpty()) {
            return collect();
        }

        $resolver = MediaResolver::withGenres($history);
        $counts = [];

        foreach ($history as $entry) {
            $model = $resolver->getForEntry($entry);

            if (! $model) {
                continue;
            }

            foreach ($model->genres as $genre) {
                $counts[$genre->id] = ($counts[$genre->id] ?? 0) + 1;
            }
        }

        arsort($counts);

        return collect(array_keys($counts))->take(5)->values();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function shuffledFallback(int $limit): array
    {
        return Movie::query()
            ->select(['id', 'title', 'poster_path', 'release_date', 'vote_average', 'genre_ids', 'overview'])
            ->where('category', Categories::TOP_RATED->value)
            ->orderByRaw('CAST(vote_average AS DECIMAL(4,2)) DESC')
            ->limit($limit)
            ->get()
            ->shuffle()
            ->map(fn (Movie $m) => MediaCardData::fromModel($m, Watchlist::has('movie', (int) $m->id))->toArray())
            ->values()
            ->all();
    }
}
