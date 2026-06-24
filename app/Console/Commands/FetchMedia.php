<?php

namespace App\Console\Commands;

use App\Actor\Services\Interfaces\ActorServiceInterface;
use App\Genre\Services\Interfaces\GenreServiceInterface;
use App\Movie\Services\Interfaces\MovieServiceInterface;
use App\Services\MediaService\MediaEnrichmentService;
use App\Tv\Services\Interfaces\TvServiceInterface;
use Illuminate\Console\Command;

/**
 * Pulls the catalogue (genres, movies, tv, actors) from TMDB into the local
 * database. Genres are fetched first so genre pivots can resolve their FKs.
 */
class FetchMedia extends Command
{
    protected $signature = 'flexter:fetch
                            {--only= : Limit to one section: genres|movies|tv|actors}';

    protected $description = 'Fetch genres, movies, tv shows and actors from TMDB into the database';

    public function handle(
        GenreServiceInterface $genres,
        MovieServiceInterface $movies,
        TvServiceInterface $tv,
        ActorServiceInterface $actors,
        MediaEnrichmentService $enricher,
    ): int {
        $only = $this->option('only');

        $this->fetchSection('genres', $only, fn () => $genres->createGenre());

        $this->fetchSection('movies', $only, function () use ($movies) {
            $movies->popular();
            $movies->nowPlaying();
            $movies->upcoming();
            $movies->topRated();
            $movies->trending();
        });

        $this->fetchSection('tv', $only, function () use ($tv) {
            $tv->popular();
            $tv->onTheAir();
            $tv->topRated();
            $tv->AiringToday();
            $tv->trending();
        });

        $this->fetchSection('actors', $only, fn () => $actors->createActors());

        // Once the catalogue (and trending flags) are in place, backfill logos
        // and trailers for the trending titles that feed the home carousel.
        if ($only === null || $only === 'movies' || $only === 'tv') {
            $this->components->task('Resolving trending logos & trailers', function () use ($enricher) {
                $enricher->enrichTrending();

                return true;
            });
        }

        $this->newLine();
        $this->info('Flexter catalogue is up to date.');

        return self::SUCCESS;
    }

    private function fetchSection(string $section, ?string $only, callable $callback): void
    {
        if ($only !== null && $only !== $section) {
            return;
        }

        $this->components->task("Fetching {$section}", function () use ($callback) {
            $callback();

            return true;
        });
    }
}
