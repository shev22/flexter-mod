<?php

namespace App\Stats\Services;

use App\Models\User;
use App\Movie\Models\Movie;
use App\Shared\Support\MediaResolver;
use App\WatchHistory\Models\WatchHistory;
use Illuminate\Support\Collection;

class StatsService
{
    /**
     * @return array<string, mixed>
     */
    public function profile(User $user): array
    {
        $entries = $user->watchHistories()->get();
        $genreResolver = MediaResolver::withGenres($entries);
        $mediaResolver = MediaResolver::fromHistoryEntries($entries);
        $genres = $this->topGenres($entries, $genreResolver);
        $hours = $this->hoursWatched($entries, $mediaResolver);

        return [
            'totals' => [
                'entries' => $entries->count(),
                'completed' => $entries->where('completed', true)->count(),
                'in_progress' => $entries->where('completed', false)->where('progress_percent', '>', 0)->count(),
                'hours' => $hours,
            ],
            'top_genres' => $genres,
            'badges' => $this->badges($entries, $hours, $genres),
            'recent_months' => $this->activityByMonth($entries),
        ];
    }

    /**
     * @param  Collection<int, WatchHistory>  $entries
     * @return array<int, array{label: string, count: int}>
     */
    private function topGenres(Collection $entries, MediaResolver $genreResolver): array
    {
        $counts = [];

        foreach ($entries as $entry) {
            $model = $genreResolver->getForEntry($entry);

            if (! $model) {
                continue;
            }

            foreach ($model->genres as $genre) {
                $counts[$genre->genre] = ($counts[$genre->genre] ?? 0) + 1;
            }
        }

        arsort($counts);

        return collect($counts)
            ->take(5)
            ->map(fn (int $count, string $label) => ['label' => $label, 'count' => $count])
            ->values()
            ->all();
    }

    /**
     * @param  Collection<int, WatchHistory>  $entries
     */
    private function hoursWatched(Collection $entries, MediaResolver $resolver): float
    {
        $minutes = $entries->sum(function (WatchHistory $entry) use ($resolver) {
            $media = $resolver->getForEntry($entry);

            if (! $media) {
                return 0;
            }

            $runtime = $media instanceof Movie
                ? (int) ($media->runtime ?? 120)
                : 45;

            return ($runtime * $entry->progress_percent) / 100;
        });

        return round($minutes / 60, 1);
    }

    /**
     * @param  Collection<int, WatchHistory>  $entries
     * @param  array<int, array{label: string, count: int}>  $genres
     * @return array<int, array{key: string, label: string, description: string}>
     */
    private function badges(Collection $entries, float $hours, array $genres): array
    {
        $badges = [];

        if ($entries->where('completed', true)->count() >= 10) {
            $badges[] = ['key' => 'marathon', 'label' => 'Marathon', 'description' => 'Completed 10+ titles'];
        }

        if ($hours >= 20) {
            $badges[] = ['key' => 'binge', 'label' => 'Binge Master', 'description' => '20+ hours tracked'];
        }

        if (collect($genres)->first()['label'] ?? null) {
            $top = $genres[0]['label'];
            $badges[] = ['key' => 'genre', 'label' => "{$top} Fan", 'description' => 'Your most-watched genre'];
        }

        if ($entries->where('media_type', 'tv')->count() >= 5) {
            $badges[] = ['key' => 'series', 'label' => 'Series Explorer', 'description' => '5+ series in your history'];
        }

        return $badges;
    }

    /**
     * @param  Collection<int, WatchHistory>  $entries
     * @return array<int, array{month: string, count: int}>
     */
    private function activityByMonth(Collection $entries): array
    {
        return $entries
            ->groupBy(fn (WatchHistory $e) => $e->last_watched_at?->format('M Y') ?? 'Unknown')
            ->map(fn ($group, $month) => ['month' => $month, 'count' => $group->count()])
            ->take(6)
            ->values()
            ->all();
    }
}
