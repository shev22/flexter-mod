<?php

namespace App\Shared\Support;

use Illuminate\Support\Facades\Cache;

final class HomeCache
{
    public static function version(): int
    {
        return (int) Cache::get('home.cache_version', 1);
    }

    public static function bust(): void
    {
        Cache::increment('home.cache_version');
    }

    public static function pageKey(?int $userId): string
    {
        $who = $userId ?? 'guest';

        return 'home.page.v'.self::version().".{$who}";
    }

    public static function railKey(string $kind, int $category, int $limit): string
    {
        return 'home.rail.v'.self::version().".{$kind}.{$category}.{$limit}";
    }
}
