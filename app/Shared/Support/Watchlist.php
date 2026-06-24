<?php

namespace App\Shared\Support;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

/**
 * Request-scoped cache of the authenticated user's watchlisted media keys
 * ("movie:123", "tv:456", "actor:789") so DTO factories avoid N+1 queries.
 */
final class Watchlist
{
    /** @var array<int, string>|null */
    private static ?array $keys = null;

    public static function normalizeType(string $typeOrClass): string
    {
        return match (true) {
            str_contains($typeOrClass, 'Tv') || $typeOrClass === 'tv' => 'tv',
            str_contains($typeOrClass, 'Actor') || $typeOrClass === 'actor' || $typeOrClass === 'person' => 'actor',
            default => 'movie',
        };
    }

    /** @return array<int, string> */
    public static function keys(): array
    {
        if (self::$keys !== null) {
            return self::$keys;
        }

        /** @var User|null $user */
        $user = Auth::user();

        if (! $user) {
            return self::$keys = [];
        }

        return self::$keys = $user->watchlist()
            ->get(['media_type', 'media_id'])
            ->map(fn ($row) => self::normalizeType($row->media_type).':'.(int) $row->media_id)
            ->all();
    }

    public static function has(string $type, int $id): bool
    {
        $key = self::normalizeType($type).":{$id}";

        return in_array($key, self::keys(), true);
    }

    public static function reset(): void
    {
        self::$keys = null;
    }
}
