<?php

namespace App\Console\Commands;

use App\Movie\Services\Interfaces\MovieServiceInterface;
use App\Services\MediaService\MediaEnrichmentService;
use App\Shared\Support\HomeCache;
use App\Tv\Services\Interfaces\TvServiceInterface;
use Illuminate\Console\Command;

/**
 * Re-syncs the "is_trending" flag against TMDB's current trending feed: titles
 * that are still trending stay flagged, titles that fell off are demoted. This
 * keeps the home carousel (which only shows is_trending = true) fresh without a
 * full catalogue re-fetch. Safe to run on a daily schedule.
 */
class RefreshTrending extends Command
{
    protected $signature = 'flexter:trending
                            {--only= : Limit to one section: movies|tv}';

    protected $description = 'Refresh which movies and tv shows are currently trending';

    public function handle(MovieServiceInterface $movies, TvServiceInterface $tv, MediaEnrichmentService $enricher): int
    {
        $only = $this->option('only');

        if ($only === null || $only === 'movies') {
            $this->components->task('Refreshing trending movies', function () use ($movies) {
                $movies->trending();

                return true;
            });
        }

        if ($only === null || $only === 'tv') {
            $this->components->task('Refreshing trending tv', function () use ($tv) {
                $tv->trending();

                return true;
            });
        }

        $this->components->task('Resolving trending logos & trailers', function () use ($enricher) {
            $enricher->enrichTrending();

            return true;
        });

        HomeCache::bust();

        $this->newLine();
        $this->info('Trending status is up to date.');

        return self::SUCCESS;
    }
}
