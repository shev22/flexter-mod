<?php

namespace App\Services\HomeService;

use App\Discovery\Services\ActorFeedService;
use App\Discovery\Services\RecommendationService;
use App\Enums\Categories;
use App\List\Services\FlexterListService;
use App\Repositories\Interfaces\HomeRepositoryInterface;
use App\Services\HomeService\Interfaces\HomeServiceInterface;
use App\Services\MediaService\MediaEnrichmentService;
use App\Shared\Support\HomeCache;
use App\Shared\Support\Present;
use App\Site\Services\Interfaces\SiteSettingsServiceInterface;
use App\WatchHistory\Services\Interfaces\WatchHistoryServiceInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class HomeService implements HomeServiceInterface
{
    public function __construct(
        protected HomeRepositoryInterface $homeRepository,
        protected MediaEnrichmentService $enricher,
        protected WatchHistoryServiceInterface $watchHistoryService,
        protected RecommendationService $recommendations,
        protected ActorFeedService $actorFeed,
        protected FlexterListService $lists,
        protected SiteSettingsServiceInterface $siteSettings,
    ) {
    }

    /**
     * @inheritDoc
     */
    public function getHomePageData(): array
    {
        $user = Auth::user();
        $key = HomeCache::pageKey($user?->id);
        $settings = $this->siteSettings->get();

        // Enrich outside the page cache so hero trailers/logos stay current even
        // when the rest of the home payload is served from cache.
        $hero = $this->homeRepository->trendingMovies()
            ->merge($this->homeRepository->trendingTv());

        // Resolve trailers/logos from cache + up to 8 TMDB detail calls per request.
        $this->enricher->enrichMany($hero, 8);

        $heroItems = Present::heroList($this->prioritizeHero($hero, $settings->heroPinnedIds));

        $payload = Cache::remember($key, now()->addMinutes(10), function () use ($user, $settings) {
            return [
                'hero' => [],
                'continueWatching' => $user
                    ? $this->watchHistoryService->continueWatching($user)
                    : [],
                'recommendations' => $settings->enableRecommendations
                    ? $this->recommendations->forUser($user, RecommendationService::POOL_SIZE)
                    : [],
                'actorFeed' => $settings->enableActorFeed
                    ? $this->actorFeed->forUser($user, ActorFeedService::POOL_SIZE)
                    : [],
                'movieRails' => [
                    $this->rail('In Theaters', $this->homeRepository->movieRail(Categories::NOW_PLAYING->value)),
                    $this->rail('Popular Movies', $this->homeRepository->movieRail(Categories::POPULAR->value)),
                    $this->rail('Top Rated', $this->homeRepository->movieRail(Categories::TOP_RATED->value)),
                    $this->rail('Coming Soon', $this->homeRepository->movieRail(Categories::UPCOMING->value)),
                ],
                'tvRails' => [
                    $this->rail('Airing Today', $this->homeRepository->tvRail(Categories::AIRING_TODAY->value)),
                    $this->rail('Popular Series', $this->homeRepository->tvRail(Categories::POPULAR->value)),
                    $this->rail('Top Rated Series', $this->homeRepository->tvRail(Categories::TOP_RATED->value)),
                    $this->rail('On The Air', $this->homeRepository->tvRail(Categories::ON_THE_AIR->value)),
                ],
            ];
        });

        $payload['hero'] = $heroItems;
        $payload['featuredLists'] = $settings->enablePublicLists
            ? $this->lists->featured()
            : [];

        return $payload;
    }

    /**
     * @param  iterable<\App\Movie\Models\Movie|\App\Tv\Models\Tv>  $hero
     * @return iterable<\App\Movie\Models\Movie|\App\Tv\Models\Tv>
     */
    private function prioritizeHero(iterable $hero, string $pinned): iterable
    {
        if ($pinned === '') {
            return $hero;
        }

        $keys = collect(explode(',', $pinned))
            ->map(fn (string $pair) => trim($pair))
            ->filter()
            ->flip();

        return collect($hero)->sortByDesc(function ($item) use ($keys) {
            $type = $item instanceof \App\Movie\Models\Movie ? 'movie' : 'tv';
            $key = "{$type}:{$item->id}";

            return $keys->has($key) ? 1 : 0;
        });
    }

    private function rail(string $title, iterable $items): array
    {
        return [
            'title' => $title,
            'items' => Present::cardList($items),
        ];
    }
}
