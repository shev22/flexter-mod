<?php

namespace App\Shared\Support;

use Closure;
use DateInterval;
use DateTimeInterface;
use Illuminate\Support\Facades\Cache;

/**
 * Versioned cache buckets. Bumping a bucket's version instantly invalidates
 * every key in that bucket without scanning or flushing the store.
 */
final class AppCache
{
    public const CATALOGUE = 'catalogue';

    public const LISTS = 'lists';

    /**
     * @template T
     *
     * @param  Closure(): T  $callback
     * @return T
     */
    public static function catalogue(string $key, Closure $callback): mixed
    {
        return self::remember(
            self::CATALOGUE,
            $key,
            config('flexter.cache.catalogue_ttl'),
            $callback,
        );
    }

    /**
     * @template T
     *
     * @param  Closure(): T  $callback
     * @return T
     */
    public static function lists(string $key, Closure $callback): mixed
    {
        return self::remember(
            self::LISTS,
            $key,
            config('flexter.cache.lists_ttl'),
            $callback,
        );
    }

    /**
     * @template T
     *
     * @param  Closure(): T  $callback
     * @return T
     */
    public static function remember(
        string $bucket,
        string $key,
        DateTimeInterface|DateInterval|int $ttl,
        Closure $callback,
    ): mixed {
        return Cache::remember(self::versionedKey($bucket, $key), $ttl, $callback);
    }

    public static function versionedKey(string $bucket, string $key): string
    {
        return "{$bucket}.v".self::version($bucket).".{$key}";
    }

    public static function version(string $bucket): int
    {
        return (int) Cache::get("{$bucket}.cache_version", 1);
    }

    public static function bust(string $bucket): void
    {
        Cache::increment("{$bucket}.cache_version");
    }

    public static function bustCatalogue(): void
    {
        self::bust(self::CATALOGUE);
    }

    public static function bustLists(): void
    {
        self::bust(self::LISTS);
    }

    public static function bustGenres(): void
    {
        Cache::forget('genres.shared');
    }

    public static function bustCatalogueAndHome(): void
    {
        self::bustCatalogue();
        HomeCache::bust();
        self::bustGenres();
    }
}
