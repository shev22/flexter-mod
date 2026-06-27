<?php

namespace App\Site\Data;

use Illuminate\Contracts\Support\Arrayable;
use JsonSerializable;

final class SiteSettingsData implements Arrayable, JsonSerializable
{
    public const MAX_TMDB_PAGES = 20;

    public function __construct(
        public string $siteName,
        public string $siteTagline,
        public int $cataloguePerPage,
        public int $moviePopularPages,
        public int $movieNowPlayingPages,
        public int $movieUpcomingPages,
        public int $movieTopRatedPages,
        public int $tvPopularPages,
        public int $tvOnTheAirPages,
        public int $tvTopRatedPages,
        public int $tvAiringTodayPages,
        public int $personPopularPages,
        public int $searchAutocompletePages,
        public int $searchFullPages,
        public bool $maintenanceMode,
        public bool $siteWideAutoplay,
        public int $tmdbDailyRequestLimit,
        public bool $enableRecommendations,
        public bool $enableActorFeed,
        public bool $enablePublicLists,
        public string $heroPinnedIds,
        public int $homeHeroLimit,
        public int $homeRecommendationsLimit,
        public int $homeActorFeedLimit,
        public int $homeFeaturedListsLimit,
        public bool $enablePayments,
    ) {}

    public static function defaults(): self
    {
        return new self(
            siteName: 'Flexter',
            siteTagline: 'Discover movies, series, and people.',
            cataloguePerPage: 24,
            moviePopularPages: 20,
            movieNowPlayingPages: 20,
            movieUpcomingPages: 20,
            movieTopRatedPages: 20,
            tvPopularPages: 20,
            tvOnTheAirPages: 20,
            tvTopRatedPages: 20,
            tvAiringTodayPages: 20,
            personPopularPages: 20,
            searchAutocompletePages: 2,
            searchFullPages: 10,
            maintenanceMode: false,
            siteWideAutoplay: false,
            tmdbDailyRequestLimit: 5000,
            enableRecommendations: true,
            enableActorFeed: true,
            enablePublicLists: true,
            heroPinnedIds: '',
            homeHeroLimit: 12,
            homeRecommendationsLimit: 20,
            homeActorFeedLimit: 10,
            homeFeaturedListsLimit: 6,
            enablePayments: false,
        );
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public static function fromArray(array $payload): self
    {
        $defaults = self::defaults();

        return new self(
            siteName: (string) ($payload['site_name'] ?? $defaults->siteName),
            siteTagline: (string) ($payload['site_tagline'] ?? $defaults->siteTagline),
            cataloguePerPage: self::int($payload['catalogue_per_page'] ?? null, $defaults->cataloguePerPage, 12, 60),
            moviePopularPages: self::tmdbPages($payload['movie_popular_pages'] ?? null, $defaults->moviePopularPages),
            movieNowPlayingPages: self::tmdbPages($payload['movie_now_playing_pages'] ?? null, $defaults->movieNowPlayingPages),
            movieUpcomingPages: self::tmdbPages($payload['movie_upcoming_pages'] ?? null, $defaults->movieUpcomingPages),
            movieTopRatedPages: self::tmdbPages($payload['movie_top_rated_pages'] ?? null, $defaults->movieTopRatedPages),
            tvPopularPages: self::tmdbPages($payload['tv_popular_pages'] ?? null, $defaults->tvPopularPages),
            tvOnTheAirPages: self::tmdbPages($payload['tv_on_the_air_pages'] ?? null, $defaults->tvOnTheAirPages),
            tvTopRatedPages: self::tmdbPages($payload['tv_top_rated_pages'] ?? null, $defaults->tvTopRatedPages),
            tvAiringTodayPages: self::tmdbPages($payload['tv_airing_today_pages'] ?? null, $defaults->tvAiringTodayPages),
            personPopularPages: self::tmdbPages($payload['person_popular_pages'] ?? null, $defaults->personPopularPages),
            searchAutocompletePages: self::int($payload['search_autocomplete_pages'] ?? null, $defaults->searchAutocompletePages, 1, self::MAX_TMDB_PAGES),
            searchFullPages: self::int($payload['search_full_pages'] ?? null, $defaults->searchFullPages, 1, self::MAX_TMDB_PAGES),
            maintenanceMode: (bool) ($payload['maintenance_mode'] ?? $defaults->maintenanceMode),
            siteWideAutoplay: (bool) ($payload['site_wide_autoplay'] ?? $defaults->siteWideAutoplay),
            tmdbDailyRequestLimit: self::int($payload['tmdb_daily_request_limit'] ?? null, $defaults->tmdbDailyRequestLimit, 100, 50000),
            enableRecommendations: (bool) ($payload['enable_recommendations'] ?? $defaults->enableRecommendations),
            enableActorFeed: (bool) ($payload['enable_actor_feed'] ?? $defaults->enableActorFeed),
            enablePublicLists: (bool) ($payload['enable_public_lists'] ?? $defaults->enablePublicLists),
            heroPinnedIds: (string) ($payload['hero_pinned_ids'] ?? $defaults->heroPinnedIds),
            homeHeroLimit: self::int($payload['home_hero_limit'] ?? null, $defaults->homeHeroLimit, 3, 30),
            homeRecommendationsLimit: self::int($payload['home_recommendations_limit'] ?? null, $defaults->homeRecommendationsLimit, 4, 48),
            homeActorFeedLimit: self::int($payload['home_actor_feed_limit'] ?? null, $defaults->homeActorFeedLimit, 4, 36),
            homeFeaturedListsLimit: self::int($payload['home_featured_lists_limit'] ?? null, $defaults->homeFeaturedListsLimit, 1, 12),
            enablePayments: (bool) ($payload['enable_payments'] ?? $defaults->enablePayments),
        );
    }

    public function syncPages(string $mediaType, string $category): int
    {
        return match ("{$mediaType}.{$category}") {
            'movie.popular' => $this->moviePopularPages,
            'movie.now_playing' => $this->movieNowPlayingPages,
            'movie.upcoming' => $this->movieUpcomingPages,
            'movie.top_rated' => $this->movieTopRatedPages,
            'tv.popular' => $this->tvPopularPages,
            'tv.on_the_air' => $this->tvOnTheAirPages,
            'tv.top_rated' => $this->tvTopRatedPages,
            'tv.airing_today' => $this->tvAiringTodayPages,
            'person.popular' => $this->personPopularPages,
            default => self::defaults()->moviePopularPages,
        };
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'site_name' => $this->siteName,
            'site_tagline' => $this->siteTagline,
            'catalogue_per_page' => $this->cataloguePerPage,
            'movie_popular_pages' => $this->moviePopularPages,
            'movie_now_playing_pages' => $this->movieNowPlayingPages,
            'movie_upcoming_pages' => $this->movieUpcomingPages,
            'movie_top_rated_pages' => $this->movieTopRatedPages,
            'tv_popular_pages' => $this->tvPopularPages,
            'tv_on_the_air_pages' => $this->tvOnTheAirPages,
            'tv_top_rated_pages' => $this->tvTopRatedPages,
            'tv_airing_today_pages' => $this->tvAiringTodayPages,
            'person_popular_pages' => $this->personPopularPages,
            'search_autocomplete_pages' => $this->searchAutocompletePages,
            'search_full_pages' => $this->searchFullPages,
            'maintenance_mode' => $this->maintenanceMode,
            'site_wide_autoplay' => $this->siteWideAutoplay,
            'tmdb_daily_request_limit' => $this->tmdbDailyRequestLimit,
            'enable_recommendations' => $this->enableRecommendations,
            'enable_actor_feed' => $this->enableActorFeed,
            'enable_public_lists' => $this->enablePublicLists,
            'hero_pinned_ids' => $this->heroPinnedIds,
            'home_hero_limit' => $this->homeHeroLimit,
            'home_recommendations_limit' => $this->homeRecommendationsLimit,
            'home_actor_feed_limit' => $this->homeActorFeedLimit,
            'home_featured_lists_limit' => $this->homeFeaturedListsLimit,
            'enable_payments' => $this->enablePayments,
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    private static function tmdbPages(mixed $value, int $default): int
    {
        return self::int($value, $default, 1, self::MAX_TMDB_PAGES);
    }

    private static function int(mixed $value, int $default, int $min, int $max): int
    {
        if (! is_numeric($value)) {
            return $default;
        }

        return max($min, min($max, (int) $value));
    }
}
